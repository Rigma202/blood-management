<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\User;
use App\Services\StaffService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class StaffController extends Controller
{
protected StaffService $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * Display all blood banks
     */
    public function index()
    {
        $staffs = $this->staffService->getAllStaff();
        return view('admin.staff-index', compact('staffs'));
    }

    public function create()
    {
        $bloodBanks = $this->staffService->getAllBloodbanks();
        return view('admin.staff-create', compact('bloodBanks'));
    }
    public function edit($id)
    {
        $staff = $this->staffService->getStaffWithBloodBanks($id);
        $bloodBanks = $this->staffService->getAllBloodbanks();
        return view('admin.staff-edit', compact('staff', 'bloodBanks'));
    }
    /**
     * Create blood bank
     */
    public function store(StoreStaffRequest $request)
    {
        $staff = $this->staffService
            ->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Staff member created successfully',
            'data' => $staff
        ], 201);
    }

    /**
     * Show single blood bank
     */
    public function show()
    {
        //
    }

    /**
     * Update blood bank
     */
    public function update(UpdateStaffRequest $request, User $staff)
    {
        $data = $request->validated();
        $bloodBankIds = $data['blood_bank_id'] ?? [];
        $currentIds = $staff->bloodBanks()->pluck('blood_banks.id')->toArray();
        $removingIds = array_diff($currentIds, $bloodBankIds);

        if (!empty($removingIds)) {
            $blockedBanks =$this->staffService->getBloodBanksWithActiveRefrigerators($removingIds);
            if ($blockedBanks->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove staff.The blood bank has active refrigerators.',
                    'blockedBanks' => $blockedBanks,
                ], 422);
            }
        }

        $staff = $this->staffService->update($staff, $data);

        return response()->json([
            'success' => true,
            'message' => 'Staff member updated successfully',
            'data' => $staff,
        ]);
    }

    /**
     * Delete blood bank
     */
public function destroy(Request $request, User $staff)
{

  $assignedBankIds = $staff->bloodBanks()->pluck('blood_banks.id')->toArray();

        $blockedBanks =$this->staffService->getBloodBanksWithActiveRefrigerators($assignedBankIds);
        if ($blockedBanks->isNotEmpty() && !$request->boolean('confirm', false)) {
            return response()->json([
                'success' => false,
                'requiresConfirmation' => true,
                'message' => 'This staff is assigned to blood bank(s) with active refrigerators.',
                'blockedBanks' => $blockedBanks,
            ]);
        }

        $this->staffService->delete($staff);

        return response()->json([
            'success' => true,
            'message' => 'Staff member deleted successfully',
        ]);
    }
    public function getStaffBloodBanks()
    {
        $bloodBanks = $this->staffService->getAssignedBloodBanks(Auth::id());
        return view('staff.index', compact('bloodBanks'));
    }
}

