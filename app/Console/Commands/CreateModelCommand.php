<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreateModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-model {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Model with permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $tableName = strtolower($name);
        $this->call('make:model', [
            'name' => $name,
        ]);

        $actions = ['create', 'view', 'update', 'delete'];

        foreach ($actions as $action) {
            $permissionName = $action . ' ' . $tableName;

            if (Permission::where('name', $permissionName)->doesntExist()) {
                Permission::create(['name' => $permissionName]);
            }
        }

    }
}
