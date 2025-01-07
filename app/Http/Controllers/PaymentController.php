<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Resident;
use App\Utils\CommonResponse;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PaymentController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validated = Validator::make(request()->query(), [
            'year' => 'numeric',
            'month' => 'numeric|min:1|max:12'
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $year = request()->query('year');
        $month = request()->query('month');
        $size = request()->query('size', 8);

        $payments = [];
        if ($year == null && $month == null) {
            $payments = Payment::with('resident')->paginate($size);
        } else {
            $payments = Payment::with('resident')
                ->whereYear('period', $year)
                ->whereMonth('period', $month)
                ->paginate($size);
        }

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $payments]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $validated = Validator::make(request()->all(), [
            'resident_id' => 'required|string',
            'payment_type' => 'required|in:sanitation,security',
            'amount' => 'required|numeric',
            'period' => 'required|date',
            'is_paid_off' => 'required|boolean'
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $resident = Resident::find(request('resident_id'));

        if ($resident == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => 'Resident not found']
            );
        }


        $payment = [
            'resident_id' => $resident->id,
            'payment_type' => request('payment_type'),
            'amount' => request('amount'),
            'period' => Carbon::parse(request('period'))->setTimezone(env('APP_TIMEZONE'))->toIso8601String(),
            'is_paid_off' => request('is_paid_off')
        ];

        return CommonResponse::commonResponse(
            Response::HTTP_CREATED,
            'Success',
            ['data' => Payment::create($payment)]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with('resident')->find($id);
        if ($payment == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'Payment not found']
            );
        }
    }
}
