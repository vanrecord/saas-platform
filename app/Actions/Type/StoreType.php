<?php
namespace App\Actions\Type;

use Carbon\Carbon;
use App\Models\Module;
use App\Models\Package;
use App\Repositories\PackageRepository;
use Illuminate\Support\Facades\Validator;

class StoreType
{
    protected $package;

    public function __construct(){  
        $this->package = App(PackageRepository::class);
    }

    public function store( array $input){
        
        Validator::make($input, [
            'name' => 'required|string|max:255|unique:packages,name,'.($input['id'] ?? 'NULL').',id,deleted_at,NULL',
        ])->validate();

        if(isset($input['id'])){
            $input['updated_by'] = auth()->user()->id;
            $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $package = $this->package->forgetCache()->update($input['id'], $input);
        }else{
            $package = $this->package->create($input);
        }

        $module_ids = []; 
        foreach($input['modules'] as $value => $module){
            $existing = Module::firstOrNew(['name'=> $module]);
            if(!$existing->id){
                $existing->save();
            }
            $module_ids[$value] = $existing->id;
        }

        $package->modules()->sync($module_ids);

        return $package;

    }
}