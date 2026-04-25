<?php

namespace App\Exceptions;

use App\Models\Taxonomy\TaxonConcept;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MissingAccordingToException extends Exception
{
    public function __construct(
        protected TaxonConcept $taxonConcept,
        string $message = "A TaxonConcept must be associated with an 'according_to' reference.",
        int $code = 422
    ) {
        parent::__construct($message, $code);
    }

    public function report(): void
    {
        Log::error('Integrity Violation: Missing AccordingTo', [
            'taxon_concept_id' => $this->taxonConcept->id,
            'taxon_name' => $this->taxonConcept->taxonName?->full_name,
            'created_by' => $this->taxonConcept->created_by_id,
        ]);
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Nomenclatural Integrity Violation',
                'message' => $this->getMessage(),
                'taxon_concept_id' => $this->taxonConcept->id,
            ], $this->code);
        }

        return back()->withErrors([
            'according_to_id' => $this->getMessage()
        ])->withInput();
    }
}