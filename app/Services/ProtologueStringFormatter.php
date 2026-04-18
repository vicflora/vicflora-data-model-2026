<?php

namespace App\Services;

use App\Models\Taxonomy\Protologue;

class ProtologueStringFormatter
{
    public function format(Protologue $protologue): string
    {
        $out = "";
        
        // Use the 'in_authors' directly from the protologue sidecar
        if ($protologue->in_authors) {
            $out .= "in {$protologue->in_authors}, ";
        }

        // Access the parent reference for the title and metadata
        $reference = $protologue->reference;
        
        $title = str_replace('~', '*', $reference->title);
        $out .= "*{$title}*";

        $volume = $reference->metadata['volume'] ?? null;
        $issue = $reference->metadata['issue'] ?? null;
        $pages = $reference->metadata['pages'] ?? $reference->metadata['page_start'] ?? null;

        if ($volume) $out .= " **{$volume}**";
        if ($issue) $out .= "({$issue})";
        
        if ($pages) {
            $end = $reference->metadata['page_end'] ?? null;
            $out .= ": " . ($end ? "{$pages}–{$end}" : $pages);
        }

        if ($reference->year) {
            $out .= " ({$reference->year})";
        }

        return $out;
    }
}