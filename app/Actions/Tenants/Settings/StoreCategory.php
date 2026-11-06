<?php
namespace App\Actions\Tenants\Settings;

use App\Repositories\Category\CategoryRepository;
use Illuminate\Support\Facades\Request;

class StoreCategory {
    protected $category;

    public function __construct(){  
        $this->category = App(CategoryRepository::class);
    }

    public function store(Array $input){
        Request::validate([
            'name_international'    => 'required|max:100|unique:categories,name_international,'.($input['id']??'NULL').',id,deleted_at,NULL',
            'name_native'           => $input['name_native']?'max:100|unique:categories,name_native,'.($input['id']??'NULL').',id,deleted_at,NULL':'',
            'order'                 => $input['order']?'integer|unique:categories,order,'.($input['id']??'NULL').',id,deleted_at,NULL':'',
        ],[
            'name_international.required' => 'The name en field is required.',
            'name_international.max' => 'The name en may not be greater than 100 characters.'
        ]);
        try {
            $user_id = auth()->user()->id;
            $input['created_by'] = $user_id;
            $input['updated_by'] = $user_id;
            if(isset($input['id'])){
                unset($input['created_by']);
                $this->category->update($input['id'],$input);
            }else{
                $this->category->create($input);
            }
        }catch(\Exception $e){
            return redirect()->back()->withErrors([
                'create' => 'Category, there was an error'
            ]);
        }
    }
}