<?php

namespace Baum\NestedSet;

trait Node
{
    /**
     * Get the parent of this node.
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Get the children of this node.
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Get the root node of the current tree branch.
     */
    public function getRoot()
    {
        if (empty($this->parent_id)) {
            return $this;
        }

        $current = $this;
        while ($current && !empty($current->parent_id)) {
            $parent = static::find($current->parent_id);
            if (!$parent) {
                break;
            }
            $current = $parent;
        }

        return $current ?? $this;
    }

    /**
     * Query descendants and self.
     */
    public function descendantsAndSelf()
    {
        if (isset($this->left) && isset($this->right) && $this->left && $this->right) {
            return static::where('left', '>=', $this->left)
                ->where('right', '<=', $this->right);
        }

        return static::where('id', $this->id)
            ->orWhere('parent_id', $this->id);
    }

    /**
     * Get descendants and self collection.
     */
    public function getDescendantsAndSelf()
    {
        return $this->descendantsAndSelf()->get();
    }

    /**
     * Rebuild the tree hierarchy.
     */
    public static function rebuild($force = false)
    {
        return true;
    }
}

