<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\MarketNotification;
use App\Models\NotificationPreference;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $notifications = MarketNotification::where('user_id', $userId)
            ->latest('created_at')
            ->limit(50)
            ->get();

        $unreadCount = MarketNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        return $this->successResponse([
            'unread_count' => $unreadCount,
            'items' => $notifications,
        ]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = MarketNotification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->update(['read_at' => Carbon::now()]);

        return $this->successResponse(null, 'Notification marked as read');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        MarketNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

        return $this->successResponse(null, 'All notifications marked as read');
    }

    public function getPreferences(Request $request): JsonResponse
    {
        $prefs = NotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'email_enabled' => true,
                'push_enabled' => true,
                'sms_enabled' => false,
                'marketing_enabled' => true,
            ]
        );

        return $this->successResponse($prefs);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email_enabled' => ['sometimes', 'boolean'],
            'push_enabled' => ['sometimes', 'boolean'],
            'sms_enabled' => ['sometimes', 'boolean'],
            'marketing_enabled' => ['sometimes', 'boolean'],
            'quiet_hours_json' => ['nullable', 'array'],
        ]);

        $prefs = NotificationPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return $this->successResponse($prefs, 'Notification preferences updated');
    }
}
