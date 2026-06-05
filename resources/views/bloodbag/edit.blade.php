@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center mt-5">

    <div class="card shadow p-4" style="width: 100%; max-width: 900px;">

        <h2 class="text-center mb-4">Edit Blood Bag</h2>

        <form id="bloodBagEditForm">

            @csrf
            @method('PUT')

            <input type="hidden" id="blood_bag_id" value="{{ $bloodBag->id }}">

            <div class="row gx-3 gy-3">

                <div class="col-md-6">
                    <label class="form-label">Blood Bank</label>
                    <input type="text" class="form-control"
                        value="{{ $bloodBag->refrigerator?->bloodBank?->name }}"
                        readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Refrigerator</label>
                    <input type="text" class="form-control"
                        value="{{ $bloodBag->refrigerator?->name }}"
                        readonly>
                </div>

                <div class="col-md-6">
                    <label>Bag Number</label>
                    <input type="text" id="bag_number" class="form-control"
                        value="{{ $bloodBag->bag_number }}">
                    <small class="text-danger" id="bag_number_error"></small>
                </div>

                <div class="col-md-6">
                    <label>Donor Name</label>
                    <input type="text" id="donor_name" class="form-control"
                        value="{{ $bloodBag->donor_name }}">
                    <small class="text-danger" id="donor_name_error"></small>
                </div>

            <div class="col-md-6">
                <label>Blood Group</label>
                <select id="blood_group" class="form-control">
                    <option value="">Select blood group</option>

                    <option value="A+" {{ $bloodBag->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ $bloodBag->blood_group == 'A-' ? 'selected' : '' }}>A-</option>

                    <option value="B+" {{ $bloodBag->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ $bloodBag->blood_group == 'B-' ? 'selected' : '' }}>B-</option>

                    <option value="AB+" {{ $bloodBag->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ $bloodBag->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>

                    <option value="O+" {{ $bloodBag->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ $bloodBag->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
                </select>

                <small class="text-danger" id="blood_group_error"></small>
            </div>

                <div class="col-md-6">
                    <label>Quantity</label>
                    <input type="number" id="quantity" class="form-control"
                        value="{{ $bloodBag->quantity }}">
                    <small class="text-danger" id="quantity_error"></small>
                </div>

                <div class="col-md-6">
                    <label>Collection Date</label>
                    <input type="date" id="collection_date" class="form-control"
                        value="{{ $bloodBag->collection_date?->format('Y-m-d') }}">
                    <small class="text-danger" id="collection_date_error"></small>
                </div>

                <div class="col-md-6">
                    <label>Expiry Date</label>
                    <input type="date" id="expiry_date" class="form-control"
                        value="{{ $bloodBag->expiry_date?->format('Y-m-d') }}">
                    <small class="text-danger" id="expiry_date_error"></small>
                </div>

                <div class="col-md-6">
                    <label>Status</label>
                    <select id="status" class="form-control">
                        @foreach(['available','reserved','used','expired'] as $status)
                            <option value="{{ $status }}"
                                {{ $bloodBag->status == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-danger" id="status_error"></small>
                </div>

                <div class="col-md-6">
                    <div class="form-check mt-3">
                        <input type="checkbox" id="is_tested"
                            {{ $bloodBag->is_tested ? 'checked' : '' }}>
                        <label>Blood tested</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-check mt-3">
                        <input type="checkbox" id="is_secure"
                            {{ $bloodBag->is_secure ? 'checked' : '' }}>
                        <label>Secure for donation</label>
                    </div>
                </div>

                <div class="col-12 text-center mt-3">
                    <a href="{{ route('blood-bags.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-success btn-sm">Update</button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
$('#bloodBagEditForm').on('submit', function (e) {
    e.preventDefault();

    // clear previous errors
    $('.text-danger').text('');

    const id = $('#blood_bag_id').val();

    $.ajax({
        url: `/blood-bags/${id}`,
        type: "POST",
        data: {
            _method: "PUT",

            bag_number: $('#bag_number').val(),
            donor_name: $('#donor_name').val(),
            blood_group: $('#blood_group').val(),
            quantity: $('#quantity').val(),
            collection_date: $('#collection_date').val(),
            expiry_date: $('#expiry_date').val(),
            status: $('#status').val(),

            is_tested: $('#is_tested').is(':checked') ? 1 : 0,
            is_secure: $('#is_secure').is(':checked') ? 1 : 0
        },

        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },

        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: response.message || 'Blood bag updated successfully'
            }).then(() => {
                window.location.href = "/blood-bags";
            });
        },

        error: function (xhr) {

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;

                Object.keys(errors).forEach(function (field) {
                    $('#' + field + '_error').text(errors[field][0]);
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Something went wrong'
                });
            }
        }
    });
});
</script>
@endpush
