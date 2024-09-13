<?php

namespace Tests\Feature\Validator;

use App\Rules\Custom\RegistrationRule;
use App\Rules\Custom\Uppercase;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Password;
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

    public function test_validation_messages(): void
    {
        App::setLocale("id");

        $data = [
            "username" => "adminex",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => ["required", "email", "max:100"],
            'password' => ["required", "min:8", "max:20"]
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
        Log::info($validator->errors()->toJson());
    }

    public function test_validation_inline_messages(): void
    {
        App::setLocale("id");

        $data = [
            "username" => "adminex",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => ["required", "email", "max:100"],
            'password' => ["required", "min:8", "max:20"]
        ];

        $messages = [
            "required" => ":attribute harus di-isi",
            "email" => ":attribute harus berupa email",
            "max" => ":attribute maksimal :max karakter",
            "min" => ":attribute minimal :min karakter",
        ];

        $validator = Validator::make($data, $rules, $messages);

        self::assertTrue($validator->fails());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
        Log::info($validator->errors()->toJson());
    }

    public function test_additional_validation(): void
    {
        App::setLocale("id");

        $data = [
            "username" => "admin@ex",
            "password" => "admin@ex",
        ];

        $rules = [
            "username" => ["required", "email", "max:100"],
            'password' => ["required", "min:8", "max:20"]
        ];

        $validator = Validator::make($data, $rules);
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $data = $validator->getData();
            if ($data['username'] == $data['password']) {
                $validator->errors()->add('password', 'Password tidak boleh sama dengan username');
            }
        });

        self::assertTrue($validator->fails());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
        Log::info($validator->errors()->toJson());
    }

    public function test_custom_rule(): void
    {
        $data = [
            "username" => "admin@example",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => ["required", "email", "max:100", new Uppercase],
            'password' => ["required", "min:8", "max:20"]
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_custom_again_with_rule(): void
    {
        $data = [
            "username" => "admin@example",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => ["required", "email", "max:100", new Uppercase],
            'password' => ["required", "min:8", "max:20", new RegistrationRule()],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_custom_function_rule(): void
    {
        $data = [
            "username" => "admin@example",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => [
                "required",
                "email",
                "max:100",
                function (string $attributes, string $value, Closure $fail): void {
                    if (strtoupper($value) != $value) {
                        $fail("The field $attributes must be uppercase");
                    }
                }
            ],
            'password' => [
                "required",
                "min:8",
                "max:20",
                new RegistrationRule()
            ],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_custom_function_custom_rule(): void
    {
        $data = [
            "username" => "admin@example",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => [
                "required",
                "email",
                "max:100",
                function (string $attributes, string $value, Closure $fail): void {
                    if (strtoupper($value) != $value) {
                        $fail("validation.custom.uppercase")->translate([
                            "attribute" => $attributes,
                            "value" => $value,
                        ]);
                    }
                }
            ],
            'password' => [
                "required",
                "min:8",
                "max:20",
                new RegistrationRule()
            ],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_rule_classes_invalid(): void
    {
        $data = [
            "username" => "admin@example",
            "password" => "rahasia",
        ];

        $rules = [
            "username" => [
                "required",
                // "email",
                // "max:100",
                new In(values: ["Arief", "Hilmi", "Putra"])
            ],
            'password' => [
                "required",
                "min:8",
                "max:20",
                Password::min(6)->letters()->numbers()->symbols()
            ],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_rule_classes_valid(): void
    {
        $data = [
            "username" => "Arief",
            "password" => "rahasia@12",
        ];

        $rules = [
            "username" => [
                "required",
                // "email",
                // "max:100",
                new In(values: ["Arief", "Hilmi", "Putra"])
            ],
            'password' => [
                "required",
                "min:8",
                "max:20",
                Password::min(6)->letters()->numbers()->symbols()
            ],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->passes());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_nested_array_validation_valid(): void
    {
        $data = [
            "name" => [
                "first" => "Arief",
                "last" => "Hermawan"
            ],
            "address" => [
                "street" => "Jl. Belum ada",
                "city" => "Bandung",
                "country" => "Indonesia",
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["max:100"],
            "address.street" => ["max:200"],
            "address.city" => ["required", "max:100"],
            "address.country" => ["required", "max:100"],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->passes());
        Log::info(json_encode($validator->passes()));
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_nested_array_validation_invalid(): void
    {
        $data = [
            // "name" => [
            //     "first" => "Arief",
            //     "last" => "Hermawan"
            // ],
            // "address" => [
            //     "street" => "Jl. Belum ada",
            //     "city" => "Bandung",
            //     "country" => "Indonesia",
            // ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["max:100"],
            "address.street" => ["max:200"],
            "address.city" => ["required", "max:100"],
            "address.country" => ["required", "max:100"],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_nested_indexed_array_validation_valid(): void
    {
        $data = [
            "name" => [
                "first" => "Arief",
                "last" => "Hermawan"
            ],
            "address" => [
                [
                    "street" => "Jl. Belum ada",
                    "city" => "Bandung",
                    "country" => "Indonesia",
                ],
                [
                    "street" => "Jl. Belum ada",
                    "city" => "Belum Ada",
                    "country" => "Indonesia",
                ],
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["max:100"],
            "address.*.street" => ["max:200"],
            "address.*.city" => ["required", "max:100"],
            "address.*.country" => ["required", "max:100"],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->passes());
        Log::info(json_encode($validator->passes()));
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function test_nested_indexed_array_validation_invalid(): void
    {
        $data = [
            "name" => [
                "first" => "",
                "last" => "Hermawan"
            ],
            "address" => [
                [
                    "street" => "Jl. Belum ada",
                    "city" => "",
                    "country" => "",
                ],
                [
                    "street" => "Jl. Belum ada",
                    "city" => "Belum Ada",
                    "country" => "",
                ],
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["max:100"],
            "address.*.street" => ["max:200"],
            "address.*.city" => ["required", "max:100"],
            "address.*.country" => ["required", "max:100"],
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson());
        // Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }
}
