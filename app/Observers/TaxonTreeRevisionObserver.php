<?php

namespace App\Observers;

use App\Models\Taxonomy\TaxonTreeRevision;
use App\Models\Taxonomy\TaxonomyVersion;
use App\Models\Taxonomy\Reference;
use App\Models\Taxonomy\ControlledTerm;
use Illuminate\Support\Carbon;

class TaxonTreeRevisionObserver
{
    /**
     * Handle the TaxonTreeRevision "created" event.
     */
    public function created(TaxonTreeRevision $revision): void
    {
        $today = Carbon::today();

        // Check if a TaxonomyVersion already exists for this tree today
        $exists = TaxonomyVersion::where('taxon_tree_id', $revision->taxon_tree_id)
            ->whereDate('created_at', $today)
            ->exists();

        if (!$exists) {
            $this->createDailyVersion($revision);
        }
    }

    /**
     * Use the sidecar pattern to create a Reference and TaxonomyVersion.
     */
    protected function createDailyVersion(TaxonTreeRevision $revision): void
    {
        $treeName = $revision->tree->name; // e.g., "VicFlora"
        $dateString = now()->format('Y-m-d');
        
        // Resolve the Reference Type for a Checklist/Version
        $refTypeId = ControlledTerm::getIdByCode('REFERENCE_TYPE', 'CHECKLIST');

        Reference::createWithSidecar('taxonomyVersion', [
            // 1. Reference Data (The bibliographic entry)
            'reference_type_id' => $refTypeId,
            'title' => "{$treeName} - Taxonomy Version ({$dateString})",
            'publication_year' => now()->year,
        ], [
            // 2. TaxonomyVersion Sidecar Data
            'taxon_tree_id' => $revision->taxon_tree_id,
            'revision_id'   => $revision->id,
            'version_date'  => now(),
            'label'         => "v{$dateString}",
        ]);
    }
}