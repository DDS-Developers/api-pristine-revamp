<?php

namespace App\Http\Controllers;

use App\Models\BandungSubmission;
use App\Models\BandungSubmissionToken;
use App\Http\Requests\Bandung\BandungSubmissionRequest;
use App\Http\Requests\Bandung\EventCheckinRequest;
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
                throw new Exception('Formulir anda tidak dapat diproses.');
            }

            if ($token->is_used) {
                throw new Exception('Formulir anda sudah terdaftar.');
            }

            $token->is_used = true;
            $token->save();

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

            Mail::to($request->email)->queue(new BandungSubmissionMail($newSubmission));

            DB::commit();

            $response = [
                'message' => 'Success.',
                'code' => 200,
                'result' => $responseData
            ];

            return response()->json($response);
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
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
                'result' => [
                    'token' => $randomString
                ]
            ];

            return response()->json($response);
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function getTotal()
    {
        try {
            $total = BandungSubmission::count();
            $response = [
                'code' => 200,
                'message' => 'Success.',
                'result' => [
                    'total' => $total
                ]
            ];

            return response()->json($response);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function eventCheckin(EventCheckinRequest $request)
    {
        return auth()->user()->role;
        DB::beginTransaction();

        try {
            $submission = BandungSubmission::where('unique_code', $request->unique_code)->where('has_checked_in', false)->first();

            if ($submission == null) {
                throw new Exception('Unique Code tidak ditemukan dalam sistem.');
            }

            $submission->has_checked_in = true;
            $submission->save();

            DB::commit();

            $submissionArray = $submission->fresh()->makeVisible('nik')->makeVisible('phone')->toArray();
            $submissionArray['nik'] = Crypt::decryptString($submissionArray['nik']);
            $submissionArray['phone'] = Crypt::decryptString($submissionArray['phone']);

            $response = [
                'code' => 200,
                'message' => 'Sukses melakukan check-in.',
                'result' => $submissionArray
            ];

            return response()->json($response);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
