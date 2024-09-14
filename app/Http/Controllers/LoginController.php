<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function form(): Response
    {
        return response()->view(
            view: "template"
        );
    }

    public function submitForm(Request $request): Response
    {
        $request->validate(
            rules: [
                "username" => "required",
                "password" => "required",
            ]
        );

        Log::debug(
            message: json_encode(
                value: $request
            )
        );

        return response(
            content: "OK",
            status: Response::HTTP_OK
        );
    }
}
