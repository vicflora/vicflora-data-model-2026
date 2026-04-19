<?php

namespace App\Observers;

use App\Models\Profile\Profile;
use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\Reference;
use App\Models\Taxonomy\TreatmentVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ProfileObserver
{
    /**
     * Handle logic BEFORE the record is written to the DB.
     * Use 'saving' to catch both creates and updates in one go.
     */
    public function saving(Profile $profile): void
    {
        $agentId = $this->resolveAgentId();

        if (!$profile->exists) {
            // Logic for NEW profiles
            $profile->version = 1;
            if ($agentId) {
                $profile->created_by_id = $agentId;
                $profile->updated_by_id = $agentId;
            }
        } else {
            // Logic for UPDATING profiles
            $profile->version++;
            if ($agentId) {
                $profile->updated_by_id = $agentId;
            }
        }
    }

    /**
     * Handle logic AFTER the record is written to the DB.
     */
    public function saved(Profile $profile): void
    {
        $today = Carbon::today();

        // Look for a Reference of type TREATMENT_VERSION for this profile created today
        $existingReference = Reference::whereHas('treatmentVersion', function ($query) use ($profile) {
                $query->where('taxon_concept_id', $profile->taxon_concept_id);
            })
            ->where('reference_type_id', ControlledTerm::getIdByCode('REFERENCE_TYPE', 'TREATMENT_VERSION'))
            ->whereDate('created_at', $today)
            ->first();

        // We call the method regardless; the method will now handle Update vs Create
        $this->persistDailyTreatmentVersion($profile, $existingReference);
    }
    
    /**
     * Create a Reference and TreatmentVersion snapshot.
     */
    protected function persistDailyTreatmentVersion(Profile $profile, ?Reference $existingReference = null): void
    {
        // Use the relationship to get the name (Assumes eager loading or lazy load)
        $taxonName = $profile->taxonConcept->taxonName->name_string;
        $dateString = now()->format('Y-m-d');

        $profile->load([
            // Load sections with their type code and body text
            'sections' => function ($query) {
                $query->select('id', 'profile_id', 'profile_section_type_id', 'body_text', 'sort_order');
            },
            'sections.type:id,code',

            // Load distribution with all the descriptive metadata codes
            'distribution' => function ($query) {
                // Ensure foreign keys are included so Laravel can match the children
                $query->select(
                    'id', 'profile_id', 'area_id', 'gazetteer_id', 
                    'occurrence_status_id', 'establishment_means_id', 
                    'degree_of_establishment_id', 'threat_status_authority_id', 
                    'threat_status_id', 'is_endemic', 'locality'
                );
            },
            'distribution.area:id,name',
            'distribution.occurrenceStatus:id,code',
            'distribution.establishmentMeans:id,code',
            'distribution.degreeOfEstablishment:id,code',
            'distribution.threatStatusAuthority:id,code',
            'distribution.threatStatus:id,code'
        ]);
        
        $refTypeId = ControlledTerm::getIdByCode('REFERENCE_TYPE', 'TREATMENT_VERSION');

        $reference = $existingReference ?? new Reference();

        $reference->persist([
            // Base reference attributes
            'reference_type_id' => $refTypeId,
            'title' => "Treatment for {$taxonName} ({$dateString})",
            'year' => now()->year,

            // Tell Reference::selectSidecarModel to return a TreatmentVersion instance
            'reference_role' => 'TREATMENT_VERSION',

            // Treatment version attributes
            'taxon_concept_id' => $profile->taxon_concept_id,
            'version_number' => $profile->version, 
            'version_label' => "v{$dateString}",
            'data_snapshot' => $profile->toJson(), 
        ]);
    }

    /**
     * Helper to map the current User to an Agent record.
     */
    protected function resolveAgentId(): ?int
    {
        if (Auth::check()) {
            return Agent::where('user_id', Auth::id())->value('id');
        }
        return null;
    }
}