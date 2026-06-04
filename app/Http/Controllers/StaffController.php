<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\User;
use App\Services\StaffService;
use Illuminate\Support\Facades\Auth;

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
        $staff = $this->staffService
            ->update(
                $staff,
                $request->validated()
            );

        return response()->json([
            'success' => true,
            'message' => 'Staff member updated successfully',
            'data' => $staff
        ]);
    }

    /**
     * Delete blood bank
     */
    public function destroy(User $user)
    {
        $this->staffService->delete($user);

        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully');
    }
    public function getStaffBloodBanks()
    {
        $bloodBanks = $this->staffService->getAssignedBloodBanks(Auth::id());
        return view('staff.index', compact('bloodBanks'));
    }
}

