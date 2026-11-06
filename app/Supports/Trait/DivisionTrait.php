<?php
namespace App\Supports\Trait;
use App\Models\Province;
use App\Models\District;
use App\Models\Commune;
use App\Models\Village;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

trait DivisionTrait
{
   protected function Provinces()
    {
        try {
            $reader     = Reader::createFromPath('database/source/provinces.csv', 'r');
            $stmt       = (new Statement())->offset(1);
            $records    = $stmt->process($reader);
            $count_row  = iterator_to_array($records->fetchPairs(), true);//pairs_without_duplicates
            print "**************************\n";
            print "  Province progress bar...\n";

            $get_code = Province::pluck('code','code')->toArray();
            $this->output->progressStart(25);
            $reader->setHeaderOffset(0);
            foreach ($reader->getRecords() as $records) {
                if(array_key_exists($records['Code'], $get_code)){
                    $province = Province::where('code',$records['Code'])->first();
                    $province->update([
                        "code"             => $records['Code'], 
                        "name_kh"          => $records['Name_KH'], 
                        "name_en"          => $records['Name_EN'],  
                        "province_type_kh" => $records['ProvTypeKh'],
                        "province_type_en" => $records['ProvTypeEn'],
                        "prefix_code"      => $records['Prefix'],
                        "calling_code"     => $records['Country_Code'],
                    ]);
    
                }else{
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

            $this->output->progressAdvance();

            }
            print "\n";
            return true;

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }

    }

    protected function Districts(){

        try {
            $reader     = Reader::createFromPath('database/source/district.csv', 'r');
            $stmt       = (new Statement())->offset(1);
            $records    = $stmt->process($reader);
            $count_row  = iterator_to_array($records->fetchPairs(), true);//pairs_without_duplicates

            print "**************************\n";
            print " District progress bar...\n";
            $get_code = District::pluck('code','code')->toArray();
            $this->output->progressStart(204);

            $reader->setHeaderOffset(0);
            foreach ($reader->getRecords() as $records) {
              
                if(array_key_exists($records['Code'], $get_code)){
                    $province = Province::where('code',$records['Province_Code'])->first();
                    $district = District::where('code',$records['Code'])->first();
                    
                    $district->update([
                        "type"             => $records['Type'],
                        "code"             => $records['Code'], 
                        "name_kh"          => $records['Name_KH'], 
                        "name_en"          => $records['Name_EN'],  
                        'reference'        => $records['Reference'],
                        'official_note'    => $records['Official_Note'],
                        "province_id"      => $province->id??""
                    ]);
    
                }else{
                    $province = Province::where('code',$records['Province_Code'])->first();
                    District::create([
                        "type"             => $records['Type'],
                        "code"             => $records['Code'],
                        "name_kh"          => $records['Name_KH'], 
                        "name_en"          => $records['Name_EN'],  
                        'reference'        => $records['Reference'],
                        'official_note'    => $records['Official_Note'],
                        "province_id"      => $province->id??""
                    ]);
                }

            $this->output->progressAdvance();
            }
            print "\n";
            return true;

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }

    protected function Communes(){

        try {
            $reader     = Reader::createFromPath('database/source/commune.csv', 'r');
            $stmt       = (new Statement())->offset(1);
            $records    = $stmt->process($reader);

            $count_row  = iterator_to_array($records->fetchPairs(), true);//pairs_without_duplicates

            print "**************************\n";
            print " Communes progress bar...\n";

            $get_code = Commune::pluck('code','code')->toArray();
            $this->output->progressStart(1652);

            $reader->setHeaderOffset(0); 
            foreach ($reader->getRecords() as $records) {

                $district = District::where('code',$records['District_Code'])->first();
                if(array_key_exists($records['Code'], $get_code)){

                    $commune = Commune::where('code',$records['Code'])->first();
                    $commune->update([
                        "type"             => $records['Type'],
                        "code"             => $records['Code'], 
                        "name_kh"          => $records['Name_KH'], 
                        "name_en"          => $records['Name_EN'],  
                        'reference'        => $records['Reference'],
                        'official_note'    => $records['Official_Note'],
                        "district_id"      => $district->id??""
                    ]);
    
                }else{

                    Commune::create([
                        "type"             => $records['Type'],
                        "code"             => $records['Code'],
                        "name_kh"          => $records['Name_KH'], 
                        "name_en"          => $records['Name_EN'],  
                        'reference'        => $records['Reference'],
                        'official_note'    => $records['Official_Note'],
                        "district_id"      => $district->id??""
                    ]);
                }

            $this->output->progressAdvance();
            }
            print "\n";
            return true;

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }

    protected function Villages(){

        try {
            $reader     = Reader::createFromPath('database/source/village.csv', 'r');
            $stmt       = (new Statement())->offset(1);
            $records    = $stmt->process($reader);

            $count_row  = iterator_to_array($records->fetchPairs(), true);//pairs_without_duplicates
            
            print "**************************\n";
            print " Village progress bar...\n";

            $get_code = Village::pluck('code','code')->toArray();
            $this->output->progressStart(14444);

            $reader->setHeaderOffset(0); 
            foreach ($reader->getRecords() as $records) {

                $commune = Commune::where('code',$records['Commune_Code'])->first();

                if(empty($commune)){
                        
                    }
                if(array_key_exists($records['Code'], $get_code)){

                    $village = Village::where('code',$records['Code'])->first();
                    $village->update([
                        "type"             => $records['Type'],
                        "code"             => $records['Code'], 
                        "name_kh"          => $records['Name_KH'], 
                        "name_en"          => $records['Name_EN'],  
                        'reference'        => $records['Reference'],
                        'official_note'    => $records['Official_Note'],
                        "commune_id"       => $commune->id??null
                    ]);
    
                }else{

                    Village::create([
                        "type"             => $records['Type'],
                        "code"             => $records['Code'],
                        "name_kh"          => $records['Name_KH'], 
                        "name_en"          => $records['Name_EN'],  
                        'reference'        => $records['Reference'],
                        'official_note'    => $records['Official_Note'],
                        "commune_id"       => $commune->id??null
                    ]);
                }
                $this->output->progressAdvance();
            }
            print "\n";
            return true;

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }
        
    }
}