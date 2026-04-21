<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class AuditFieldsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blueprint::macro('auditable', function () {
            // The audit trail
            $this->unsignedSmallInteger('version')->default(1);
            
            // Blame IDs
            $this->foreignId('created_by_id')
                ->nullable()
                ->constrained('agents')
                ->onDelete('restrict');
                
            $this->foreignId('updated_by_id')
                ->nullable()
                ->constrained('agents')
                ->onDelete('restrict');

            // Standard Timestamps
            $this->timestampsTz();
        });
    }
}