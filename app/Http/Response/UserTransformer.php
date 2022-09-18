<?php

namespace App\Http\Response;

class UserTransformer
{
    public static function details($data, $message = 'Success')
    {
        $response = [
            'message' => $message,
            'result' => self::reformUser($data)
        ];

        return response()->json($response);
    }

    private static function reformUser($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'avatar' => $data->avatar,
        ];
    }
}
