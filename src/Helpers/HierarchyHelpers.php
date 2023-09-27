<?php

namespace Roghumi\Press\Crud\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Roghumi\Press\Crud\Exceptions\HierarchyLoopException;

class HierarchyHelpers
{
    /**
     * Connect hierarchy ids as parent child.
     *
     *
     * @return void
     *
     * @throws HierarchyLoopException
     */
    public static function addHierarchyAsChild(int|string $parentId, int|string $childId, string $table)
    {
        $childDescendants = DB::table($table)
            ->where('parent_id', $childId)
            ->get();

        // check if parentId is not in childIds children, this prevents creating loops
        if ($childDescendants->pluck('child_id')->contains($parentId)) {
            throw new HierarchyLoopException();
        }

        self::removeHierarchyFromParent($childId, $table);

        $parentAncestors = DB::table($table)
            ->where('child_id', $parentId)
            ->get();

        $inserts = [
            [
                'parent_id' => $parentId,
                'child_id' => $childId,
                'depth' => 1,
            ],
        ];
        foreach ($parentAncestors as $parentAncestor) {
            $inserts[] = [
                'parent_id' => $parentAncestor->parent_id,
                'child_id' => $childId,
                'depth' => $parentAncestor->depth + 1,
            ];

            foreach ($childDescendants as $childDescendant) {
                $inserts[] = [
                    'parent_id' => $parentAncestor->parent_id,
                    'child_id' => $childDescendant->child_id,
                    'depth' => $parentAncestor->depth + $childDescendant->depth,
                ];
            }
        }
        foreach ($childDescendants as $childDescendant) {
            $inserts[] = [
                'parent_id' => $parentId,
                'child_id' => $childDescendant->child_id,
                'depth' => 1 + $childDescendant->depth,
            ];
        }

        DB::table($table)->insert($inserts);
    }

    /**
     * Make all items in child ids as children of parent id.
     *
     * @param  int|string  $parentId parent item id.
     * @param  array  $childIds array of child ids to add to parent.
     * @param  string  $table hierarchy table name.
     * @return void
     *
     * @throws HierarchyLoopException
     */
    public static function addHierarchiesAsChild(int|string $parentId, array $childIds, string $table)
    {
        $childDescendants = DB::table($table)
            ->whereIn('parent_id', $childIds)
            ->get();

        // check if parentId is not in childIds children, this prevents creating loops
        if ($childDescendants->pluck('child_id')->contains($parentId)) {
            throw new HierarchyLoopException();
        }

        self::removeHierarchiesFromParent($childIds, $table);

        $parentAncestors = DB::table($table)
            ->where('child_id', $parentId)
            ->get();

        $inserts = [];
        foreach ($childIds as $childId) {
            $inserts[] = [
                'parent_id' => $parentId,
                'child_id' => $childId,
                'depth' => 1,
            ];
        }

        foreach ($parentAncestors as $parentAncestor) {
            $inserts[] = [
                'parent_id' => $parentAncestor->parent_id,
                'child_id' => $childId,
                'depth' => $parentAncestor->depth + 1,
            ];

            foreach ($childDescendants as $childDescendant) {
                $inserts[] = [
                    'parent_id' => $parentAncestor->parent_id,
                    'child_id' => $childDescendant->child_id,
                    'depth' => $parentAncestor->depth + $childDescendant->depth,
                ];
            }
        }
        foreach ($childDescendants as $childDescendant) {
            $inserts[] = [
                'parent_id' => $parentId,
                'child_id' => $childDescendant->child_id,
                'depth' => 1 + $childDescendant->depth,
            ];
        }

        DB::table($table)->insert($inserts);
    }

    /**
     * Remove hierarchy id from parent, and make it a root item
     *
     *
     * @return void
     */
    public static function removeHierarchyFromParent(int|string $itemId, string $table)
    {
        $parentRecords = DB::table($table)
            ->where('child_id', $itemId)
            ->get();

        DB::transaction(function () use ($table, $itemId, $parentRecords) {
            // remove domainId from parents
            DB::table($table)
                ->where('child_id', $itemId)
                ->delete();
            // remove domainId children from domainIds parents
            $childRecords = DB::table($table)
                ->where('parent_id', $itemId)
                ->get();
            DB::table($table)
                ->whereIn('parent_id', $parentRecords->pluck('parent_id')->toArray())
                ->whereIn('child_id', $childRecords->pluck('child_id')->toArray())
                ->delete();
        });
    }

    /**
     * Remove hierarchy id from parent, and make it a root item
     *
     *
     * @return void
     */
    public static function removeHierarchiesFromParent(array $itemIds, string $table)
    {
        $parentRecords = DB::table($table)
            ->whereIn('child_id', $itemIds)
            ->get();

        DB::transaction(function () use ($table, $itemIds, $parentRecords) {
            // remove domainId from parents
            DB::table($table)
                ->whereIn('child_id', $itemIds)
                ->delete();
            // remove domainId children from domainIds parents
            $childRecords = DB::table($table)
                ->whereIn('parent_id', $itemIds)
                ->get();
            DB::table($table)
                ->whereIn('parent_id', $parentRecords->pluck('parent_id')->toArray())
                ->whereIn('child_id', $childRecords->pluck('child_id')->toArray())
                ->delete();
        });
    }

    /**
     * Get a collection of hierarchy item ancestor ids
     */
    public static function getAncestorHierarchyIds(int|string $itemId, string $table): Collection
    {
        return DB::table($table)
            ->where('child_id', $itemId)
            ->orderBy('depth', 'desc')
            ->pluck('parent_id');
    }

    /**
     * Get a list of a hierarchy item descendant ids
     *
     * @return Collection<int|string>
     */
    public static function getChildHierarchyIds(int|string $itemId, string $table): Collection
    {
        return DB::table($table)
            ->where('parent_id', $itemId)
            ->orderBy('depth', 'desc')
            ->pluck('child_id');
    }

    /**
     * Get root hierarchy Id for this item Id
     */
    public static function getRootHierarchyId(int|string $itemId, string $table): int|string
    {
        $rootLink = DB::table($table)
            ->where('child_id', $itemId)
            ->orderBy('depth', 'desc')
            ->first();

        // item itself is a root domain
        if (is_null($rootLink)) {
            return $itemId;
        }

        return $rootLink->parent_id;
    }

    /**
     * Get parent id for hierarchy item if exists.
     *
     * @param  int|string  $hierarchyId
     */
    public static function getFirstParentHierarchyId(int|string $itemId, string $table): int|string|null
    {
        $rootLink = DB::table($table)
            ->where('child_id', $itemId)
            ->orderBy('depth', 'asc')
            ->first();

        return $rootLink?->id;
    }
}
