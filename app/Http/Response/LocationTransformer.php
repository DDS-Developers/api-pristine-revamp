<?php

namespace App\Http\Response;

use Carbon\Carbon;

class LocationTransformer
{

    public static function region($data, $type, $message = 'Success')
    {

        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($data->items())->transform(function ($v) use ($type) {
                switch ($type) {
                    case 'city':
                        return self::city($v);
                        break;
                    case 'district':
                        return self::district($v);
                        break;
                    case 'sub_district':
                        return self::subDistrict($v);
                        break;
                    default:
                        return self::province($v);
                }
            });
            $return = [
                'data' => $items,
                'current_page' => $data->currentPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'total' => $data->total(),
                'total_page' => $data->lastPage(),
                'per_page' => $data->perPage()
            ];
        } else {

            $return = [
                'data' => $data->transform(function ($v) use ($type) {
                    switch ($type) {
                        case 'city':
                            return self::city($v);
                            break;
                        case 'district':
                            return self::district($v);
                            break;
                        case 'sub_district':
                            return self::subDistrict($v);
                            break;
                        default:
                            return self::province($v);
                    }
                }),
                'total' => count($data)
            ];
        }
        return response()->json([
            'message' => $message,
            'result' => $return
        ]);
    }

    private static function province($v)
    {
        return [
            'id' => $v->id,
            'name_id' => $v->name_id,
            'name_en' => $v->name_en,
        ];
    }

    private static function city($v)
    {
        return [
            'id' => $v->id,
            'province_id' => $v->province_id,
            'name_id' => $v->name_id,
            'name_en' => $v->name_en,
        ];
    }

    private static function district($v)
    {
        return [
            'id' => $v->id,
            'city_id' => $v->city_id,
            'name_id' => $v->name_id,
            'name_en' => $v->name_en,
        ];
    }

    private static function subDistrict($v)
    {
        return [
            'id' => $v->id,
            'district_id' => $v->district_id,
            'name_id' => $v->name_id,
            'name_en' => $v->name_en,
            'postal' => $v->postal
        ];
    }
}
