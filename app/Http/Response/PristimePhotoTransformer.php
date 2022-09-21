<?php

namespace App\Http\Response;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class PristimePhotoTransformer
{
    public static function albums($data, $message = 'Success')
    {
        if ($data instanceof LengthAwarePaginator) {
            $result = [
                'data' => collect(
                    $data->items()
                )->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'album_date' => $row->album_date,
                        'total' => $row->pristime_photo_album_contents_count,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ];
                }),
                'current_page' => $data->currentPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'total' => $data->total(),
                'total_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
            ];
        } else {
            $result = [
                'data' => $data,
                'total' => count($data),
            ];
        }

        $response = [
            'message' => $message,
            'result' => $result
        ];

        return response()->json($response);
    }

    public static function details($data, $message = 'Success')
    {
        $response = [
            'message' => $message,
            'result' => self::reformAlbum($data)
        ];

        return response()->json($response);
    }

    public static function download($data, $message = 'Success')
    {
        $response = [
            'message' => $message,
            'result' => [
                'url' => $data
            ]
        ];

        return response()->json($response);
    }

    public static function destroy($message = 'Success')
    {
        $response = [
            'message' => $message
        ];

        return response()->json($response);
    }

    private static function reformAlbum($data)
    {
        $mappedPhotos = $data->pristimePhotoAlbumContents->map(function ($row) {
            return url($row->file_path);
        });

        return [
            'id' => $data->id,
            'album_date' => Carbon::parse($data->album_date)->format('Y-m-d'),
            'photos' => $mappedPhotos
        ];
    }
}
