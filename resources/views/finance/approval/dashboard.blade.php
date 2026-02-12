@extends('layouts.index')

@section('title', 'Financial Approval Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Compact, interactive header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2"
                                    style="width:48px; height:48px; background:rgba(0,123,255,.1);">
                                    <i class="fas fa-check-circle text-primary"></i>
                                </div>
                                <div class="lh-sm">
                                    <h5 class="mb-0 fw-semibold text-dark">Financial Approval Dashboard</h5>
                                    <small class="text-muted">
                                        @if($canApprove)
                                            Review and approve pending records
                                        @else
                                            View pending financial records (Approval requires Pastor/Admin access)
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Modern Summary Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100"
                                    style="background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);">
                                    <div class="card-body text-center text-white p-4">
                                        <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                            style="width: 60px; height: 60px;">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                        <h3 class="fw-bold mb-1">{{ $totalPending }}</h3>
                                        <p class="mb-0 text-white-75">Pending Records</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100"
                                    style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                    <div class="card-body text-center text-white p-4">
                                        <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                            style="width: 60px; height: 60px;">
                                            <i class="fas fa-dollar-sign fa-2x"></i>
                                        </div>
                                        <h3 class="fw-bold mb-1">TZS {{ number_format($totalPendingAmount, 0) }}</h3>
                                        <p class="mb-0 text-white-75">Pending Amount</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100"
                                    style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                    <div class="card-body text-center text-white p-4">
                                        <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                            style="width: 60px; height: 60px;">
                                            <i class="fas fa-calendar-day fa-2x"></i>
                                        </div>
                                        <h3 class="fw-bold mb-1">{{ $today->format('M d') }}</h3>
                                        <p class="mb-0 text-white-75">Today's Date</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100"
                                    style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                                    <div class="card-body text-center text-white p-4">
                                        <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                            style="width: 60px; height: 60px;">
                                            <i class="fas fa-user-tie fa-2x"></i>
                                        </div>
                                        <h3 class="fw-bold mb-1" style="font-size: 1.2rem;">
                                            {{ $approverName ?? $currentUser->name ?? auth()->user()->name }}</h3>
                                        <p class="mb-0 text-white-75">Approver</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modern Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body p-4">
                                        <h5 class="card-title mb-3 text-dark fw-bold">
                                            <i class="fas fa-bolt text-warning me-2"></i>
                                            Quick Actions
                                        </h5>
                                        <div class="d-flex flex-wrap gap-3">
                                            @if($canApprove)
                                                <button type="button"
                                                    class="btn btn-success px-4 py-2 rounded-pill shadow-sm fw-bold"
                                                    onclick="bulkApprove()">
                                                    <i class="fas fa-check-double me-2"></i>
                                                    Bulk Approve
                                                </button>
                                            @endif
                                            <button type="button"
                                                class="btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-bold"
                                                onclick="refreshData()">
                                                <i class="fas fa-sync me-2"></i>
                                                Refresh
                                            </button>
                                            <button type="button"
                                                class="btn btn-info px-4 py-2 rounded-pill shadow-sm fw-bold"
                                                onclick="exportPending()">
                                                <i class="fas fa-download me-2"></i>
                                                Export
                                            </button>
                                            <a href="{{ route('finance.approval.funding-requests') }}"
                                                class="btn btn-warning px-4 py-2 rounded-pill shadow-sm fw-bold text-white">
                                                <i class="fas fa-hand-holding-usd me-2"></i>
                                                Funding
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modern Pending Records Tabs -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white border-0 p-0">
                                        <div class="bg-light rounded-top p-4">
                                            <h5 class="mb-1 text-dark fw-bold">
                                                <i class="fas fa-list-check text-primary me-2"></i>
                                                Pending Financial Records
                                            </h5>
                                            <p class="text-muted mb-0">Review and approve pending financial transactions</p>
                                        </div>
                                        <ul class="nav nav-pills nav-fill bg-white px-4 pt-3 pb-2" id="pendingTabs"
                                            role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active rounded-pill me-2 mb-3 px-3 py-2"
                                                    id="tithes-tab" data-bs-toggle="tab" data-bs-target="#tithes"
                                                    type="button" role="tab">
                                                    <i class="fas fa-coins me-2"></i>
                                                    <span class="fw-bold">Tithes</span>
                                                    <span
                                                        class="badge bg-warning text-dark ms-2 px-2 py-1">{{ $pendingTithes->count() }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill me-2 mb-3 px-3 py-2" id="offerings-tab"
                                                    data-bs-toggle="tab" data-bs-target="#offerings" type="button"
                                                    role="tab">
                                                    <i class="fas fa-gift me-2"></i>
                                                    <span class="fw-bold">Offerings</span>
                                                    <span
                                                        class="badge bg-warning text-dark ms-2 px-2 py-1">{{ $pendingOfferings->count() }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill me-2 mb-3 px-3 py-2" id="donations-tab"
                                                    data-bs-toggle="tab" data-bs-target="#donations" type="button"
                                                    role="tab">
                                                    <i class="fas fa-heart me-2"></i>
                                                    <span class="fw-bold">Donations</span>
                                                    <span
                                                        class="badge bg-warning text-dark ms-2 px-2 py-1">{{ $pendingDonations->count() }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill me-2 mb-3 px-3 py-2" id="expenses-tab"
                                                    data-bs-toggle="tab" data-bs-target="#expenses" type="button"
                                                    role="tab">
                                                    <i class="fas fa-receipt me-2"></i>
                                                    <span class="fw-bold">Expenses</span>
                                                    <span
                                                        class="badge bg-warning text-dark ms-2 px-2 py-1">{{ $pendingExpenses->count() }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill me-2 mb-3 px-3 py-2" id="budgets-tab"
                                                    data-bs-toggle="tab" data-bs-target="#budgets" type="button" role="tab">
                                                    <i class="fas fa-wallet me-2"></i>
                                                    <span class="fw-bold">Budgets</span>
                                                    <span
                                                        class="badge bg-warning text-dark ms-2 px-2 py-1">{{ $pendingBudgets->count() }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill me-2 mb-3 px-3 py-2"
                                                    id="pledge-payments-tab" data-bs-toggle="tab"
                                                    data-bs-target="#pledge-payments" type="button" role="tab">
                                                    <i class="fas fa-handshake me-2"></i>
                                                    <span class="fw-bold">Pledge Payments</span>
                                                    <span
                                                        class="badge bg-warning text-dark ms-2 px-2 py-1">{{ $pendingPledgePayments->count() }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill me-2 mb-3 px-3 py-2"
                                                    id="community-offerings-tab" data-bs-toggle="tab"
                                                    data-bs-target="#community-offerings" type="button" role="tab">
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    <span class="fw-bold">Community Offerings</span>
                                                    <span
                                                        class="badge bg-warning text-dark ms-2 px-2 py-1">{{ $pendingCommunityOfferings->count() }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link rounded-pill me-2 mb-3 px-3 py-2"
                                                    id="funding-requests-tab" data-bs-toggle="tab"
                                                    data-bs-target="#funding-requests" type="button" role="tab">
                                                    <i class="fas fa-hand-holding-usd me-2"></i>
                                                    <span class="fw-bold">Funding Requests</span>
                                                    <span
                                                        class="badge bg-warning text-dark ms-2 px-2 py-1">{{ $pendingFundingRequests->count() }}</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="pendingTabsContent">
                                            <!-- Tithes Tab -->
                                            <div class="tab-pane fade show active" id="tithes" role="tabpanel">
                                                @include('finance.approval.partials.tithes-table', ['records' => $pendingTithes, 'canApprove' => $canApprove])
                                            </div>

                                            <!-- Offerings Tab -->
                                            <div class="tab-pane fade" id="offerings" role="tabpanel">
                                                @include('finance.approval.partials.offerings-table', ['records' => $pendingOfferings, 'canApprove' => $canApprove])
                                            </div>

                                            <!-- Donations Tab -->
                                            <div class="tab-pane fade" id="donations" role="tabpanel">
                                                @include('finance.approval.partials.donations-table', ['records' => $pendingDonations, 'canApprove' => $canApprove])
                                            </div>

                                            <!-- Expenses Tab -->
                                            <div class="tab-pane fade" id="expenses" role="tabpanel">
                                                @include('finance.approval.partials.expenses-table', ['records' => $pendingExpenses, 'canApprove' => $canApprove])
                                            </div>

                                            <!-- Budgets Tab -->
                                            <div class="tab-pane fade" id="budgets" role="tabpanel">
                                                @include('finance.approval.partials.budgets-table', ['records' => $pendingBudgets, 'canApprove' => $canApprove])
                                            </div>

                                            <!-- Pledge Payments Tab -->
                                            <div class="tab-pane fade" id="pledge-payments" role="tabpanel">
                                                @include('finance.approval.partials.pledge-payments-table', ['pendingPledgePayments' => $pendingPledgePayments, 'canApprove' => $canApprove])
                                            </div>

                                            <!-- Community Offerings Tab -->
                                            <div class="tab-pane fade" id="community-offerings" role="tabpanel">
                                                @include('finance.approval.partials.community-offerings-table', ['records' => $pendingCommunityOfferings, 'canApprove' => $canApprove])
                                            </div>

                                            <!-- Funding Requests Tab -->
                                            <div class="tab-pane fade" id="funding-requests" role="tabpanel">
                                                @include('finance.approval.partials.funding-requests-table', ['records' => $pendingFundingRequests, 'canApprove' => $canApprove])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compact Recent Approvals -->
                        @if($recentApprovals->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-gradient text-white"
                                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                                                    <i class="fas fa-history fa-lg"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0 fw-bold">Recent Approvals</h5>
                                                    <p class="mb-0 text-white-75 small">Last 7 days approved transactions</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="border-0 px-4 py-3">
                                                                <i class="fas fa-tag me-2 text-muted"></i>Type
                                                            </th>
                                                            <th class="border-0 px-4 py-3">
                                                                <i class="fas fa-user me-2 text-muted"></i>Member/Donor
                                                            </th>
                                                            <th class="border-0 px-4 py-3">
                                                                <i class="fas fa-coins me-2 text-muted"></i>Amount
                                                            </th>
                                                            <th class="border-0 px-4 py-3">
                                                                <i class="fas fa-calendar me-2 text-muted"></i>Date
                                                            </th>
                                                            <th class="border-0 px-4 py-3">
                                                                <i class="fas fa-user-check me-2 text-muted"></i>Approved By
                                                            </th>
                                                            <th class="border-0 px-4 py-3">
                                                                <i class="fas fa-clock me-2 text-muted"></i>Approved At
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($recentApprovals as $record)
                                                            <tr class="border-bottom">
                                                                <td class="px-4 py-3">
                                                                    @if($record->type == 'offering')
                                                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                                                            <i class="fas fa-gift me-1"></i>{{ ucfirst($record->type) }}
                                                                        </span>
                                                                    @elseif($record->type == 'tithe')
                                                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                                                            <i
                                                                                class="fas fa-coins me-1"></i>{{ ucfirst($record->type) }}
                                                                        </span>
                                                                    @elseif($record->type == 'donation')
                                                                        <span class="badge bg-info rounded-pill px-3 py-2">
                                                                            <i
                                                                                class="fas fa-heart me-1"></i>{{ ucfirst($record->type) }}
                                                                        </span>
                                                                    @elseif($record->type == 'expense')
                                                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                                                            <i
                                                                                class="fas fa-receipt me-1"></i>{{ ucfirst($record->type) }}
                                                                        </span>
                                                                    @elseif($record->type == 'budget')
                                                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                                                            <i
                                                                                class="fas fa-wallet me-1"></i>{{ ucfirst($record->type) }}
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                                            <i class="fas fa-file me-1"></i>{{ ucfirst($record->type) }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                            style="width: 40px; height: 40px;">
                                                                            <i class="fas fa-user text-muted"></i>
                                                                        </div>
                                                                        <div>
                                                                            <div class="fw-bold text-dark">
                                                                                @if($record->member)
                                                                                    {{ $record->member->full_name }}
                                                                                @elseif(isset($record->donor_name))
                                                                                    {{ $record->donor_name }}
                                                                                @else
                                                                                    Anonymous
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <span class="fw-bold text-success fs-6">TZS
                                                                        {{ number_format($record->amount, 0) }}</span>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <span
                                                                        class="text-muted">{{ $record->date ? \Carbon\Carbon::parse($record->date)->format('M d, Y') : '-' }}</span>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2"
                                                                            style="width: 32px; height: 32px;">
                                                                            <i class="fas fa-check text-primary"
                                                                                style="font-size: 0.8rem;"></i>
                                                                        </div>
                                                                        <span
                                                                            class="fw-medium">{{ $record->approver_display_name ?? $record->approver->name ?? 'System' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <span
                                                                        class="text-muted">{{ $record->approved_at ? \Carbon\Carbon::parse($record->approved_at)->format('M d, Y H:i') : '-' }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Record</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="approvalForm">
                        <input type="hidden" id="approvalType" name="type">
                        <input type="hidden" id="approvalId" name="id">

                        <div class="form-group">
                            <label for="approvalNotes">Approval Notes (Optional)</label>
                            <textarea class="form-control" id="approvalNotes" name="approval_notes" rows="3"
                                placeholder="Add any notes about this approval..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitApproval()">
                        <i class="fas fa-check mr-1"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Record</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="rejectionForm">
                        <input type="hidden" id="rejectionType" name="type">
                        <input type="hidden" id="rejectionId" name="id">

                        <div class="form-group">
                            <label for="rejectionReason">Rejection Reason *</label>
                            <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="3"
                                placeholder="Please provide a reason for rejection..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitRejection()">
                        <i class="fas fa-times mr-1"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDetailsModalLabel">Record Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewDetailsBody">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Custom styles for the finance approval dashboard */

        /* Header widgets */
        .dashboard-header .widget {
            transition: transform .2s ease, background .2s ease;
        }

        .dashboard-header .widget:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .dashboard-header h5 {
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .dashboard-header .header-widgets {
                width: 100%;
            }

            .dashboard-header .widget {
                flex: 1;
                justify-content: center;
            }
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .nav-pills .nav-link {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .nav-pills .nav-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            font-weight: bold !important;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .badge {
            transition: all 0.3s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        /* Animation for cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease-out;
        }

        .card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .card:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Gradient text effect */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Custom scrollbar */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }

        /* Pulse animation for badges */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .badge {
            animation: pulse 2s infinite;
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Enhanced card styling */
        .card-body {
            padding: 1.5rem !important;
        }

        .card-body.p-3 {
            padding: 1rem !important;
        }

        .card-body.p-4 {
            padding: 1.5rem !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .nav-pills .nav-link {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .card-body {
                padding: 0.75rem !important;
            }

            .card-body.p-3 {
                padding: 0.5rem !important;
            }
        }

        /* Enhanced focus states */
        .btn:focus,
        .nav-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Status indicators */
        .status-pending {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
        }

        .status-approved {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .status-rejected {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Test if JavaScript is loading
        console.log('Approval dashboard JavaScript loaded');

        // Initialize tab functionality
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Initializing tab functionality');

            // Add click event listeners to tab buttons
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    console.log('Tab clicked:', this.id, 'Target:', this.getAttribute('data-bs-target'));
                });
            });

            // Initialize Bootstrap tabs
            const tabList = document.querySelector('#pendingTabs');
            if (tabList) {
                const tab = new bootstrap.Tab(tabList.querySelector('.nav-link.active'));
                console.log('Bootstrap tabs initialized');
            }
        });

        function approveRecord(type, id) {
            console.log('Approving record:', type, id);
            document.getElementById('approvalType').value = type;
            document.getElementById('approvalId').value = id;
            document.getElementById('approvalNotes').value = '';

            // Show modal using Bootstrap 5
            const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
            modal.show();
        }

        function rejectRecord(type, id) {
            document.getElementById('rejectionType').value = type;
            document.getElementById('rejectionId').value = id;
            document.getElementById('rejectionReason').value = '';

            // Show modal using Bootstrap 5
            const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
            modal.show();
        }

        function submitApproval() {
            const form = document.getElementById('approvalForm');
            const formData = new FormData(form);

            fetch('{{ route("finance.approval.approve") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Hide modal using vanilla JS
                        const modal = document.getElementById('approvalModal');
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('error', data.message || 'An error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred while approving the record: ' + error.message);
                });
        }

        function submitRejection() {
            const form = document.getElementById('rejectionForm');
            const formData = new FormData(form);

            if (!formData.get('rejection_reason').trim()) {
                showAlert('error', 'Please provide a rejection reason');
                return;
            }

            fetch('{{ route("finance.approval.reject") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Hide modal using vanilla JS
                        const modal = document.getElementById('rejectionModal');
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('error', data.message || 'An error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred while rejecting the record: ' + error.message);
                });
        }

        function bulkApprove() {
            if (confirm('Are you sure you want to approve all pending records for today?')) {
                const records = [];

                // Collect all pending records
                document.querySelectorAll('[data-record-type]').forEach(element => {
                    records.push({
                        type: element.dataset.recordType,
                        id: element.dataset.recordId
                    });
                });

                if (records.length === 0) {
                    showAlert('info', 'No pending records to approve');
                    return;
                }

                fetch('{{ route("finance.approval.bulk-approve") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        records: records,
                        approval_notes: 'Bulk approved by ' + '{{ $approverName ?? $currentUser->name ?? auth()->user()->name }}'
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            location.reload();
                        } else {
                            showAlert('error', data.message || 'An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('error', 'An error occurred during bulk approval');
                    });
            }
        }

        function refreshData() {
            location.reload();
        }

        function exportPending() {
            window.open('{{ route("finance.approval.export-pending") }}', '_blank');
        }

        function viewDetails(type, id) {
            console.log('Viewing details for:', type, id);

            // Show loading state
            const modalBody = document.getElementById('viewDetailsBody');
            modalBody.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading details...</div>';

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
            modal.show();

            // Fetch the record details
            fetch(`/finance/approval/view-details/${type}/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch details');
                    }
                    return response.json();
                })
                .then(data => {
                    // Format dates properly
                    const formatDate = (dateString) => {
                        if (!dateString) return 'N/A';
                        try {
                            const date = new Date(dateString);
                            return date.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });
                        } catch (e) {
                            return dateString;
                        }
                    };

                    const formatDateTime = (dateString) => {
                        if (!dateString) return 'N/A';
                        try {
                            const date = new Date(dateString);
                            return date.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        } catch (e) {
                            return dateString;
                        }
                    };

                    // Format additional funding for display
                    const formatAdditionalFunding = (funding) => {
                        if (!funding || !Array.isArray(funding) || funding.length === 0) {
                            return '';
                        }

                        let html = '<div class="table-responsive mt-2">';
                        html += '<table class="table table-sm table-bordered">';
                        html += '<thead class="table-light"><tr><th>Offering Type</th><th>Amount</th></tr></thead>';
                        html += '<tbody>';

                        let total = 0;
                        funding.forEach(fund => {
                            const offeringType = (fund.offering_type || '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            const amount = parseFloat(fund.amount) || 0;
                            total += amount;
                            html += `<tr><td>${offeringType}</td><td class="text-end">TZS ${amount.toLocaleString()}</td></tr>`;
                        });

                        html += '<tr class="table-info fw-bold"><td>Total Additional Funding</td><td class="text-end">TZS ' + total.toLocaleString() + '</td></tr>';
                        html += '</tbody></table></div>';

                        return html;
                    };

                    // Populate the modal with the record details
                    modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                        <div class="mb-2"><strong>Type:</strong> ${data.type || 'N/A'}</div>
                        <div class="mb-2"><strong>Amount:</strong> ${data.amount ? `TZS ${data.amount.toLocaleString()}` : (data.total_budget ? `TZS ${data.total_budget.toLocaleString()}` : 'N/A')}</div>
                        <div class="mb-2"><strong>Date:</strong> ${formatDate(data.date)}</div>
                        <div class="mb-2"><strong>Status:</strong> <span class="badge bg-warning">Pending Approval</span></div>
                        ${data.type === 'Expense' && data.expense_category ? `<div class="mb-2"><strong>Category:</strong> ${data.expense_category.charAt(0).toUpperCase() + data.expense_category.slice(1)}</div>` : ''}
                        ${data.type === 'Expense' && data.budget_name ? `<div class="mb-2"><strong>Budget:</strong> ${data.budget_name}</div>` : ''}
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>${data.type === 'Budget' ? 'Budget Information' : (data.type === 'Expense' ? 'Expense Information' : 'Member/Donor Information')}</h6>
                        ${data.type === 'Budget' ? `
                            <div class="mb-2"><strong>Budget Name:</strong> ${data.budget_name || 'N/A'}</div>
                            <div class="mb-2"><strong>Budget Type:</strong> ${data.budget_type ? data.budget_type.charAt(0).toUpperCase() + data.budget_type.slice(1) : 'N/A'}</div>
                            <div class="mb-2"><strong>Fiscal Year:</strong> ${data.fiscal_year || 'N/A'}</div>
                        ` : data.type === 'Expense' ? `
                            ${data.vendor ? `<div class="mb-2"><strong>Vendor:</strong> ${data.vendor}</div>` : ''}
                            ${data.payment_method ? `<div class="mb-2"><strong>Payment Method:</strong> ${data.payment_method.charAt(0).toUpperCase() + data.payment_method.slice(1).replace(/_/g, ' ')}</div>` : ''}
                            ${data.reference_number ? `<div class="mb-2"><strong>Reference Number:</strong> ${data.reference_number}</div>` : ''}
                            ${data.receipt_number ? `<div class="mb-2"><strong>Receipt Number:</strong> ${data.receipt_number}</div>` : ''}
                        ` : `
                            <div class="mb-2"><strong>Name:</strong> ${data.member_name || data.donor_name || 'Anonymous'}</div>
                        `}
                        <div class="mb-2"><strong>Recorded By:</strong> ${data.recorded_by || 'System'}</div>
                        <div class="mb-2"><strong>Created:</strong> ${formatDateTime(data.created_at)}</div>
                    </div>
                </div>
                ${data.type === 'Expense' && data.additional_funding ? `
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Additional Funding Sources</h6>
                                </div>
                                <div class="card-body">
                                    ${formatAdditionalFunding(data.additional_funding)}
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Note:</strong> This expense requires additional funding sources to cover the expense amount. The above funding sources have been allocated by the user.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ` : ''}
                ${data.notes || data.description ? `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i>${data.type === 'Budget' ? 'Description' : (data.type === 'Expense' ? 'Description' : 'Notes')}</h6>
                            <p class="text-muted">${data.notes || data.description || ''}</p>
                        </div>
                    </div>
                ` : ''}
                ${data.type === 'Budget' && (data.start_date || data.end_date || data.purpose) ? `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="fas fa-calendar me-2"></i>Budget Details</h6>
                            ${data.start_date ? `<div class="mb-2"><strong>Start Date:</strong> ${data.start_date}</div>` : ''}
                            ${data.end_date ? `<div class="mb-2"><strong>End Date:</strong> ${data.end_date}</div>` : ''}
                            ${data.purpose ? `<div class="mb-2"><strong>Purpose:</strong> ${data.purpose}</div>` : ''}
                            ${data.allocated_amount !== undefined ? `<div class="mb-2"><strong>Allocated Amount:</strong> TZS ${data.allocated_amount.toLocaleString()}</div>` : ''}
                            ${data.spent_amount !== undefined ? `<div class="mb-2"><strong>Spent Amount:</strong> TZS ${data.spent_amount.toLocaleString()}</div>` : ''}
                        </div>
                    </div>
                ` : ''}
            `;
                })
                .catch(error => {
                    console.error('Error fetching details:', error);
                    modalBody.innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle mb-2"></i>
                    <p>Failed to load details. Please try again.</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('${type}', ${id})">
                        <i class="fas fa-redo me-1"></i>Retry
                    </button>
                </div>
            `;
                });
        }

        function showAlert(type, message) {
            // Create a Bootstrap alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show`;
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.minWidth = '300px';

            alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
    </script>
@endsection