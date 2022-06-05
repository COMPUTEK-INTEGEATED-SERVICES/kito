<?php

namespace Tests\Unit;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_register_action()
    {
        $request = new \stdClass();
        $request->name = $this->faker->name;
        $request->email = $this->faker->safeEmail();
        $request->phone = $this->faker->phoneNumber;
        $request->password = '12345678';
        $request->state_id = 1;
        $request->city_id = 1;

        $response = (new \App\Actions\AuthenticationActions())->register($request);

        $this->assertTrue($response);
    }
}
