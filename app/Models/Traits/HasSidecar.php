<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;

/**
 * Trait HasSidecar
 *
 * Provides functionality for models that have a "sidecar" extension table,
 * allowing for seamless creation and updating of both the base record and its
 * associated sidecar data within a single transaction. This trait is designed
 * to be used with models that represent entities where additional fields are
 * stored in a separate table linked by a foreign key.
 *
 * The trait includes methods for:
 * - Creating a new record in both the base and sidecar tables simultaneously.
 * - Promoting an existing base record to the sidecar model, with optional
 *   sidecar data updates.
 * - Updating both base and sidecar fields in a single transaction.
 *
 * Models using this trait must implement the following abstract methods to
 * specify their base table, base model class, extension table, and sidecar
 * fields:
 * - getBaseTable(): string
 * - getBaseModelClass(): string
 * - getExtensionTable(): string
 * - getSidecarFields(): array
 *
 */
trait HasSidecar
{
    /**
     * The parent base model class (e.g. Reference::class).
     */
    abstract public function getBaseModelClass(): string;

    /**
     * The physical base table name (e.g. 'references_base').
     */
    abstract public function getBaseTable(): string;

    /**
     * The physical extension table name (e.g. 'protologues_ext').
     */
    abstract protected function getExtensionTable(): string;

    /**
     * The columns for the extension table.
     */
    abstract protected function getSidecarFields(): array;

    /**
     * Initial creation for the sidecar extension table.
     * @param array $attributes
     * @return self
     */
    public function createWithSidecar(array $attributes)
    {
        $this->fill($attributes);

        if (method_exists($this, 'runPrePersistLogic')) {
            $this->runPrePersistLogic();
        }

        return DB::table($this->getExtensionTable())->insert(array_merge(
            ['id' => $this->id],
            $this->extractExtensionAttributes()
        ));
    }

    /**
     * Promote an existing base record to the sidecar model, optionally updating sidecar fields.
     * @param array $attributes
     * @return self
     */
    public function promote(array $attributes = [])
    {
        return DB::transaction(function () use ($attributes) {
            $this->fill($attributes);

            // Ensure the sidecar-specific strings are generated 
            // before the first-ever insert into the _ext table
            if (method_exists($this, 'runPrePersistLogic')) {
                $this->runPrePersistLogic();
            }

            return DB::table($this->getExtensionTable())->insert(array_merge(
                ['id' => $this->id],
                $this->extractExtensionAttributes()
            ));
        });
    }

    /**
     * Update both base and sidecar fields in a single transaction.
     *
     * @param array $attributes
     * @return bool
     */
    public function updateWithSidecar(array $attributes)
    {
        $this->fill($attributes);

        if (method_exists($this, 'runPrePersistLogic')) {
            $this->runPrePersistLogic();
        }

        return DB::table($this->getExtensionTable())
            ->where('id', $this->id)
            ->update($this->extractExtensionAttributes());
    }

    protected function extractExtensionAttributes(): array
    {
        // Filters the model's attributes against the allowed extension fields
        return array_intersect_key(
            $this->getAttributes(),
            array_flip($this->getSidecarFields())
        );
    }
}