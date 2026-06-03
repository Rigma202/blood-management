@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center mt-5">

    <div class="card shadow p-4" style="width: 450px;">

        <h2 class="text-center mb-4">Add Blood Bank</h2>

        <form action="{{ route('blood-banks.store') }}" method="POST" id="bloodBankForm">

            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control form-control-sm">
                <small class="text-danger" id="name_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control form-control-sm">
                <small class="text-danger" id="location_error"></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone number</label>
                <input type="text" name="phone" id="contact_number" class="form-control form-control-sm">
                <small class="text-danger" id="contact_number_error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control form-control-sm">
                <small class="text-danger" id="email_error"></small>
            </div>
            <div class="text-center">
                <a href="{{ route('blood-banks.index') }}" class="btn btn-danger btn-sm px-4">
                    Cancel
                </a>
                <button class="btn btn-success btn-sm px-4">
                    Save
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
@push('scripts')
<script>
$('#bloodBankForm').submit(function(e){

    let name=document.getElementById('name').value;
    let location=document.getElementById('location').value;
    let contact_number=document.getElementById('contact_number').value;
    let email=document.getElementById('email').value;
    console.log(name,location,contact_number,email);

    e.preventDefault();
    $.ajax({
        url: "/blood-banks",
        type: 'POST',
        data: {

            name:name,
            location:location,
            contact_number:contact_number,
            email:email

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        success: function(response){
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text:'Blood bank added successfully'
            }).then(() => {
                window.location.href = "/blood-banks";
            });

        },
        error: function(xhr){
            if(xhr.status === 422){

                let errors = xhr.responseJSON.errors;
                if(errors.name){
                    $('#name_error').text(errors.name[0]);
                }
                if(errors.location){
                    $('#location_error').text(errors.location[0]);
                }
                if(errors.contact_number){
                    $('#contact_number_error').text(errors.contact_number[0]);
                }
                if(errors.email){
                    $('#email_error').text(errors.email[0]);
                }
            }

        }

    });

});

</script>
@endpush
