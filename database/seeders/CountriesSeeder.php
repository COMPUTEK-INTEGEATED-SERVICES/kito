<?php

namespace Database\Seeders;

use App\Models\Countries;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = json_decode(file_get_contents(__DIR__ . '/../../resources/extras/countries1.json'));

        foreach ($countries as $country) {
            Countries::create([
                'name'=>$country->name,
            ]);
        }

    }
}
