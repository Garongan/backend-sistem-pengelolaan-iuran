<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Utils\CommonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ExpenseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::paginate(8);
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
            'date' => request('date'),
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

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id)
    {
        $validated = Validator::make(request()->all(), [
            'description' => 'string',
            'amount' => 'numeric',
            'date' => 'date',
        ]);

        if ($validated->fails()) {
            return CommonResponse::commonResponse(
                Response::HTTP_BAD_REQUEST,
                'Error',
                ['error' => $validated->errors()]
            );
        }

        $expense = Expense::find($id);
        if ($expense == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'Expense not found']
            );
        }

        $updatedExpense = [
            'description' => request('description'),
            'amount' => request('amount'),
            'date' => request('date'),
        ];

        $expense->update($updatedExpense);
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $expense]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::find($id);
        if ($expense == null) {
            return CommonResponse::commonResponse(
                Response::HTTP_NOT_FOUND,
                'Error',
                ['error' => 'Expense not found']
            );
        }

        $expense->delete();
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => null]
        );
    }
}
