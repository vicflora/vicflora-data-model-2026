<?php

namespace App\Services;

use App\Models\Shared\Reference;

class ReferenceFormatter
{
    /**
     * Generate a Markdown-formatted citation.
     */
    public function format(Reference $reference): string
    {
        $type = $reference->type?->code;
        $authors = $reference->citation_authorship_string;
        $year = $reference->year ?? 'n.d.';
        
        // Handle the ~ to * conversion for titles as seen in the old app
        $title = str_replace('~', '*', $reference->title);

        $out = match ($type) {
            'JOURNAL', 'SERIES' => $title,

            'ARTICLE' => $this->formatArticle($reference, $authors, $year, $title),

            'BOOK', 'REPORT', 'AUDIO_VISUAL_DOCUMENT' => $this->formatBook($reference, $authors, $year, $title),

            'BOOK_CHAPTER' => $this->formatChapter($reference, $authors, $year, $title),

            'PROTOLOGUE' => $this->formatProtologue($reference, $title),

            default => "**{$authors} ({$year}).** {$title}."
        };

        return $this->appendDoi($reference, $out);
    }

    public function formatShort(Reference $reference): string
    {
        $agents = $reference->authors_for_citation;
        $count = $agents->count();
        $year = $reference->year ?? 'n.d.';

        if ($count === 0) return "Anon. ({$year})";

        $names = match (true) {
            $count === 1 => $agents[0]->short_name,
            $count === 2 => "{$agents[0]->short_name} & {$agents[1]->short_name}",
            default      => "{$agents[0]->short_name} et al.",
        };

        return "{$names} ({$year})";
    }

    protected function formatArticle(Reference $ref, $authors, $year, $title): string
    {
        $out = "**{$authors} ({$year}).** {$title}. ";
        
        if ($ref->container) {
            $out .= "*{$ref->container->title}*";
        }

        $volume = $ref->metadata['volume'] ?? null;
        $issue = $ref->metadata['issue'] ?? null;
        $pages = $ref->metadata['pages'] ?? $ref->metadata['page_start'] ?? null;

        if ($volume) {
            $out .= " **{$volume}**";
            if ($issue) $out .= "({$issue})";
            if ($pages) {
                $end = $ref->metadata['page_end'] ?? null;
                $out .= ": " . ($end ? "{$pages}–{$end}" : $pages);
            }
        } elseif ($number = ($ref->metadata['number'] ?? null)) {
            $out .= " {$number}";
        }

        return "{$out}.";
    }

    protected function formatBook(Reference $ref, $authors, $year, $title): string
    {
        $out = "**{$authors} ({$year}).** {$title}";
        
        if ($edition = ($ref->metadata['edition'] ?? null)) {
            $out .= ", edn {$edition}";
        }

        $out .= ". ";

        $publisher = $ref->metadata['publisher'] ?? null;
        if ($publisher) {
            $out .= "{$publisher}";
            if ($place = ($ref->metadata['place_of_publication'] ?? null)) {
                $out .= ", {$place}";
            }
            $out .= ".";
        }

        return rtrim($out);
    }

    protected function formatChapter(Reference $ref, $authors, $year, $title): string
    {
        $out = "**{$authors} ({$year}).** {$title}. In: ";
        
        if ($container = $ref->container) {
            $containerAuthors = $container->citation_authorship_string;
            $out .= "{$containerAuthors}, *&zwj;{$container->title}&zwj;*";
            
            if ($edition = ($container->metadata['edition'] ?? null)) {
                $out .= ", edn {$edition}";
            }

            $pages = $ref->metadata['page_start'] ?? null;
            $end = $ref->metadata['page_end'] ?? null;
            if ($pages) {
                $out .= ", pp. " . ($end ? "{$pages}–{$end}" : $pages);
            }
            
            $publisher = $container->metadata['publisher'] ?? null;
            if ($publisher) {
                $out .= ". {$publisher}";
                if ($place = ($container->metadata['place_of_publication'] ?? null)) {
                    $out .= ", {$place}";
                }
            }
        }
        
        return "{$out}.";
    }

    protected function formatProtologue(Reference $ref, $title): string
    {
        $out = "";
        if ($ref->author_string) {
            $out .= "in {$ref->author_string}, ";
        }
        $out .= "*{$title}*";
        
        $volume = $ref->metadata['volume'] ?? null;
        $issue = $ref->metadata['issue'] ?? null;
        $pages = $ref->metadata['pages'] ?? null;
        
        if ($volume) $out .= " **{$volume}**";
        if ($issue) $out .= "({$issue})";
        if ($pages) $out .= ": {$pages}";
        if ($ref->year) $out .= " ({$ref->year})";
        
        return $out;
    }

    /**
     * Append DOI to the citation string if it exists in metadata.
     */
    protected function appendDoi(Reference $ref, string $citation): string
    {
        $doi = $ref->metadata['doi'] ?? null;

        if (!$doi) {
            return $citation;
        }

        // Ensure the citation ends with a period before adding the DOI
        $citation = rtrim($citation, '.') . '.';

        // Clean the DOI (strip https://doi.org/ if already present to avoid doubling)
        $doi = str_replace(['https://doi.org/', 'http://doi.org/'], '', $doi);

        return "{$citation} [doi:{$doi}](https://doi.org/{$doi})";
    }
}