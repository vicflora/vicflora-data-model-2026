<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait ManagesSidecars
{
    /**
     * Define the name of the underlying base table (e.g., 'references').
     */
    abstract protected function baseTable(): string;

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
            $this->fill($attributes);
            $isExisting = $this->exists;

            if (method_exists($this, 'runPrePersistLogic')) {
                $this->runPrePersistLogic();
            }

            $this->prepareMetadata($isExisting);

            // Using the abstract method instead of a property
            $table = $this->baseTable();

            if ($isExisting) {
                DB::table($table)
                    ->where('id', $this->id)
                    ->update($this->extractBaseAttributes());
            } else {
                $this->id = DB::table($table)
                    ->insertGetId($this->extractBaseAttributes());
            }

            $sidecar = $this->selectSidecarModel($attributes);
            
            if ($sidecar) {
                $sidecar->id = $this->id;
                $sidecar->updateWithSidecar($this->getAttributes());
            }

            return $this;
        });
    }

    protected function prepareMetadata(bool $isExisting)
    {
        if ($this->usesTimestamps()) {
            $now = $this->freshTimestamp();
            $this->setUpdatedAt($now);
            if (!$isExisting) {
                $this->setCreatedAt($now);
            }
        }

        $fields = $this->baseTableFields();
        if (in_array('updated_by', $fields)) {
            $this->updated_by = Auth::id();
            if (!$isExisting && in_array('created_by', $fields)) {
                $this->created_by = Auth::id();
            }
        }

        if ($isExisting && method_exists($this, 'incrementVersion')) {
            $this->incrementVersion();
        }
    }

    protected function extractBaseAttributes(): array
    {
        $techFields = [$this->getUpdatedAtColumn(), 'updated_by', 'version'];
        if (!$this->exists) {
            $techFields[] = $this->getCreatedAtColumn();
            $techFields[] = 'created_by';
        }

        $allowed = array_merge($this->baseTableFields(), $techFields);

        return array_intersect_key(
            $this->getAttributes(),
            array_flip($allowed)
        );
    }
}