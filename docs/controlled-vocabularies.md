# Developer Guidelines: Implementing & Using Controlled Vocabularies

## 1. Background: The "Hybrid" Enum Strategy
In the **VicFlora** ecosystem, we avoid hardcoding taxonomy logic into native PHP Enums. Instead, we use a **Database-Backed Vocabulary** system. This allows us to:
* Map our data to international standards (Darwin Core, IUCN) via **IRIs**.
* Update labels or descriptions without a code deployment.
* Support multi-tenancy (different floras may eventually use different subsets of terms).

## 2. The Database Schema
All vocabularies follow a parent-child relationship:
* **`controlled_vocabularies`**: Stores the "Type" (e.g., `TAXON_RANK`, `THREAT_STATUS`).
* **`controlled_terms`**: Stores the actual options (e.g., `SPECIES`, `VULNERABLE`).
    * **`code`**: The unique string key (always uppercase, e.g., `CR`).
    * **`label`**: The display string (Sentence case).
    * **`description`**: The technical definition from the standard.
    * **`iri`**: The global identifier (URL) for data exchange.

## 3. Usage in Laravel (Backend)

### Validation

**Rule: Never hardcode Database IDs.** Primary keys (`id`) can vary between your local environment, the staging server, and the production database. All validation must be performed against the string-based `code` of the vocabulary.

#### Recommended Approach: The `InVocabulary` Rule
To keep controllers clean and environment-agnostic, use the custom `InVocabulary` validation rule. This rule checks that the provided string exists as a `code` within a specific parent vocabulary.

**Usage in a Controller or FormRequest:**
```php
use App\Rules\InVocabulary;

$request->validate([
    'rank' => ['required', new InVocabulary('RANK')],
    'threat_status' => ['nullable', new InVocabulary('THREAT_STATUS')],
    'establishment_means' => ['required', new InVocabulary('ESTABLISHMENT_MEANS')],
]);
```

#### Why we do this:
1. **Readable Code:** It is immediately clear to any developer that you are validating against the "RANK" or "THREAT_STATUS" set.
2. **Database Integrity:** It ensures that a `code` belonging to the "Rank" vocabulary (e.g., `SPECIES`) cannot be accidentally saved into a "Threat Status" field.
3. **No Extra Queries:** You don't need to manually fetch the `vocabulary_id` before running your validation logic.

#### Under the Hood (For Reference)
The rule performs a join to ensure the term is scoped correctly:
```php
// Logic inside the InVocabulary rule
return DB::table('vocabulary_terms')
    ->join('vocabularies', 'vocabulary_terms.vocabulary_id', '=', 'vocabularies.id')
    ->where('vocabularies.code', $this->vocabularyCode) // e.g., 'RANK'
    ->where('vocabulary_terms.code', $value)           // e.g., 'SPECIES'
    ->exists();
```
#### Scoping with Models
When a Model (like `Taxon`) belongs to a vocabulary term, use the `code` for readability in queries, but store the `id` for relational integrity.

```php
// Example: Fetching all families
$taxa = TaxonConcept::whereHas('taxonRank', function($query) {
    $query->where('code', 'FAMILY');
})->get();
```

---

## 4. Usage in React/Inertia (Frontend)

#### Passing Data to Components
Instead of hardcoding dropdown options in React, the Controller should pass the relevant vocabulary terms via Inertia.

```php
// In the Controller
return Inertia::render('TaxonConcept/Edit', [
    'taxonConcept' => $taxon,
    'ranks' => Vocabulary::where('code', 'TAXON_RANK')
                        ->first()
                        ->terms()
                        ->get(['code', 'label', 'description'])
]);
```

#### Implementation in Forms
Use the `description` as a tooltip or "help text" to assist contractors and taxonomists in choosing the correct value.

```jsx
// React Select Component
<select value={data.taxon_rank} onChange={e => setData('taxon_rank', e.target.value)}>
    {taxonRanks.map(status => (
        <option key={status.code} value={status.code} title={status.description}>
            {status.label}
        </option>
    ))}
</select>
```

---

## 5. Best Practices
* **Never Hardcode IDs:** Always refer to terms by their `code` (e.g., `NATIVE`). IDs can change between local, staging, and production environments; codes remain constant.
* **The "Unranked" Catch-all:** For taxonomic ranks, if a rank doesn't fit the standard hierarchy, use the `UNRANKED` code rather than leaving it null.
* **Syncing Standards:** If a contractor needs to add a term, they must provide the corresponding **IRI**. We do not add "custom" terms without a backing standard or a very strong project-specific justification.