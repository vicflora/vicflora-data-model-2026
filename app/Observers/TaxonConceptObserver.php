<?php

namespace App\Observers;

use App\Models\Profile\Profile;
use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonConcept;
use App\Models\Taxonomy\TaxonConceptLabel;
use App\Models\Taxonomy\TaxonName;
use App\Models\Taxonomy\Treatment;

class TaxonConceptObserver
{
/**
     * Handle the TaxonConcept "created" event.
     */
    public function created(TaxonConcept $taxonConcept): void
    {
        if ($taxonConcept->taxon_tree_id) {
            $this->initializeTreatmentAndProfile($taxonConcept);
        }

        if (!$taxonConcept->according_to_id) {
            throw new \Exception("TaxonConcept [{$taxonConcept->id}] created without an 'according_to_id'.");
        }

        $this->syncConceptLabel($taxonConcept->fresh(['accordingTo', 'taxonName']));
    }

    /**
     * Creates the Treatment container and the associated Profile.
     */
    protected function initializeTreatmentAndProfile(TaxonConcept $taxonConcept): void
    {
        $taxonomy = $taxonConcept->taxonTree->taxonomy;

        // 1. Create the Treatment (which creates the underlying Reference)
        $treatment = Treatment::createWithSidecar([
            'reference_type_id' => ControlledTerm::getIdByCode('REFERENCE_TYPE', 'TREATMENT'),
            'author_string' => $taxonomy->author_string,
            'publication_year' => now()->year,
            'title' => "Treatment for " . $taxonConcept->taxonName->full_name,
        ], [
            'taxon_concept_id' => $taxonConcept->id,
            'taxonomy_id' => $taxonomy->id,
        ]);

        // 2. Update the local instance and the database record
        $taxonConcept->according_to_id = $treatment->id;
        $taxonConcept->saveQuietly(); // Quietly to avoid any recursive 'updated' loops

        // 3. Create the Profile
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