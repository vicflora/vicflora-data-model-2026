<?php

namespace App\Observers;

use App\Models\Shared\Reference;
use App\Services\ReferenceFormatter;

class ReferenceObserver
{
    /**
     * Handle the Reference "saving" event.
     * Fires before the database write for both 'create' and 'update'.
     */
    public function saving(Reference $reference): void
    {
        $formatter = app(ReferenceFormatter::class);

        // 1. Populate the raw author string (e.g., "Klazenga, N. & Walsh, N. G.")
        // This leverages the accessor we built earlier.
        $reference->author_string = $reference->citation_authorship_string;

        // 2. Populate the full Markdown reference (Article/Book/Chapter formatting)
        $reference->full_reference_string = $formatter->format($reference);

        // 3. Populate the short citation (e.g., "Klazenga et al. (2026)")
        $reference->short_citation_string = $formatter->formatShort($reference);
    }
}