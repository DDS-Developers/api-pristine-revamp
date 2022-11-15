<?php

namespace App\Http\Service;

use App\Enums\PromoStatusEnum;
use App\Models\Promo;
use App\Http\Service\UploadFileService;
use App\Enums\UploadFileFolderEnum;
use Illuminate\Support\Facades\Storage;
use DB;

class PromoService
{
    public function getData($request)
    {
        $data = Promo::search($request)
            ->order($request)
            ->returnType($request);

        return $data;
    }

    public function createData($request)
    {
        $uploadFileService = new  UploadFileService;
        DB::beginTransaction();
        try {
            $promoThumbnail = $uploadFileService->uploadImageFromFile(
                $request->file('promo_thumbnail'),
                UploadFileFolderEnum::PromoThumbnail
            );

            $promoImage = $uploadFileService->uploadImageFromFile(
                $request->file('promo_image'),
                UploadFileFolderEnum::PromoImage
            );
            $promo = Promo::create([
                'promo_title' => $request->promo_title,
                'max_redeem' => $request->max_redeem,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'voucher_code' => $request->voucher_code,
                'status' => PromoStatusEnum::Draft,
                'term_cond' => $request->term_cond,
                'galon' => (int) $request->galon,
                'refill_galon' => (int)$request->refill_galon,
                'botol_15lt' => (int)$request->botol_15lt,
                'botol_600ml' => (int)$request->botol_600ml,
                'botol_400ml' => (int)$request->botol_400ml,

                'promo_thumbnail' => $promoThumbnail['path'],
                'promo_image' => $promoImage['path'],
            ]);
            DB::commit();
            return $promo;
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::delete([$promoThumbnail['storage'], $promoImage['storage']]);
            throw new \Exception($e->getMessage());
        }
    }

    public function updateData(Promo $promo, $request)
    {
        $uploadFileService = new UploadFileService;
        DB::beginTransaction();
        try {
            $fill = $request->all();
            if ($request->hasFile('promo_thumbnail')) {
                $promoThumbnail = $uploadFileService->uploadImageFromFile(
                    $request->file('promo_thumbnail'),
                    UploadFileFolderEnum::PromoThumbnail
                );

                $fill['promo_thumbnail'] = $promoThumbnail['path'];
                $uploadFileService->deleteFile($promo->promo_thumbnail);
            }

            if ($request->hasFile('promo_image')) {
                $promoImage = $uploadFileService->uploadImageFromFile(
                    $request->file('promo_image'),
                    UploadFileFolderEnum::PromoImage
                );
                $fill['promo_image']  = $promoImage['path'];
                $uploadFileService->deleteFile($promo->promo_image);
            }

            $promo->update($fill);
            DB::commit();
            return $promo->fresh();
        } catch (\Exception $e) {
            Storage::delete([$promoThumbnail['storage'], $promoImage['storage']]);
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteData(Promo $promo)
    {
        $uploadFileService = new UploadFileService;
        DB::beginTransaction();
        try {
            $uploadFileService->deleteFile($promo->getOriginal('promo_thumbnail'));
            $uploadFileService->deleteFile($promo->getOriginal('promo_image'));
            $promo->delete();
            return $promo;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
