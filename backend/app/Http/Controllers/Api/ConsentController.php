<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\UserConsent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsentController extends Controller
{
    use ApiResponse;

    /**
     * Record accepted consent version.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'in:terms_of_service,privacy_policy,age_declaration,marketing_optin'],
            'version' => ['required', 'string', 'max:20'],
        ]);

        $consent = UserConsent::create([
            'user_id' => $request->user()->id,
            'document_type' => $validated['document_type'],
            'version' => $validated['version'],
            'accepted_at' => Carbon::now(),
            'ip_hash' => hash('sha256', (string) $request->ip()),
        ]);

        return $this->successResponse($consent, 'Consent recorded successfully', 201);
    }
}
