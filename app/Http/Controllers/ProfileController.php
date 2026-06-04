<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\DashboardService;
use App\Models\BloodBank;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    public function index(Request $request)
    {
        if (Auth::check()) {

        $user = Auth::user();
        return match ($user->role) {
            'admin' => view('admin.dashboard', [
                'dashboardData' => $this->dashboardService->getAdminDashboardSummary(),
                'bloodBanks' => BloodBank::orderBy('name')
                    ->get(['id', 'name']),
            ]),

            'staff' => view('staff.dashboard', [
                'dashboardData' => $this->dashboardService->getDashboardSummary()
            ]),

            'monitoring_user' => view('monitor-user.dashboard', [
                'dashboardData' => $this->dashboardService->getDashboardSummary()
            ]),

            default => abort(403),
        };
    }
    }
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
