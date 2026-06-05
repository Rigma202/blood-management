@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Blood Bags</h3>
        <a href="{{ route('blood-bags.create') }}" class="btn btn-primary btn-sm">
            + Add Blood Bag
        </a>
    </div>

    <div class="card shadow p-3">

        <div class="table-responsive">

            <table class="table table-bordered table-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Blood Bank</th>
                        <th> Donor Name </th>
                        <th>Blood Group</th>
                        <th>Units</th>
                        <th>Status</th>
                        <th>Collection Date</th>
                        <th>Expiry Date</th>
                        <th>Created by</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="bloodBagTable">
                    @forelse($bloodBags as $key => $bag)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $bag->refrigerator?->bloodBank?->name ?? '-' }}</td>
                            <td>{{ $bag->donor_name }}</td>
                            <td>{{ $bag->blood_group }}</td>
                            <td>{{ $bag->quantity}}</td>
                            <td>{{$bag->status }}</td>                            <td>{{ $bag->collection_date->format('d M Y') }}</td>
                            <td>{{ $bag->expiry_date->format('d M Y') }}</td>
                            <td>{{ $bag->creator?->name ?? 'System' }}</td>
                            <td>
                                <a href="{{ route('blood-bags.edit', $bag->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('blood-bags.destroy', $bag->id) }}"
                                    method="POST"
                                    data-status="{{ $bag->status }}"
                                    class="delete-bloodbag-form"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No blood bags found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>

$(function () {

    $('form.delete-bloodbag-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const status = $form.data('status');

        const isSafe = (status === 'expired' || status === 'dispatched');

        let text = '';

        if (isSafe) {
            text = `Are you sure you want to delete this ${status} blood bag?`;
        } else {
            text = `This blood bag is currently "${status}". Are you sure you want to delete it?`;
        }

        Swal.fire({
            title: 'Confirm Delete',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Cancel',
            confirmButtonText: isSafe ? 'Yes, delete it' : 'Proceed Anyway'
        }).then((result) => {

            if (!result.isConfirmed) return;

            // final AJAX call
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong'
                    });
                }
            });

        });
    });

});
</script>
@endpush
