<?php
namespace App\Actions\Tenants\Settings;

use App\Models\Composition;
use App\Repositories\Compositions\CompositionRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Throwable;

class StoreComposition 
{
    protected $composition;

    public function __construct(){  
        $this->composition = App(CompositionRepository::class);
    }

    public function store(array $input){
        $id = $input['id']??null;
        $rqId = $id?(",$id,id,deleted_at,NULL"):(",null,1,deleted_at,NULL");
        $request = Request::validate([
            'name' => ['required', 'max:100','unique:compositions,name'.$rqId],
        ]);

        $user_id = auth()->user()->id;
        $input['created_by'] = $user_id;
        $input['updated_by'] = $user_id;
        if(isset($input['is_active']) && $input['is_active']){
            $input['is_active'] = 'true';
        }else{
            $input['is_active'] = 'false';
        }

        if(isset($input['id']) && !empty($input['id'])){
            unset($input['created_by']);
            $this->composition->update($input['id'],$input);
        }else{
            $this->composition->create($input);
        }
    }

    public function create( array $input){
        
        $this->store($input);
        
    }
    public function update(array $input){
        $this->store($input);
    }
}