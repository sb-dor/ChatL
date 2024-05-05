<?php

namespace App\Traits;

trait ResponsesTrait
{
    // $data should be sent as an "array"
    public function success($data = null)
    {
        $response = [
            "success" => true,
        ];

        if ($data) $response = array_merge($response, $data);

        return response()->json(
            $response
        );
    }

    // $data should be sent as an "array"
    public function fail($data = null)
    {
        $response = [
            "success" => false,
        ];

        if ($data) $response = array_merge($response, $data);

        return response()->json(
            $response
        );
    }
}
