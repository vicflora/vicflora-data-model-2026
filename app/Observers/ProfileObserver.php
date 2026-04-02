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

        // Check if this specific profile already has a version today
        $exists = TreatmentVersion::where('profile_id', $profile->id)
            ->whereDate('created_at', $today)
            ->exists();

        if (!$exists) {
            $this->createDailyTreatmentVersion($profile);
        }
    }

    /**
     * Create a Reference and TreatmentVersion snapshot.
     */
    protected function createDailyTreatmentVersion(Profile $profile): void
    {
        // Use the relationship to get the name (Assumes eager loading or lazy load)
        $taxonName = $profile->taxonConcept->taxonName->name_string;
        $dateString = now()->format('Y-m-d');
        
        $refTypeId = ControlledTerm::getIdByCode('REFERENCE_TYPE', 'TREATMENT_VERSION');

        Reference::createWithSidecar('treatmentVersion', [
            'reference_type_id' => $refTypeId,
            'title' => "Treatment for {$taxonName} ({$dateString})",
            'publication_year' => now()->year,
        ], [
            'profile_id' => $profile->id,
            'version_number' => $profile->version, 
            'label' => "v{$dateString}",
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