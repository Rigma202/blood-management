@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center mt-5">
<div class="card shadow p-4" style="width:500px;">
    <h2 class="text-center mb-4">Add Staff User</h2>

    <form id="staffForm">

        @csrf

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text"
                   id="name"
                   class="form-control form-control-sm">
            <small class="text-danger" id="name_error"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   id="email"
                   class="form-control form-control-sm">
            <small class="text-danger" id="email_error"></small>
        </div>


        <div class="mb-3">
            <label class="form-label">Blood Bank</label>

            <select id="blood_bank_id"
                    name="blood_bank_id[]"
                    class="form-control select2" multiple>

                <option value="">Select Blood Bank</option>

                @foreach($bloodBanks as $bloodBank)
                    <option value="{{ $bloodBank->id }}">
                        {{ $bloodBank->name }}
                    </option>
                @endforeach

            </select>

            <small class="text-danger"
                   id="blood_bank_id_error"></small>
        </div>

        <div class="text-center">

            <a href="{{ route('staff.index') }}"
               class="btn btn-danger btn-sm">
                Cancel
            </a>

            <button type="submit"
                    class="btn btn-success btn-sm">
                Save
            </button>

        </div>

    </form>

</div>

</div>

@endsection

@push('scripts')

<script>

$('.select2').select2({
    width:'100%'
});

$('#staffForm').submit(function(e){

    e.preventDefault();

    $('.text-danger').text('');

    $.ajax({

        url:'/staff',
        type:'POST',

        data:{
            name:$('#name').val(),
            email:$('#email').val(),
            blood_bank_id:$('#blood_bank_id').val()
        },

        headers:{
            'X-CSRF-TOKEN':
            $('meta[name="csrf-token"]').attr('content')
        },

        success:function(response){

            Swal.fire({
                icon:'success',
                title:'Success',
                text:'Staff created successfully'
            }).then(()=>{
                window.location.href='/staff';
            });

        },

        error:function(xhr){

            let errors=xhr.responseJSON.errors;

            $.each(errors,function(key,value){

                $('#' + key + '_error')
                    .text(value[0]);

            });

        }

    });

});

</script>

@endpush
