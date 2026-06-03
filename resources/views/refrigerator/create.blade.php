@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center mt-5">

    <div class="card shadow p-4" style="width:500px;">

        <h2 class="text-center mb-4">
            Add Refrigerator
        </h2>

        <form id="refrigeratorForm">

            <div class="mb-3">

                <label class="form-label">
                    Blood Bank
                </label>

                <select id="blood_bank_id"
                    class="form-control select2">

                    <option value="">
                        Select Blood Bank
                    </option>

                    @foreach($bloodBanks as $bloodBank)

                    <option value="{{ $bloodBank->id }}">
                        {{ $bloodBank->name }}
                    </option>

                    @endforeach

                </select>

                <small class="text-danger"
                    id="blood_bank_id_error">
                </small>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Refrigerator Name
                </label>

                <input type="text"
                    id="name"
                    class="form-control">

                <small class="text-danger"
                    id="name_error">
                </small>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Serial Number
                </label>

                <input type="text"
                    id="serial_number"
                    class="form-control">

                <small class="text-danger"
                    id="serial_number_error">
                </small>

            </div>

            <div class="mb-3">

                <label>Status</label>

                <select id="is_active"
                    class="form-control">

                    <option value="1">
                        Active
                    </option>

                    <option value="0">
                        Inactive
                    </option>

                </select>

            </div>

            <div class="text-center">

                <a href="{{ route('refrigerators.index') }}"
                    class="btn btn-danger">

                    Cancel

                </a>

                <button class="btn btn-success">

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

$('#refrigeratorForm').submit(function(e){

    e.preventDefault();

    $('.text-danger').text('');

    $.ajax({

        url:'/refrigerators',
        type:'POST',

        data:{
            name:$('#name').val(),
            serial_number:$('#serial_number').val(),
            is_active:$('#is_active').val(),
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
                text:'Refrigerator created successfully'
            }).then(()=>{
                window.location.href='/refrigerators';
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
