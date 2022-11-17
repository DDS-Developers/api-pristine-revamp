<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Article extends Model
{
    const tableName = 'articles';

    protected $table = 'articles';

    protected $fillable = [
        'title',
        'slug',
        'banner',
        'content',
        'status',
        'published_at',
        'meta_title',
        'meta_description'
    ];

    public function getColumn()
    {
        $table = $this->getTable();
        return Schema::getColumnListing($table);
    }

    public function scopeOrder($q, $request)
    {
        $orderBy = $request->input('order_by', 'created_at');
        $sort = $request->input('sort', 'desc');
        if (in_array($orderBy, $this->getColumn()) && isset($orderBy) && isset($sort)) {
            $q->orderBy($orderBy, $sort);
        }
    }

    public function scopeSearch($q, $request)
    {
        $field = $request->input('searchBy');
        $keyword = $request->input('keyword');
        if (in_array($field, $this->getColumn()) && isset($field) && isset($keyword)) {
            $q->where($field, $keyword);
        }
    }

    public function scopeReturnType($q, $request)
    {
        if ($request->has('all') && $request->all == true) {
            return $q->get();
        }
        return $q->paginate($request->input('per_page', 10));
    }
}
