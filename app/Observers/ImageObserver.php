<?php

namespace App\Observers;

use App\Models\Profile\Image;

class ImageObserver
{
    public function saved(Image $image): void
    {
        // If attribution metadata changed, update all associated captions
        if ($image->wasChanged(['creator', 'rights_holder', 'license', 'source', 'scientific_name'])) {
            
            // We iterate and save to trigger the ImageCaption's own 'saving' logic
            foreach ($image->captions as $caption) {
                $caption->save(); 
            }
        }
    }
}
