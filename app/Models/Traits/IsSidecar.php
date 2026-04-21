<?php

namespace App\Models\Traits;

/**
 * Trait IsSidecar
 *
 * Implemented on extension models (e.g., ScientificName, Protologue).
 * Handles the synchronization of data from a base model while 
 * triggering Eloquent lifecycle events.
 */
trait IsSidecar
{
    /**
     * Define the columns that belong to this specific sidecar.
     * Usually maps to the model's $fillable array.
     */
    abstract protected function getSidecarFields(): array;

    /**
     * Synchronize attributes from the base model persist() call.
     */
    public function updateWithSidecar(array $attributes): bool
    {
        // Filter attributes to only those relevant to this sidecar
        $sidecarData = array_intersect_key(
            $attributes, 
            array_flip($this->getSidecarFields())
        );

        $this->fill($sidecarData);

        if (method_exists($this, 'runPrePersistLogic')) {
            $this->runPrePersistLogic();
        }

        // save() handles both first-time creation and subsequent updates
        // and triggers the Auditable trait versioning.
        return $this->save();
    }
}