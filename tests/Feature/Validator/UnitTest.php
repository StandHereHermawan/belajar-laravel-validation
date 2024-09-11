<?php

namespace Tests\Feature\Validator;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UnitTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example_validator(): void
    {
        $data = [
            "username" => "admin",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => "required",
            'password' => "required"
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());
    }

    public function test_invalid_validator(): void
    {
        $data = [
            "username" => "",
            "password" => "",
        ];

        $rules = [
            "username" => "required",
            'password' => "required"
        ];

        $validator = Validator::make($data, $rules);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());
    }
}
