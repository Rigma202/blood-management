<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Refrigerator;
use App\Services\BloodBagService;
use App\Models\BloodBag;
use App\Http\Requests\StoreBloodBagRequest;
use App\Http\Requests\UpdateBloodBagRequest;
use App\Services\BloodExpiryService;

class BloodBagController extends Controller
{
    protected BloodBagService $service;
    protected BloodExpiryService $expiryService;

    public function __construct(BloodBagService $service,BloodExpiryService $expiryService)
    {
        $this->service = $service;
        $this->expiryService = $expiryService;
    }

    public function index()
    {
        $bloodBags = $this->service->getAll();
        return view('bloodbag.index', compact('bloodBags'));
    }

    public function create()
    {
        $userId = Auth::id();
        $bloodBanks = $this->service->getUserBloodBanksWithRefrigerators($userId);
        return view('bloodbag.create', compact('bloodBanks'));
    }

    public function refrigeratorsByBank(Request $request)
    {
        $request->validate([
            'blood_bank_id' => 'required|integer'
        ]);

        $userId = Auth::id();
        $bankId = (int) $request->input('blood_bank_id');

        $refrigerators = $this->service->getRefrigeratorsForUserAndBank($userId, $bankId);

        return response()->json($refrigerators);
    }

    public function store(StoreBloodBagRequest $request)
    {
        $data = $request->validated();
        $this->service->create($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Blood bag created successfully']);
        }

        return redirect()->route('blood-bags.index')
            ->with('success', 'Blood bag created successfully');
    }

    public function update(
        BloodBag $bloodBag,
        UpdateBloodBagRequest $request
    ) {
        $data = $request->validated();
        $bloodBag = $this->service->update($bloodBag, $data);

        return redirect()->route('blood-bags.index')
            ->with('success', 'Blood bag updated successfully');
    }

    public function delete(BloodBag $bloodBag)
    {
        return $this->destroy(request(), $bloodBag);
    }

    public function destroy(Request $request, BloodBag $bloodBag)
    {
        if ($bloodBag->status !== 'expired') {
            $message = 'Only expired blood bags can be deleted.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'status' => $bloodBag->status,
                ], 422);
            }

            return redirect()->route('blood-bags.index')->with('error', $message);
        }

        $bloodBag->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Blood bag deleted successfully']);
        }

        return redirect()->route('blood-bags.index')->with('success', 'Blood bag deleted');
    }

    public function findById($id)
    {
        return $this->service->findById($id);
    }
     public function expiryDashboard(Request $request)
    {
        $userId = Auth::id();
        $bloodBanks = $this->service->getUserBloodBanksWithRefrigerators($userId);
        $refrigerators = $bloodBanks->flatMap(fn($bank) => $bank->refrigerators);
        $selectedRefrigerator = null;
        $summary = null;

        if ($request->filled('refrigerator_id')) {
            $selectedRefrigerator = Refrigerator::find($request->input('refrigerator_id'));
            if ($selectedRefrigerator) {
                $summary = $this->expiryService->summary($selectedRefrigerator);
            }
        }

        return view('bloodbag.blood-expiry-dashboard', compact(
            'refrigerators',
            'selectedRefrigerator',
            'summary'
        ));
    }

}
