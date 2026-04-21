<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ManagesSidecars
{
    /**
     * Define the columns that belong to the base table.
     */
    abstract protected function baseTableFields(): array;

    /**
     * Identify which sidecar model to use for the current record.
     */
    abstract public function selectSidecarModel(array $attributes = []);

    /**
     * The unified entry point for persistence.
     */
    public function persist(array $attributes = [])
    {
        return DB::transaction(function () use ($attributes) {
            // 1. Fill and handle pre-persist logic (e.g. formatting citations)
            $this->fill($attributes);

            if (method_exists($this, 'runPrePersistLogic')) {
                $this->runPrePersistLogic();
            }

            // 2. Save the base model. 
            // Because we are now using tables, $this->save() works perfectly.
            // This triggers the Auditable trait hooks automatically!
            $this->save();

            // 3. Identify and sync the sidecar
            $sidecar = $this->selectSidecarModel($attributes);
            
            if ($sidecar) {
                // Ensure the sidecar shares the same ID as the base record
                $sidecar->id = $this->id;
                
                // We pass all attributes; the sidecar's own 'fillable' 
                // or specific logic handles the rest.
                $sidecar->updateWithSidecar($this->getAttributes());
            }

            return $this;
        });
    }

    /**
     * Optional: If you still need to extract attributes for specific 
     * non-Eloquent operations, though $this->save() makes this less critical.
     */
    protected function extractBaseAttributes(): array
    {
        return array_intersect_key(
            $this->getAttributes(),
            array_flip($this->baseTableFields())
        );
    }
}