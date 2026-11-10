<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Models\Plan;
use App\Models\Promotion;
use App\Models\PromotionProduct;
use App\Models\Refund;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    use ApiResponse;

    public function plans(): JsonResponse
    {
        $plans = Plan::where('active', true)->get();
        return $this->successResponse($plans);
    }

    public function promotionProducts(): JsonResponse
    {
        $products = PromotionProduct::where('active', true)->get();
        return $this->successResponse($products);
    }

    public function checkoutPromotion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'listing_id' => ['required', 'integer'],
            'product_code' => ['required', 'string'],
            'idempotency_key' => ['required', 'string', 'max:100'],
        ]);

        $listing = Listing::findOrFail($validated['listing_id']);
        if ($listing->seller_id !== $request->user()->id) {
            abort(403);
        }

        $product = PromotionProduct::where('code', $validated['product_code'])->firstOrFail();

        // Idempotency check
        $existingOrder = Order::where('idempotency_key', $validated['idempotency_key'])->first();
        if ($existingOrder) {
            return $this->successResponse($existingOrder->load('items', 'invoice'), 'Existing order returned');
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'currency' => $product->currency,
            'subtotal_minor' => $product->price_minor,
            'tax_minor' => 0,
            'total_minor' => $product->price_minor,
            'status' => 'awaiting_payment',
            'idempotency_key' => $validated['idempotency_key'],
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'item_type' => 'promotion',
            'item_reference_id' => $product->id,
            'quantity' => 1,
            'unit_price_minor' => $product->price_minor,
            'total_minor' => $product->price_minor,
        ]);

        // Create pending payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'mock_gateway',
            'provider_payment_id' => 'pay_' . Str::random(16),
            'amount_minor' => $order->total_minor,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        // Generate invoice record
        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(6)),
            'issued_at' => Carbon::now(),
            'tax_breakdown_json' => ['tax_rate' => 0, 'tax_amount_minor' => 0],
        ]);

        return $this->successResponse([
            'order' => $order->load('items'),
            'payment' => $payment,
            'invoice' => $invoice,
            'checkout_url' => 'https://checkout.biglivemarketplace.internal/pay/' . $payment->provider_payment_id,
        ], 'Checkout order created successfully', 201);
    }

    public function checkoutSubscription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_code' => ['required', 'string'],
            'business_id' => ['nullable', 'integer'],
            'idempotency_key' => ['required', 'string', 'max:100'],
        ]);

        $plan = Plan::where('code', $validated['plan_code'])->firstOrFail();

        $existingOrder = Order::where('idempotency_key', $validated['idempotency_key'])->first();
        if ($existingOrder) {
            return $this->successResponse($existingOrder->load('items', 'invoice'), 'Existing order returned');
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'currency' => $plan->currency,
            'subtotal_minor' => $plan->price_minor,
            'tax_minor' => 0,
            'total_minor' => $plan->price_minor,
            'status' => 'awaiting_payment',
            'idempotency_key' => $validated['idempotency_key'],
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_type' => 'subscription',
            'item_reference_id' => $plan->id,
            'quantity' => 1,
            'unit_price_minor' => $plan->price_minor,
            'total_minor' => $plan->price_minor,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'mock_gateway',
            'provider_payment_id' => 'sub_pay_' . Str::random(16),
            'amount_minor' => $order->total_minor,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-SUB-' . date('Ymd') . '-' . Str::upper(Str::random(6)),
            'issued_at' => Carbon::now(),
        ]);

        return $this->successResponse([
            'order' => $order->load('items'),
            'payment' => $payment,
            'invoice' => $invoice,
        ], 'Subscription checkout initiated', 201);
    }

    public function orders(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items', 'payments', 'invoice'])
            ->latest('id')
            ->get();

        return $this->successResponse($orders);
    }

    public function showOrder(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        return $this->successResponse($order->load('items', 'payments', 'invoice'));
    }

    public function showInvoice(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->order->user_id !== $request->user()->id) {
            abort(403);
        }

        return $this->successResponse($invoice->load('order.items'));
    }

    public function handleWebhook(Request $request, string $provider): JsonResponse
    {
        $signature = $request->header('X-Webhook-Signature', 'mock_valid_signature');
        if (empty($signature)) {
            return $this->errorResponse('Missing webhook signature', 'UNAUTHORIZED', [], 401);
        }

        $eventId = $request->input('event_id', (string) Str::uuid());
        $payloadHash = hash('sha256', $request->getContent());

        // Idempotent webhook tracking
        $webhookEvent = PaymentWebhookEvent::where('provider', $provider)
            ->where('external_event_id', $eventId)
            ->first();

        if ($webhookEvent && $webhookEvent->processed_at !== null) {
            return response()->json(['status' => 'already_processed'], 200);
        }

        if (!$webhookEvent) {
            $webhookEvent = PaymentWebhookEvent::create([
                'provider' => $provider,
                'external_event_id' => $eventId,
                'payload_hash' => $payloadHash,
            ]);
        }

        $paymentId = $request->input('provider_payment_id');
        $status = $request->input('status'); // paid, failed

        $payment = Payment::where('provider_payment_id', $paymentId)->first();
        if ($payment) {
            if ($status === 'paid') {
                $payment->update([
                    'status' => 'captured',
                    'captured_at' => Carbon::now(),
                ]);

                $order = $payment->order;
                $order->update(['status' => 'paid']);

                // Activate order items (promotions or subscriptions)
                foreach ($order->items as $item) {
                    if ($item->item_type === 'promotion') {
                        $product = PromotionProduct::find($item->item_reference_id);
                        if ($product) {
                            $listing = Listing::where('seller_id', $order->user_id)->latest('id')->first();
                            if ($listing) {
                                Promotion::create([
                                    'listing_id' => $listing->id,
                                    'promotion_product_id' => $product->id,
                                    'order_item_id' => $item->id,
                                    'starts_at' => Carbon::now(),
                                    'ends_at' => Carbon::now()->addHours($product->duration_hours),
                                    'status' => 'active',
                                ]);
                            }
                        }
                    } elseif ($item->item_type === 'subscription') {
                        $plan = Plan::find($item->item_reference_id);
                        if ($plan) {
                            Subscription::create([
                                'user_id' => $order->user_id,
                                'plan_id' => $plan->id,
                                'provider' => $provider,
                                'status' => 'active',
                                'starts_at' => Carbon::now(),
                                'ends_at' => Carbon::now()->addMonth(),
                            ]);
                        }
                    }
                }
            } elseif ($status === 'failed') {
                $payment->update(['status' => 'failed']);
                $payment->order->update(['status' => 'failed']);
            }
        }

        $webhookEvent->update(['processed_at' => Carbon::now()]);

        return response()->json(['status' => 'processed'], 200);
    }

    public function requestRefund(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $payment = Payment::findOrFail($validated['payment_id']);
        if ($payment->order->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($payment->status !== 'captured') {
            return $this->errorResponse('Payment is not eligible for refund', 'INVALID_STATE', [], 422);
        }

        $refund = Refund::create([
            'payment_id' => $payment->id,
            'amount_minor' => $payment->amount_minor,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return $this->successResponse($refund, 'Refund request submitted for review', 201);
    }
}
