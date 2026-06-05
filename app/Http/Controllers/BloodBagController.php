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
use App\Models\BloodBank;

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
    public function edit(BloodBag $bloodBag)
    {
        $bloodBag->load('refrigerator.bloodBank');
        return view('bloodbag.edit', compact('bloodBag'));
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
    public function findById($id)
    {
        return $this->service->findById($id);
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


public function destroy(Request $request, BloodBag $bloodBag)
{
    $bloodBag->delete();

    return response()->json([
        'success' => true,
        'message' => 'Blood bag deleted successfully.'
    ]);
}


public function expiryDashboard(Request $request)
{
    $userId = Auth::id();
    $refrigerators = $this->service->getRefrigeratorDropdownForUser($userId);

    $selectedRefrigerator = null;
    $summary = null;

    if ($request->filled('refrigerator_id')) {

        $selectedRefrigerator = $this->service
            ->findUserRefrigerator($userId, $request->refrigerator_id);

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
