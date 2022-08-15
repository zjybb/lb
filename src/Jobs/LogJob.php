<?php

namespace Zjybb\Lb\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public $message;
    public array $context;
    public string $channel;
    public string $level;

    public string $requestId = '';

    /**
     * Create a new job instance.
     *
     * @param $message
     * @param array $context
     * @param string $channel
     * @param string $level
     */
    public function __construct($message, array $context, string $channel = 'default', string $level = 'INFO')
    {
        $this->message = $message;
        $this->context = $context;
        $this->channel = $channel;
        $this->level   = $level;

        $this->requestId = get_request_id();
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        request()->server->set('X-REQUEST-ID', $this->requestId);

        Log::channel($this->channel)->log($this->level, $this->message, $this->context);
    }

}
