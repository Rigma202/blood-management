<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodBankRequest;
use App\Http\Requests\UpdateBloodBankRequest;
use App\Models\BloodBank;
use App\Services\BloodBankService;
use Illuminate\Http\Request;

class BloodBankController extends Controller
{
    protected BloodBankService $bloodBankService;

    public function __construct(BloodBankService $bloodBankService)
    {
        $this->bloodBankService = $bloodBankService;
    }

    /**
     * Display all blood banks
     */
    public function index()
    {
        $bloodBanks = $this->bloodBankService->getAll();
        return view('admin.blood-bank', compact('bloodBanks'));
    }

    public function create()
    {
        return view('admin.blood-bank-create');
    }
    public function edit($id)
    {
        $bloodBank = BloodBank::findOrFail($id);
        return view('admin.blood-bank-edit', compact('bloodBank'));
    }
    /**
     * Create blood bank
     */
    public function store(StoreBloodBankRequest $request)
    {
        $bloodBank = $this->bloodBankService
            ->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Blood bank created successfully',
            'data' => $bloodBank
        ], 201);
    }

    /**
     * Show single blood bank
     */
    public function show(BloodBank $bloodBank)
    {
        //
    }

    /**
     * Update blood bank
     */
    public function update(
        UpdateBloodBankRequest $request,
        BloodBank $bloodBank
    ) {
        $bloodBank = $this->bloodBankService
            ->update(
                $bloodBank,
                $request->validated()
            );

        return response()->json([
            'success' => true,
            'message' => 'Blood bank updated successfully',
            'data' => $bloodBank
        ]);
    }

    /**
     * Delete blood bank
     */
    public function destroy(Request $request, BloodBank $bloodBank)
    {
        $activeRefrigerators = $bloodBank->refrigerators()
            ->where('is_active', true)
            ->pluck('name');

        if ($activeRefrigerators->isNotEmpty() && !$request->boolean('confirm')) {

            return response()->json([
                'status'  => 'warning',
                'message' => 'This blood bank has active refrigerators: '
                    . $activeRefrigerators->implode(', ')
                    . '. Are you sure you want to delete it?'
            ], 409);
        }

        $this->bloodBankService->delete($bloodBank);

        return response()->json([
            'status' => 'success',
            'message' => 'Blood bank deleted successfully.'
        ]);
    }
}
