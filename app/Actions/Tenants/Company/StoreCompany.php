<?php
namespace App\Actions\Tenants\Company;

use Illuminate\Support\Facades\Request;
use App\Repositories\Companies\CompanyRepository;
use App\Repositories\Domain\DomainRepository;

class StoreCompany 
{
    protected $company;
    protected $domain;

    public function __construct(){  
        $this->company = App(CompanyRepository::class);
        $this->domain = App(DomainRepository::class);
    }
    public function create($input){
        //custom validation messages
        $request = Request::validate([
            'name_international' => 'required|max:255'
        ],[
            'name_international.required' => 'The company name en is required',
            'name_international.max' => 'The company name en may not be greater than 255 characters.'
        ]);
        $company = $this->company;
        $input['social_links'] = '{"link": "'.$input->social_links.'"}';
        if($input->id){
            $companyModel = $company->find($input->id);
            $company = $this->company->update($input['id'],$input->except('logo'));
            if($input->hasFile('logo')){
                $companyModel->deleteMedia();
                $companyModel
                    ->addMediaFromRequest('logo')
                    ->toMediaCollection('logo');
            }
            // Update company domain and tenancy
            tenancy()->central(function($tenancy) use ($input){
                $domain = $tenancy->domain;
                str_replace('http://', '.',$domain);
                $dataDomain['domain']   = $domain;
                $dataDomain['company'] = $input->name_international;
                $dataDomain['name_international'] = $input->name_international;

                $domainCentral = $tenancy->domains->first();
                $this->domain->update($domainCentral->id, $dataDomain);
                $tenancy->update($dataDomain);
             });
             // End update company domain and tenancy

            $msg        = 'Company created successfully';

        }else{
            $company= $company->create($input->except('logo','_token'));
            $msg       = 'Company created successfully';
        }
        
    }
}