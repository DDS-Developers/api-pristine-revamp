<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bandung\PristimePhotoAlbumRequest;
use App\Http\Response\PristimePhotoTransformer;
use App\Models\PristimePhotoAlbum;
use App\Models\PristimePhotoAlbumContent;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PristimePhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $startDate = Carbon::today()->addWeeks(-1);
            $endDate = Carbon::today();

            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
            }

            $albums = PristimePhotoAlbum::withCount('pristimePhotoAlbumContents')->whereDate('album_date', '>=', $startDate)->whereDate('album_date', '<=', $endDate);

            if ($request->has('all')) {
                $albums = $albums->get();
            } else {
                $albums = $albums->paginate(10);
            }

            return PristimePhotoTransformer::albums($albums);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PristimePhotoAlbumRequest $request)
    {
        DB::beginTransaction();

        try {
            $album = new PristimePhotoAlbum;
            $album->album_date = Carbon::parse($request->album_date);
            $album->save();

            foreach ($request->photos as $photo) {
                $this->storePhoto($album, $photo);
            }

            DB::commit();

            return PristimePhotoTransformer::details($album);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $album = PristimePhotoAlbum::find($id);

            return PristimePhotoTransformer::details($album);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $album = PristimePhotoAlbum::find($id);
            $album->album_date = Carbon::parse($request->album_date);
            $album->save();

            foreach ($request->photos as $photo) {
                $this->storePhoto($album, $photo);
            }

            DB::commit();

            return PristimePhotoTransformer::details($album);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            PristimePhotoAlbum::find($id)->delete();
            PristimePhotoAlbumContent::where('pristime_photo_album_id', $id)->delete();

            DB::commit();

            $response = [
                'message' => 'Success'
            ];

            return response()->json($response);
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    private function storePhoto($album, $photo)
    {
        switch (explode(':', $photo)[0]) {
            case 'data':
                $extension = explode('/', mime_content_type($photo))[1];
                $fileName = 'pristime_photos_' . Str::uuid() . '.' . $extension;
                $fullPath = 'public/pristime_photos/' . $fileName;
                $base64 = explode(',', $photo)[1];

                Storage::put($fullPath, base64_decode($base64));

                $url = Storage::url($fullPath);

                $albumContents = new PristimePhotoAlbumContent;
                $albumContents->pristime_photo_album_id = $album->id;
                $albumContents->file_path = $url;
                $albumContents->save();
        }
    }
}
