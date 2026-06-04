@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="card shadow p-4" style="width:500px;">
        <h2 class="text-center mb-4">Edit Refrigerator</h2>

        <form id="refrigeratorEditForm">
            <input type="hidden" id="refrigerator_id" value="{{ $refrigerator->id }}">

            <div class="mb-3">
                <label class="form-label">Blood Bank</label>
                <select id="blood_bank_id" class="form-control select2">
                    <option value="">Select Blood Bank</option>
                    @foreach($bloodBanks as $bloodBank)
                        <option value="{{ $bloodBank->id }}" {{ $bloodBank->id == $refrigerator->blood_bank_id ? 'selected' : '' }}>
                            {{ $bloodBank->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-danger" id="blood_bank_id_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Refrigerator Name</label>
                <input type="text" id="name" class="form-control" value="{{ $refrigerator->name }}">
                <small class="text-danger" id="name_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Serial Number</label>
                <input type="text" id="serial_number" class="form-control" value="{{ $refrigerator->serial_number }}">
                <small class="text-danger" id="serial_number_error"></small>
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select id="is_active" class="form-control">
                    <option value="1" {{ $refrigerator->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ ! $refrigerator->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="text-center">
                <a href="{{ route('refrigerators.index') }}" class="btn btn-danger">Cancel</a>
                <button class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('.select2').select2({ width:'100%' });

$('#refrigeratorEditForm').submit(function(e){
    e.preventDefault();
    $('.text-danger').text('');

    const id = $('#refrigerator_id').val();

    $.ajax({
        url: '/refrigerators/' + id,
        type: 'PUT',
        data: {
            name: $('#name').val(),
            serial_number: $('#serial_number').val(),
            is_active: $('#is_active').val(),
            blood_bank_id: $('#blood_bank_id').val()
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response){
            if (response.success === true) {
            
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Refrigerator updated successfully'
                }).then(() => window.location.href = '/refrigerators');
            }
        },
       error: function(xhr){
            // Handle 422 Unprocessable Entity (constraint errors)
            if (xhr.status === 422) {
                const response = xhr.responseJSON;
                
                if (response.success === false) {
                    // Business logic error (blocked bags)
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Update',
                        text: response.message
                    });
                } else if (response.errors) {
                    // Validation errors
                    $.each(response.errors, function(key, value){
                        $('#' + key + '_error').text(value[0]);
                    });
                }
            } else {
                Swal.fire('Error', 'Unexpected error occurred', 'error');
            }
        }
    });
});
</script>
@endpush