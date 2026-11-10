<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\UserDevice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDeviceController extends Controller
{
    use ApiResponse;

    /**
     * List all registered devices for the user.
     */
    public function index(Request $request): JsonResponse
    {
        $devices = $request->user()->devices()->latest('last_seen_at')->get();

        return $this->successResponse($devices);
    }

    /**
     * Revoke access for a specific device.
     */
    public function destroy(Request $request, UserDevice $device): JsonResponse
    {
        if ($device->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized access to this device record', 'FORBIDDEN', [], 403);
        }

        $device->update(['revoked_at' => Carbon::now()]);

        return $this->successResponse(null, 'Device access revoked successfully');
    }
}
