<?php

namespace App\Http\Controllers;

use App\BandungSubmission;
use App\Http\Requests\BandungSubmissionRequest;
use App\Mail\BandungSubmissionMail;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BandungSubmissionController extends Controller
{
    public function create(BandungSubmissionRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['nik'] = Crypt::encryptString($request->nik);
            $data['phone'] = Crypt::encryptString($request->phone);

            $submission = new BandungSubmission();
            $submission->fill($data);
            $submission->save();

            $id = $submission->id;
            $uniqueCode = 'PRSTM' . $id;

            $newSubmission = BandungSubmission::find($id);
            $newSubmission->unique_code = $uniqueCode;
            $newSubmission->save();
            $newSubmission->refresh();

            $responseData = $newSubmission->toArray();
            $responseData['nik'] = Crypt::decryptString($newSubmission->nik);
            $responseData['phone'] = Crypt::decryptString($newSubmission->phone);

            Mail::to($request->email)->queue(new BandungSubmissionMail());

            DB::commit();

            $response = [
                'message' => 'Success.',
                'code' => 200,
                'data' => $responseData
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
