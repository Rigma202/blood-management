@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Refrigerator Temperature Logs</h2>

    <div class="mb-3">
        <label class="form-label">Select Refrigerator</label>
        <select id="refrigerator_id" class="form-select">
            <option value="">Choose refrigerator</option>
            @foreach($refrigerators as $refrigerator)
                <option value="{{ $refrigerator->id }}">
                    {{ $refrigerator->name }} — {{ $refrigerator->serial_number }}
                </option>
            @endforeach
        </select>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Recorded At</th>
                <th>Temperature</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="temperatureLogsBody">
            <tr>
                <td colspan="3" class="text-center">
                    Select a refrigerator to load logs.
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$('#refrigerator_id').on('change', function () {
    const refrigeratorId = $(this).val();
    const $body = $('#temperatureLogsBody');

    if (!refrigeratorId) {
        $body.html('<tr><td colspan="3" class="text-center">Select a refrigerator to load logs.</td></tr>');
        return;
    }

    $.ajax({
        url: "{{ url('refrigerators') }}/" + refrigeratorId + "/logs",
        method: 'GET',
        success: function(logs) {
            if (!logs.length) {
                $body.html('<tr><td colspan="3" class="text-center">No temperature logs found.</td></tr>');
                return;
            }

            const rows = logs.map(log => `
                <tr>
                    <td>${log.recorded_at}</td>
                    <td>${log.temperature} °C</td>
                    <td>${log.status}</td>
                </tr>
            `).join('');

            $body.html(rows);
        },
        error: function() {
            $body.html('<tr><td colspan="3" class="text-center text-danger">Unable to load logs.</td></tr>');
        }
    });
});
</script>
@endpush