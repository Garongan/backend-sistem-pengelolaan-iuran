<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Utils\CommonResponse;
use DateTime;
use Illuminate\Http\Response;

class ReportController
{
    public function monthlySummary()
    {
        $month = request()->query('month', now()->month);
        $year = request()->query('year', now()->year);

        $income = Payment::whereYear('period', $year)
            ->whereMonth('period', $month)
            ->sum('amount');

        $spending = Expense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        $response = [
            'income' => $income,
            'spending' => $spending,
            'balance' => $spending - $income
        ];
        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $response]
        );
    }

    public function yearlySummary()
    {
        $year = request()->query('year', now()->year - 1);

        $data = [];

        for ($month = 1; $month < 12; $month++) {
            $income = Payment::whereYear('period', $year)
                ->whereMonth('period', $month)
                ->sum('amount');

            $spending = Expense::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');

            $data[] = [
                'month' => $month,
                'income' => $income,
                'spending' => $spending,
                'balance' => $income - $spending
            ];
        }

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $data]
        );
    }
}
