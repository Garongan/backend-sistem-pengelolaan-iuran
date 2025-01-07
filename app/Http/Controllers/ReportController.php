<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Utils\CommonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReportController
{
    private function getMonthlySummary($month, $year)
    {
        $incomes = Payment::with('resident')
            ->whereYear('period', $year)
            ->whereMonth('period', $month)
            ->get();

        $income = Payment::whereYear('period', $year)
            ->whereMonth('period', $month)
            ->sum('amount');

        $spendings = Expense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $spending = Expense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        return [
            'incomes' => $incomes,
            'spendings' => $spendings,
            'income' => strval($income),
            'spending' => strval($spending),
            'balance' => strval($income - $spending)
        ];
    }

    public function monthlySummary()
    {
        $month = request()->query('month', now()->month);
        $year = request()->query('year', now()->year);

        $data = $this->getMonthlySummary($month, $year);

        $response = [
            'incomes' => $data['incomes'],
            'spendings' => $data['spendings'],
            'income' => $data['income'],
            'spending' => $data['spending'],
            'balance' => $data['balance']
        ];

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $response]
        );
    }

    public function downloadMonthlySummary()
    {
        $month = request()->query('month', now()->month);
        $year = request()->query('year', now()->year);

        $data = $this->getMonthlySummary($month, $year);

        $pdf = Pdf::loadView('monthlySummaryPdf', [
            'month' => $month,
            'year' => $year,
            'incomes' => $data['incomes'],
            'spendings' => $data['spendings'],
            'income' => $data['income'],
            'spending' => $data['spending'],
            'balance' => $data['balance']
        ]);

        return $pdf->download('laporan bulanan-' . date('F', mktime(0, 0, 0, $month, 1)) . '-' . $year . '.pdf');
    }

    private function getYearlySummary($year)
    {
        $data = [];

        for ($month = 1; $month < 12; $month++) {
            $income = Payment::whereYear('period', $year)
                ->whereMonth('period', $month)
                ->sum('amount');

            $spending = Expense::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');

            $data[] = [
                'month' => strval($month),
                'income' => strval($income),
                'spending' => strval($spending),
                'balance' => strval($income - $spending)
            ];
        }

        return $data;
    }

    public function yearlySummary()
    {
        $year = request()->query('year', now()->year - 1);

        $data = $this->getYearlySummary($year);

        return CommonResponse::commonResponse(
            Response::HTTP_OK,
            'Success',
            ['data' => $data]
        );
    }
}
