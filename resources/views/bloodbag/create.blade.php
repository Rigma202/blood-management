@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center mt-5">

    <div class="card shadow p-4" style="width: 450px;">

        <h2 class="text-center mb-4">Add Blood Bag</h2>

        <form id="bloodBagForm">

            @csrf

            <div class="mb-3">
                <label class="form-label">Blood Bank</label>
                <select id="blood_bank_id" name="blood_bank_id" class="form-control select2">
                    <option value="">Select blood bank</option>
                    @foreach($bloodBanks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
                <small id="blood_bank_id_error" class="text-danger"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Refrigerator</label>
                <select id="refrigerator_id" name="refrigerator_id" class="form-control">
                    <option value="">Select refrigerator</option>
                </select>
                <small id="refrigerator_id_error" class="text-danger"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Bag Number</label>
                <input type="text" name="bag_number" id="bag_number" class="form-control form-control-sm">
                <small class="text-danger" id="bag_number_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Donor Name</label>
                <input type="text" name="donor_name" id="donor_name" class="form-control form-control-sm">
                <small class="text-danger" id="donor_name_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Blood Group</label>
                <input type="text" name="blood_group" id="blood_group" class="form-control form-control-sm">
                <small class="text-danger" id="blood_group_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control form-control-sm" min="1">
                <small class="text-danger" id="quantity_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Collection Date</label>
                <input type="date" name="collection_date" id="collection_date" class="form-control form-control-sm">
                <small class="text-danger" id="collection_date_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date" class="form-control form-control-sm">
                <small class="text-danger" id="expiry_date_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select id="status" name="status" class="form-control form-control-sm">
                    <option value="">Select status</option>
                    <option value="available">Available</option>
                    <option value="reserved">Reserved</option>
                    <option value="used">Used</option>
                    <option value="expired">Expired</option>
                </select>
                <small class="text-danger" id="status_error"></small>
            </div>

            <div class="text-center">
                <a href="{{ route('blood-bags.index') }}" class="btn btn-danger btn-sm px-4">Cancel</a>
                <button type="submit" class="btn btn-success btn-sm px-4">Save</button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')

<script>
$('.select2').select2({ width: '100%' });

// load refrigerators when bank changes
$('#blood_bank_id').on('change', function () {
    const bankId = $(this).val();
    const $refrigerator = $('#refrigerator_id');

    $refrigerator.empty().append('<option value="">Select refrigerator</option>');

    if (!bankId) {
        $refrigerator.prop('disabled', true);
        return;
    }

    $refrigerator.prop('disabled', false);

    $.ajax({
        url: "{{ route('refrigerators.byBank') }}",
        data: { blood_bank_id: bankId },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (refrigerators) {
            if (!refrigerators || refrigerators.length === 0) return;
            refrigerators.forEach(function (r) {
                $refrigerator.append(
                    $('<option>', { value: r.id, text: r.name + ' — ' + (r.serial_number || '') })
                );
            });
        }
    });
});

// initialize disabled state
if (!$('#blood_bank_id').val()) {
    $('#refrigerator_id').prop('disabled', true);
}

// submit via ajax
$('#bloodBagForm').submit(function(e){
    e.preventDefault();

    // clear old errors
    $('.text-danger').text('');

    $.ajax({
        url: "/blood-bags",
        type: "POST",
        data: {
            refrigerator_id: $('#refrigerator_id').val(),
            bag_number: $('#bag_number').val(),
            donor_name: $('#donor_name').val(),
            blood_group: $('#blood_group').val(),
            quantity: $('#quantity').val(),
            collection_date: $('#collection_date').val(),
            expiry_date: $('#expiry_date').val(),
            status: $('#status').val(),
            blood_bank_id: $('#blood_bank_id').val()
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },

        success: function(response){
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: response.message || 'Blood bag created successfully'
            }).then(() => {
                window.location.href = "/blood-bags";
            });
        },

        error: function(xhr){
            if(xhr.status === 422){
                let errors = xhr.responseJSON.errors || {};
                // show each field error if present
                Object.keys(errors).forEach(function(field){
                    const el = $('#' + field + '_error');
                    if(el.length){
                        el.text(errors[field][0]);
                    }
                });
            } else {
                // generic error
                Swal.fire('Error', 'An unexpected error occurred', 'error');
            }
        }
    });

});
</script>
@endpush