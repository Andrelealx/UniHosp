<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PainelAtualizado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(public array $payload) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [new Channel('unihosp.paineis')];

        if (! empty($this->payload['setor_id'])) {
            $channels[] = new Channel('unihosp.setor.'.$this->payload['setor_id']);
        }

        if (! empty($this->payload['painel_slug'])) {
            $channels[] = new Channel('unihosp.painel.'.$this->payload['painel_slug']);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'PainelAtualizado';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
