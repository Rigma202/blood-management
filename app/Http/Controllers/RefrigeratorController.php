<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreRefrigeratorRequest;
use App\Http\Requests\UpdateRefrigeratorRequest;
use App\Models\Refrigerator;
use App\Services\RefrigeratorService;
use Illuminate\Http\Request;

class RefrigeratorController extends Controller
{
protected RefrigeratorService $refrigeratorService;

    public function __construct(
        RefrigeratorService $refrigeratorService
    )
    {
        $this->refrigeratorService =
            $refrigeratorService;
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
}
