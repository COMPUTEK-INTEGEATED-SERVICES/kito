<?php

namespace Tests;

use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;
    protected \Faker\Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
        //Artisan::call('migrate');
        //seed all here
        //Artisan::call('db:seed --class=CountriesSeeder');
        //Artisan::call('db:seed --class=StateCitySeeder');
    }

    /*public function tearDown(): void
    {
        Artisan::call('migrate:rollback');
        parent::tearDown();
    }*/
}
