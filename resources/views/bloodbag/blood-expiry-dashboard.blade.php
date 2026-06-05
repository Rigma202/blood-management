@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Blood Expiry Prediction</h2>

    <form method="GET" action="{{ route('blood-bags.expiry-dashboard') }}" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Refrigerator</label>
                <select name="refrigerator_id" class="form-select select2">
                    <option value="">Select refrigerator</option>
                    @foreach($refrigerators as $refrigerator)
                        <option value="{{ $refrigerator->id }}"
                            @selected(optional($selectedRefrigerator)->id == $refrigerator->id)>
                            {{ $refrigerator->name }} ({{ $refrigerator->serial_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Load</button>
            </div>
        </div>
    </form>

    @if($summary)
        <div class="row gy-3">
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="text-muted">Total Bags</div>
                    <h4>{{ $summary['total_bags'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="text-muted">Expiring in 24h</div>
                    <h4>{{ $summary['expiring_within_24']->count() }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="text-muted">Already Expired</div>
                    <h4>{{ $summary['already_expired']->count() }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="text-muted">Near-risk %</div>
                    <h4>{{ $summary['near_risk_pct'] }}%</h4>
                </div>
            </div>
        </div>

        <div class="row mt-4 gx-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">Expiring in 24 hours</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Bag #</th>
                                    <th>Group</th>
                                    <th>Expiry</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary['expiring_within_24'] as $bag)
                                    <tr>
                                        <td>{{ $bag->bag_number }}</td>
                                        <td>{{ $bag->blood_group }}</td>
                                        <td>{{ $bag->expiry_date->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No bags expiring soon.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">Already expired</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Bag #</th>
                                    <th>Group</th>
                                    <th>Expiry</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary['already_expired'] as $bag)
                                    <tr>
                                        <td>{{ $bag->bag_number }}</td>
                                        <td>{{ $bag->blood_group }}</td>
                                        <td>{{ $bag->expiry_date->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No expired bags.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function () {
    $('.select2').select2({
        width: '100%',
        placeholder: "Select refrigerator",
        allowClear: true
    });
});
</script>
@endpush
