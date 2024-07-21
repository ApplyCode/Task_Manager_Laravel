<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Task;

class StoreCommentRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
            'squad_id' => auth()->user()->squad_id,
        ]);
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // there are three people who can authorize a request, if the comment's task is private:
        // 1. the user who created the task
        // 2. the user who is assigned to the task
        // 3. one of the team members of the task
        // if the task is public, anyone can comment
        $user = $this->user();
        $task = Task::find($this->task_id);
        $owner = $task->user_id;
        $assignee = $task->assigned_to ?? null;
        $team = $task->team;
        if ($user->id === $owner || $user->id === $assignee || in_array($user->id, $team) || $task->public === true) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'task_id' => 'required|exists:tasks,_id',
            'body' => 'required|string',
            'user_id' => 'required',
            'squad_id' => 'required',
        ];
    }
}
