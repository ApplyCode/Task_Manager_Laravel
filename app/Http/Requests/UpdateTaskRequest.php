<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // there are three people who can authorize a request, if a task is private:
        // 1. the user who created the task
        // 2. the user who is assigned to the task
        // 3. one of the team members of the task
        // if the task is public, anyone can update it

        $user = $this->user();
        $task = $this->route('task');
        $owner = $task->user_id;
        $assignee = $task->assigned_to ?? null;
        $team = $task->team;
        if ($user->id === $owner || $user->id === $assignee || in_array($user->id, $team) || $task->public === true) {
            return true;
        }
        return false;
    }

    /**
     * Format the input before validating the request.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('start_date')) {
            $this->merge([
                'start_date' => new \MongoDB\BSON\UTCDateTime(strtotime($this->start_date) * 1000),
            ]);
        }
        if ($this->has('due_date')) {
            $this->merge([
                'due_date' => new \MongoDB\BSON\UTCDateTime(strtotime($this->due_date) * 1000),
            ]);
        }
        if ($this->has('actual_start_date')) {
            $this->merge([
                'actual_start_date' => new \MongoDB\BSON\UTCDateTime(strtotime($this->actual_start_date) * 1000),
            ]);
        }
        if ($this->has('completed_at')) {
            $this->merge([
                'completed_at' => new \MongoDB\BSON\UTCDateTime(strtotime($this->completed_at) * 1000),
            ]);
        }
        if ($this->has('reminder_date')) {
            $this->merge([
                'reminder_date' => new \MongoDB\BSON\UTCDateTime(strtotime($this->reminder_date) * 1000),
            ]);
        }
        $this->merge([
            'public' => $this->input('public') === 'true',
        ]);
        if ($this->has('task_progress')) {
            $this->merge([
                'task_progress' => (int) $this->task_progress,
            ]);
        } else {
            $this->merge([
                'task_progress' => 0,
            ]);
        }
        if ($this->has('team')) {
            $team = $this->team;
            $team = array_map(function ($item) {
                return $item['id'];
            }, $team);
            $this->merge([
                'team' => $team,
            ]);
        }
        if ($this->has('actual_effort')) {
            $this->merge([
                'actual_effort' => (int) $this->actual_effort,
            ]);
        }
        if ($this->has('planned_effort')) {
            $this->merge([
                'planned_effort' => (int) $this->planned_effort,
            ]);
        }
        if ($this->has('cost')) {
            $this->merge([
                'cost' => (int) $this->cost,
            ]);
        }
        if ($this->has('working_days')) {
            $this->merge([
                'working_days' => (int) $this->working_days,
            ]);
        }


    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'project_id' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'start_date' => 'nullable',
            'due_date' => 'nullable',
            'actual_start_date' => 'nullable',
            'completed_at' => 'nullable',
            'task_progress' => 'nullable|integer|min:0|max:100',
            'completed' => 'nullable|boolean',
            'priority' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'public' => 'required|boolean',
            'assigned_to' => 'nullable|string|max:255',
            'team' => 'nullable|array',
            'labels' => 'nullable|array',
            'category' => 'nullable|string|max:255',
            'working_days' => 'nullable|integer',
            'planned_effort' => 'nullable|integer',
            'actual_effort' => 'nullable|integer',
            'cost' => 'nullable|integer',
            'reminder_date' => 'nullable',
            'description' => 'nullable|string',
        ];
    }
}
