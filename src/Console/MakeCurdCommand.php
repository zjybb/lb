<?php

namespace Zjybb\Lb\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCurdCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lb:make_curd
                       {service : service name} 
                       {class : class name}
                       {--D|del : del}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make curd template';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $serviceName = Str::studly($this->argument('service'));
        $className = Str::studly($this->argument('class'));

        if (blank($serviceName)) {
            $this->error('service name is empty');
        }

        if (blank($className)) {
            $this->error('class name is empty');
        }

        $makePaths = $this->getPaths($serviceName);

        if ($this->option('del')) {
            $this->del($makePaths, $serviceName, $className);
        } else {
            $this->make($makePaths, $serviceName, $className);
        }

    }

    public function del($makePaths, $serviceName, $className)
    {
        foreach ($makePaths as $type => $path) {
            $file = $path . '/' . $this->getFileName($type, $className);
            if (!File::isFile($file)) {
                $this->warn("{$file} is not exist");
            } else {
                File::delete($file);
                $this->info("Delete {$file} success");
            }
        }
    }

    public function make($makePaths, $serviceName, $className)
    {
        $stubs = __DIR__ . '/../../stubs';

        foreach ($makePaths as $type => $path) {
            $stub = $stubs . '/' . $type . '.stub';
            !File::isFile($stub) && $this->error('file not found :' . $stub);
            $content = File::get($stub);

            $content = str_replace(
                ['#CLASS#', '#class#', '#SERVICE#'],
                [$className, lcfirst($className), $serviceName],
                $content
            );

            !File::isDirectory($path) && File::makeDirectory($path, 0777, true, true);

            $file = $path . '/' . $this->getFileName($type, $className);
            if (File::isFile($file)) {
                $this->warn("{$file} is exist");
            } else {
                File::put($file, $content);
                $this->info("create {$file} success");
            }
        }
    }

    public function getFileName(string $type, string $className): string
    {
        $names = [
            'service' => "{$className}Service.php",
            'interface' => "{$className}ServiceInterface.php",
            'model' => "{$className}.php",
            'controller' => "{$className}Controller.php",
            'request' => "{$className}Request.php",
            'resource' => "{$className}Resource.php",
        ];

        return $names[$type] ?? '';
    }

    public function getPaths(string $serviceName): array
    {
        $requestPath = "app/Http/Requests";
        $resourcePath = "app/Http/Resources";
        $controllerPath = "app/Http/Controllers";
        $servicePath = "app/Services/{$serviceName}";

        return [
            'service' => "{$servicePath}/Services",
            'interface' => "{$servicePath}/Interfaces",
            'model' => "{$servicePath}/Models",
            'controller' => "{$controllerPath}",
            'request' => "{$requestPath}/{$serviceName}",
            'resource' => "{$resourcePath}/{$serviceName}",
        ];
    }
}
