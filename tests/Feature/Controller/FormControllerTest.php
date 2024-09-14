<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    public function testLoginFailed(): void
    {
        $response = $this->post(
            uri: '/form/login',
            data: [
                "username" => "",
                "password" => "",
            ]
        );

        Log::info(json_encode($response));

        $response->assertStatus(status: 400);
    }

    public function testLoginSuccess(): void
    {
        $response = $this->post(
            uri: '/form/login',
            data: [
                "username" => "admin",
                "password" => "adminrahasia",
            ]
        );

        Log::info(json_encode($response));

        $response->assertStatus(status: 200);
    }
}
