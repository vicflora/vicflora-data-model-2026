<?php

namespace App\Observers;

use App\Models\Taxonomy\TaxonTreeRevision;
use App\Models\Taxonomy\TaxonomyVersion;
use App\Models\Shared\Reference;
use App\Models\Shared\ControlledTerm;
use Illuminate\Support\Carbon;

class TaxonTreeRevisionObserver
{
    /**
     * Handle the TaxonTreeRevision "created" event.
     */
    public function created(TaxonTreeRevision $revision): void
    {
        $today = now()->startOfDay();

        // 1. Check if a version already exists for this tree today
        $version = TaxonomyVersion::where('taxon_tree_id', $revision->taxon_tree_id)
            ->where('created_at', '>=', $today)
            ->first();

        // 2. If not, create it using the trait on the TaxonomyVersion model
        if (!$version) {
            $version = $this->createDailyVersion($revision);
        }

        // 3. Update the revision to link it to the newly found/created version
        // Using the primary key of the sidecar table (reference_id)
        $revision->updateQuietly([
            'taxonomy_version_id' => $version->reference_id
        ]);
    }

    /**
     * Use the sidecar pattern to create a Reference and TaxonomyVersion.
     */
    protected function createDailyVersion(TaxonTreeRevision $revision): TaxonomyVersion
    {
        $treeName = $revision->taxonTree->name;
        $dateString = now()->format('Y-m-d');
        $refTypeId = ControlledTerm::getIdByCode('REFERENCE_TYPE', 'CHECKLIST');

        return TaxonomyVersion::createWithSidecar([
            'reference_type_id' => $refTypeId,
            'title' => "{$treeName} - Taxonomy Version ({$dateString})",
            'publication_year' => now()->year,
        ], [
            'taxon_tree_id' => $revision->taxon_tree_id,
            'version_date'  => now(),
            'label'         => "v{$dateString}",
        ]);
    }
}