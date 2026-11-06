<?php
namespace App\Actions\Tenants\Settings;

use App\Repositories\Lab\LabRepository;
use App\Repositories\Manufactures\ManufactureRepository;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class StoreLab {
    protected $lab;
    protected $manufacture;

    public function __construct(){  
        $this->lab = App(LabRepository::class);
        $this->manufacture = App(ManufactureRepository::class);
    }

    public function store($request){
        Request::validate([
            'name' => 'required|max:100|unique:labs,name,'.($request->id??'null').',id,deleted_at,NULL',
            'manufacture_id' => 'required',
            'code'  => 'required|max:30|unique:labs,code,'.($request->id??'null').',id,deleted_at,NULL',
            'phone'  => 'required|max:30',
        ]);
        try {
            if($request->id){
                unset($request->created_by);
                $this->lab->update($request->id,$request->except('logo','_token','id'));
                $msg = 'Lab updated successfully';
            }else{
                $this->lab->create($request->except('logo','_token'));
                $msg = 'Lab created successfully';
            }
        }catch(\Exception $e){
            return redirect()->back()->withErrors([
                'create' => 'Lab, there was an error'
            ]);
        }
        return $msg;
    }
}