<?php
namespace App\Services;

use App\Models\State;
use App\Repositories\Countries\CountryRepository;
use App\Repositories\States\StateRepository;

class Country{
	protected $country_repo;
	protected $state;

	public function __construct(){
		$this->country_repo = app(CountryRepository::class);
		$this->state = app(StateRepository::class);
	}

	public function getCountryData($id = null){
        $data = tenancy()->central(function($tenancy) use($id){
            if(!empty($id)){
                return $this->country_repo->find($id);
            }else{
                return $this->country_repo->get();
            }
        });
        return $data;
    }

    public function getStateData($id = null){
        $data = tenancy()->central(function() use($id){
            if(!empty($id)){
                return $this->state->find($id);
            }else{
                return $this->state->findAll();
            }
        });
        return $data;
    }

    public function getPhoneCode($id = null) {
    	$data = tenancy()->central(function($tenancy) use($id){
            if(!empty($id)){
                return $this->country_repo->find($id)->phonecode;
            }else{
                return $this->country_repo->get();
            }
        });
        return $data;
    }

    public function getStateByCountry($country_id='')
    {
        $data = tenancy()->central(function() use($country_id){
            return $this->state->query()->where('country_id','=',(int)$country_id)->get();
        });
        return $data;
    }
}