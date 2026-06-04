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
        $bloodBanks = $request->user()->bloodBanks;

        return view('refrigerator.edit',compact(
            'refrigerator',
            'bloodBanks'
        ));
    }
    public function update( UpdateRefrigeratorRequest $request, Refrigerator $refrigerator)
    {
        $data = $request->validated();

            $changingBloodBank = isset($data['blood_bank_id'])
                && $data['blood_bank_id'] != $refrigerator->blood_bank_id;

            $deactivating = array_key_exists('is_active', $data)
                && $data['is_active'] == 0;

            if ($changingBloodBank || $deactivating) {
                $hasBlockedBags = $refrigerator->bloodBags()
                    ->whereIn('status', ['available', 'reserved'])
                    ->exists();

                if ($hasBlockedBags) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot change blood bank or make inactive
                        this refrigerator since it has available or reserved blood bags.'
                    ], 422);
                }
            }

            $this->refrigeratorService->update($refrigerator, $data);

            return response()->json(['success' => true]);
        }

    public function destroy(Refrigerator $refrigerator)
    {
        $hasBlockedBags = $refrigerator->bloodBags()
            ->whereIn('status', ['available', 'reserved'])
            ->exists();

        if ($hasBlockedBags) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete refrigerator while it has available or reserved blood bags.'
            ], 422);
        }
        $this->refrigeratorService->delete($refrigerator);
        return response()->json([
            'success' => true,
            'message' => 'Refrigerator deleted successfully.'
        ]);
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
                $analysis = $this->temperatureService->dailyTempAnalysis(
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
