<?php

namespace Zjybb\Lb;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Support\Str;
use Zjybb\Lb\Sign\Md5;
use Zjybb\Lb\Sign\SignInterface;


class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        $this->app->bind(SignInterface::class, Md5::class);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../migrations');
            $this->publishRes();
            $this->registerCommands();
        }

        $this->registerRoute();
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'lb');

        $this->handleSqlLog();
        $this->extendValidator();
    }

    protected function handleSqlLog()
    {
        if (!config('lb.sql_log.enable') ||
            Str::startsWith(request()->getPathInfo(), config('lb.sql_log.filter', []))
        ) {
            return;
        }

        DB::listen(function (QueryExecuted $query) {
            if (config('lb.sql_log.slow', 2000) >= $query->time) {
                return;
            }

            $sqlWithPlaceholders = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
            $bindings = $query->connection->prepareBindings($query->bindings);
            $pdo = $query->connection->getPdo();
            $realSql = $sqlWithPlaceholders;
            $duration = format_duration($query->time / 1000);

            if (count($bindings) > 0) {
                $realSql = vsprintf($sqlWithPlaceholders, array_map([$pdo, 'quote'], $bindings));
            }

            $sql = request()->server->get('sql_log', [
                    'time' => 0,
                    'count' => 0,
                    'sql' => []
                ]
            );

            $sql['time'] += $query->time;
            $sql['count']++;
            $sql['sql'][] = [
                'sql' => $realSql,
                'time' => $duration,
            ];

            request()->server->set('sql_log', $sql);
        });
    }

    protected function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/log.php', 'logging.channels');
        $this->mergeConfigFrom(__DIR__ . '/../config/lb.php', 'lb');

        config(['logging.default' => 'default']);

        if (config('lb.change_local')) {
            config(['app.locale' => 'zh-CN']);
            config(['app.timezone' => 'Asia/Shanghai']);
        }
    }

    protected function registerRoute()
    {
        RateLimiter::for('ping', function () {
            return Limit::perMinute(60);
        });

        Route::middleware('throttle:ping')->get('ping', function () {
            return response('pong');
        })->name('ping');
    }

    protected function publishRes()
    {
        $this->publishes([
            __DIR__ . '/../config/lb.php' => config_path('lb.php')
        ], 'lb-config');

        $this->publishes([
            __DIR__ . '/../migrations' => database_path('migrations')
        ], 'lb-migrations');
    }

    protected function registerCommands()
    {
        $this->commands([
            Console\RedisFlushCommand::class,
            Console\MakeCurdCommand::class,
        ]);
    }

    protected function extendValidator()
    {
        Validator::extend('remark', function ($attribute, $value, $parameters, $validator) {
            return $value <= 128;
        }, trans('lb::msg.remark'));

        Validator::extend('mb_alpha_dash', function ($attribute, $value, $parameters, $validator) {
            return is_string($value) && preg_match('/^[a-zA-Z0-9_-]+$/u', $value);
        });
    }
}
