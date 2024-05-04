<?php

namespace App\Traits;

trait ResponsesTrait
{
    public function success($data = null)
    {
        return response()->json(
            [
                'success' => true,
                $data,
            ]
        );
    }

    public function fail($data = null)
    {
        return response()->json(
            [
                'success' => false,
                $data
            ]
        );
    }
}
