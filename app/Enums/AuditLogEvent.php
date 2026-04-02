<?php

namespace App\Enums;

enum AuditLogEvent: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case Synced   = 'synced';

    /**
     * Optional: Helper to get labels for a UI dropdown if needed.
     */
    public function label(): string
    {
        return match($this) {
            self::Created  => 'Record Created',
            self::Updated  => 'Record Updated',
            self::Deleted  => 'Record Deleted',
            self::Restored => 'Record Restored',
            self::Synced   => 'External Sync',
        };
    }
}

