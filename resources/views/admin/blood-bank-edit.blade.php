@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center mt-5">

```
<div class="card shadow p-4" style="width: 450px;">

    <h2 class="text-center mb-4">Edit Blood Bank</h2>

    <form id="bloodBankForm">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ $bloodBank->name }}"
                   class="form-control form-control-sm">
            <small class="text-danger" id="name_error"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text"
                   name="location"
                   id="location"
                   value="{{ $bloodBank->location }}"
                   class="form-control form-control-sm">
            <small class="text-danger" id="location_error"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text"
                   name="contact_number"
                   id="contact_number"
                   value="{{ $bloodBank->contact_number }}"
                   class="form-control form-control-sm">
            <small class="text-danger" id="contact_number_error"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   id="email"
                   value="{{ $bloodBank->email }}"
                   class="form-control form-control-sm">
            <small class="text-danger" id="email_error"></small>
        </div>

        <div class="text-center">

            <a href="{{ route('blood-banks.index') }}"
               class="btn btn-danger btn-sm px-4">
                Cancel
            </a>

            <button type="submit"
                    class="btn btn-success btn-sm px-4">
                Update
            </button>

        </div>

    </form>

</div>
```

</div>

@endsection

@push('scripts')

<script>

$('#bloodBankForm').submit(function(e){

    e.preventDefault();

    $('.text-danger').text('');

    $.ajax({

        url: "/blood-banks/{{ $bloodBank->id }}",
        type: "PUT",

        data: {
            name: $('#name').val(),
            location: $('#location').val(),
            contact_number: $('#contact_number').val(),
            email: $('#email').val()
        },

        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        success: function(response){

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Blood bank updated successfully'
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
