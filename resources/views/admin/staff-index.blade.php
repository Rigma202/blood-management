@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h2>Staff Management</h2>

    <a href="{{ route('staff.create') }}"
       class="btn btn-primary">
        Add Staff
    </a>

</div>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Assigned Blood Bank</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

    @foreach($staffs as $staff)

        <tr>

            <td>{{ $staff->name }}</td>

            <td>{{ $staff->email }}</td>

            <td>
                <span class="badge bg-success">
                    {{ ucfirst($staff->role) }}
                </span>
            </td>

            <td>

                @foreach($staff->bloodBanks as $bloodBank)

                    <span>
                        {{ $bloodBank->name }}
                    </span>

                @endforeach

            </td>

            <td>

                <a href="{{ route('staff.edit', $staff->id) }}"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('staff.destroy', $staff->id) }}"
                      method="POST"
                      class="delete-form"
                      style="display:inline;">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger btn-sm">
                        Delete
                    </button>

                </form>

            </td>

        </tr>

    @endforeach

    </tbody>

</table>

<div class="mt-3">
    {{ $staffs->links() }}
</div>

@endsection

@push('scripts')

<script>

$(document).on('submit', '.delete-form', function(e){

    e.preventDefault();

    let form = this;

    Swal.fire({
        title: "Delete Staff?",
        text: "This staff user will be removed permanently.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Delete"
    }).then((result) => {

        if(result.isConfirmed){
            form.submit();
        }

    });

});

</script>

@endpush
