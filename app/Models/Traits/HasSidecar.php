<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
    * Create a new record in the base table and the sidecar extension table within a single transaction.
    *
    * @param array $baseData Data for the base table (e.g., 'references').
    * @param array $extData Data for the extension table (sidecar fields).
    * @return self
    */
    public static function createWithSidecar(array $baseData, array $extData = []): self
    {
        return DB::transaction(function () use ($baseData, $extData) {
            $model = new static();
            $baseClass = $model->getBaseModelClass();
            
            // Resolve Agent for Attribution
            $agentId = Auth::check() 
                ? DB::table('agents')->where('user_id', Auth::id())->value('id') 
                : null;

            $base = new $baseClass();
            $base->setTable($model->getBaseTable());
            
            // Manually set audit fields since we're using a Base Model instance
            // but might be bypassing standard save() logic in some implementations
            $base->fill(array_merge($baseData, [
                'created_at' => now(),
                'updated_at' => now(),
                'created_by_id' => $agentId,
                'updated_by_id' => $agentId,
            ]));
            $base->save();

            return static::promote($base, $extData);
        });
    }

    /**
     * Promote an existing base record to the sidecar model, optionally updating sidecar fields.
     *
     * @param Model $baseRecord
     * @param array $extData
     * @return self
     */
    public static function promote(Model $baseRecord, array $extData = []): self
    {
        $instance = new static();
        
        // 1. Perform the Sidecar Insert/Update
        DB::table($instance->getExtensionTable())->updateOrInsert(
            [$instance->getSidecarForeignKey() => $baseRecord->id],
            $extData
        );

        // 2. Resolve Agent for Base Table Attribution
        $agentId = Auth::check() 
            ? DB::table('agents')->where('user_id', Auth::id())->value('id') 
            : null;

        // 3. Touch the Base Table
        // This ensures the 'updated_at' and 'updated_by_id' reflect the new Role/Extension
        DB::table($instance->getBaseTable())
            ->where('id', $baseRecord->id)
            ->update([
                'updated_at' => now(),
                'updated_by_id' => $agentId,
            ]);

        return static::find($baseRecord->id);
    }

    /**
     * Update both base and sidecar fields in a single transaction.
     *
     * @param array $data
     * @return bool
     */
    public function updateWithSidecar(array $data): bool
    {
        return DB::transaction(function () use ($data) {
            $sidecarFieldsList = $this->getSidecarFields();

            // 1. Update Extension Table
            $sidecarFields = array_intersect_key($data, array_flip($sidecarFieldsList));
            if (!empty($sidecarFields)) {
                DB::table($this->getExtensionTable())
                    ->where($this->getSidecarForeignKey(), $this->id)
                    ->update($sidecarFields);
            }

            // 2. Resolve Agent ID (Looking up the Agent owning this User)
            $agentId = Auth::check() 
                ? DB::table('agents')->where('user_id', Auth::id())->value('id') 
                : null;

            // 3. Update Base Table + Attribution
            $baseFields = array_diff_key($data, array_flip($sidecarFieldsList));
            
            $auditFields = [
                'updated_at' => now(),
                'updated_by_id' => $agentId, 
            ];

            DB::table($this->getBaseTable())
                ->where('id', $this->id)
                ->update(array_merge($baseFields, $auditFields));

            return true;
        });
    }

    /**
     * Abstract methods that must be implemented by the model using this trait to specify
     * the base table, base model class, extension table, and sidecar fields.
     */
    abstract public function getBaseModelClass(): string; // e.g. Reference::class
    abstract public function getBaseTable(): string;
    abstract public function getExtensionTable(): string;
    abstract public function getSidecarFields(): array;


    /**
     * Get the foreign key name used in the sidecar extension table to link back to the base table.
     * Defaults to 'reference_id' but can be overridden by models if needed (e.g., 'taxon_name_id').
     *
     * @return string
     */
    protected function getSidecarForeignKey(): string
    {
        // Default to reference_id, but can be overridden (e.g. taxon_name_id)
        return 'reference_id'; 
    }
}