<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\Package;
use Illuminate\Console\Command;

class CreateTypeCommand extends Command
{
    protected $signature = 'py:create-type';

    protected $description = 'Seed type Agency, Cooperation and Agency+Cooperation to central';
    
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $type = [
            [
                'name'      => 'Agency',
                'is_active' => true,
            ],
            [
                'name'      => 'Cooperation',
                'is_active' => true,
            ],
            [
                'name'      => 'Agency+Cooperation',
                'is_active' => true,
            ],
        ];
        $config_modules = config('module');
        $data = [];

        // Array module config 
        collect($config_modules)->map(function ($module) use (&$data){
            return $data = array_merge($data, array_keys($module));
        });


        // Create module 
        $ids = [];
        foreach($data as $key => $value){
            $existing = Module::firstOrNew(['name' => $value]);
            if(!$existing->id){
                $existing->save();
            }
            $ids[$key] = $existing->id;
        }

        // create package 
        foreach ($type as $value) {
            $package = Package::firstOrNew(['name'=> $value['name']], $value);
            if (!$package->id) {
                $package->save();
                $package->modules()->sync($ids);
            }
            
        }
        $this->info('Create type successfully');
        return Command::SUCCESS;

    }
}
