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
                        <th>Collection Date</th>
                        <th>Expiry Date</th>
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
                            <td>{{ $bag->collection_date->format('d M Y') }}</td>
                            <td>{{ $bag->expiry_date->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('blood-bags.edit', $bag->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('blood-bags.destroy', $bag->id) }}"
                                      method="POST"
                                      style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure?')">
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