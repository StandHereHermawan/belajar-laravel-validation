<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
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

    public function formAgain(): Response
    {
        return response()->view(
            view: "template.again"
        );
    }

    public function submitFormAgain(LoginRequest $request): Response
    {
        $data = $request->validated();

        Log::debug(
            message: json_encode(
                value: $data
            )
        );

        Log::debug(
            message: json_encode(
                value: $request->all()
            )
        );

        return response(
            content: "OK",
            status: Response::HTTP_OK
        );
    }
}
