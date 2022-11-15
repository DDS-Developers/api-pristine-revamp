<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Http\Requests\Promo\CreateRequest;
use App\Http\Service\PromoService;
use App\Http\Resources\PromoResource;

class PromoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = (new PromoService)->getData($request);
            return PromoResource::collection($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(CreateRequest $request)
    {
        $data = (new PromoService)->createData($request);
        return new PromoResource($data);
    }

    public function update(Promo $promo, CreateRequest $request)
    {
        $data = (new PromoService)->updateData($promo, $request);
        $response = new PromoResource($data);
        return $response;
    }

    public function delete(Promo $promo)
    {
        $data = (new PromoService)->deleteData($promo);
        $response = new PromoResource($data);
        return $response;
    }
}
