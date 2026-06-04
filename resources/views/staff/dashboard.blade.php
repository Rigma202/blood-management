@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row gy-3">
        <div class="col-md-2">
            <div class="card p-3 bg-primary">
                <div class="text-muted">Total Bags</div>
                <h4>{{ $dashboardData['total_bags'] }}</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 bg-success">
                <div class="text-muted">Active Fridges</div>
                <h4>{{ $dashboardData['active_fridges'] }}</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 bg-warning">
                <div class="text-muted">Expired Bags</div>
                <h4>{{ $dashboardData['expired_bags'] }}</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 bg-info">
                <div class="text-muted">Health Score</div>
                <h4>{{ $dashboardData['health_score'] }}%</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 bg-secondary">
                <div class="text-muted">Avg Temp Today</div>
                <h4>{{ $dashboardData['avg_temp_today'] ?? 'N/A' }}°C</h4>
            </div>
        </div>
    </div>

    <div class="row mt-4 gy-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Stock by Blood Group</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Blood Group</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboardData['stock_by_group'] as $stock)
                                <tr>
                                    <td><strong>{{ $stock['blood_group'] }}</strong></td>
                                    <td>{{ $stock['quantity'] }} units</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No stock data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Critical Temperature Alerts</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Refrigerator</th>
                                <th>Temperature</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboardData['critical_alerts'] as $alert)
                                <tr>
                                    <td>{{ $alert['refrigerator_name'] }}</td>
                                    <td><span class="badge bg-danger">{{ $alert['temperature'] }}°C</span></td>
                                    <td>{{ $alert['recorded_at'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No critical alerts</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
