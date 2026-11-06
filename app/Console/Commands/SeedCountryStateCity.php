<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SeedCountryStateCity extends Command
{
    protected $signature = 'fwta:seed-country-state';

    protected $description = 'Seed country and state data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Importing Countries and States');

        $existing = Country::pluck('iso3');

        $countries = json_decode(file_get_contents(database_path('source/countries+states.json')));

        $newCountries = collect($countries)->filter(function ($country) use ($existing) {
            return !$existing->contains($country->iso3);
        });

        if (!$newCountries->count()) {
            $this->info('There are no new countries to import');

            return Command::SUCCESS;
        }

        DB::transaction(function () use ($newCountries) {
            $this->withProgressBar($newCountries, function ($country) {
                $model = Country::create([
                    'name'      => $country->name,
                    'iso3'      => $country->iso3,
                    'iso2'      => $country->iso2,
                    'phonecode' => $country->phone_code,
                    'capital'   => $country->capital,
                    'currency'  => $country->currency,
                    'native'    => $country->native,
                    'emoji'     => $country->emoji,
                    'emoji_u'   => $country->emojiU,
                ]);

                $states = collect($country->states)->map(function ($state) {
                    return [
                        'name' => $state->name,
                        'code' => $state->state_code,
                    ];
                });

                $model->states()->createMany($states->toArray());
            });
        });

        $this->line('');

        return Command::SUCCESS;
    }
}
