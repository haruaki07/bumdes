<?php

namespace App\Services;

use App\Models\FundingRequest;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Carbon\Carbon;

class LabaRugiService
{
    /**
     * Generate Laba Rugi report data
     *
     * @param  string  $startDate
     * @param  string  $endDate
     */
    public function generateReport($startDate, $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get verified transactions only
        $incomes = $this->getIncomeData($start, $end);
        $expenses = $this->getExpenseData($start, $end);

        // Calculate interest income from funding requests
        $interestIncome = $this->calculateInterestIncome($start, $end);

        // Totals
        $totalIncome = $incomes['total'] + $interestIncome['total'];
        $totalExpense = $expenses['total'];
        $netProfit = $totalIncome - $totalExpense;

        return [
            'period' => [
                'start' => $start->locale('id')->translatedFormat('Y-m-d'),
                'end' => $end->locale('id')->translatedFormat('Y-m-d'),
                'start_formatted' => $start->locale('id')->translatedFormat('d F Y'),
                'end_formatted' => $end->locale('id')->translatedFormat('d F Y'),
            ],
            'income' => [
                'categories' => $incomes['categories'],
                'interest' => $interestIncome,
                'total' => $totalIncome,
                'total_formatted' => $this->formatCurrency($totalIncome),
            ],
            'expense' => [
                'categories' => $expenses['categories'],
                'total' => $totalExpense,
                'total_formatted' => $this->formatCurrency($totalExpense),
            ],
            'net_profit' => [
                'amount' => $netProfit,
                'amount_formatted' => $this->formatCurrency($netProfit),
                'percentage' => $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0,
                'is_profit' => $netProfit >= 0,
            ],
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_profit' => $netProfit,
            ],
        ];
    }

    /**
     * Get income data grouped by category
     */
    protected function getIncomeData(Carbon $start, Carbon $end): array
    {
        $categories = TransactionCategory::income()
            ->active()
            ->parents()
            ->with(['children' => function ($query) {
                $query->active()->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        $result = [];
        $total = 0;

        foreach ($categories as $category) {
            $categoryTotal = Transaction::verified()
                ->income()
                ->where('transaction_category_id', $category->id)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $subcategories = [];
            foreach ($category->children as $child) {
                $subTotal = Transaction::verified()
                    ->income()
                    ->where('transaction_category_id', $child->id)
                    ->whereBetween('transaction_date', [$start, $end])
                    ->sum('amount');

                if ($subTotal > 0) {
                    $subcategories[] = [
                        'id' => $child->id,
                        'name' => $child->name,
                        'code' => $child->code,
                        'amount' => (float) $subTotal,
                        'amount_formatted' => $this->formatCurrency($subTotal),
                    ];
                    $categoryTotal += $subTotal;
                }
            }

            if ($categoryTotal > 0) {
                $result[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'amount' => (float) $categoryTotal,
                    'amount_formatted' => $this->formatCurrency($categoryTotal),
                    'subcategories' => $subcategories,
                ];
                $total += $categoryTotal;
            }
        }

        return [
            'categories' => $result,
            'total' => (float) $total,
        ];
    }

    /**
     * Get expense data grouped by category
     */
    protected function getExpenseData(Carbon $start, Carbon $end): array
    {
        $categories = TransactionCategory::expense()
            ->active()
            ->parents()
            ->with(['children' => function ($query) {
                $query->active()->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        $result = [];
        $total = 0;

        foreach ($categories as $category) {
            $categoryTotal = Transaction::verified()
                ->expense()
                ->where('transaction_category_id', $category->id)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $subcategories = [];
            foreach ($category->children as $child) {
                $subTotal = Transaction::verified()
                    ->expense()
                    ->where('transaction_category_id', $child->id)
                    ->whereBetween('transaction_date', [$start, $end])
                    ->sum('amount');

                if ($subTotal > 0) {
                    $subcategories[] = [
                        'id' => $child->id,
                        'name' => $child->name,
                        'code' => $child->code,
                        'amount' => (float) $subTotal,
                        'amount_formatted' => $this->formatCurrency($subTotal),
                    ];
                    $categoryTotal += $subTotal;
                }
            }

            if ($categoryTotal > 0) {
                $result[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'amount' => (float) $categoryTotal,
                    'amount_formatted' => $this->formatCurrency($categoryTotal),
                    'subcategories' => $subcategories,
                ];
                $total += $categoryTotal;
            }
        }

        return [
            'categories' => $result,
            'total' => (float) $total,
        ];
    }

    /**
     * Calculate interest income from funding requests
     */
    protected function calculateInterestIncome(Carbon $start, Carbon $end): array
    {
        // Get all disbursed funding requests
        $fundingRequests = FundingRequest::whereIn('status', ['disbursed', 'completed'])
            ->whereNotNull('disbursement_date')
            ->whereNotNull('interest_rate')
            ->where('interest_rate', '>', 0)
            ->get();

        $totalInterest = 0;
        $details = [];

        foreach ($fundingRequests as $request) {
            // Calculate interest for the period that overlaps with the report period
            $disbursementDate = Carbon::parse($request->disbursement_date);
            $dueDate = $request->due_date ? Carbon::parse($request->due_date) : null;

            // Determine the calculation period
            $calcStart = $disbursementDate->greaterThan($start) ? $disbursementDate : $start;
            $calcEnd = $dueDate && $dueDate->lessThan($end) ? $dueDate : $end;

            // Only calculate if there's overlap
            if ($calcStart->lessThanOrEqualTo($calcEnd) && $disbursementDate->lessThanOrEqualTo($end)) {
                $daysInPeriod = $calcStart->diffInDays($calcEnd) + 1;
                $amount = $request->disbursed_amount ?? $request->amount;

                // Calculate interest: (Principal × Rate × Days) / 365
                $interest = ($amount * ($request->interest_rate / 100) * $daysInPeriod) / 365;

                if ($interest > 0) {
                    $details[] = [
                        'funding_request_id' => $request->id,
                        'business_name' => $request->business->name ?? '-',
                        'principal' => (float) $amount,
                        'interest_rate' => (float) $request->interest_rate,
                        'days' => $daysInPeriod,
                        'interest' => (float) $interest,
                        'interest_formatted' => $this->formatCurrency($interest),
                    ];
                    $totalInterest += $interest;
                }
            }
        }

        return [
            'total' => (float) $totalInterest,
            'total_formatted' => $this->formatCurrency($totalInterest),
            'details' => $details,
        ];
    }

    /**
     * Get chart data for the report
     */
    public function getChartData($startDate, $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Income vs Expense comparison
        $incomeExpenseData = $this->getIncomeExpenseComparison($start, $end);

        // Category breakdown for pie charts
        $incomeCategoryData = $this->getIncomeCategoryBreakdown($start, $end);
        $expenseCategoryData = $this->getExpenseCategoryBreakdown($start, $end);

        // Trend data (daily/weekly based on period length)
        $trendData = $this->getTrendData($start, $end);

        return [
            'income_expense' => $incomeExpenseData,
            'income_categories' => $incomeCategoryData,
            'expense_categories' => $expenseCategoryData,
            'trend' => $trendData,
        ];
    }

    /**
     * Get income vs expense comparison
     */
    protected function getIncomeExpenseComparison(Carbon $start, Carbon $end): array
    {
        $income = Transaction::verified()
            ->income()
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('amount');

        $expense = Transaction::verified()
            ->expense()
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('amount');

        $interestIncome = $this->calculateInterestIncome($start, $end);
        $totalIncome = (float) $income + $interestIncome['total'];

        return [
            'labels' => ['Pendapatan', 'Beban'],
            'data' => [$totalIncome, (float) $expense],
        ];
    }

    /**
     * Get income category breakdown
     */
    protected function getIncomeCategoryBreakdown(Carbon $start, Carbon $end): array
    {
        $categories = TransactionCategory::income()
            ->active()
            ->parents()
            ->orderBy('order')
            ->get();

        $labels = [];
        $data = [];

        foreach ($categories as $category) {
            $total = Transaction::verified()
                ->income()
                ->where('transaction_category_id', $category->id)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            // Include subcategories
            $childIds = $category->children()->pluck('id');
            if ($childIds->isNotEmpty()) {
                $subTotal = Transaction::verified()
                    ->income()
                    ->whereIn('transaction_category_id', $childIds)
                    ->whereBetween('transaction_date', [$start, $end])
                    ->sum('amount');
                $total += $subTotal;
            }

            if ($total > 0) {
                $labels[] = $category->name;
                $data[] = (float) $total;
            }
        }

        // Add interest income
        $interestIncome = $this->calculateInterestIncome($start, $end);
        if ($interestIncome['total'] > 0) {
            $labels[] = 'Pendapatan Bunga';
            $data[] = $interestIncome['total'];
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get expense category breakdown
     */
    protected function getExpenseCategoryBreakdown(Carbon $start, Carbon $end): array
    {
        $categories = TransactionCategory::expense()
            ->active()
            ->parents()
            ->orderBy('order')
            ->get();

        $labels = [];
        $data = [];

        foreach ($categories as $category) {
            $total = Transaction::verified()
                ->expense()
                ->where('transaction_category_id', $category->id)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            // Include subcategories
            $childIds = $category->children()->pluck('id');
            if ($childIds->isNotEmpty()) {
                $subTotal = Transaction::verified()
                    ->expense()
                    ->whereIn('transaction_category_id', $childIds)
                    ->whereBetween('transaction_date', [$start, $end])
                    ->sum('amount');
                $total += $subTotal;
            }

            if ($total > 0) {
                $labels[] = $category->name;
                $data[] = (float) $total;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get trend data over time
     */
    protected function getTrendData(Carbon $start, Carbon $end): array
    {
        $daysDiff = $start->diffInDays($end);

        // If period is more than 60 days, use weekly grouping, else daily
        $groupBy = $daysDiff > 60 ? 'week' : 'day';

        if ($groupBy === 'day') {
            return $this->getDailyTrend($start, $end);
        } else {
            return $this->getWeeklyTrend($start, $end);
        }
    }

    /**
     * Get daily trend data
     */
    protected function getDailyTrend(Carbon $start, Carbon $end): array
    {
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        $current = $start->copy();
        while ($current->lessThanOrEqualTo($end)) {
            $labels[] = $current->locale('id')->translatedFormat('d M');

            $income = Transaction::verified()
                ->income()
                ->whereDate('transaction_date', $current)
                ->sum('amount');

            $expense = Transaction::verified()
                ->expense()
                ->whereDate('transaction_date', $current)
                ->sum('amount');

            $incomeData[] = (float) $income;
            $expenseData[] = (float) $expense;

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $incomeData,
                ],
                [
                    'label' => 'Beban',
                    'data' => $expenseData,
                ],
            ],
        ];
    }

    /**
     * Get weekly trend data
     */
    protected function getWeeklyTrend(Carbon $start, Carbon $end): array
    {
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        $current = $start->copy()->startOfWeek();
        while ($current->lessThanOrEqualTo($end)) {
            $weekEnd = $current->copy()->endOfWeek();
            if ($weekEnd->greaterThan($end)) {
                $weekEnd = $end->copy();
            }

            $labels[] = $current->locale('id')->translatedFormat('d M').' - '.$weekEnd->locale('id')->translatedFormat('d M');

            $income = Transaction::verified()
                ->income()
                ->whereBetween('transaction_date', [$current, $weekEnd])
                ->sum('amount');

            $expense = Transaction::verified()
                ->expense()
                ->whereBetween('transaction_date', [$current, $weekEnd])
                ->sum('amount');

            $incomeData[] = (float) $income;
            $expenseData[] = (float) $expense;

            $current->addWeek();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $incomeData,
                ],
                [
                    'label' => 'Beban',
                    'data' => $expenseData,
                ],
            ],
        ];
    }

    /**
     * Format currency
     */
    protected function formatCurrency($amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}
