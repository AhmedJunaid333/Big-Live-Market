<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\UserVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use ApiResponse;

    /**
     * Submit an identity verification request.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:cnic,passport,trade_license'],
            'evidence_private_path' => ['required', 'string', 'max:255'],
        ]);

        $existingPending = UserVerification::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return $this->errorResponse(
                'You already have a pending verification request under review',
                'VERIFICATION_PENDING',
                [],
                422
            );
        }

        $verification = UserVerification::create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'evidence_private_path' => $validated['evidence_private_path'],
            'status' => 'pending',
        ]);

        return $this->successResponse($verification, 'Verification request submitted successfully', 201);
    }
}
