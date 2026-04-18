<?php

namespace App\Observers;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;

class AgentObserver
{
    /**
     * Handle the Agent "saving" event.
     * This catches both 'creating' and 'updating'.
     */
    public function saving(Agent $agent): void
    {
        // Check if the agent is a person
        if ($this->isPerson($agent)) {
            // Collect name parts and filter out nulls/empty strings
            $parts = array_filter([
                $agent->last_name, 
                $agent->first_name
            ]);

            // Formats to "Klazenga, Niels" or "Klazenga" if first name is missing
            // This ensures the non-nullable 'name' column is satisfied.
            $agent->name = implode(', ', $parts);
        }
    }

    /**
     * Helper to determine if the agent is a person.
     */
    protected function isPerson(Agent $agent): bool
    {
        return $agent->agentType?->code === 'PERSON' || 
               $agent->agent_type_id === ControlledTerm::getIdByCode('AGENT_TYPE', 'PERSON');
    }
}