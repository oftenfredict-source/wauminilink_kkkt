@extends('layouts.index')

@section('title', 'Pastor Dashboard')

@section('content')
<div class="container-fluid">
	<!-- Compact, interactive header -->
	<div class="row mb-3">
		<div class="col-12">
			<div class="card border-0 shadow-sm dashboard-header" style="background:white;">
				<div class="card-body py-2 px-3">
					<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 gap-md-3">
						<div class="d-flex align-items-center gap-2 gap-md-3">
							<div class="dashboard-profile-img">
								@if($pastor && $pastor->member->profile_picture)
									<img src="{{ asset('storage/' . $pastor->member->profile_picture) }}" alt="Pastor Profile" class="rounded-circle border border-primary border-2" style="width:48px; height:48px; object-fit:cover;">
								@else
									<div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2" style="width:48px; height:48px; background:rgba(0,123,255,.1);">
										<i class="fas fa-user-tie text-primary"></i>
									</div>
								@endif
							</div>
							<div class="lh-sm dashboard-welcome-text">
								@if($pastor && $pastor->member)
									<h5 class="mb-0 fw-semibold text-dark dashboard-title">Welcome, Pastor {{ $pastor->member->full_name }}</h5>
									<small class="text-muted dashboard-subtitle">{{ $pastor->position_display }}</small>
								@else
									<h5 class="mb-0 fw-semibold text-dark dashboard-title">Welcome, Pastor</h5>
									<small class="text-muted dashboard-subtitle">Dashboard overview</small>
								@endif
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
                <div class="card-body p-3">
                    <!-- Modern Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);">
                                <div class="card-body text-center text-white p-3">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 summary-icon" style="width: 50px; height: 50px;">
                                        <i class="fas fa-clock fa-lg"></i>
                                    </div>
                                    <h4 class="fw-bold mb-1 summary-number">{{ $pendingTithes + $pendingOfferings + $pendingDonations + $pendingExpenses + $pendingBudgets + $pendingPledges + ($pendingPledgePayments ?? 0) }}</h4>
                                    <p class="mb-0 text-white-75 small summary-label">Pending Records</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                <div class="card-body text-center text-white p-3">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 summary-icon" style="width: 50px; height: 50px;">
                                        <i class="fas fa-coins fa-lg"></i>
                                    </div>
                                    <h4 class="fw-bold mb-1 summary-number">TZS {{ number_format($pendingAmount, 0) }}</h4>
                                    <p class="mb-0 text-white-75 small summary-label">Pending Amount</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                <div class="card-body text-center text-white p-3">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 summary-icon" style="width: 50px; height: 50px;">
                                        <i class="fas fa-users fa-lg"></i>
                                    </div>
                                    <h4 class="fw-bold mb-1 summary-number">{{ $totalMembers }}</h4>
                                    <p class="mb-0 text-white-75 small summary-label">Total Members</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                                <div class="card-body text-center text-white p-3">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 summary-icon" style="width: 50px; height: 50px;">
                                        <i class="fas fa-chart-line fa-lg"></i>
                                    </div>
                                    <h4 class="fw-bold mb-1 summary-number">TZS {{ number_format($totalIncome, 0) }}</h4>
                                    <p class="mb-0 text-white-75 small summary-label">Monthly Income</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Financial Overview -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-3 text-dark">
                                        <i class="fas fa-chart-pie text-primary me-2"></i>
                                        Monthly Financial Overview
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center">
                                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 financial-icon" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-coins fa-lg text-success"></i>
                                                </div>
                                                <h6 class="fw-bold text-success financial-amount">TZS {{ number_format($monthlyTithes, 0) }}</h6>
                                                <small class="text-muted financial-label">Tithes</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center">
                                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 financial-icon" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-gift fa-lg text-info"></i>
                                                </div>
                                                <h6 class="fw-bold text-info financial-amount">TZS {{ number_format($monthlyOfferings, 0) }}</h6>
                                                <small class="text-muted financial-label">Offerings</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center">
                                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 financial-icon" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-heart fa-lg text-info"></i>
                                                </div>
                                                <h6 class="fw-bold text-info financial-amount">TZS {{ number_format($monthlyDonations, 0) }}</h6>
                                                <small class="text-muted financial-label">Donations</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center">
                                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 financial-icon" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-handshake fa-lg text-warning"></i>
                                                </div>
                                                <h6 class="fw-bold text-warning financial-amount">TZS {{ number_format($monthlyPledges, 0) }}</h6>
                                                <small class="text-muted financial-label">Pledges</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center">
                                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 financial-icon" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-receipt fa-lg text-danger"></i>
                                                </div>
                                                <h6 class="fw-bold text-danger financial-amount">TZS {{ number_format($monthlyExpenses, 0) }}</h6>
                                                <small class="text-muted financial-label">Expenses</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center">
                                                <div class="bg-{{ $netIncome >= 0 ? 'success' : 'danger' }} bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 financial-icon" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-chart-line fa-lg text-{{ $netIncome >= 0 ? 'success' : 'danger' }}"></i>
                                                </div>
                                                <h6 class="fw-bold text-{{ $netIncome >= 0 ? 'success' : 'danger' }} financial-amount">TZS {{ number_format($netIncome, 0) }}</h6>
                                                <small class="text-muted financial-label">Net Income</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-3 text-dark">
                                        <i class="fas fa-bolt text-warning me-2"></i>
                                        Quick Actions
                                    </h6>
                                    <div class="d-flex flex-wrap gap-2 quick-actions">
                                        <a href="{{ route('finance.approval.dashboard') }}" class="btn btn-warning px-3 py-2 rounded-pill shadow-sm quick-action-btn">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <span class="fw-bold">Review Pending</span>
                                        </a>
                                        <a href="{{ route('finance.dashboard') }}" class="btn btn-primary px-3 py-2 rounded-pill shadow-sm quick-action-btn">
                                            <i class="fas fa-chart-line me-2"></i>
                                            <span class="fw-bold">Finance Dashboard</span>
                                        </a>
                                        <a href="{{ route('reports.index') }}" class="btn btn-success px-3 py-2 rounded-pill shadow-sm quick-action-btn">
                                            <i class="fas fa-chart-pie me-2"></i>
                                            <span class="fw-bold">Reports</span>
                                        </a>
                                        <a href="{{ route('members.view') }}" class="btn btn-info px-3 py-2 rounded-pill shadow-sm quick-action-btn">
                                            <i class="fas fa-users me-2"></i>
                                            <span class="fw-bold">Members</span>
                                        </a>
                                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary px-3 py-2 rounded-pill shadow-sm quick-action-btn">
                                            <i class="fas fa-calendar-check me-2"></i>
                                            <span class="fw-bold">Attendance</span>
                                        </a>
                                        <a href="{{ route('special.events.index') }}" class="btn btn-dark px-3 py-2 rounded-pill shadow-sm quick-action-btn">
                                            <i class="fas fa-calendar-plus me-2"></i>
                                            <span class="fw-bold">Events</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Pending Records Breakdown -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0 p-0">
                                    <div class="bg-light rounded-top p-3">
                                        <h6 class="mb-0 text-dark fw-bold">
                                            <i class="fas fa-list-check text-primary me-2"></i>
                                            Pending Records Breakdown
                                        </h6>
                                        <p class="text-muted mb-0 mt-1 small">Detailed view of pending financial records by category</p>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center p-3 border rounded-3 bg-warning bg-opacity-10">
                                                <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 pending-icon" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-coins fa-lg text-warning"></i>
                                                </div>
                                                <h5 class="fw-bold text-warning mb-1 pending-number">{{ $pendingTithes }}</h5>
                                                <small class="text-muted pending-label">Tithes</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center p-3 border rounded-3 bg-info bg-opacity-10">
                                                <div class="bg-info bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 pending-icon" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-gift fa-lg text-info"></i>
                                                </div>
                                                <h5 class="fw-bold text-info mb-1 pending-number">{{ $pendingOfferings }}</h5>
                                                <small class="text-muted pending-label">Offerings</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center p-3 border rounded-3 bg-info bg-opacity-10">
                                                <div class="bg-info bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 pending-icon" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-heart fa-lg text-info"></i>
                                                </div>
                                                <h5 class="fw-bold text-info mb-1 pending-number">{{ $pendingDonations }}</h5>
                                                <small class="text-muted pending-label">Donations</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center p-3 border rounded-3 bg-danger bg-opacity-10">
                                                <div class="bg-danger bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 pending-icon" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-receipt fa-lg text-danger"></i>
                                                </div>
                                                <h5 class="fw-bold text-danger mb-1 pending-number">{{ $pendingExpenses }}</h5>
                                                <small class="text-muted pending-label">Expenses</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center p-3 border rounded-3 bg-secondary bg-opacity-10">
                                                <div class="bg-secondary bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 pending-icon" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-wallet fa-lg text-secondary"></i>
                                                </div>
                                                <h5 class="fw-bold text-secondary mb-1 pending-number">{{ $pendingBudgets }}</h5>
                                                <small class="text-muted pending-label">Budgets</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center p-3 border rounded-3 bg-success bg-opacity-10">
                                                <div class="bg-success bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 pending-icon" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-handshake fa-lg text-success"></i>
                                                </div>
                                                <h5 class="fw-bold text-success mb-1 pending-number">{{ $pendingPledges }}</h5>
                                                <small class="text-muted pending-label">Pledges</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                                            <div class="text-center p-3 border rounded-3 bg-info bg-opacity-10">
                                                <div class="bg-info bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-2 pending-icon" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-money-bill-wave fa-lg text-info"></i>
                                                </div>
                                                <h5 class="fw-bold text-info mb-1 pending-number">{{ $pendingPledgePayments ?? 0 }}</h5>
                                                <small class="text-muted pending-label">Pledge Payments</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Recent Approvals -->
                    @if($recentApprovals->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
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
                                                        @if(strtolower($record->type) == 'offering')
                                                            <span class="badge rounded-pill px-3 py-2" style="background-color: #0d6efd; color: white;">
                                                                <i class="fas fa-gift me-1"></i>{{ ucfirst($record->type) }}
                                                            </span>
                                                        @elseif(strtolower($record->type) == 'tithe')
                                                            <span class="badge rounded-pill px-3 py-2" style="background-color: #198754; color: white;">
                                                                <i class="fas fa-coins me-1"></i>{{ ucfirst($record->type) }}
                                                            </span>
                                                        @elseif(strtolower($record->type) == 'donation')
                                                            <span class="badge rounded-pill px-3 py-2" style="background-color: #0dcaf0; color: white;">
                                                                <i class="fas fa-heart me-1"></i>{{ ucfirst($record->type) }}
                                                            </span>
                                                        @elseif(strtolower($record->type) == 'expense')
                                                            <span class="badge rounded-pill px-3 py-2" style="background-color: #dc3545; color: white;">
                                                                <i class="fas fa-receipt me-1"></i>{{ ucfirst($record->type) }}
                                                            </span>
                                                        @elseif(strtolower($record->type) == 'budget')
                                                            <span class="badge rounded-pill px-3 py-2" style="background-color: #ffc107; color: #000;">
                                                                <i class="fas fa-wallet me-1"></i>{{ ucfirst($record->type) }}
                                                            </span>
                                                        @elseif(strtolower($record->type) == 'pledge')
                                                            <span class="badge rounded-pill px-3 py-2" style="background-color: #6f42c1; color: white;">
                                                                <i class="fas fa-handshake me-1"></i>{{ ucfirst($record->type) }}
                                                            </span>
                                                        @elseif(strtolower($record->type) == 'pledge payment')
                                                            <span class="badge rounded-pill px-3 py-2" style="background-color: #20c997; color: white;">
                                                                <i class="fas fa-money-bill-wave me-1"></i>{{ ucfirst($record->type) }}
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill px-3 py-2" style="background-color: #6c757d; color: white;">
                                                                <i class="fas fa-file me-1"></i>{{ ucfirst($record->type) }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
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
                                                        <span class="fw-bold text-success fs-6">TZS {{ number_format($record->amount, 0) }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="text-muted">{{ $record->date ? \Carbon\Carbon::parse($record->date)->format('M d, Y') : '-' }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                                <i class="fas fa-check text-primary" style="font-size: 0.8rem;"></i>
                                                            </div>
                                                            <span class="fw-medium">{{ $record->approver_display_name ?? ($record->approver->name ?? 'System') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="text-muted">{{ $record->approved_at ? \Carbon\Carbon::parse($record->approved_at)->format('M d, Y H:i') : '-' }}</span>
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
@endsection

@section('styles')
<style>
    /* Custom styles for the pastor dashboard */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
    
    .card:nth-child(1) { animation-delay: 0.1s; }
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.3s; }
    .card:nth-child(4) { animation-delay: 0.4s; }
    
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
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .badge {
        animation: pulse 2s infinite;
    }
    
    /* Header widgets */
    .dashboard-header .widget{
        transition: transform .2s ease, background .2s ease;
    }

    .dashboard-header .widget:hover{
        transform: translateY(-2px);
        opacity: 0.9;
    }

    .dashboard-header h5{ font-weight:600; }

    @media (max-width: 576px){
        .dashboard-header .header-widgets{ width:100%; }
        .dashboard-header .widget{ flex:1; justify-content:center; }
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 0.25rem !important;
        }
        
        /* Header adjustments */
        .dashboard-header .card-body {
            padding: 0.75rem !important;
        }
        
        .dashboard-title {
            font-size: 1rem !important;
            line-height: 1.3;
        }
        
        .dashboard-subtitle {
            font-size: 0.8rem !important;
        }
        
        .dashboard-profile-img img,
        .dashboard-profile-img .rounded-circle {
            width: 40px !important;
            height: 40px !important;
        }
        
        .dashboard-welcome-text {
            flex: 1;
            min-width: 0;
        }
        
        /* Summary cards adjustments */
        .summary-icon {
            width: 45px !important;
            height: 45px !important;
        }
        
        .summary-number {
            font-size: 1.3rem !important;
        }
        
        .summary-label {
            font-size: 0.8rem !important;
        }
        
        /* Financial overview icons smaller */
        .financial-icon {
            width: 50px !important;
            height: 50px !important;
        }
        
        .financial-amount {
            font-size: 0.9rem !important;
            word-break: break-word;
        }
        
        .financial-label {
            font-size: 0.75rem !important;
        }
        
        /* Quick actions buttons */
        .quick-actions {
            flex-direction: column;
        }
        
        .quick-action-btn {
            width: 100%;
            padding: 0.6rem 1rem !important;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            justify-content: center;
        }
        
        .quick-action-btn .fw-bold {
            font-size: 0.85rem;
        }
        
        /* Card body padding */
        .card-body {
            padding: 0.75rem !important;
        }
        
        .card-body.p-3 {
            padding: 0.75rem !important;
        }
        
        /* Card header adjustments */
        .card-header {
            padding: 0.75rem !important;
        }
        
        .card-header h5,
        .card-header h6 {
            font-size: 0.95rem;
        }
        
        .card-header .small {
            font-size: 0.75rem;
        }
        
        /* Financial overview section */
        .row.g-3 > .col-lg-2 {
            margin-bottom: 0.75rem;
        }
        
        /* Pending breakdown section */
        .row.g-3 > .col-lg-2.col-md-4 {
            margin-bottom: 0.75rem;
        }
        
        /* Better spacing */
        .mb-4 {
            margin-bottom: 1rem !important;
        }
        
        .mb-3 {
            margin-bottom: 0.75rem !important;
        }
        
        /* Table responsive */
        .table-responsive {
            font-size: 0.875rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.375rem;
            margin: 0 -0.75rem;
            padding: 0 0.75rem;
        }
        
        .table {
            min-width: 600px;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.5rem;
            white-space: nowrap;
            vertical-align: middle;
            font-size: 0.8rem;
        }
        
        .table th {
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        /* Hide some table columns on mobile */
        .table th:nth-child(5),
        .table td:nth-child(5),
        .table th:nth-child(6),
        .table td:nth-child(6) {
            display: none; /* Hide Approved By and Approved At on mobile */
        }
        
        /* Better badge display on mobile */
        .table .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            white-space: nowrap;
        }
        
        /* Better user icon display */
        .table .bg-light.rounded-circle {
            width: 32px !important;
            height: 32px !important;
        }
        
        .table .bg-primary.bg-opacity-10.rounded-circle {
            width: 28px !important;
            height: 28px !important;
        }
        
        /* Better touch targets for mobile */
        .table tbody tr {
            min-height: 48px;
        }
        
        /* Pending breakdown cards */
        .pending-icon {
            width: 40px !important;
            height: 40px !important;
        }
        
        .pending-number {
            font-size: 1.1rem !important;
        }
        
        .pending-label {
            font-size: 0.7rem !important;
        }
        
        /* Smaller icons */
        .fa-lg {
            font-size: 1em !important;
        }
        
        /* Smaller headings */
        h4 {
            font-size: 1.25rem;
        }
        
        h5 {
            font-size: 1.1rem;
        }
        
        h6 {
            font-size: 0.95rem;
        }
    }
    
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            padding-top: 0.15rem !important;
        }
        
        /* Header adjustments */
        .dashboard-header .card-body {
            padding: 0.5rem !important;
        }
        
        .dashboard-title {
            font-size: 0.9rem !important;
            line-height: 1.2;
            word-break: break-word;
        }
        
        .dashboard-subtitle {
            font-size: 0.7rem !important;
        }
        
        .dashboard-profile-img img,
        .dashboard-profile-img .rounded-circle {
            width: 36px !important;
            height: 36px !important;
        }
        
        .dashboard-welcome-text {
            flex: 1;
            min-width: 0;
        }
        
        .dashboard-profile-img {
            flex-shrink: 0;
        }
        
        /* Summary cards adjustments */
        .summary-icon {
            width: 40px !important;
            height: 40px !important;
        }
        
        .summary-number {
            font-size: 1.1rem !important;
        }
        
        .summary-label {
            font-size: 0.7rem !important;
        }
        
        /* Financial overview - smaller icons */
        .financial-icon {
            width: 45px !important;
            height: 45px !important;
        }
        
        .financial-amount {
            font-size: 0.8rem !important;
        }
        
        .financial-label {
            font-size: 0.65rem !important;
        }
        
        /* Quick actions stack */
        .quick-actions {
            flex-direction: column;
            gap: 0.5rem !important;
        }
        
        .quick-action-btn {
            width: 100%;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        
        .quick-action-btn .fw-bold {
            font-size: 0.8rem;
        }
        
        .quick-action-btn i {
            font-size: 0.9rem;
        }
        
        /* Table improvements */
        .table-responsive {
            font-size: 0.75rem;
            margin: 0 -0.5rem;
            padding: 0 0.5rem;
        }
        
        .table {
            min-width: 550px;
        }
        
        .table th,
        .table td {
            padding: 0.4rem 0.3rem;
            font-size: 0.7rem;
        }
        
        .table th {
            font-weight: 600;
            font-size: 0.7rem;
        }
        
        /* Hide more columns on very small screens */
        .table th:nth-child(4),
        .table td:nth-child(4) {
            display: none; /* Hide Date column on very small screens */
        }
        
        /* Better badge display on mobile */
        .table .badge {
            display: inline-block;
            white-space: nowrap;
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
        
        /* Better user icon display */
        .table .bg-light.rounded-circle {
            width: 28px !important;
            height: 28px !important;
        }
        
        .table .bg-primary.bg-opacity-10.rounded-circle {
            width: 24px !important;
            height: 24px !important;
        }
        
        .table .bg-light.rounded-circle i {
            font-size: 0.7rem;
        }
        
        .table .bg-primary.bg-opacity-10.rounded-circle i {
            font-size: 0.6rem;
        }
        
        /* Card header adjustments */
        .card-header h5,
        .card-header h6 {
            font-size: 0.9rem;
        }
        
        .card-body h6 {
            font-size: 0.85rem;
        }
        
        /* Smaller text */
        .small {
            font-size: 0.7rem;
        }
        
        /* Badge adjustments */
        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Pending breakdown cards */
        .pending-icon {
            width: 35px !important;
            height: 35px !important;
        }
        
        .pending-number {
            font-size: 1rem !important;
        }
        
        .pending-label {
            font-size: 0.65rem !important;
        }
        
        h4 {
            font-size: 1.1rem;
        }
        
        h5 {
            font-size: 1rem;
        }
        
        h6 {
            font-size: 0.85rem;
        }
        
        /* Card padding adjustments */
        .card-body.p-3 {
            padding: 0.5rem !important;
        }
        
        /* Better spacing for mobile */
        .row.g-3 {
            --bs-gutter-y: 0.75rem;
        }
    }
    
    /* Extra small devices (phones in portrait, less than 400px) */
    @media (max-width: 400px) {
        .container-fluid {
            padding-left: 0.25rem !important;
            padding-right: 0.25rem !important;
        }
        
        .summary-number {
            font-size: 1rem !important;
        }
        
        .financial-amount {
            font-size: 0.75rem !important;
        }
        
        .pending-number {
            font-size: 0.9rem !important;
        }
        
        .quick-action-btn {
            font-size: 0.8rem !important;
            padding: 0.45rem 0.5rem !important;
        }
    }
    
    /* Enhanced focus states */
    .btn:focus {
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
    
    /* Gradient text effect */
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Print Styles */
    @media print {
        /* Hide header, sidebar, and footer */
        .sb-topnav,
        #layoutSidenav_nav,
        .sb-sidenav,
        footer,
        #sidebarToggle,
        .navbar-nav,
        .btn,
        .quick-actions,
        .quick-action-btn {
            display: none !important;
        }
        
        /* Adjust layout for printing */
        body {
            margin: 0;
            padding: 0;
            background: white !important;
        }
        
        #layoutSidenav {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        #layoutSidenav_content {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        main {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Remove container padding and margins */
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        /* Remove shadows and borders for cleaner print */
        .card,
        .shadow-sm {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }
        
        /* Optimize dashboard header for print */
        .dashboard-header {
            margin-bottom: 0.5rem !important;
            padding: 0.5rem !important;
            border: 1px solid #ddd !important;
        }
        
        .dashboard-header .card-body {
            padding: 0.5rem !important;
        }
        
        /* Reduce spacing between sections */
        .row {
            margin-bottom: 0.5rem !important;
        }
        
        .mb-3,
        .mb-4 {
            margin-bottom: 0.5rem !important;
        }
        
        /* Optimize summary cards for print */
        .row.g-3 {
            margin-bottom: 0.5rem !important;
        }
        
        .row.g-3 > div {
            margin-bottom: 0.5rem !important;
        }
        
        /* Remove gradients, use solid colors for print */
        .card[style*="gradient"] {
            background: #f8f9fa !important;
            color: #000 !important;
        }
        
        .card[style*="gradient"] .text-white {
            color: #000 !important;
        }
        
        /* Optimize card body padding */
        .card-body {
            padding: 0.5rem !important;
        }
        
        .card-body.p-3 {
            padding: 0.5rem !important;
        }
        
        /* Reduce icon sizes */
        .summary-icon,
        .financial-icon,
        .pending-icon {
            width: 35px !important;
            height: 35px !important;
        }
        
        /* Optimize font sizes for print */
        h4 {
            font-size: 1.1rem !important;
        }
        
        h5 {
            font-size: 1rem !important;
        }
        
        h6 {
            font-size: 0.9rem !important;
        }
        
        .summary-number {
            font-size: 1.1rem !important;
        }
        
        .financial-amount {
            font-size: 0.85rem !important;
        }
        
        .pending-number {
            font-size: 1rem !important;
        }
        
        /* Optimize table for print */
        .table-responsive {
            overflow: visible !important;
            font-size: 0.75rem !important;
        }
        
        .table {
            font-size: 0.75rem !important;
            width: 100% !important;
        }
        
        .table th,
        .table td {
            padding: 0.25rem 0.5rem !important;
            border: 1px solid #ddd !important;
        }
        
        .table thead {
            background: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Show all table columns in print */
        .table th,
        .table td {
            display: table-cell !important;
        }
        
        /* Remove background colors that don't print well */
        .bg-opacity-10,
        .bg-opacity-20 {
            background: #f8f9fa !important;
        }
        
        /* Ensure text is black for readability */
        .text-muted {
            color: #333 !important;
        }
        
        /* Page break controls */
        .card {
            page-break-inside: avoid;
        }
        
        .row {
            page-break-inside: avoid;
        }
        
        /* Remove unnecessary spacing */
        .gap-2,
        .gap-3 {
            gap: 0.25rem !important;
        }
        
        /* Optimize summary cards (4 items) */
        .row.g-3 > .col-lg-3.col-md-6.col-12 {
            width: 25% !important;
            flex: 0 0 25% !important;
            max-width: 25% !important;
        }
        
        /* Optimize financial overview section (6 items) */
        .row.g-3 > .col-lg-2.col-md-4.col-sm-6.col-6 {
            width: 16.666667% !important;
            flex: 0 0 16.666667% !important;
            max-width: 16.666667% !important;
        }
        
        /* Ensure all content fits on one page */
        @page {
            margin: 0.5cm;
            size: A4;
        }
        
        /* Hide decorative elements */
        .fa-lg,
        .fa {
            font-size: 0.8em !important;
        }
        
        /* Ensure badges are visible */
        .badge {
            background: #f8f9fa !important;
            color: #000 !important;
            border: 1px solid #ddd !important;
            padding: 0.2rem 0.4rem !important;
        }
        
        /* Remove rounded corners for cleaner print */
        .rounded,
        .rounded-3,
        .rounded-pill,
        .rounded-circle {
            border-radius: 0 !important;
        }
        
        /* Optimize pending breakdown cards */
        .col-lg-2.col-md-4.col-sm-6.col-6 {
            width: 14.285714% !important;
            flex: 0 0 14.285714% !important;
            max-width: 14.285714% !important;
        }
        
        /* Remove card header gradients */
        .card-header[style*="gradient"] {
            background: #f8f9fa !important;
            color: #000 !important;
            border-bottom: 2px solid #ddd !important;
        }
        
        .card-header .text-white {
            color: #000 !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pastor dashboard loaded successfully');
});
</script>
@endsection


