<?php

namespace App\Http\Controllers;

use App\Enums\BusinessStatus;
use App\Enums\FundingRequestStatus;
use App\Models\Business;
use App\Models\FundingRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Get dashboard metrics data.
     */
    public function metrics(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $date = $request->get('date', now()->format('Y-m'));

        // Parse date based on period
        $dateRange = $this->getDateRange($period, $date);

        // Get metrics
        $metrics = [
            'active_businesses' => $this->getActiveBusinessesMetrics($dateRange),
            'ongoing_funding' => $this->getOngoingFundingMetrics($dateRange),
            'funding_summary' => $this->getFundingSummaryMetrics($dateRange),
            'charts' => [
                'business_growth' => $this->getBusinessGrowthChart($dateRange),
                'funding_distribution' => $this->getFundingDistributionChart($dateRange),
                'funding_amount_trend' => $this->getFundingAmountTrendChart($dateRange),
                'repayment_status' => $this->getRepaymentStatusChart($dateRange),
                'business_by_type' => $this->getBusinessByTypeChart(),
            ],
            'recent_data' => [
                'businesses' => $this->getRecentBusinesses(),
                'funding' => $this->getRecentFunding(),
            ],
            'period_info' => [
                'period' => $period,
                'date' => $date,
                'start' => $dateRange['start']->format('Y-m-d'),
                'end' => $dateRange['end']->format('Y-m-d'),
                'label' => $this->getPeriodLabel($period, $date),
            ],
        ];

        return response()->json($metrics);
    }

    /**
     * Get date range based on period type.
     */
    private function getDateRange(string $period, string $date): array
    {
        try {
            switch ($period) {
                case 'daily':
                    $start = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
                    $end = $start->copy()->endOfDay();
                    break;
                case 'weekly':
                    $start = Carbon::createFromFormat('Y-m-d', $date)->startOfWeek();
                    $end = $start->copy()->endOfWeek();
                    break;
                case 'yearly':
                    $start = Carbon::createFromFormat('Y', $date)->startOfYear();
                    $end = $start->copy()->endOfYear();
                    break;
                case 'monthly':
                default:
                    $start = Carbon::createFromFormat('Y-m', $date)->startOfMonth();
                    $end = $start->copy()->endOfMonth();
                    break;
            }
        } catch (\Exception $e) {
            $start = now()->startOfMonth();
            $end = $start->copy()->endOfMonth();
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Get period label for display.
     */
    private function getPeriodLabel(string $period, string $date): string
    {
        $dateRange = $this->getDateRange($period, $date);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $fmt = function ($d) {
            return $d->translatedFormat('d M Y');
        };

        return $fmt($start).' - '.$fmt($end);
    }

    /**
     * Get active businesses metrics.
     */
    private function getActiveBusinessesMetrics(array $dateRange): array
    {
        $activeCount = Business::where('status', BusinessStatus::ACTIVE->value)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $totalActive = Business::where('status', BusinessStatus::ACTIVE->value)->count();

        $newThisPeriod = Business::where('status', BusinessStatus::ACTIVE->value)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        return [
            'total' => $totalActive,
            'new_this_period' => $newThisPeriod,
            'active_count' => $activeCount,
        ];
    }

    /**
     * Get ongoing funding metrics.
     */
    private function getOngoingFundingMetrics(array $dateRange): array
    {
        $ongoingStatuses = [
            FundingRequestStatus::SUBMITTED->value,
            FundingRequestStatus::APPROVED->value,
            FundingRequestStatus::MOU_SIGNED->value,
            FundingRequestStatus::READY_TO_DISBURSE->value,
            FundingRequestStatus::DISBURSED->value,
            FundingRequestStatus::REPAYING->value,
        ];

        $ongoingCount = FundingRequest::whereIn('status', $ongoingStatuses)->count();

        $totalAmount = FundingRequest::whereIn('status', $ongoingStatuses)
            ->sum('amount');

        $disbursedAmount = FundingRequest::whereIn('status', $ongoingStatuses)
            ->sum('disbursed_amount');

        $newRequestsThisPeriod = FundingRequest::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        return [
            'ongoing_count' => $ongoingCount,
            'total_amount' => $totalAmount,
            'disbursed_amount' => $disbursedAmount,
            'new_requests_this_period' => $newRequestsThisPeriod,
        ];
    }

    /**
     * Get funding summary metrics.
     */
    private function getFundingSummaryMetrics(array $dateRange): array
    {
        $totalDisbursed = FundingRequest::whereIn('status', [
            FundingRequestStatus::DISBURSED->value,
            FundingRequestStatus::REPAYING->value,
            FundingRequestStatus::COMPLETED->value,
        ])->sum('disbursed_amount');

        $totalRepaid = DB::table('funding_repayments')
            ->whereNotNull('verified_at')
            ->sum('amount');

        $outstanding = $totalDisbursed - $totalRepaid;

        $completedCount = FundingRequest::where('status', FundingRequestStatus::COMPLETED->value)->count();

        return [
            'total_disbursed' => $totalDisbursed,
            'total_repaid' => $totalRepaid,
            'outstanding' => $outstanding,
            'completed_count' => $completedCount,
            'repayment_rate' => $totalDisbursed > 0 ? ($totalRepaid / $totalDisbursed) * 100 : 0,
        ];
    }

    /**
     * Get business growth chart data.
     */
    private function getBusinessGrowthChart(array $dateRange): array
    {
        $businesses = Business::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $businesses->pluck('date')->toArray(),
            'data' => $businesses->pluck('count')->toArray(),
        ];
    }

    /**
     * Get funding distribution chart data.
     */
    private function getFundingDistributionChart(array $dateRange): array
    {
        $distribution = DB::table('funding_requests')
            ->selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereNull('deleted_at')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $distribution->map(function ($item) {
                try {
                    return FundingRequestStatus::from($item->status)->label();
                } catch (\Exception $e) {
                    return $item->status;
                }
            })->toArray(),
            'data' => $distribution->pluck('count')->toArray(),
        ];
    }

    /**
     * Get funding amount trend chart data.
     */
    private function getFundingAmountTrendChart(array $dateRange): array
    {
        $data = DB::table('funding_requests')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereNull('deleted_at')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * Get repayment status chart data.
     */
    private function getRepaymentStatusChart(array $dateRange): array
    {
        $ongoingWithRepayment = FundingRequest::whereIn('status', [
            FundingRequestStatus::DISBURSED->value,
            FundingRequestStatus::REPAYING->value,
        ])->get();

        $data = [
            'on_track' => 0,
            'behind' => 0,
            'completed' => 0,
        ];

        foreach ($ongoingWithRepayment as $funding) {
            if ($funding->disbursed_amount > 0) {
                $repaidAmount = $funding->getTotalRepaidAttribute();
                $progress = ($repaidAmount / $funding->disbursed_amount) * 100;

                // Simple logic: if more than 50% paid, consider on track
                if ($progress >= 50) {
                    $data['on_track']++;
                } else {
                    $data['behind']++;
                }
            }
        }

        $data['completed'] = FundingRequest::where('status', FundingRequestStatus::COMPLETED->value)->count();

        return [
            'labels' => ['Lancar', 'Terlambat', 'Selesai'],
            'data' => [$data['on_track'], $data['behind'], $data['completed']],
        ];
    }

    /**
     * Get business by type chart data.
     */
    private function getBusinessByTypeChart(): array
    {
        $data = DB::table('businesses')
            ->join('business_types', 'businesses.business_type_id', '=', 'business_types.id')
            ->selectRaw('business_types.name as type, COUNT(*) as count')
            ->where('businesses.status', BusinessStatus::ACTIVE->value)
            ->whereNull('businesses.deleted_at')
            ->groupBy('business_types.id', 'business_types.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('type')->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get recent businesses.
     */
    private function getRecentBusinesses(int $limit = 10): array
    {
        $businesses = Business::with(['businessType', 'owner'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $businesses->map(function ($business) {
            return [
                'id' => $business->id,
                'name' => $business->name,
                'type' => $business->businessType->name ?? '-',
                'status' => $business->status,
                'status_label' => BusinessStatus::from($business->status)->label(),
                'owner' => $business->owner->name ?? '-',
                'created_at' => $business->created_at->format('d M Y'),
                'url' => route('businesses.show', $business),
            ];
        })->toArray();
    }

    /**
     * Get recent funding requests.
     */
    private function getRecentFunding(int $limit = 10): array
    {
        $funding = FundingRequest::with(['business', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $funding->map(function ($request) {
            return [
                'id' => $request->id,
                'business_name' => $request->business->name ?? '-',
                'amount' => $request->amount,
                'amount_formatted' => 'Rp '.number_format($request->amount, 0, ',', '.'),
                'status' => $request->status->value,
                'status_label' => $request->status->label(),
                'user' => $request->user->name ?? '-',
                'created_at' => $request->created_at->format('d M Y'),
                'url' => route('funding-requests.show', $request),
            ];
        })->toArray();
    }
}
