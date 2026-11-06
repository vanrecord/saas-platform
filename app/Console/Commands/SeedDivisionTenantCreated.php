<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\ExtendsLaravelCommand;
use App\Supports\Trait\DivisionTrait;
use Stancl\Tenancy\Concerns\TenantAwareCommand;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use App\Models\Village;
use App\Models\Commune;
use App\Models\Province;
use App\Models\District;


class SeedDivisionTenantCreated extends Command
{
    use HasATenantsOption, ExtendsLaravelCommand;

    protected $description = 'Seed division to tenant';
   
    protected static function getTenantCommandName(): string
    {
        return 'py:seed-division-tenant-created';
    }
  
    public function handle()
    {

        tenancy()->runForMultiple($this->option('tenants'), function ($tenant) {
            $this->line("Tenant: {$tenant->getTenantKey()}");
            $this->Provinces();
            $this->Districts();
            $this->Communes();
            $this->Villages();
        });
    }

    protected function Provinces()
    {
        try {
            if(Province::count('id') == 0 ){
                $reader     = Reader::createFromPath('./../database/source/provinces.csv', 'r');
                $stmt       = (new Statement())->offset(1);
                $records    = $stmt->process($reader);
                
                $reader->setHeaderOffset(0);
            
                foreach ($reader->getRecords() as $records) {
                    Province::create([
                            "code"             => $records['Code'],
                            "name_kh"          => $records['Name_KH'], 
                            "name_en"          => $records['Name_EN'],  
                            "province_type_kh" => $records['ProvTypeKh'],
                            "province_type_en" => $records['ProvTypeEn'],
                            "prefix_code"      => $records['Prefix'],
                            "calling_code"     => $records['Country_Code'],
                        ]);

                }
            }
            return true;

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }

    }

    protected function Districts(){

        try {
            if(District::count('id') == 0){
                $reader     = Reader::createFromPath('./../database/source/district.csv', 'r');
                $stmt       = (new Statement())->offset(1);
                $records    = $stmt->process($reader);

                $reader->setHeaderOffset(0);
                $provinces = Province::pluck('id','code')->all();
            
                foreach ($reader->getRecords() as $records) {
                  
                    $province = $provinces[$records['Province_Code']];
                    District::create([
                        "type"             => $records['Type'],
                        "code"             => $records['Code'],
                        "name_kh"          => $records['Name_KH'], 
                        "name_en"          => $records['Name_EN'],  
                        'reference'        => $records['Reference'],
                        'official_note'    => $records['Official_Note'],
                        "province_id"      => $province??""
                    ]);

                }
            }

            return true;

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }

    protected function Communes(){

        try {
            if(Commune::count('id') == 0){
                $reader     = Reader::createFromPath('./../database/source/commune.csv', 'r');
                $stmt       = (new Statement())->offset(1);
                $records    = $stmt->process($reader);

                $reader->setHeaderOffset(0); 
                $districts = District::pluck('id','code')->all();
                foreach ($reader->getRecords() as $records) {

                    $district = $districts[$records['District_Code']];
                    Commune::create([
                            "type"             => $records['Type'],
                            "code"             => $records['Code'],
                            "name_kh"          => $records['Name_KH'], 
                            "name_en"          => $records['Name_EN'],  
                            'reference'        => $records['Reference'],
                            'official_note'    => $records['Official_Note'],
                            "district_id"      => $district??""
                        ]);
                }
            }
            return true;

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }

    protected function Villages(){

        try {
            if(Village::count('id') == 0){
                $reader     = Reader::createFromPath('./../database/source/village.csv', 'r');
                $stmt       = (new Statement())->offset(1);
                $records    = $stmt->process($reader);

                $reader->setHeaderOffset(0); 
                $communes = Commune::pluck('id','code')->all();
                foreach ($reader->getRecords() as $records) {
                    if($records['Commune_Code']){
                        $commune = $communes[$records['Commune_Code']];
                    }
                    Village::create([
                            "type"             => $records['Type'],
                            "code"             => $records['Code'],
                            "name_kh"          => $records['Name_KH'], 
                            "name_en"          => $records['Name_EN'],  
                            'reference'        => $records['Reference'],
                            'official_note'    => $records['Official_Note'],
                            "commune_id"       => $commune??null
                        ]);
                }
            }
            return true;

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }
        
    }

    
}
