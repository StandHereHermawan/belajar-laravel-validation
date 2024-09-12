<?php

namespace Tests\Feature\Validator;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function test_exception_from_validator(): void
    {
        $data = [
            "username" => "",
            "password" => "",
        ];

        // $data = [
        //     "username" => "admin",
        //     "password" => "rahasia",
        // ];

        $rules = [
            "username" => "required",
            'password' => "required"
        ];

        $validator = Validator::make($data, $rules);

        try {
            $validator->validate();
            self::fail("ValidationException not thrown");
        } catch (\Illuminate\Validation\ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function test_validation_rules(): void
    {
        $data = [
            "username" => "adminex",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => "required|email|max:100",
            'password' => ["required", "min:8", "max:20"]
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
        Log::info($validator->errors()->toJson());
    }

    public function test_valid_data(): void
    {
        $data = [
            "username" => "admin@localhost.gw",
            "password" => "rahasia12",
            "admin" => true,
            "others" => "xxx"
        ];

        $rules = [
            "username" => ["required", "email", "max:100"],
            'password' => ["required", "min:8", "max:20"]
        ];

        $validator = Validator::make($data, $rules);

        try {
            $result = $validator->validate();
            self::assertNotNull($result);
            Log::info(json_encode($result));
            // Log::info(json_encode($result, JSON_PRETTY_PRINT));
        } catch (\Illuminate\Validation\ValidationException $exception) {
            self::fail($exception->getMessage());
        }
    }
}
