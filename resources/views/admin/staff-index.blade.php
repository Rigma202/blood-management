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
                <span>
                    {{ ucfirst($staff->role) }}
                </span>
            </td>

            <td>

            @foreach($staff->bloodBanks as $bloodBank)
                <span>
                    {{ $bloodBank->name }}@if(!$loop->last), @endif
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

$(document).on('submit', '.delete-form', function(e) {
    e.preventDefault();

    const form = this;
    const url = $(form).attr('action');

    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.requiresConfirmation) {

                Swal.fire({
                    title: 'Active refrigerators found',
                    text: response.message + ' Proceed with deletion?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Proceed',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { confirm: true },
                            success: function(res) {
                                Swal.fire('Deleted', res.message, 'success')
                                    .then(() => location.reload());
                            }
                        });
                    }
                });
                return;
            }

            Swal.fire('Deleted', response.message, 'success')
                .then(() => location.reload());
        },
        error: function(xhr) {
            Swal.fire('Error', 'Unable to delete staff.', 'error');
        }
    });
});
</script>

@endpush
