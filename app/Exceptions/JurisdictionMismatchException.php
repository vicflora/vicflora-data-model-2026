<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JurisdictionMismatchException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(
        protected int $geographicAreaId,
        protected int $threatStatusAreaId,
        string $message = "The threat status jurisdiction does not match the geographic area.",
        int $code = 422
    ) {
        parent::__construct($message, $code);
    }

    /**
     * Report the exception (e.g., to Sentry or Logs).
     */
    public function report(): void
    {
        Log::warning('Jurisdiction Mismatch Attempted', [
            'geographic_area_id' => $this->geographicAreaId,
            'threat_status_area_id' => $this->threatStatusAreaId,
        ]);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $payload = [
            'error' => 'Jurisdiction Mismatch',
            'message' => $this->getMessage(),
            'context' => [
                'expected_area_id' => $this->geographicAreaId,
                'received_area_id' => $this->threatStatusAreaId,
            ],
        ];

        // If it's an API call, return JSON.
        if ($request->expectsJson()) {
            return response()->json($payload, $this->getCode());
        }

        // If it's an Inertia/Web request, redirect back with error bag.
        return back()->withErrors([
            'threat_status_id' => $this->getMessage() . " (Area mismatch: {$this->geographicAreaId} vs {$this->threatStatusAreaId})"
        ])->withInput();
    }
}