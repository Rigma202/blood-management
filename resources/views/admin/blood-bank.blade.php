@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h2>Blood Banks</h2>

    <a href="{{ route('blood-banks.create') }}" class="btn btn-primary">
        Add Blood Bank
    </a>

</div>

<table class="table table-bordered">

    <tr>
        <th>Name</th>
        <th>Location</th>
        <th>Contact Number</th>
        <th>Email</th>
        <th>Action</th>
    </tr>
    @foreach($bloodBanks as $bloodBank)

    <tr>

        <td>{{ $bloodBank->name }}</td>
        <td>{{ $bloodBank->location }}</td>
        <td>{{ $bloodBank->contact_number }}</td>
        <td>{{ $bloodBank->email }}</td>

        <td>

            <a href="{{ route('blood-banks.edit',$bloodBank->id) }}"
               class="btn btn-warning btn-sm">
                Edit
            </a>

            <form action="{{ route('blood-banks.destroy',$bloodBank->id) }}"
                  method="POST"
                  class="delete-form"
                  style="display:inline;">

                @csrf
                @method('DELETE')

                <button class="btn btn-danger btn-sm">
                    Delete
                </button>

            </form>

        </td>

    </tr>

    @endforeach

</table>
<div class="mt-3">
    {{ $bloodBanks->links() }}
</div>

@endsection
@push('scripts')
<script>
$(document).on('submit', '.delete-form', function(e){
    e.preventDefault();
    let form = this;
    Swal.fire({
        title: "Are you sure?",
        text: "This blood bank will be deleted permanently!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {

        if (result.isConfirmed) {
            form.submit();
        }

    });

});
</script>
@endpush
