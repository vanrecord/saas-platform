<?php
namespace App\Actions\Tenants\Settings;

use App\Repositories\ItemLevel\ItemLevelRepository;
use Illuminate\Support\Facades\Request;

class StoreItemLevel {
    protected $itemLevel;

    public function __construct(){
        $this->itemLevel = App(ItemLevelRepository::class);
    }

    public function store($request){
        $request->validate([
            'level_number' => 'unique:item_levels,level_number,'.($request->id??'null').',id,deleted_at,NULL',
        ]);
        if($request->id){
            $this->itemLevel->update($request->id,$request->except('id'));
        }else{
            $this->itemLevel->create($request->all());
        }
    }
}
