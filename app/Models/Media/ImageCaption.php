<?php

namespace App\Models\Media;

use App\Models\Shared\Agent;
use App\Models\Taxonomy\TaxonTree;
use App\Models\Traits\Auditable;
use App\Observers\ImageCaptionObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class ImageCaption
 *
 * Represents a caption for an image, which may include metadata such as the
 * creator, rights holder, and license information. This model is based on the
 * 'image_captions' database table, which captures the captions associated with
 * images in the application.
 *
 * The model includes relationships to the image (Image) that the caption
 * describes and the taxon tree (TaxonTree) that provides context for the
 * caption.
 *
 * @property int $id
 * @property int $image_id
 * @property int $profile_id
 * @property string|null $caption_body
 * @property string|null $formatted_caption
 * @property string|null $creator
 * @property string|null $rights_holder
 * @property int|null $license_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Image|null $image
 * @property-read TaxonTree|null $taxonTree
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(
    name: 'image_captions', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'image_id', 
    'profile_id', 
    'caption_body', 
    'formatted_caption', 
    'creator', 
    'rights_holder', 
    'license_id'
])]
#[ObservedBy(ImageCaptionObserver::class)]
class ImageCaption extends Model
{
    use Auditable;

    /**
     * Image the caption belongs to
     *
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Taxon tree the caption belongs to. The taxon tree acts as a namespace, 
     * allowing the same image to have different captions in different trees 
     * if needed.
     *
     * @return BelongsTo
     */
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'profile_id', 'taxon_concept_id');
    }

        
    /**
     * Assemble the caption into an HTML string.
     * This runs only on save, avoiding N+1 during reads.
     */
    public function generateHtml(): string
    {
        $image = $this->image;
        // Reach up to get the current concept and its accepted name
        $taxonConcept = $this->profile?->taxonConcept;
        
        if (!$taxonConcept || !$image) {
            return $this->caption_body; // Fallback
        }

        // 1. SCIENTIFIC NAME ASSEMBLER
        // Use the accepted name as the primary identity
        $acceptedName = $taxonConcept->acceptedConcept->taxonName->name_string;
        $scientificName = "<i>{$acceptedName}</i>";

        // "As" Logic
        if ($image->original_scientific_name) {
            $scientificName .= " (as <i>{$image->original_scientific_name}</i>)";
        } 
        elseif ($taxonConcept->id !== $taxonConcept->accepted_id) {
            // If the concept itself is a synonym, show the synonym name
            $scientificName .= " (as <i>{$taxonConcept->taxonName->name_string}</i>)";
        }

        // Fix italics for infraspecific ranks
        $search = [' subsp. ', ' var. ', ' f. '];
        $replace = ['</i> subsp. <i>', '</i> var. <i>', '</i> f. <i>'];
        $scientificName = str_replace($search, $replace, $scientificName);

        // 2. LICENSE & URL BUILDER
        $licenseHtml = '';
        if (str_starts_with($image->license, 'CC BY')) {
            $bits = explode(' ', $image->license);
            $type = strtolower($bits[1] ?? 'by');
            $version = $bits[2] ?? '4.0';
            $region = isset($bits[3]) ? '/' . strtolower($bits[3]) : '';
            
            $url = "https://creativecommons.org/licenses/{$type}/{$version}{$region}";
            $licenseHtml = "<a href='{$url}' target='_blank'>{$image->license}</a>";
        } 
        elseif ($image->license === 'All rights reserved') {
            $licenseHtml = 'all rights reserved';
        } 
        else {
            // Default Fallback
            $licenseHtml = "<a href='https://creativecommons.org/licenses/by-nc-sa/4.0' target='_blank'>CC BY-NC-SA 4.0</a>";
        }

        // 3. ASSEMBLE THE HTML STRING
        $html = "<span>{$scientificName}";

        // Add caption body (Support Markdown for just this part if desired)
        if ($this->caption_body) {
            $html .= ". " . \Illuminate\Support\Str::markdown($this->caption_body);
        }
        $html .= "</span><br/>";

        // Source line
        if ($image->source) {
            $html .= "<b>Source:</b> {$image->source}<br/>";
        }

        // Attribution Line
        $label = ($image->subtype === 'Illustration') ? '<b>Illustration:</b> ' : '<b>Photo:</b> ';
        $html .= $label . $image->creator;

        // Copyright Logic
        if (str_contains($licenseHtml, 'CC BY')) {
            $html .= ", {$licenseHtml}";
        } 
        else {
            $year = date('Y');
            $owner = ($image->copyright_owner === 'Royal Botanic Gardens Victoria') 
                ? 'Royal Botanic Gardens Board' 
                : ($image->copyright_owner ?: 'Royal Botanic Gardens Board');
                
            $html .= ", &copy; {$year} {$owner}, {$licenseHtml}";
        }

        return $html;
    }
}