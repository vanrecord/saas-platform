<?php

namespace App\Supports\Trait;
use Spatie\MediaLibrary\InteractsWithMedia;

trait MediaLibrary{

    use InteractsWithMedia;

    public function saveMedia(){
        foreach ($this->mediaImage as $key){
            if(is_array(request()->{$key})){
                if(count(request()->{$key})>0 && request()->hasFile($key)){
                    $this->addMultipleMediaFromRequest([$key])->each(function($fileAdder) use ($key){  
                        $fileAdder->toMediaCollection($key);  
                    });
                }
            }else{
                if(request()->hasFile($key)){
                    $this
                        ->addMediaFromRequest($key)
                        ->toMediaCollection($key);
                }
            }
        }
    }

    public function deleteMedia($is_delete=false,$is_multi=false){
        foreach ($this->mediaImage as $key){
            if($is_multi){
                $newMediaArray = is_array(request()->{$key}) ? request()->{$key} : [];
                $this->removeMediaItemsNotPresentInArray($newMediaArray,$key);
            }
            else if(request()->has($key)){
                $this->clearMediaCollection($key);
            }
            elseif($is_delete) {
                $this->clearMediaCollection($key);
            }
        }
    }

    public function getAttribute($key){
        if(in_array($key, $this->mediaImage)){
            return $this->getMedia($key);
        }else{
            return parent::getAttribute($key);
        }
    }
    
    protected static function bootMediaLibrary(){
        static::created(function($model){
            $model->saveMedia();
        });
        static::updated(function($model){
            // $model->deleteMedia();
            $model->saveMedia();
        });
        static::deleted(function($model){
            $model->deleteMedia(true);
        });
    }
}