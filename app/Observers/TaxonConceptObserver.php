<?php

namespace App\Observers;

use App\Models\Taxonomy\TaxonConcept;
use App\Models\Taxonomy\Reference;
use App\Models\Taxonomy\ControlledTerm;
use App\Models\Taxonomy\Profile;

class TaxonConceptObserver
{
    /**
     * Handle the TaxonConcept "created" event.
     */
    public function created(TaxonConcept $taxonConcept): void
    {
        // Only automate if the concept is scoped to a specific tree (Flora)
        if ($taxonConcept->taxon_tree_id) {
            $this->initializeTreatmentAndProfile($taxonConcept);
        }
    }

    /**
     * Creates the Reference/Treatment container and the associated Profile.
     */
    protected function initializeTreatmentAndProfile(TaxonConcept $taxonConcept): void
    {
        // 1. Create the Reference with Treatment Sidecar
        // Treatment acts as the 'According To' for this specific concept.
        $reference = Reference::createWithSidecar('treatment', [
            'reference_type_id' => ControlledTerm::getIdByCode('REFERENCE_TYPE', 'TREATMENT'),
            'title' => "Treatment for " . $taxonConcept->taxon_name->scientific_name,
        ], [
            'taxon_concept_id' => $taxonConcept->id,
        ]);

        // 2. Attach the new Treatment (Reference) as the 'According To'
        $taxonConcept->update(['according_to_id' => $reference->id]);

        // 3. Create the Profile (The actual workspace)
        // Note: Status is now on the Profile per your instruction.
        Profile::create([
            'taxon_concept_id' => $taxonConcept->id,
            'status_id' => ControlledTerm::getIdByCode('PUBLICATION_STATUS', 'DRAFT'),
            'version' => 1,
            'is_published' => false,
        ]);
    }
}