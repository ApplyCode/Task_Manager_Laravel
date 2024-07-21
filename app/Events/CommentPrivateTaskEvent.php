<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;


class CommentPrivateTaskEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $type;
    private Collection $participants_ids;
    private array $message;
    private array $channels;

    /**
     * Create a new event instance.
     * @param Collection $participants_ids
     * @param array $message
     * @param string $type
     * @return void
     */
    public function __construct(Collection $participants_ids, array $message, string $type)
    {
        $this->type = $type;
        $this->participants_ids = $participants_ids;
        $this->channels = $participants_ids->map(function ($participantId) {
            return new PrivateChannel('user.' . $participantId);
        })->toArray();
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
        // broadcast to every private channel in the $this->channels array
        return $this->channels;
    }

    /**
     * The event's broadcast name.
     * 
     * @return string
     */
    public function broadcastAs()
    {
        return 'comment-event';
    }

    /**
     * The event's broadcast data.
     * 
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }
}
