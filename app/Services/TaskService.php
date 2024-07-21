<?php

namespace App\Services;

use App\Events\PrivateTaskEvent;
use App\Events\PublicTaskEvent;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Broadcasting\PendingBroadcast;
use Illuminate\Support\Collection;

class TaskService
{
    public function getTaskParticipantsIds(Task $task): Collection
    {
        return collect($task->team)->merge([$task->user_id, $task->assigned_to])->unique()->filter(function ($value) {
            return !is_null($value);
        });
    }

    public function store(array $validated): PendingBroadcast
    {
        // create a new task with the validated data
        $createdTask = Task::create($validated);
        $createdTask->load('user', 'comments', 'project');
        //if task is public, fire the PublicTaskEvent event with the squad_id and the validated data, else merge user_id, assigned_to and squad_id to one array, remove duplicates and null values and fire the PrivateTaskEvent event with the validated data
        if ($createdTask->public)
            return broadcast(new PublicTaskEvent($createdTask->squad_id, $createdTask, 'create'));
        return broadcast(new PrivateTaskEvent($this->getTaskParticipantsIds($createdTask), $createdTask, 'create'));
    }

    public function update(Task $task, array $validated): PendingBroadcast
    {
        // update the task with the validated data
        $task->update($validated);
        $task->refresh();
        $task->load('comments', 'user', 'project');
        //if task is public, fire the PublicTaskEvent event with the squad_id and the validated data, else merge user_id, assigned_to and squad_id to one array, remove duplicates and null values and fire the PrivateTaskEvent event with the validated data
        if ($task->public)
            return broadcast(new PublicTaskEvent($task->squad_id, $task, 'update'));
        return broadcast(new PrivateTaskEvent($this->getTaskParticipantsIds($task), $task, 'update'));
    }

    public function destroy(Task $task): PendingBroadcast
    {
        // get the task
        $taskToDelete = Task::find($task->_id)->toArray();
        // delete the task
        $task->delete();
        Comment::where('task_id', $taskToDelete->_id)->delete();
        //if task is public, fire the PublicTaskEvent event with the squad_id and the validated data, else merge user_id, assigned_to and squad_id to one array, remove duplicates and null values and fire the PrivateTaskEvent event with the validated data
        if ($taskToDelete->public)
            return broadcast(new PublicTaskEvent($taskToDelete->squad_id, $taskToDelete, 'delete'));
        return broadcast(new PrivateTaskEvent($this->getTaskParticipantsIds($taskToDelete), $taskToDelete, 'delete'));
    }

    public function destroyBulk(array $ids): void
    {
        $tasksQuery = Task::whereIn('_id', (array) $ids);
        Comment::whereIn('task_id', (array) $ids)->delete();
        $tasks = $tasksQuery->get();
        foreach ($tasks as $key => $task) {
            if ($task->public) {
                broadcast(new PublicTaskEvent($task->squad_id, $task, 'delete'));
            } else {
                broadcast(new PrivateTaskEvent($this->getTaskParticipantsIds($task), $task, 'delete'));
            }
        }
        $tasksQuery->delete();
    }
}
