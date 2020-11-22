<?php

namespace Zjybb\Lb\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Redis;

class RedisFlushCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lb:redis_flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush redis all table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        Redis::flushall();

        $this->info('Flush redis successfully.');

        return 1;
    }

}
