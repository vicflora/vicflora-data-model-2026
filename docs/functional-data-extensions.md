## Technical Guidance: Functional Data Extensions (Sidecar Pattern)

The application uses **Functional Data Extensions** to attach domain-specific metadata to base entities. This allows a single core record (e.g., a `Reference` or a `TaxonName`) to take on multiple specialized roles simultaneously.

### 1. The "View-Model" Rule
All specialized models (e.g., `Protologue`, `ScientificName`, `Treatment`) point to **Database Views**. 
* **Read Operations:** Use standard Eloquent (e.g., `Protologue::where(...)`). The view automatically flattens the base and extension data.
* **Write Operations:** Standard `save()` or `update()` calls on the model **will fail**. You must use the "Functional Extension API" provided by the `HasSidecar` trait.

### 2. The Functional Extension API
Every model using this pattern provides three standardized methods for persistence:

| Method | Use Case |
| :--- | :--- |
| `createWithSidecar(base, ext)` | Creates a **new** base record and its extension in one transaction. |
| `promote(baseModel, ext)` | Takes an **existing** base record and adds/updates the functional extension. |
| `updateWithSidecar(data)` | Updates an existing record, automatically routing fields to the correct tables. |

### 3. Implementation Requirements
When creating a new Functional Data Extension model, you must implement three abstract methods to configure the trait:

1.  **`getBaseTable()`**: Usually `'references'` or `'taxon_names'`.
2.  **`getExtensionTable()`**: The name of the `_ext` table (e.g., `'protologues_ext'`).
3.  **`getSidecarFields()`**: An array of fields that live in the extension table. **Return `[]` if the extension is a "marker" only (no extra columns).**

### 4. Code Example: Updating a Protologue
```php
// The contractor simply passes the combined data from the request
$protologue = Protologue::find($id);

$protologue->updateWithSidecar([
    'title' => 'Updated Journal Title', // Routed to 'references'
    'microreference' => 'p. 155'        // Routed to 'protologues_ext'
]);
```

### 5. Best Practices
* **Role Checking:** To check if a base `Reference` has a specific role, use the `$reference->reference_roles` string (or the `hasRole()` helper) provided by the `references_view`.
* **Atomic Transactions:** The trait handles database transactions. Do not wrap these calls in additional transactions unless you are performing multiple unrelated writes.
* **Validation:** Validate all fields (base and extension) together in your Request classes. The `$fillable` array on the model acts as the "source of truth" for allowed fields.