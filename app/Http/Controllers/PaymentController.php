<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Utils\CommonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PaymentController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with('resident')->paginate(8);
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

        $payment = [
            'resident_id' => request('resident_id'),
            'payment_type' => request('payment_type'),
            'amount' => request('amount'),
            'period' => request('period'),
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

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id)
    {
        $payment = Payment::find($id);
        if ($payment == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'Payment not found']
            );
        }

        $validated = Validator::make(request()->all(), [
            'payment_type' => 'in:sanitation,security',
            'amount' => 'numeric',
            'period' => 'date',
            'is_paid_off' => 'boolean'
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $updatedPayment = [
            'resident_id' => request('resident_id'),
            'payment_type' => request('payment_type'),
            'amount' => request('amount'),
            'period' => request('period'),
            'is_paid_off' => request('is_paid_off')
        ];

        $payment->update($updatedPayment);
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $payment]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::find($id);
        if ($payment == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'Payment not found']
            );
        }

        $payment->delete();
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => null]
        );
    }
}
