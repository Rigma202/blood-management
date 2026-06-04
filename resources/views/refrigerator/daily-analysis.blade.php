@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Refrigerator Daily Temperature Analysis</h2>

    <form method="GET" action="{{ route('refrigerators.dailyAnalysis') }}" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Refrigerator</label>
                <select name="refrigerator_id" class="form-select">
                    <option value="">Select refrigerator</option>
                    @foreach($refrigerators as $refrigerator)
                        <option value="{{ $refrigerator->id }}"
                            @selected(optional($selectedRefrigerator)->id == $refrigerator->id)>
                            {{ $refrigerator->name }} ({{ $refrigerator->serial_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" name="date"
                    value="{{ request('date', now()->toDateString()) }}"
                    class="form-control">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Load</button>
            </div>
        </div>
    </form>

    @if($analysis)
        <div class="row gy-3">
            <div class="col-md-2">
                <div class="card p-3">
                    <div class="text-muted">Date</div>
                    <h5>{{ $analysis['date'] }}</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <div class="text-muted">Average</div>
                    <h5>{{ $analysis['average_temp'] }}°C</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <div class="text-muted">Highest</div>
                    <h5>{{ $analysis['highest_temp'] }}°C</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <div class="text-muted">Lowest</div>
                    <h5>{{ $analysis['lowest_temp'] }}°C</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <div class="text-muted">Unsafe mins</div>
                    <h5>{{ $analysis['unsafe_minutes'] }}</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <div class="text-muted">Risk</div>
                    <h5>{{ $analysis['risk_percentage'] }}%</h5>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                Latest temperature logs for
                {{ $selectedRefrigerator->name ?? 'selected refrigerator' }}
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Temperature</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log['recorded_at']->format('H:i:s') }}</td>
                                <td>{{ $log['temperature'] }}°C</td>
                                <td>{{ $log['status'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    No logs found for this date.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection