<?php

namespace App\Observers;

use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class TopicObserver
{
    /**
     * Handle the Topic "created" event.
     */
    public function created(Topic $topic): void
    {
        $path = $topic->id;

        // Check if current topic has a parent
        if ($topic->parent_id) {
            $parent = Topic::find($topic->parent_id);
            $path = $parent->path . '/' . $topic->id;
        }

        // Update the path
        Topic::where('id', $topic->id)
            ->update(['path' => $path]);
    }

    /**
     * Handle the Topic "updated" event.
     */
    public function updating(Topic $topic): void
    {
        $originalTopic = Topic::find($topic->id);
        $originalParentId = $originalTopic->parent_id;

        // Get the new parent_id from the updated model
        $newParentId = $topic->parent_id;

        // Check if the parent is changing
        if ($originalParentId != $newParentId) {
            // Get the old path from the original topic in the database
            $oldPath = $originalTopic->path;

            // Get the new parent topic(model) if it exists
            $newParent = null;
            if ($newParentId) {
                $newParent = Topic::find($newParentId);
            }

            // Calculate the new path for the topic we are changing
            $newPath = $topic->id;

            // Check if we actually have a topic instance as the new parent
            if ($newParent) {
                // Change the path according to the new parent path
                $newPath = $newParent->path . '/' . $topic->id;
            }

            // Update the paths for all the children in the database
            DB::table('topics')
                ->where('path', 'like', $oldPath . '/%')
                ->update([
                    'path' => DB::raw("REPLACE(path, '" . $oldPath . "/', '" . $newPath . "/')")
                ]);

            // Set the new path on the main topic itself
            $topic->path = $newPath;
        }
    }

}
