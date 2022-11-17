<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ArticleStatusEnum;
use App\Models\Article;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules =  [
            'title' => 'required|unique:' . Article::tableName . ',title',
            'slug' => 'required|unique:' . Article::tableName . ',slug',
            'banner' => 'required|file',
            'content' => 'required',
            'status' => 'required|in:' . implode(',', ArticleStatusEnum::getListStatus()),
            'published_at' => 'required|date|date_format:Y-m-d',
            'meta_title' => 'required',
            'meta_description' => 'required'
        ];

        if (isset($this->article)) {
            unset($rules['banner']);
            $rules['title'] = $rules['title'] . ',' . $this->article->id;
            $rules['slug'] = $rules['slug'] . ',' . $this->article->id;
        }

        return $rules;
    }

    public function messages()
    {
        return [];
    }
}
