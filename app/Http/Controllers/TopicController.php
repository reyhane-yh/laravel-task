<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TopicController extends Controller
{
    public function store(Request $request, ?Topic $parentTopic = null){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Check if the new topic has a parent topic
        if ($parentTopic) {
            $validated['parent_id'] = $parentTopic->id;
        } else {
            $validated['parent_id'] = null;
        }

        $topic = Topic::create($validated);
        $topic->refresh();

        return response()->json($topic, 201);
    }

    public function update(Request $request, Topic $topic){
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:topics,id',
                Rule::notIn([$topic->id]),
            ],
        ]);


        // Check if we actually have a new parent_id
        if ($request->has('parent_id') && $request->input('parent_id') != $topic->parent_id) {
            $newParentId = $request->input('parent_id');

            if ($newParentId) {
                $newParent = Topic::find($newParentId);

                // Just to make sure we don't assign a topic to it's descendants
                if ($newParent && str_starts_with($newParent->path, $topic->path . '/')) {
                    // The new parent path starts with the old path -> new parent is actually a descendant of old parent!
                    return response()->json([
                        'message' => 'Invalid move.',
                        'errors' => [
                            'parent_id' => ['You cannot move a topic under one of its own descendants.']
                        ]
                    ], 422);
                }
            }
        }

        // Just to make sure that all updates to the topic and its sub-topics performs together
        DB::transaction(function () use ($topic, $validated) {
            $topic->update($validated);
        });
        $topic->refresh();

        return response()->json($topic, 200);
    }

    public function question_list(Request $request, Topic $topic){
        $topic_ids = Topic::where('path', 'like', $topic->path . '%')->get()->pluck('id')->toArray();
        $questions = Question::whereIn('topic_id', $topic_ids)->get();

        return response()->json($questions);
    }
}
