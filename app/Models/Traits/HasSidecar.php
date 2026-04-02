<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HasSidecar
{
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

    abstract public function getBaseTable(): string;
    abstract public function getBaseModelClass(): string; // e.g. Reference::class
    abstract public function getExtensionTable(): string;
    abstract public function getSidecarFields(): array;

    protected function getSidecarForeignKey(): string
    {
        // Default to reference_id, but can be overridden (e.g. taxon_name_id)
        return 'reference_id'; 
    }
}