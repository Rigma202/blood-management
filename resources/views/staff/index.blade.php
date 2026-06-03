@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        My Blood Banks
    </h2>

    <div class="card">

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($bloodBanks as $bloodBank)

                    <tr>

                        <td>
                            {{ $bloodBank->name }}
                        </td>

                        <td>
                            {{ $bloodBank->location }}
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="2"
                            class="text-center">

                            No Blood Banks Assigned

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
