<?php

namespace App\Observers;

use App\Models\Profile\Profile;
use App\Models\Shared\Reference;
use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonConcept;
use App\Models\Taxonomy\TaxonConceptLabel;
use App\Models\Taxonomy\TaxonName;

class TaxonConceptObserver
{
    /**
     * Handle the TaxonConcept "created" event.
     */
    public function created(TaxonConcept $taxonConcept): void
    {
        // 1. Flora-specific automation
        if ($taxonConcept->taxon_tree_id) {
            $this->initializeTreatmentAndProfile($taxonConcept);
        }

        // 2. Strict Governance Check
        // If for some reason it still doesn't have an according_to_id, 
        // we should probably throw an exception to prevent 'orphan' concepts.
        if (!$taxonConcept->according_to_id) {
            throw new \Exception("TaxonConcept [{$taxonConcept->id}] created without an 'according_to_id'.");
        }

        // 3. Finalize the Label
        // Refresh to ensure we have the new according_to relationship loaded
        $this->syncConceptLabel($taxonConcept->fresh(['accordingTo', 'taxonName']));
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

    /**
     * Create or update the sidecar label for the concept.
     */
    protected function syncConceptLabel(TaxonConcept $taxonConcept): void
    {
        // 1. Generate the string: "Base Name sec. Author, Year"
        $baseName = $taxonConcept->taxonName;
        $author = $taxonConcept->accordingTo->authorship; // Or your preferred reference string
        $year = $taxonConcept->accordingTo->publication_year;
        
        $labelString = "{$baseName->full_name} sec. {$author} ({$year})";

        // 2. Create the "Label Name" record in taxon_names
        // This is the identity of the sidecar
        $labelName = TaxonName::create([
            'name_string' => $baseName->name_string,
            'full_name' => $labelString,
            'rank_id' => $baseName->rank_id,
            'created_by_id' => $taxonConcept->updated_by_id ?? $taxonConcept->created_by_id,
        ]);

        // 3. Promote it to a TaxonConceptLabel sidecar
        TaxonConceptLabel::promote($labelName, [
            'base_name_id' => $taxonConcept->taxon_name_id,
            'taxon_concept_id' => $taxonConcept->id,
        ]);
    }
}