@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center mt-5">
<div class="card shadow p-4" style="width:500px;">
    <h2 class="text-center mb-4">Edit Staff User</h2>

    <form id="staffEditForm">

        @csrf
        @method('PUT')

        <input type="hidden" id="staff_id" value="{{ $staff->id }}">

        <!-- Name -->
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text"
                   id="name"
                   class="form-control form-control-sm"
                   value="{{ $staff->name }}">
            <small class="text-danger" id="name_error"></small>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   id="email"
                   class="form-control form-control-sm"
                   value="{{ $staff->email }}">
            <small class="text-danger" id="email_error"></small>
        </div>

        <!-- Blood Banks -->
        <div class="mb-3">
            <label class="form-label">Blood Banks</label>

            <select id="blood_bank_id"
                    name="blood_bank_id[]"
                    class="form-control select2"
                    multiple>

                @foreach($bloodBanks as $bloodBank)
                    <option value="{{ $bloodBank->id }}">
                        {{ $bloodBank->name }}
                    </option>
                @endforeach

            </select>

            <small class="text-danger" id="blood_bank_id_error"></small>
        </div>

        <div class="text-center">

            <a href="{{ route('staff.index') }}"
               class="btn btn-danger btn-sm">
                Cancel
            </a>

            <button type="submit"
                    class="btn btn-success btn-sm">
                Update
            </button>

        </div>

    </form>

</div>
</div>

@endsection
@push('scripts')

<script>
@php
    $assignedBanks = $staff->bloodBanks ? $staff->bloodBanks->pluck('id')->toArray() : [];
@endphp

$(document).ready(function () {
    $('#blood_bank_id').select2({
        width: '100%',
        placeholder: "Select Blood Banks",
        allowClear: true
    });
});

var assignedBanks = @json($assignedBanks);
$('#blood_bank_id').val(assignedBanks).trigger('change');

$('#staffEditForm').submit(function(e){

    e.preventDefault();

    $('.text-danger').text('');

    $.ajax({

        url: '/staff/' + $('#staff_id').val(),
        type: 'PUT',

        data: (function() {
            const payload = {
                name: $('#name').val(),
                email: $('#email').val(),
                blood_bank_id: $('#blood_bank_id').val()
            };
            return payload;
        })(),

        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        success: function(response){

            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: 'Staff updated successfully'
            }).then(() => {
                window.location.href = '/staff';
            });

        },

        error: function(xhr) {
            const response = xhr.responseJSON || {};

            if (response.errors) {
                $.each(response.errors, function(key, value) {
                    $('#' + key + '_error').text(value[0]);
                });
            }
            if (response.message) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: response.message
                });
            }
        }

    });

});

</script>

@endpush
