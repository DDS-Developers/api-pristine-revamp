<?php

namespace App\Http\Controllers;

use App\BandungSubmission;
use App\Http\Requests\BandungSubmissionRequest;
use App\Mail\BandungSubmissionMail;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BandungSubmissionController extends Controller
{
    public function create(BandungSubmissionRequest $request)
    {
        DB::beginTransaction();

        try {
            $submission = new BandungSubmission();
            $submission->fill($request->validated());
            $submission->save();

            Mail::to($request->email)->queue(new BandungSubmissionMail());

            DB::commit();

            $response = [
                'message' => 'Success.',
                'code' => 200,
            ];

            return response()->json($response);
        } catch (Exception $e) {
            DB::rollBack();

            $response = [
                'message' => $e->getMessage(),
                'code' => 500,
            ];

            return response()->json($response, 500);
        }
    }
}
