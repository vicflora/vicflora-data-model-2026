<?php

namespace App\Models\Traits;

trait IncrementsVersion
{
    /**
     * Boot the trait and register the updating listener.
     */
    public static function bootIncrementsVersion(): void
    {
        static::updating(function ($model) {
            // Only increment if actual data changed, not just a touch/timestamp update
            // unless you want "touches" to also bump the version.
            if ($model->isDirty()) {
                $model->version++;
            }
        });
    }
}