<?php

namespace Modules\EBilling\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\EBilling\Enums\CustomerStatus;
use Modules\EBilling\Enums\InvoiceStatus;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        return view('e-billing::dashboard');
    }

    public function metrics(Request $request)
    {
        $type = strtolower((string) $request->get('type', 'monthly'));
        if (! in_array($type, ['monthly', 'annual'], true)) {
            $type = 'monthly';
        }

        $now = now();
        if ($type === 'monthly') {
            $period = (string) $request->get('period', $now->format('Y-m'));
            try {
                $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
            } catch (\Throwable) {
                $start = $now->copy()->startOfMonth();
                $period = $start->format('Y-m');
            }
            $end = $start->copy()->endOfMonth();
            $prevStart = $start->copy()->subMonth()->startOfMonth();
            $prevEnd = $start->copy()->subMonth()->endOfMonth();
        } else {
            $period = (string) $request->get('period', $now->format('Y'));
            try {
                $start = Carbon::createFromFormat('Y', $period)->startOfYear();
            } catch (\Throwable) {
                $start = $now->copy()->startOfYear();
                $period = $start->format('Y');
            }
            $end = $start->copy()->endOfYear();
            $prevStart = $start->copy()->subYear()->startOfYear();
            $prevEnd = $start->copy()->subYear()->endOfYear();
        }

        // === Combined KPI Query ===
        $kpi = Invoice::query()
            ->selectRaw('
            COALESCE(SUM(CASE WHEN status = ? AND paid_at BETWEEN ? AND ? THEN amount END),0) as income,
            COUNT(CASE WHEN status = ? AND paid_at BETWEEN ? AND ? THEN 1 END) as paid_count,
            COUNT(CASE WHEN status = ? AND created_at BETWEEN ? AND ? THEN 1 END) as unpaid_count,
            COALESCE(SUM(CASE WHEN status = ? AND created_at BETWEEN ? AND ? THEN amount END),0) as unpaid_total
        ', [
                InvoiceStatus::PAID,
                $start,
                $end,
                InvoiceStatus::PAID,
                $start,
                $end,
                InvoiceStatus::UNPAID,
                $start,
                $end,
                InvoiceStatus::UNPAID,
                $start,
                $end,
            ])
            ->first();

        $prevIncome = (int) Invoice::query()
            ->where('status', InvoiceStatus::PAID)
            ->whereBetween('paid_at', [$prevStart, $prevEnd])
            ->sum('amount');

        $incomeGrowthPct = $prevIncome > 0
            ? round((($kpi->income - $prevIncome) / $prevIncome) * 100, 2)
            : null;

        $activeCustomers = (int) Customer::query()
            ->where('status', CustomerStatus::ACTIVE)
            ->count();

        // === Chart Query (daily/monthly in one go) ===
        $chartQuery = Invoice::query()
            ->selectRaw(
                $type === 'monthly'
                    ? 'DATE(paid_at) as grp, COALESCE(SUM(amount),0) as total'
                    : "DATE_FORMAT(paid_at,'%Y-%m') as grp, COALESCE(SUM(amount),0) as total"
            )
            ->where('status', InvoiceStatus::PAID)
            ->whereBetween('paid_at', [$start, $end])
            ->groupBy('grp')
            ->orderBy('grp')
            ->pluck('total', 'grp');

        $labels = [];
        $seriesIncome = [];

        if ($type === 'monthly') {
            for ($d = 1; $d <= $start->daysInMonth; $d++) {
                $date = $start->copy()->setDay($d)->toDateString();
                $labels[] = Carbon::parse($date)->format('d M');
                $seriesIncome[] = (int) ($chartQuery[$date] ?? 0);
            }
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $date = $start->copy()->setMonth($m)->format('Y-m');
                $labels[] = Carbon::parse($date.'-01')->format('M Y');
                $seriesIncome[] = (int) ($chartQuery[$date] ?? 0);
            }
        }

        $topPackages = Invoice::query()
            ->select('package_id', DB::raw('COALESCE(SUM(amount),0) as total'))
            ->where('status', InvoiceStatus::PAID)
            ->whereBetween('paid_at', [$start, $end])
            ->with(['package:id,name'])
            ->groupBy('package_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->package->name ?? 'Tanpa Paket',
                'total' => (int) $row->total,
            ])->values();

        return response()->json([
            'type' => $type,
            'period' => $period,
            'range' => [
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
            ],
            'kpis' => [
                'income' => (int) $kpi->income,
                'incomePrev' => $prevIncome,
                'incomeGrowthPct' => $incomeGrowthPct,
                'paidCount' => (int) $kpi->paid_count,
                'unpaidCount' => (int) $kpi->unpaid_count,
                'unpaidTotal' => (int) $kpi->unpaid_total,
                'activeCustomers' => $activeCustomers,
            ],
            'charts' => [
                'revenue' => [
                    'labels' => $labels,
                    'data' => $seriesIncome,
                ],
                'packages' => $topPackages,
            ],
        ]);
    }
}
