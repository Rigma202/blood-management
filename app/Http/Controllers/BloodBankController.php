<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodBankRequest;
use App\Http\Requests\UpdateBloodBankRequest;
use App\Models\BloodBank;
use App\Services\BloodBankService;

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
    public function destroy(BloodBank $bloodBank)
    {
        $this->bloodBankService->delete($bloodBank);

        return redirect()->route('blood-banks.index')->with('success', 'Blood bank deleted successfully');
    }
}
