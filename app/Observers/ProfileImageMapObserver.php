<?php

namespace App\Observers;

use App\Models\Profile\ProfileImageMap;
use App\Models\Shared\ControlledTerm;

class ProfileImageMapObserver
{
    public function saving(ProfileImageMap $map): void
    {
        $heroRoleId = ControlledTerm::getIdByCode('IMAGE_ROLE', 'HERO');
        $galleryRoleId = ControlledTerm::getIdByCode('IMAGE_ROLE', 'GALLERY');

        // If this specific mapping is being set to HERO
        if ($map->image_role_id == $heroRoleId) {
            
            // Find any OTHER image in this profile/tree that is currently the HERO
            ProfileImageMap::where('profile_id', $map->profile_id)
                ->where('taxon_tree_id', $map->taxon_tree_id)
                ->where('image_id', '!=', $map->image_id)
                ->where('image_role_id', $heroRoleId)
                ->update(['image_role_id' => $galleryRoleId]);
        }
    }
}