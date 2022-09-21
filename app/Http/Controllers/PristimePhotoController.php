<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bandung\PristimePhotoAlbumRequest;
use App\Http\Requests\Bandung\PristimeSendPhotoRequest;
use App\Http\Response\PristimePhotoTransformer;
use App\Models\PristimePhotoAlbum;
use App\Models\PristimePhotoAlbumContent;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use ZipArchive;

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
            $albums = PristimePhotoAlbum::withCount('pristimePhotoAlbumContents');

            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);

                $albums = $albums->whereDate('album_date', '>=', $startDate)->whereDate('album_date', '<=', $endDate);
            }

            if ($request->has('all')) {
                $albums = $albums->get();
            } else {
                $albums = $albums->paginate(50);
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

            $unchangedPhotos = collect($request->photos)->filter(function ($row) {
                $check = explode(':', $row)[0];

                if ($check == 'http' || $check == 'https') {
                    return $row;
                }
            })->values();
            $mappedUnchangedPhotos = $unchangedPhotos->map(function ($row) {
                $rowArray = explode('/', $row);

                unset($rowArray[0]);
                unset($rowArray[1]);
                unset($rowArray[2]);

                $newPath = '/' . implode('/', $rowArray);

                return $newPath;
            })->values()->all();

            $changedPhotos = PristimePhotoAlbumContent::where('pristime_photo_album_id', $id)->whereNotIn('file_path', $mappedUnchangedPhotos)->get();

            $this->deletePhoto($changedPhotos);

            PristimePhotoAlbumContent::where('pristime_photo_album_id', $id)->whereNotIn('file_path', $mappedUnchangedPhotos)->delete();

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
            $albumContents = PristimePhotoAlbumContent::where('pristime_photo_album_id', $id)->get();

            $this->deletePhoto($albumContents);

            PristimePhotoAlbumContent::where('pristime_photo_album_id', $id)->delete();
            PristimePhotoAlbum::find($id)->delete();

            DB::commit();

            return PristimePhotoTransformer::destroy();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function downloadPhoto(PristimeSendPhotoRequest $request)
    {
        try {
            $mappedPhotos = collect($request->photos)->map(function ($row) {
                $rowArray = explode('/', $row);

                unset($rowArray[0]);
                unset($rowArray[1]);
                unset($rowArray[2]);

                $newRow = '/' . implode('/', $rowArray);

                return $newRow;
            })->values()->all();
            $albumContents = PristimePhotoAlbumContent::whereIn('file_path', $mappedPhotos)->pluck('clean_file_path');
            $zipName = str_replace(' ', '-', $request->name) . '-pristime-photos.zip';
            $zipPath = storage_path('app/public/pristime_photo_zips/' . $zipName);

            $zip = new ZipArchive;

            if ($zip->open($zipPath, ZipArchive::CREATE)) {
                foreach ($albumContents as $content) {
                    $cleanFilePath = substr($content, 8);
                    $fileName = substr($content, 31);
                    $filePath = storage_path('app/public/' . $cleanFilePath);

                    $zip->addFile($filePath, $fileName);
                }

                $zip->close();
            }

            $fileUrl = url(Storage::url('public/pristime_photo_zips/' . $zipName));

            return PristimePhotoTransformer::download($fileUrl);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function storePhoto($album, $photo)
    {
        set_time_limit(120);

        switch (explode(':', $photo)[0]) {
            case 'data':
                $extension = explode('/', mime_content_type($photo))[1];
                $fileName = 'pristime_photo_' . Str::uuid() . '.' . $extension;
                $cleanFileName = 'clean_pristime_photo_' . Str::uuid() . '.' . $extension;
                $fullPath = 'public/pristime_photos/' . $fileName;
                $cleanFullPath = 'public/clean_pristime_photos/' . $cleanFileName;
                $base64 = explode(',', $photo)[1];

                $image = Image::make($base64);
                $watermark = Image::make(public_path('images/pristime/pristime-watermark.png'));
                $watermark->opacity(50);

                $image->insert($watermark, 'center');
                $image->insert($watermark, 'top-left');
                $image->insert($watermark, 'top-right');
                $image->insert($watermark, 'bottom-left');
                $image->insert($watermark, 'bottom-right');
                $image->save(storage_path('app/' . $fullPath));

                Storage::put($cleanFullPath, base64_decode($base64));

                $url = Storage::url($fullPath);
                $cleanUrl = Storage::url($cleanFullPath);

                $albumContents = new PristimePhotoAlbumContent;
                $albumContents->pristime_photo_album_id = $album->id;
                $albumContents->file_path = $url;
                $albumContents->clean_file_path = $cleanUrl;
                $albumContents->save();
        }
    }

    private function deletePhoto($model)
    {
        $mappedChangedFilePath = $model->map(function ($row) {
            return 'public' . substr($row->file_path, 8);
        })->values()->all();
        $mappedChangedCleanFilePath = $model->map(function ($row) {
            return 'public' . substr($row->clean_file_path, 8);
        })->values()->all();
        $mergedChanged = array_merge($mappedChangedFilePath, $mappedChangedCleanFilePath);

        Storage::delete($mergedChanged);
    }
}
