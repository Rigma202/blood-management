@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h2>Refrigerator Management</h2>

    <a href="{{ route('refrigerators.create') }}"
       class="btn btn-primary">
        Add Refrigerator
    </a>

</div>

<table class="table table-bordered">

    <thead>

        <tr>
            <th>Name</th>
            <th>Reference Number</th>
            <th>Blood Bank</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

    </thead>

    <tbody>

    @forelse($refrigerators as $refrigerator)

        <tr>

            <td>
                {{ $refrigerator->name }}
            </td>

            <td>
                {{ $refrigerator->serial_number }}
            </td>

            <td>
                {{ $refrigerator->bloodBank->name ?? 'N/A' }}
            </td>

            <td>

                @if($refrigerator->is_active)

                    <span class="badge bg-success">
                        Active
                    </span>

                @else

                    <span class="badge bg-danger">
                        Inactive
                    </span>

                @endif

            </td>

            <td>

                <a href="{{ route('refrigerators.edit', $refrigerator->id) }}"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('refrigerators.destroy', $refrigerator->id) }}"
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

    @empty

        <tr>

            <td colspan="5"
                class="text-center">
                No Refrigerators Found
            </td>

        </tr>

    @endforelse

    </tbody>

</table>

<div class="mt-3">

    {{ $refrigerators->links() }}

</div>

@endsection

@push('scripts')

<script>
$(document).on('submit', '.delete-form', function(e) {
    e.preventDefault();

    const form = this;
    const url = $(form).attr('action');

    Swal.fire({
        title: 'Delete Refrigerator?',
        text: 'This refrigerator will be removed permanently.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Delete'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: url,
            type: 'DELETE',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: response.message || 'Refrigerator deleted successfully.'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Delete',
                        text: xhr.responseJSON.message
                    });
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to delete refrigerator. Please try again.'
                });
            }
        });
    });
});
</script>

@endpush
