<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    public function testFormSuccess(): void
    {
        $response = $this->post(
            uri: "/form",
            data: [
                'username' => 'admin',
                "password" => "rahasia",
            ]
        );

        Log::info(
            message: json_encode($response)
        );

        $response->assertStatus(
            status: 200
        );
    }

    public function testFormFailed(): void
    {
        $response = $this->post(
            uri: "/form",
            data: [
                'username' => '',
                "password" => "",
            ]
        );

        Log::info(
            message: json_encode($response)
        );

        $response->assertStatus(
            status: 302
        );
    }

    public function testFormAgainSuccess(): void
    {
        $response = $this->post(
            uri: "/form/v1",
            data: [
                'username' => 'arief@disini.com',
                "password" => "rahasia1234.",
            ]
        );

        Log::info(
            message: json_encode($response)
        );

        $response->assertStatus(
            status: 200
        );
    }
}
