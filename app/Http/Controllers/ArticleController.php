<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Service\ArticleService;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\Article\CreateRequest;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = (new ArticleService)->getData($request);
            return ArticleResource::collection($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(CreateRequest $request)
    {
        $data = (new ArticleService)->createData($request);
        return new ArticleResource($data);
    }

    public function update(Article $article, CreateRequest $request)
    {
        $data = (new ArticleService)->updateData($article, $request);
        return new ArticleResource($data);
    }

    public function delete(Article $article)
    {
        $data = (new ArticleService)->deleteData($article);
        return new ArticleResource($data);
    }
}
