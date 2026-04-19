<?php

namespace App\Observers;

use App\Models\Media\ImageCaption;

class ImageCaptionObserver
{
    /**
     * Handle the ImageCaption "saving" event.
     * This covers both 'creating' and 'updating'.
     * @param ImageCaption $caption
     * @return void
     */
    public function saving(ImageCaption $caption): void
    {
        $caption->formatted_caption = $caption->generateHtml();
    }
}