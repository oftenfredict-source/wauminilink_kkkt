@extends('layouts.index')

@section('title', 'Enter Offering Data')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Recording Offerings: {{ $session->campus->name }}</h1>
        <span class="badge bg-secondary">{{ $session->collection_date->format('d M Y') }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('sunday-offering.update-entry', $session->id) }}" method="POST">
        @csrf

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Enter Amounts by Fellowship (Jumuiya)</h6>
                <div>
                    <button type="submit" name="save_draft" value="1" class="btn btn-sm btn-outline-primary me-2">
                        <i class="fas fa-save me-1"></i> Save Draft
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">Jumuiya</th>
                                <th class="text-center">Unity (Umoja)</th>
                                <th class="text-center">Building (Jengo)</th>
                                <th class="text-center">Pledges (Ahadi)</th>
                                <th class="text-center">Other (Nyingine)</th>
                                <th class="text-center fw-bold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($communities as $community)
                                @php
                                    $item = $minItems[$community->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="align-middle fw-bold">
                                        {{ $community->name }}
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $community->id }}][unity]"
                                            class="form-control form-control-sm offering-input"
                                            value="{{ $item ? $item->amount_unity : 0 }}" min="0" step="0.01"
                                            data-community="{{ $community->id }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $community->id }}][building]"
                                            class="form-control form-control-sm offering-input"
                                            value="{{ $item ? $item->amount_building : 0 }}" min="0" step="0.01"
                                            data-community="{{ $community->id }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $community->id }}][pledge]"
                                            class="form-control form-control-sm offering-input"
                                            value="{{ $item ? $item->amount_pledge : 0 }}" min="0" step="0.01"
                                            data-community="{{ $community->id }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $community->id }}][other]"
                                            class="form-control form-control-sm offering-input"
                                            value="{{ $item ? $item->amount_other : 0 }}" min="0" step="0.01"
                                            data-community="{{ $community->id }}">
                                    </td>
                                    <td class="fw-bold text-end">
                                        <span class="community-total" id="total-{{ $community->id }}">0</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td>TOTALS</td>
                                <td id="grand-unity">0</td>
                                <td id="grand-building">0</td>
                                <td id="grand-pledge">0</td>
                                <td id="grand-other">0</td>
                                <td id="grand-total" class="text-end pe-3 text-primary">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-end py-3">
                <button type="button" class="btn btn-secondary me-2"
                    onclick="window.location.href='{{ route('sunday-offering.index') }}'">Cancel</button>
                <button type="submit" name="save_and_submit" value="1" class="btn btn-success"
                    onclick="return confirm('Are you sure you want to submit? This will lock the session for General Secretary verification.')">
                    <i class="fas fa-check-circle me-1"></i> Submit to General Secretary
                </button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.offering-input');

            function calculateRow(communityId) {
                let total = 0;
                document.querySelectorAll(`.offering-input[data-community="${communityId}"]`).forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                document.getElementById(`total-${communityId}`).textContent = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let unity = 0, building = 0, pledge = 0, other = 0;

                document.querySelectorAll('input[name$="[unity]"]').forEach(i => unity += parseFloat(i.value) || 0);
                document.querySelectorAll('input[name$="[building]"]').forEach(i => building += parseFloat(i.value) || 0);
                document.querySelectorAll('input[name$="[pledge]"]').forEach(i => pledge += parseFloat(i.value) || 0);
                document.querySelectorAll('input[name$="[other]"]').forEach(i => other += parseFloat(i.value) || 0);

                document.getElementById('grand-unity').textContent = unity.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('grand-building').textContent = building.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('grand-pledge').textContent = pledge.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('grand-other').textContent = other.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('grand-total').textContent = (unity + building + pledge + other).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            inputs.forEach(input => {
                input.addEventListener('input', function () {
                    calculateRow(this.dataset.community);
                });
                // Initial calc
                calculateRow(input.dataset.community);
            });
        });
    </script>
@endsection