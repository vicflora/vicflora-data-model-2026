<?php

namespace App\Observers\Taxonomy;

use App\Models\Taxonomy\TaxonTreeNode;

class TaxonTreeNodeObserver
{
    /**
     * Handle the TaxonTreeNode "saving" event.
     */
    public function saving(TaxonTreeNode $node): void
    {
        // 1. If it's a root node (no parent)
        if (is_null($node->parent_id)) {
            // We still use taxon_concept_id for the visual path string 
            // as it's more meaningful for biological IDs than surrogate PKs
            $node->path = (string) $node->taxon_concept_id;
        } 
        // 2. If it has a parent, build the path based on the parent's path
        else {
            // parent_id now refers to the 'id' (PK) of the parent node
            $parent = $node->parent; 
            
            if ($parent) {
                $node->path = "{$parent->path}.{$node->taxon_concept_id}";
            }
        }
    }

    /**
     * Handle "updated" for branch moves.
     */
    public function updated(TaxonTreeNode $node): void
    {
        if ($node->wasChanged('path')) {
            // IMPORTANT: parent_id now references the PK 'id', 
            // not the 'taxon_concept_id'.
            $descendants = TaxonTreeNode::where('parent_id', $node->id)->get();
            
            foreach ($descendants as $child) {
                // This triggers the 'saving' event for the child, 
                // recursively recalculating paths down the tree.
                $child->save(); 
            }
        }
    }
}