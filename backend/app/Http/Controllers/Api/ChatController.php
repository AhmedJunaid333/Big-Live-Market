<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\Offer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    use ApiResponse;

    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Conversation::where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->with(['listing:id,title,price_minor,currency', 'buyer:id,name', 'seller:id,name'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return $this->successResponse($conversations);
    }

    public function startConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'listing_id' => ['required', 'integer'],
            'initial_message' => ['sometimes', 'string', 'max:2000'],
        ]);

        $listing = Listing::findOrFail($validated['listing_id']);
        $buyerId = $request->user()->id;
        $sellerId = $listing->seller_id;

        if ($buyerId === $sellerId) {
            return $this->errorResponse('You cannot start a conversation on your own listing', 'BAD_REQUEST', [], 400);
        }

        // Check if seller blocked buyer or vice versa
        if ($this->isBlocked($buyerId, $sellerId)) {
            return $this->errorResponse('Cannot message this user due to block restrictions', 'FORBIDDEN', [], 403);
        }

        $conversation = Conversation::firstOrCreate(
            [
                'listing_id' => $listing->id,
                'buyer_id' => $buyerId,
                'seller_id' => $sellerId,
            ],
            [
                'last_message_at' => Carbon::now(),
                'status' => 'active',
            ]
        );

        if (!empty($validated['initial_message'])) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $buyerId,
                'type' => 'text',
                'body' => $validated['initial_message'],
            ]);
            $conversation->update(['last_message_at' => Carbon::now()]);
        }

        return $this->successResponse($conversation->load(['listing', 'seller:id,name']), 'Conversation initialized', 201);
    }

    public function showConversation(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $conversation->load(['listing', 'buyer:id,name', 'seller:id,name']);
        return $this->successResponse($conversation);
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $messages = $conversation->messages()
            ->with(['sender:id,name', 'media'])
            ->latest('id')
            ->cursorPaginate(30);

        return $this->collectionResponse(
            $messages->items(),
            $messages->nextCursor()?->encode(),
            200
        );
    }

    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $otherId = $conversation->buyer_id === $request->user()->id ? $conversation->seller_id : $conversation->buyer_id;
        if ($this->isBlocked($request->user()->id, $otherId)) {
            return $this->errorResponse('Cannot send message due to block restrictions', 'FORBIDDEN', [], 403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'media_id' => ['nullable', 'integer'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'type' => !empty($validated['media_id']) ? 'image' : 'text',
            'body' => $validated['body'],
            'media_id' => $validated['media_id'] ?? null,
        ]);

        $conversation->update(['last_message_at' => Carbon::now()]);

        return $this->successResponse($message->load('sender:id,name'), 'Message sent', 201);
    }

    public function markRead(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

        return $this->successResponse(null, 'Messages marked as read');
    }

    public function offers(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $offers = $conversation->offers()->latest('id')->get();
        return $this->successResponse($offers);
    }

    public function createOffer(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $validated = $request->validate([
            'amount_minor' => ['required', 'integer', 'min:100'],
        ]);

        $offer = Offer::create([
            'conversation_id' => $conversation->id,
            'listing_id' => $conversation->listing_id,
            'buyer_id' => $conversation->buyer_id,
            'seller_id' => $conversation->seller_id,
            'amount_minor' => $validated['amount_minor'],
            'currency' => $conversation->listing->currency,
            'status' => 'pending',
            'expires_at' => Carbon::now()->addDays(3),
        ]);

        // Post system message into thread
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'type' => 'offer',
            'body' => 'Made an offer: ' . $conversation->listing->currency . ' ' . number_format($offer->amount_minor / 100),
        ]);

        $conversation->update(['last_message_at' => Carbon::now()]);

        return $this->successResponse($offer, 'Offer created', 201);
    }

    public function acceptOffer(Request $request, Offer $offer): JsonResponse
    {
        // Only recipient can accept (if buyer made offer, seller accepts)
        if ($request->user()->id !== $offer->seller_id && $request->user()->id !== $offer->buyer_id) {
            abort(403);
        }

        if ($offer->status !== 'pending') {
            return $this->errorResponse('Offer is no longer pending', 'INVALID_STATE', [], 422);
        }

        $offer->update([
            'status' => 'accepted',
            'responded_at' => Carbon::now(),
        ]);

        return $this->successResponse($offer, 'Offer accepted');
    }

    public function rejectOffer(Request $request, Offer $offer): JsonResponse
    {
        if ($request->user()->id !== $offer->seller_id && $request->user()->id !== $offer->buyer_id) {
            abort(403);
        }

        $offer->update([
            'status' => 'rejected',
            'responded_at' => Carbon::now(),
        ]);

        return $this->successResponse($offer, 'Offer rejected');
    }

    public function counterOffer(Request $request, Offer $offer): JsonResponse
    {
        if ($request->user()->id !== $offer->seller_id && $request->user()->id !== $offer->buyer_id) {
            abort(403);
        }

        $validated = $request->validate([
            'amount_minor' => ['required', 'integer', 'min:100'],
        ]);

        $offer->update([
            'status' => 'countered',
            'responded_at' => Carbon::now(),
        ]);

        $counter = Offer::create([
            'conversation_id' => $offer->conversation_id,
            'listing_id' => $offer->listing_id,
            'buyer_id' => $offer->buyer_id,
            'seller_id' => $offer->seller_id,
            'amount_minor' => $validated['amount_minor'],
            'currency' => $offer->currency,
            'status' => 'pending',
            'parent_offer_id' => $offer->id,
            'expires_at' => Carbon::now()->addDays(3),
        ]);

        return $this->successResponse($counter, 'Counter offer submitted', 201);
    }

    public function blockUser(Request $request, int $userId): JsonResponse
    {
        if ($request->user()->id === $userId) {
            return $this->errorResponse('Cannot block yourself', 'BAD_REQUEST', [], 400);
        }

        DB::table('user_blocks')->updateOrInsert([
            'blocker_id' => $request->user()->id,
            'blocked_id' => $userId,
        ], [
            'created_at' => Carbon::now(),
        ]);

        return $this->successResponse(null, 'User blocked successfully');
    }

    public function unblockUser(Request $request, int $userId): JsonResponse
    {
        DB::table('user_blocks')
            ->where('blocker_id', $request->user()->id)
            ->where('blocked_id', $userId)
            ->delete();

        return $this->successResponse(null, 'User unblocked successfully');
    }

    protected function isBlocked(int $userA, int $userB): bool
    {
        return DB::table('user_blocks')
            ->where(function ($q) use ($userA, $userB) {
                $q->where('blocker_id', $userA)->where('blocked_id', $userB);
            })
            ->orWhere(function ($q) use ($userA, $userB) {
                $q->where('blocker_id', $userB)->where('blocked_id', $userA);
            })
            ->exists();
    }

    protected function authorizeParticipant($user, Conversation $conversation): void
    {
        if (!$conversation->isParticipant($user->id)) {
            abort(403, 'Unauthorized access to this conversation');
        }
    }
}
