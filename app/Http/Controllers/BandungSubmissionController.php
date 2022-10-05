<?php

namespace App\Http\Controllers;

use App\Models\BandungSubmission;
use App\Models\BandungSubmissionToken;
use App\Http\Requests\Bandung\BandungSubmissionRequest;
use App\Http\Requests\Bandung\EventCheckinRequest;
use App\Mail\BandungSubmissionMail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BandungSubmissionController extends Controller
{
    public function create(BandungSubmissionRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $token = BandungSubmissionToken::where('token', $data['token'])->first();

            if ($token == null) {
                throw new Exception('Formulir anda tidak dapat diproses.');
            }

            if ($token->is_used) {
                throw new Exception('Formulir anda sudah terdaftar.');
            }

            $token->is_used = true;
            $token->save();

            $total = BandungSubmission::count();
            $todaysTotal = BandungSubmission::whereDate('created_at', '>=', Carbon::today()->startOfDay())->whereDate('created_at', '<=', Carbon::today()->endOfDay())->count();

            if ($total > 5000 || $todaysTotal > 100) {
                throw new Exception('Mohon maaf kuota telah habis.');
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

            $newBanner = Image::make(public_path('images/pristime/pristime-email-banner.jpg'));
            $newBanner->text($newSubmission->unique_code, 300, 190, function ($font) {
                $font->file(public_path('fonts/Quicksand-Bold.ttf'));
                $font->size(32);
                $font->color('#2eb5a9');
                $font->align('center');
                $font->valign('middle');
            });
            $newBanner->text($newSubmission->name, 300, 340, function ($font) {
                $font->file(public_path('fonts/Quicksand-Bold.ttf'));
                $font->size(64);
                $font->color('#2eb5a9');
                $font->align('center');
                $font->valign('middle');
            });
            $newBanner->save(public_path('images/pristime/banners/' . $uniqueCode . '.jpg'));

            Mail::to($request->email)->queue(new BandungSubmissionMail($newSubmission));

            DB::commit();

            $responseData = $newSubmission->toArray();

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
            $todaysTotal = BandungSubmission::whereDate('created_at', '>=', Carbon::today()->startOfDay())->whereDate('created_at', '<=', Carbon::today()->endOfDay())->count();

            $response = [
                'code' => 200,
                'message' => 'Success.',
                'result' => [
                    'total' => $total,
                    'todays_total' => $todaysTotal
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

    public function downloadResultImage(Request $request)
    {
        try {
            if ($request->has('result') == false) {
                throw new Exception('Please provide result.');
            }

            $fileName = 'RESULT-SI-' . $request->result . '.png';
            $filePath = public_path('images/pristime/results/' . $fileName);
            $base64 = base64_encode(file_get_contents($filePath));
            $response = [
                'code' => 200,
                'message' => 'Success.',
                'result' => [
                    'image' => $base64,
                    'filename' => $fileName
                ]
            ];

            return response()->json($response);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function decryptTable()
    {
        DB::beginTransaction();

        try {
            $submissions = BandungSubmission::where('id', '>=', 139)->get();
            $mappedSubmissions = $submissions->map(function ($row) {
                $data = [
                    'id' => $row->id,
                    'data' => [
                        'nik' => Crypt::decryptString($row->nik),
                        'phone' => Crypt::decryptString($row->phone)
                    ]
                ];

                return $data;
            })->values()->all();

            foreach ($mappedSubmissions as $submission) {
                DB::table('bandung_submissions')->where('id', $submission['id'])->update($submission['data']);
            }

            DB::commit();

            $response = [
                'message' => 'Successfully updated ' . count($mappedSubmissions) . ' data.',
            ];

            return response()->json($response);
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function sendBulkInvitationMail()
    {
        try {
            $startDate = Carbon::today()->addDays(-7)->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $submissions = BandungSubmission::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->get();

            foreach ($submissions as $submission) {
                $this->generateInvitationImage($submission);

                Mail::to($submission->email)->queue(new BandungSubmissionMail($submission));
            }

            return response()->json('Emails sent successfully.');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function sendInvitationMail(Request $request)
    {
        try {
            $submission = BandungSubmission::where('email', $request->email)->first();

            $this->generateInvitationImage($submission);

            Mail::to($submission->email)->queue(new BandungSubmissionMail($submission));

            return response()->json('Email sent successfully.');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function generateInvitationImage($submission)
    {
        if (File::exists(public_path('images/pristime/banners/' . $submission->unique_code . '.jpg')) == false) {
            $newBanner = Image::make(public_path('images/pristime/pristime-email-banner.jpg'));
            $newBanner->text($submission->unique_code, 300, 190, function ($font) {
                $font->file(public_path('fonts/Quicksand-Bold.ttf'));
                $font->size(32);
                $font->color('#2eb5a9');
                $font->align('center');
                $font->valign('middle');
            });
            $newBanner->text($submission->name, 300, 340, function ($font) {
                $font->file(public_path('fonts/Quicksand-Bold.ttf'));
                $font->size(64);
                $font->color('#2eb5a9');
                $font->align('center');
                $font->valign('middle');
            });
            $newBanner->save(public_path('images/pristime/banners/' . $submission->unique_code . '.jpg'));
        }
    }
}
