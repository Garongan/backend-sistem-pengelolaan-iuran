<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Utils\CommonResponse;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ExpenseController
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

        $expenses = [];
        if ($year == null && $month == null) {
            $expenses = Expense::paginate($size);
        } else {
            $expenses = Expense::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->paginate($size);
        }

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $expenses]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $validated = Validator::make(request()->all(), [
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $expense = [
            'description' => request('description'),
            'amount' => request('amount'),
            'date' => Carbon::parse(request('date')),
        ];

        $response = Expense::create($expense);
        return CommonResponse::commonResponse(
            Response::HTTP_CREATED,
            'Success',
            ['data' => $response]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::find($id);
        if ($expense == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'Expense not found']
            );
        }

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $expense]
        );
    }
}
