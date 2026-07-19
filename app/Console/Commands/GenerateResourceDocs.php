<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateResourceDocs extends Command
{
    protected $signature = 'docs:generate
            {--baseUrl= : The base URL for links (e.g. /vicflora-docs)}
            {--output= : The absolute path to the Jigsaw source folder}
            {--csv= : The absolute path to the CSV file (defaults to sibling of output)}';

    protected $description = 'Generate unified Markdown documentation for Resources and Controlled Vocabularies';

    protected Collection $resources;
    protected array $usageMap = [];
    protected string $outputBase;
    protected string $baseUrl;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Initialize State
        $this->outputBase = rtrim($this->option('output') ?? storage_path('app/private/docs'), '/');
        $this->baseUrl = rtrim($this->option('baseUrl') ?? '', '/');
        
        $csvPath = $this->option('csv') ?? dirname($this->outputBase) . '/vicflora-resources.csv';
        if (!File::exists($csvPath)) {
            $this->error("CSV not found at: {$csvPath}");
            return;
        }

        $rows = array_map('str_getcsv', file($csvPath));
        $header = array_shift($rows);
        $this->resources = collect($rows)->map(fn($row) => (object) array_combine($header, $row));

        // 2. Prepare Usage Map for Vocabularies
        $this->buildUsageMap();

        // 3. Execution Swarm
        $this->generateResourceMap();
        $this->generateResources();
        $this->generateVocabularies();
        $this->generateVocabMap();

        $this->info('Successfully generated unified documentation swarm!');
    }

    /**
     * Phase 1: Generate individual Resource pages.
     */
    private function generateResources(): void
    {
        $resourceDir = Str::contains($this->outputBase, '/source') 
            ? Str::finish($this->outputBase, '/_resources') 
            : $this->outputBase . '/resources';

        $this->clearMarkdownFiles($resourceDir);

        $this->resources->each(function($resource) use ($resourceDir) {
            $this->info("Processing Resource: {$resource->resource}");

            $markdown = $this->buildResourceMarkdown($resource);
            
            $path = $resourceDir . '/' . $this->getResourceSlug($resource->resource) . '.md';
            File::put($path, $markdown);
        });
    }

    /**
     * Phase 2: Generate Controlled Vocabulary pages.
     */
    private function generateVocabularies(): void
    {
        $vocabDir = Str::contains($this->outputBase, '/source') 
            ? Str::finish($this->outputBase, '/_vocabularies') 
            : $this->outputBase . '/vocabularies';

        $this->clearMarkdownFiles($vocabDir);

        $vocabularies = DB::table('controlled_vocabularies')->get();
        $this->info("Exporting " . $vocabularies->count() . " vocabularies...");

        foreach ($vocabularies as $vocab) {
            $terms = DB::table('controlled_terms')
                ->where('controlled_vocabulary_id', $vocab->id)
                ->orderBy('sort_order')->orderBy('label')->get();

            $usage = $this->usageMap[$vocab->code] ?? $this->usageMap[$vocab->name] ?? [];
            $markdown = $this->buildVocabularyMarkdown($vocab, $terms, $usage);

            $filename = Str::slug($vocab->code) . '.md';
            File::put($vocabDir . '/' . $filename, $markdown);
        }
    }

    /**
     * Build the usage map from the resources collection.
     */
    private function buildUsageMap(): void
    {
        $this->resources->each(function ($resource) {
            if ($resource->vocabularies) {
                $decoded = json_decode($resource->vocabularies, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $column => $vocabCode) {
                        $this->usageMap[$vocabCode][] = [
                            'resource' => $resource->resource,
                            'column' => $column
                        ];
                    }
                }
            }
        });
    }

    /**
     * Resource Markdown Template.
     */
    private function buildResourceMarkdown(object $resource): string
    {
        $schema = $resource->table_schema ?? 'public';
        $table = $resource->table_name;
        $layer = $this->resolveLayerMetadata($resource);
        
        $displayTable = ($schema !== 'public') ? "{$schema}.{$table}" : $table;
        if ($resource->table_type === 'materialized view') $displayTable .= " (materialized view)";

        $content = <<<EOT
---
extends: _layouts.vicflora
title: {$resource->resource}
---

## Resource metadata

<x-responsive-table>

| Key               | Value |
|----------------|------|
| **Resource type** | {$resource->type} |
| **Layer**         | [{$layer->name}]({$layer->link}) |
| **Model name**    | {$resource->model} |
| **Table name**    | {$displayTable} |

</x-responsive-table>

EOT;

        if ($resource->table_type === 'materialized view') {
            return $content . $this->getMaterializedViewDdl($table);
        }

        return $content . <<<EOT

## Columns

<x-responsive-table>

{$this->getColumnsTable($schema, $table)}

</x-responsive-table>

## Indexes

{$this->getIndexesTable($schema, $table)}

## Foreign key relationships

### References to other tables

{$this->getReferencesFrom($schema, $table)}

### Referenced by

{$this->getReferencedBy($schema, $table)}

## Eloquent relationships

<x-responsive-table>

{$this->getEloquentRelationshipsTable($resource->model)}

</x-responsive-table>

{$this->getVocabulariesSection($resource->vocabularies)}
EOT;
    }

    /**
     * Vocabulary Markdown Template.
     */
    private function buildVocabularyMarkdown(object $vocab, Collection $terms, array $usage): string
    {
        $markdown = "---\nextends: _layouts.vicflora\ntitle: {$vocab->name}\n---\n\n";
        $markdown .= "## Vocabulary metadata\n\n<x-responsive-table>\n\n| Key | Value |\n|---|---|\n";
        $markdown .= "| name | {$vocab->name} |\n| code | `{$vocab->code}` |\n";
        $markdown .= "| description | " . ($vocab->description ?? '') . " |\n| iri | " . ($vocab->iri ?? '') . " |\n\n</x-responsive-table>\n\n";

        $markdown .= "## Controlled terms\n\n<x-responsive-table>\n\n| Label | Code | Description | IRI |\n| :--- | :--- | :--- | :--- |\n";
        foreach ($terms as $term) {
            $desc = $term->description ? Str::replace(["\r", "\n"], " ", $term->description) : '&nbsp;';
            $markdown .= "| **{$term->label}** | `{$term->code}` | {$desc} | " . ($term->iri ?? '&nbsp;') . " |\n";
        }
        $markdown .= "\n</x-responsive-table>\n\n";

        if (!empty($usage)) {
            $markdown .= "## Usage\n\nThis vocabulary is used by the following resources:\n\n<x-responsive-table>\n\n| Resource | Foreign Key Column |\n|---|---|\n";
            foreach ($usage as $item) {
                $link = "{$this->baseUrl}/resources/" . $this->getResourceSlug($item['resource']);
                $markdown .= "| [{$item['resource']}]({$link}) | `{$item['column']}` |\n";
            }
            $markdown .= "\n</x-responsive-table>\n";
        }

        return $markdown;
    }

    /**
     * Ensures the URL and Filename logic are identical.
     */
    private function getResourceSlug(string $resourceName): string
    {
        return (string) Str::of($resourceName)
            ->replace(['_MAP', '_EXT'], ['Map', 'Ext'])
            ->replace('_', '')
            ->kebab();
    }

    /**
     * Creates the JSON map for the JS to consume.
     */
    private function generateResourceMap(): void
    {
        $map = $this->resources->mapWithKeys(fn($r) => [$r->resource => $this->baseUrl . "/resources/" . $this->getResourceSlug($r->resource)])->toArray();
        $path = dirname(Str::finish($this->outputBase, '/')) . '/resource-map.json';
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function getVocabulariesSection(?string $vocabJson): string
    {
        if (!$vocabJson || $vocabJson === '{}') return '';

        $vocabs = json_decode($vocabJson, true);

        // If json_decode fails (returns null) and the input wasn't "null", it's a syntax error
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = json_last_error_msg();
            throw new \InvalidArgumentException(
                "Invalid JSON in 'vocabularies' column: {$error}. Data: {$vocabJson}"
            );
        }

        $markdown = "## Controlled Vocabularies\n\n<x-responsive-table>\n\n| Column | Vocabulary |\n| :--- | :--- |\n";
        foreach ($vocabs as $column => $vocabCode) {
            $name = Str::studly(strtolower($vocabCode)) . ' Vocabulary';
            $url = $this->baseUrl . '/vocabularies/' . Str::slug($vocabCode);
            $markdown .= "| `{$column}` | [{$name}]({$url}) |\n";
        }
        return $markdown . "\n</x-responsive-table>\n";
    }

    private function resolveLayerMetadata(object $resource): object
    {
        $layer = $resource->layer ?? 'N/A';
        $sublayer = $resource->sublayer ?? null;
        
        // Use match to handle the specific behavior of Layer 8
        [$pageName, $displayName] = match (true) {
            str_contains($layer, '8. Extension') => [$sublayer, $sublayer],
            default                              => [$layer, $sublayer ?: $layer],
        };

        $slug = \Illuminate\Support\Str::slug($pageName);
        // Prepend the captured baseUrl to ensure the link is portable
        $url = $this->baseUrl . "/layers/layer-{$slug}";

        // Only append anchor if we're NOT on an Extension page and have a sublayer
        // if (!str_contains($layer, '8. Extension') && $sublayer) {
        //     $url .= '#' . \Illuminate\Support\Str::slug($sublayer);
        // }

        return (object) [
            'name' => $displayName,
            'link' => $url,
        ];
    }

    private function getColumnsTable(string $schema, string $table): string
    {
        $columns = DB::select("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns
            WHERE table_schema = ? AND table_name = ?
            ORDER BY ordinal_position", [$schema, $table]);

        if (empty($columns)) return "_No columns found._";

        $rows = "| column name | data type | nullable | default |\n|---|---|---|---|\n";
        foreach ($columns as $c) {
            $default = str_replace('|', '\\|', $c->column_default ?? '');
            $rows .= "| {$c->column_name} | {$c->data_type} | {$c->is_nullable} | {$default} |\n";
        }
        return $rows;
    }

    private function getIndexesTable(string $schema, string $table): string
    {
        $indexes = DB::select("
            SELECT indexname as name, indexdef as definition 
            FROM pg_indexes 
            WHERE schemaname = ? AND tablename = ?", [$schema, $table]);

        if (empty($indexes)) return "_No indexes found._";

        $rows = "<x-responsive-table>\n\n";
        $rows .= "| name | definition |\n|---|---|\n";
        foreach ($indexes as $i) {
            $rows .= "| {$i->name} | {$i->definition} |\n";
        }
        $rows .= "\n</x-responsive-table>\n\n";

        return $rows;
    }

    private function getReferencesFrom(string $schema, string $table): string
    {
        $fks = DB::select("
            SELECT
                tc.constraint_name as name,
                CASE WHEN tc.table_schema = 'public' THEN tc.table_name ELSE tc.table_schema||'.'||tc.table_name END AS table, 
                kcu.column_name AS column,
                CASE WHEN ccu.table_schema = 'public' THEN ccu.table_name ELSE ccu.table_schema||'.'||ccu.table_name END AS referenced_table,
                ccu.column_name AS referenced_column
            FROM 
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                ON tc.constraint_name = kcu.constraint_name
                AND tc.table_schema = kcu.table_schema
                JOIN information_schema.constraint_column_usage AS ccu
                ON ccu.constraint_name = tc.constraint_name
            WHERE tc.constraint_type = 'FOREIGN KEY' 
            AND tc.table_schema = ? AND tc.table_name = ?", [$schema, $table]);

        if (empty($fks)) return "_No outgoing references._";

        $rows = "<x-responsive-table>\n\n";
        $rows .= "| name | table | column | referenced table | referenced column |\n|---|---|---|---|---|\n";
        foreach ($fks as $f) {
            $tableLink = $this->getResourceLink($f->referenced_table, 'table_name');

            $rows .= "| {$f->name} | {$f->table} | {$f->column} | {$tableLink} | {$f->referenced_column} |\n";
        }
        $rows .= "\n</x-responsive-table>\n";

        return $rows;
    }

    private function getReferencedBy(string $schema, string $table): string
    {
        $fks = DB::select("
            SELECT
                tc.constraint_name as name,
                CASE WHEN tc.table_schema = 'public' THEN tc.table_name ELSE tc.table_schema||'.'||tc.table_name END AS table, 
                kcu.column_name AS column,
                CASE WHEN ccu.table_schema = 'public' THEN ccu.table_name ELSE ccu.table_schema||'.'||ccu.table_name END AS referenced_table,
                ccu.column_name AS referenced_column
            FROM 
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                ON tc.constraint_name = kcu.constraint_name
                AND tc.table_schema = kcu.table_schema
                JOIN information_schema.constraint_column_usage AS ccu
                ON ccu.constraint_name = tc.constraint_name
            WHERE tc.constraint_type = 'FOREIGN KEY' 
            AND ccu.table_schema = ? AND ccu.table_name = ?", [$schema, $table]);

        if (empty($fks)) return "_No incoming references._";

        $rows = "<x-responsive-table>\n\n";
        $rows .= "| Name | Table | Column | Referenced Table | Referenced Column |\n|---|---|---|---|---|\n";

        foreach ($fks as $f) {
            // Use our new helper to find the resource link for the source table
            $tableLink = $this->getResourceLink($f->table, 'table_name');
            
            $rows .= "| {$f->name} | {$tableLink} | {$f->column} | {$f->referenced_table} | {$f->referenced_column} |\n";
        }

        $rows .= "\n</x-responsive-table>\n";
        return $rows;
    }

    /**
     * Reflects on the model to extract Eloquent relationship metadata.
     * 
     * @param string $modelClass The full namespace (e.g. \App\Models\Taxonomy\TaxonConcept)
     * @return string Markdown table
     */
    private function getEloquentRelationshipsTable(string $modelClass): string
    {
        if (!class_exists($modelClass)) {
            return "_Model class not found._";
        }

        $model = new $modelClass;
        $reflection = new \ReflectionClass($model);
        $rows = [];

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            // Skip methods with parameters or inherited from base Model/Traits
            if ($method->getNumberOfParameters() > 0 || $method->class === 'Illuminate\Database\Eloquent\Model') {
                continue;
            }

            $ignoredMethods = ['persist', 'selectSidecarModel', 'baseTableFields', 'extractBaseAttributes'];
            if (in_array($method->getName(), $ignoredMethods)) continue;

            try {
                $return = $method->invoke($model);

                if ($return instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    $relType = (new \ReflectionClass($return))->getShortName();
                    
                    // Get the FULL namespace for the lookup
                    $relatedModelClass = get_class($return->getRelated());
                    
                    // Use the helper to get a link if the resource exists
                    $relatedModelLink = $this->getResourceLink($relatedModelClass, 'model');
                    
                    $details = [];

                    // 1. Handle BelongsTo (Show the Foreign Key)
                    if ($return instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                        $details[] = "FK: `{$return->getForeignKeyName()}`";
                    }

                    // 2. Handle HasOne / HasMany
                    if ($return instanceof \Illuminate\Database\Eloquent\Relations\HasOneOrMany && 
                        !$return instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                        $details[] = "Remote FK: `{$return->getForeignKeyName()}`";
                    }

                    // 3. Handle BelongsToMany (Pivot Table)
                    // We can also try to link the Pivot Table if it exists as a resource!
                    if ($return instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                        $pivotTable = $return->getTable();
                        $pivotLink = $this->getResourceLink($pivotTable, 'table_name');
                        $details[] = "Pivot: `{$pivotLink}`";
                    }

                    // 4. Handle "Through" Relationships
                    if (method_exists($return, 'getThroughParent')) {
                        $throughClass = get_class($return->getThroughParent());
                        $throughLink = $this->getResourceLink($throughClass, 'model');
                        $details[] = "Through: **{$throughLink}**";
                    }

                    // 5. Morph Detection
                    if ($relType === 'MorphTo') {
                        $details[] = "Polymorphic";
                    }

                    // 6. Custom Constraints
                    $wheres = $return->getBaseQuery()->wheres;
                    if (count($wheres) > 1) {
                        $details[] = '<span style="color:orange;">Has custom constraints</span>';
                    }

                    $detailString = implode('<br>', $details);
                    $rows[] = "| `{$method->getName()}()` | **{$relType}** | {$relatedModelLink} | {$detailString} |";
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        if (empty($rows)) {
            return "_No Eloquent relationships defined._";
        }

        $header = "| Method | Type | Related Model | Details |\n| :--- | :--- | :--- | :--- |\n";
        return $header . implode("\n", $rows);
    }

    protected function getResourceLink(string $value, $key = 'table_name')
    {
        $searchValue = $value;

        if ($key === 'model') {
            // Handle full namespaces by removing leading slash
            $searchValue = ltrim($value, '\\');
        } elseif ($key === 'table_name' && str_contains($value, '.')) {
            // Handle schema-prefixed tables (e.g. 'taxonomy.taxon_concepts' -> 'taxon_concepts')
            $searchValue = last(explode('.', $value));
        }

        $match = $this->resources->first(function ($resource) use ($key, $searchValue) {
            // Ensure comparison is also clean on the resource side
            $resourceValue = ($key === 'model') ? ltrim($resource->{$key}, '\\') : $resource->{$key};
            return $resourceValue === $searchValue;
        });

        if ($match) {
            $slug = Str::of($match->resource)
                ->replace(['_MAP', '_EXT'], ['Map', 'Ext'])
                ->replace('_', '')
                ->kebab();
            return "[{$value}]({$this->baseUrl}/resources/{$slug})";
        }

        return $value;
    }

    private function getMaterializedViewDdl(string $table): string
    {
        $ddlPath = dirname($this->outputBase) . '/_materialized-views/' . Str::slug($table). '.md';
        $ddl = File::exists($ddlPath) ? File::get($ddlPath) : "```sql\n-- Add materialized view DDL here\n```";
        return "\n## Definition\n\n<x-code lang=\"sql\">\n\n{$ddl}\n\n</x-code>\n";
    }

    private function clearMarkdownFiles(string $directory): void
    {
        // 1. Ensure it exists (creates it if missing, does nothing if it exists)
        File::ensureDirectoryExists($directory);

        // 2. Now it is safe to glob
        $files = File::glob($directory . '/*.md');
        
        foreach ($files as $file) {
            File::delete($file);
        }
    }

    /**
     * Generate a mapping of fields to absolute vocabulary URLs.
     */
    protected function generateVocabMap(): void
    {
        $vocabMap = [];

        foreach ($this->resources as $resource) {
            if (empty($resource->vocabularies)) {
                continue;
            }

            $mappings = json_decode($resource->vocabularies, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException(
                    "Invalid JSON in 'vocabularies' column for resource '{$resource->resource}': " . 
                    json_last_error_msg()
                );
            }

            foreach ($mappings as $fieldId => $vocabCode) {
                // 'rank_id' -> 'rank'
                $fieldName = str_replace('_id', '', $fieldId);
                $vocabSlug = \Illuminate\Support\Str::slug($vocabCode);
                $url = "{$this->baseUrl}/vocabularies/{$vocabSlug}";

                // Test if the key exists
                if (array_key_exists($fieldName, $vocabMap)) {
                    // Safety check: Ensure the field name isn't being 
                    // re-used for a different vocabulary.
                    if ($vocabMap[$fieldName] !== $url) {
                        $this->error("Mapping Conflict: Field '{$fieldName}' is linked to multiple vocabularies: " . 
                            $vocabMap[$fieldName] . " and " . $url);
                    }
                    continue; // Skip if it's already mapped to the same vocab
                }

                $vocabMap[$fieldName] = $url;
            }
        }

        $path = dirname(Str::finish($this->outputBase, '/')) . '/vocab-map.json';
        File::ensureDirectoryExists(dirname($path));
        File::put(
            $path, 
            json_encode($vocabMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        
        $this->info("Successfully generated vocab-map.json with " . count($vocabMap) . " unique enum mappings.");
    }
}