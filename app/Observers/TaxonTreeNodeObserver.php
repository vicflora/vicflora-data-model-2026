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
            $node->path = (string) $node->taxon_concept_id;
        } 
        // 2. If it has a parent, build the path based on the parent's path
        else {
            $parent = TaxonTreeNode::find($node->parent_id);
            
            if ($parent) {
                $node->path = "{$parent->path}.{$node->taxon_concept_id}";
            }
        }
    }

    /**
     * Optional: Handle "updated" for branch moves.
     * If a parent moves, all children's paths must update.
     */
    public function updated(TaxonTreeNode $node): void
    {
        if ($node->wasChanged('path')) {
            // This is the recursive "Cascade" to update all descendants
            // only if the path itself changed (e.g. node moved to new parent)
            $descendants = TaxonTreeNode::where('parent_id', $node->taxon_concept_id)->get();
            
            foreach ($descendants as $child) {
                // Triggering 'save' on children will fire their own 'saving' observer
                $child->save(); 
            }
        }
    }
}