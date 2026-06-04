@extends('layouts.app')

    @push('styles')
    <style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding-top: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    </style>
    @endpush

@section('content')
<div class="container-fluid mt-4">

    <h2 class="mb-4">Admin Dashboard</h2>

    <div class="row gy-3">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <h6>Total Blood Banks</h6>
                    <h2>{{ $dashboardData['total_blood_banks'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <h6>Banks With Active Refrigerators</h6>
                    <h2>{{ $dashboardData['banks_with_active_fridges'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body">
                    <h6>Total Staff</h6>
                    <h2>{{ $dashboardData['total_staff_count'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <h6>Maximum Refrigerators</h6>

                    @if($dashboardData['bank_with_max_refrigerators'])
                    <h5>
                        {{ $dashboardData['bank_with_max_refrigerators']['name'] }}
                    </h5>

                    <small>
                        {{
                                $dashboardData['bank_with_max_refrigerators']['refrigerator_count']
                            }} Refrigerators
                    </small>
                    @else
                    <h5>N/A</h5>
                    @endif

                </div>
            </div>
        </div>
        <div class="card mt-4 shadow-sm">


            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label"><b>Blood Bank</b></label>

                    <select
                        name="blood_bank_id"
                        id="blood_bank_id"
                        class="form-select select2">

                        <option value="">
                            Search and Select Blood Bank
                        </option>

                        @foreach($bloodBanks as $bank)
                            <option value="{{ $bank->id }}">
                                {{ $bank->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div id="staff-list">

                    <div class="alert alert-info">
                        Select a blood bank to view staff.
                    </div>

                </div>

            </div>

        </div>
@endsection
@push('scripts')
<script>
$(document).ready(function () {

    $('#blood_bank_id').select2({
        placeholder: 'Search Blood Bank',
        allowClear: true,
        width: '100%'
    });

    $('#blood_bank_id').on('change', function () {

        let bloodBankId = $(this).val();

        if (!bloodBankId) {

            $('#staff-list').html(`
                <div class="alert alert-info">
                    Select a blood bank to view staff.
                </div>
            `);

            return;
        }

        $.ajax({
            url: '/admin/blood-banks/' + bloodBankId + '/staff',
            type: 'GET',
            dataType: 'json',

            success: function (staff) {

                let html = '';

                if (staff.length === 0) {

                    html = `
                        <div class="alert alert-warning">
                            No staff assigned to this blood bank.
                        </div>
                    `;

                } else {

                    html = `
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    staff.forEach(function (user) {

                        html += `
                            <tr>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                        </table>
                    `;
                }

                $('#staff-list').html(html);
            },

            error: function (xhr) {

                $('#staff-list').html(`
                    <div class="alert alert-danger">
                        Failed to load staff.
                    </div>
                `);

                console.error(xhr);
            }
        });

    });

});

</script>
@endpush
