<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $type;
    private string $squad_id;
    private Project $project;

    /**
     * Create a new event instance.
     * @param string $squad_id
     * @param Project $project
     * @param string $type
     *
     * @return void
     */
    public function __construct(string $squad_id, Project $project, string $type)
    {
        $this->type = $type;
        $this->squad_id = 'squad.' . $squad_id;
        $this->project = $project;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel($this->squad_id);
    }

    /**
     * The event's broadcast name.
     * 
     * @return string
     */
    public function broadcastAs()
    {
        return 'project-event';
    }

    /**
     * The event's broadcast data.
     * 
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'type' => $this->type,
            'project' => $this->project
        ];
    }
}
