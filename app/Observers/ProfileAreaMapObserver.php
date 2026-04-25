<?php

namespace App\Observers;

use App\Exceptions\JurisdictionMismatchException;
use App\Models\Profile\ProfileAreaMap;

class ProfileAreaMapObserver
{
    /**
     * Handle the ProfileAreaMap "saving" event.
     */
    public function saving(ProfileAreaMap $map): void
    {
        if (!$map->threat_status_id) return;

        $geographicAreaId = $map->areaCode->area_id;
        $threatStatusAreaId = $map->threatStatus->area_id;

        if ($geographicAreaId !== $threatStatusAreaId) {
            throw new JurisdictionMismatchException($geographicAreaId, $threatStatusAreaId);
        }
    }
}