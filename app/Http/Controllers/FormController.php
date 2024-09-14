<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    public function login(Request $request): Response
    {
        try {
            $rules = [
                "username" => "required",
                "password" => "required",
            ];

            $data = $request->validate(
                rules: $rules
            );

            Log::debug(json_encode($data));

            // do something with $data
            return response("OK", Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return response($exception->errors(), Response::HTTP_BAD_REQUEST);
        }
    }
}
