<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreRefrigeratorRequest;
use App\Http\Requests\UpdateRefrigeratorRequest;
use App\Models\Refrigerator;
use App\Services\RefrigeratorService;
use Illuminate\Http\Request;
use App\Services\TemperatureService;

class RefrigeratorController extends Controller
{
protected RefrigeratorService $refrigeratorService;
protected TemperatureService $temperatureService;

    public function __construct(
        RefrigeratorService $refrigeratorService,
        TemperatureService $temperatureService
    )
    {
        $this->refrigeratorService =
            $refrigeratorService;
        $this->temperatureService =
            $temperatureService;
    }
    public function index()
    {
        $refrigerators =
            $this->refrigeratorService
                ->getAll();

        return view('refrigerator.index',compact('refrigerators'));
    }
    public function create(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $bloodBanks = $user->bloodBanks;

        return view('refrigerator.create', compact('bloodBanks'));
    }
    public function store(StoreRefrigeratorRequest $request)
    {
        $this->refrigeratorService
            ->create(
                $request->validated()
            );

        return response()->json([
            'success' => true
        ]);
    }
    public function edit(Refrigerator $refrigerator, Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $bloodBanks = $user->bloodBanks;

        return view(
            'staff.refrigerator-edit',
            compact(
                'refrigerator',
                'bloodBanks'
            )
        );
    }
    public function update( UpdateRefrigeratorRequest $request, Refrigerator $refrigerator)
    {
        $this->refrigeratorService
            ->update(
                $refrigerator,
                $request->validated()
            );

        return response()->json([
            'success' => true
        ]);
    }
    public function destroy( Refrigerator $refrigerator )
    {
        $this->refrigeratorService
            ->delete($refrigerator);

        return redirect()
            ->route('refrigerators.index');
    }
     public function logsPage()
    {
        $refrigerators = Refrigerator::where('is_active', true)->get();
        return view('refrigerator.temperature-logs', compact('refrigerators'));
    }
        public function logsData(Refrigerator $refrigerator)
    {
        $logs = $refrigerator->temperatureLogs()
            ->latest('recorded_at')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'recorded_at' => $log->recorded_at->format('Y-m-d H:i:s'),
                    'temperature' => $log->temperature,
                    'status'      => $this->temperatureService->getStatus($log->temperature),
                ];
            });

        return response()->json($logs);
    }
        public function dailyAnalysis(Request $request)
    {
        $refrigerators = Refrigerator::where('is_active', true)->get();

        $selectedRefrigerator = null;
        $analysis = null;
        $logs = collect();

        if ($request->filled('refrigerator_id')) {
            $selectedRefrigerator = Refrigerator::find($request->input('refrigerator_id'));

            if ($selectedRefrigerator) {
                $analysis = $this->temperatureService->dailyAnalysis(
                    $selectedRefrigerator->id,
                    $request->input('date')
                );

                $logs = $selectedRefrigerator->temperatureLogs()
                    ->whereDate('recorded_at', $analysis['date'])
                    ->latest('recorded_at')
                    ->get()
                    ->map(function ($log) {
                        return [
                            'recorded_at' => $log->recorded_at,
                            'temperature' => $log->temperature,
                            'status' => $this->temperatureService->getStatus($log->temperature),
                        ];
                    });
            }
        }

        return view('refrigerator.daily-analysis', compact(
            'refrigerators',
            'selectedRefrigerator',
            'analysis',
            'logs'
        ));
    }
}
