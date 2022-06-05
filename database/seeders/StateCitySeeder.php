<?php

namespace Database\Seeders;

use App\Models\Cities;
use App\Models\Countries;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //get nigeria
        $nigeria = Countries::where('name', 'Nigeria')->first();

        //fetch the json file
        $sc = json_decode(file_get_contents(__DIR__ . '/../../resources/extras/states-and-cities.json'));

        foreach ($sc as $s){
            //get the country id
            $country_id = $nigeria->id;

            //create the state
            $state = \App\Models\States::create([
                'name'=>$s->name,
                'country_id'=>$country_id
            ]);

            foreach ($s->cities as $city){
                Cities::create([
                    'name'=>$city,
                    'state_id'=>$state->id
                ]);
            }
        }
    }
}
