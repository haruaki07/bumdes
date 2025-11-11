<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateLabaRugiRequest;
use App\Services\LabaRugiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LabaRugiController extends Controller
{
    protected LabaRugiService $labaRugiService;

    public function __construct(LabaRugiService $labaRugiService)
    {
        $this->labaRugiService = $labaRugiService;
    }

    /**
     * Display laba rugi report page
     */
    public function index()
    {
        Gate::authorize('viewLabaRugi');

        // Default to current month
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        return view('laba-rugi.index', compact('startDate', 'endDate'));
    }

    /**
     * Generate report data via AJAX
     */
    public function generate(GenerateLabaRugiRequest $request)
    {
        Gate::authorize('viewLabaRugi');

        $validated = $request->validated();

        try {
            $reportData = $this->labaRugiService->generateReport(
                $validated['start_date'],
                $validated['end_date']
            );

            $chartData = $this->labaRugiService->getChartData(
                $validated['start_date'],
                $validated['end_date']
            );

            return response()->json([
                'success' => true,
                'data' => $reportData,
                'charts' => $chartData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat laporan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        Gate::authorize('viewLabaRugi');

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $reportData = $this->labaRugiService->generateReport(
                $request->start_date,
                $request->end_date
            );

            $pdf = Pdf::loadView('laba-rugi.pdf', [
                'data' => $reportData,
                'generatedAt' => now()->locale('id')->translatedFormat('d F Y H:i:s'),
            ]);

            $filename = 'Laporan_Laba_Rugi_'.
                Carbon::parse($request->start_date)->format('Ymd').'_'.
                Carbon::parse($request->end_date)->format('Ymd').'.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengekspor PDF: '.$e->getMessage());
        }
    }
}
