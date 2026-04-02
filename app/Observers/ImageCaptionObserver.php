<?php

namespace App\Observers;

use App\Models\Profile\ImageCaption;

class ImageCaptionObserver
{
    /**
     * Handle the ImageCaption "saving" event.
     * This covers both 'creating' and 'updating'.
     */
    public function saving(ImageCaption $caption): void
    {
        $caption->formatted_caption = $caption->generateHtml();
    }
}