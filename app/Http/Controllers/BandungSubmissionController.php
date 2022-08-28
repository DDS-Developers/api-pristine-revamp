<?php

namespace App\Http\Controllers;

use App\BandungSubmission;
use App\BandungSubmissionToken;
use App\Http\Requests\BandungSubmissionRequest;
use App\Mail\BandungSubmissionMail;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BandungSubmissionController extends Controller
{
    public function create(BandungSubmissionRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['nik'] = Crypt::encryptString($request->nik);
            $data['phone'] = Crypt::encryptString($request->phone);

            $token = BandungSubmissionToken::where('token', $data['token'])->first();

            if ($token == null) {
                $response = [
                    'code' => 400,
                    'message' => 'Formulir anda tidak dapat diproses.',
                ];

                return response()->json($response, 400);
            }

            if ($token->is_used) {
                $response = [
                    'code' => 400,
                    'message' => 'Formulir anda sudah terdaftar.',
                ];

                return response()->json($response, 400);
            }

            $token->is_used = true;
            $token->save();

            $submissionTotal = BandungSubmission::count();

            if ($submissionTotal > 1500) {
                $response = [
                    'message' => 'Mohon maaf, kuota peserta untuk acara ini sudah terpenuhi. Sampai jumpa di acara selanjutnya!',
                    'code' => 400
                ];

                return response()->json($response, 400);
            }

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

    public function generateToken()
    {
        DB::beginTransaction();

        try {
            $randomString = Str::random(100);

            $newToken = new BandungSubmissionToken;
            $newToken->token = $randomString;
            $newToken->save();

            DB::commit();

            $response = [
                'code' => 200,
                'message' => 'Success.',
                'token' => $randomString
            ];

            return response()->json($response);
        } catch (Exception $e) {
            DB::rollBack();

            $response = [
                'code' => 500,
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }
}
