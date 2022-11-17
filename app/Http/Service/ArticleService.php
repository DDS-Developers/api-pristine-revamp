<?php

namespace App\Http\Service;

use App\Models\Article;
use App\Http\Service\UploadFileService;
use App\Enums\UploadFileFolderEnum;
use DB;

class ArticleService
{

    public function getData($request)
    {
        $data = Article::search($request)
            ->order($request)
            ->returnType($request);

        return $data;
    }

    public function createData($request)
    {
        $uploadFileService = new UploadFileService;
        DB::beginTransaction();
        try {
            $banner = $uploadFileService->uploadImageFromFile(
                $request->file('banner'),
                UploadFileFolderEnum::ArticleBanner
            );
            $article = Article::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'banner' => $banner['path'],
                'content' => $request->content,
                'status' => $request->status,
                'published_at' => $request->published_at,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ]);
            DB::commit();
            return $article;
        } catch (\Exception $e) {
            Storage::delete($banner['storage']);
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateData(Article $article, $request)
    {
        $uploadFileService = new UploadFileService;
        DB::beginTransaction();
        try {
            $fill = $request->all();
            if ($request->hasFile('banner')) {
                $banner = $uploadFileService->uploadImageFromFile(
                    $request->file('banner'),
                    UploadFileFolderEnum::ArticleBanner
                );
                $fill['banner'] = $banner['path'];
                $uploadFileService->deleteFile($article->banner);
            }
            $article->update($fill);
            DB::commit();
            return $article->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteData(Article $article)
    {
        $uploadFileService = new UploadFileService;
        DB::beginTransaction();
        try {
            $uploadFileService->deleteFile($article->banner);
            $article->delete();
            DB::commit();
            return $article;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
