@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="card border-0 shadow-sm mb-4 analytics-hero">
        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="hero-icon d-flex align-items-center justify-content-center rounded-2">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h1 class="h5 mb-0 text-dark fw-semibold">Analytics Dashboard</h1>
                    <p class="mb-0 small text-muted">Comprehensive insights and trends</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Analytics Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header section-header-primary py-2 rounded-top">
                    <h6 class="mb-0 fw-semibold" style="color: #ffffff !important;">
                        <i class="fas fa-dollar-sign me-2" style="color: #ffffff !important;"></i>Financial Analytics
                    </h6>
                </div>
                <div class="card-body section-body">
                    <!-- Financial Summary Cards -->
                    <div class="row mb-4 g-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 analytics-kpi">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Tithes
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                TZS {{ number_format($financialData['totals']['tithes'], 2) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-money-bill-wave fa-2x text-primary opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 analytics-kpi">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Offerings
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                TZS {{ number_format($financialData['totals']['offerings'], 2) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-hand-holding-usd fa-2x text-success opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 analytics-kpi">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Donations
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                TZS {{ number_format($financialData['totals']['donations'], 2) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-heart fa-2x text-info opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 analytics-kpi">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1 kpi-net {{ $financialData['totals']['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                Net Income
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                TZS {{ number_format($financialData['totals']['net_income'], 2) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-chart-line fa-2x {{ $financialData['totals']['net_income'] >= 0 ? 'text-success' : 'text-danger' }} opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Financial Trends Chart -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-area me-2"></i>Monthly Financial Trends (Last 12 Months)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="monthlyFinancialChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Breakdown Charts -->
                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-pie me-2"></i>Income Sources Distribution
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="incomeSourcesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-bar me-2"></i>Yearly Financial Overview (Last 5 Years)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="yearlyFinancialChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Analytics Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header section-header-success py-2 rounded-top">
                    <h6 class="mb-0 fw-semibold" style="color: #ffffff !important;">
                        <i class="fas fa-users me-2" style="color: #ffffff !important;"></i>Member Analytics
                    </h6>
                </div>
                <div class="card-body section-body">
                    <!-- Member Summary Cards -->
                    <div class="row mb-4 g-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-left-primary shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Members
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($memberData['totals']['total']) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-left-info shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Male Members
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($memberData['totals']['male']) }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $memberData['totals']['total'] > 0 ? round(($memberData['totals']['male'] / $memberData['totals']['total']) * 100, 1) : 0 }}% of total
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-male fa-2x text-info opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-left-warning shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Female Members
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($memberData['totals']['female']) }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $memberData['totals']['total'] > 0 ? round(($memberData['totals']['female'] / $memberData['totals']['total']) * 100, 1) : 0 }}% of total
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-female fa-2x text-warning opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-left-success shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Children
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($memberData['totals']['children']) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-child fa-2x text-success opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Charts -->
                    <div class="row mb-4 g-3">
                        <div class="col-12 col-lg-6">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-line me-2"></i>Monthly Registration Trends
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="monthlyRegistrationsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-pie me-2"></i>Member Type Distribution
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="memberTypeChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-bar me-2"></i>Age Group Distribution
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="ageGroupChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-bar me-2"></i>Membership Type Distribution
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="membershipTypeChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Analytics Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header section-header-info py-2 rounded-top">
                    <h6 class="mb-0 fw-semibold" style="color: #ffffff !important;">
                        <i class="fas fa-user-check me-2" style="color: #ffffff !important;"></i>Attendance Analytics
                    </h6>
                </div>
                <div class="card-body section-body">
                    <!-- Attendance Summary -->
                    <div class="row mb-4 g-3">
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border-left-primary shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Attendance
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($attendanceData['total']) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-clipboard-check fa-2x text-primary opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border-left-success shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Average per Service
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($attendanceData['average_attendance'], 1) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-chart-line fa-2x text-success opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border-left-info shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Service Types
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $attendanceData['service_types']->count() }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-list fa-2x text-info opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Charts -->
                    <div class="row mb-4 g-3">
                        <div class="col-12 col-lg-6">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-line me-2"></i>Monthly Attendance Trends
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="monthlyAttendanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-pie me-2"></i>Service Type Distribution
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="serviceTypeChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Attendees -->
                    @if($attendanceData['top_attendees']->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-star me-2"></i>Top Attendees (Last 30 Days)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Member Name</th>
                                                    <th class="text-end">Attendance Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($attendanceData['top_attendees'] as $index => $attendee)
                                                <tr>
                                                    <td>
                                                        @if($index == 0)
                                                            <span class="badge bg-warning text-dark">🥇</span>
                                                        @elseif($index == 1)
                                                            <span class="badge bg-secondary">🥈</span>
                                                        @elseif($index == 2)
                                                            <span class="badge bg-info">🥉</span>
                                                        @else
                                                            <strong>#{{ $index + 1 }}</strong>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $attendee['name'] ?? 'Unknown' }}
                                                        @if(isset($attendee['type']) && $attendee['type'] === 'child')
                                                            <span class="badge bg-info ms-2">Child</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="badge bg-primary">{{ $attendee['attendance_count'] ?? 0 }}</span>
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

    <!-- Event Analytics Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header section-header-warning py-2 rounded-top">
                    <h6 class="mb-0 fw-semibold" style="color: #ffffff !important;">
                        <i class="fas fa-calendar-alt me-2" style="color: #ffffff !important;"></i>Event Analytics
                    </h6>
                </div>
                <div class="card-body section-body">
                    <!-- Event Summary Cards -->
                    <div class="row mb-4 g-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-left-primary shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Events
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($eventData['events']['total']) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-calendar fa-2x text-primary opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-left-success shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Upcoming Events
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($eventData['events']['upcoming']) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-calendar-check fa-2x text-success opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-left-info shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Celebrations
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($eventData['celebrations']['total']) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-birthday-cake fa-2x text-info opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-left-warning shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Upcoming Celebrations
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($eventData['celebrations']['upcoming']) }}
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-gift fa-2x text-warning opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Event Trends -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm rounded-3">
                                <div class="card-header bg-light header-overlay">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-chart-area me-2"></i>Monthly Events & Celebrations Trends
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="monthlyEventsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart.js default configuration
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.plugins.legend.position = 'bottom';
    
    // Color palette
    const colors = {
        primary: '#4e73df',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b',
        secondary: '#858796',
        purple: '#6f42c1',
        pink: '#e91e63'
    };

    // Monthly Financial Trends Chart
    const monthlyFinancialCtx = document.getElementById('monthlyFinancialChart');
    if (monthlyFinancialCtx) {
        const monthlyFinancialData = @json($financialData['monthly_trends']);
        new Chart(monthlyFinancialCtx, {
            type: 'line',
            data: {
                labels: monthlyFinancialData.map(d => d.month),
                datasets: [
                    {
                        label: 'Tithes',
                        data: monthlyFinancialData.map(d => d.tithes),
                        borderColor: colors.primary,
                        backgroundColor: colors.primary + '20',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Offerings',
                        data: monthlyFinancialData.map(d => d.offerings),
                        borderColor: colors.success,
                        backgroundColor: colors.success + '20',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Donations',
                        data: monthlyFinancialData.map(d => d.donations),
                        borderColor: colors.info,
                        backgroundColor: colors.info + '20',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Expenses',
                        data: monthlyFinancialData.map(d => d.expenses),
                        borderColor: colors.danger,
                        backgroundColor: colors.danger + '20',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Net Income',
                        data: monthlyFinancialData.map(d => d.net),
                        borderColor: colors.warning,
                        backgroundColor: colors.warning + '20',
                        tension: 0.4,
                        borderWidth: 3,
                        borderDash: [5, 5]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'TZS ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Income Sources Distribution Chart
    const incomeSourcesCtx = document.getElementById('incomeSourcesChart');
    if (incomeSourcesCtx) {
        const totals = @json($financialData['totals']);
        new Chart(incomeSourcesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Tithes', 'Offerings', 'Donations'],
                datasets: [{
                    data: [totals.tithes, totals.offerings, totals.donations],
                    backgroundColor: [colors.primary, colors.success, colors.info]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': TZS ' + context.parsed.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Yearly Financial Chart
    const yearlyFinancialCtx = document.getElementById('yearlyFinancialChart');
    if (yearlyFinancialCtx) {
        const yearlyData = @json($financialData['yearly_trends']);
        new Chart(yearlyFinancialCtx, {
            type: 'bar',
            data: {
                labels: yearlyData.map(d => d.year),
                datasets: [
                    {
                        label: 'Income',
                        data: yearlyData.map(d => d.income),
                        backgroundColor: colors.success
                    },
                    {
                        label: 'Expenses',
                        data: yearlyData.map(d => d.expenses),
                        backgroundColor: colors.danger
                    },
                    {
                        label: 'Net',
                        data: yearlyData.map(d => d.net),
                        backgroundColor: colors.warning
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'TZS ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Monthly Registrations Chart
    const monthlyRegistrationsCtx = document.getElementById('monthlyRegistrationsChart');
    if (monthlyRegistrationsCtx) {
        const monthlyRegData = @json($memberData['monthly_registrations']);
        new Chart(monthlyRegistrationsCtx, {
            type: 'line',
            data: {
                labels: monthlyRegData.map(d => d.month),
                datasets: [{
                    label: 'New Members',
                    data: monthlyRegData.map(d => d.count),
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Member Type Chart
    const memberTypeCtx = document.getElementById('memberTypeChart');
    if (memberTypeCtx) {
        const memberTypes = @json($memberData['member_types']);
        new Chart(memberTypeCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(memberTypes),
                datasets: [{
                    data: Object.values(memberTypes),
                    backgroundColor: [colors.primary, colors.success, colors.info, colors.warning, colors.danger]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Age Group Chart
    const ageGroupCtx = document.getElementById('ageGroupChart');
    if (ageGroupCtx) {
        const ageGroups = @json($memberData['age_groups']);
        new Chart(ageGroupCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(ageGroups),
                datasets: [{
                    label: 'Members',
                    data: Object.values(ageGroups),
                    backgroundColor: colors.primary
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Membership Type Chart
    const membershipTypeCtx = document.getElementById('membershipTypeChart');
    if (membershipTypeCtx) {
        const membershipTypes = @json($memberData['membership_types']);
        new Chart(membershipTypeCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(membershipTypes),
                datasets: [{
                    data: Object.values(membershipTypes),
                    backgroundColor: [colors.primary, colors.success, colors.warning]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Monthly Attendance Chart
    const monthlyAttendanceCtx = document.getElementById('monthlyAttendanceChart');
    if (monthlyAttendanceCtx) {
        const monthlyAttData = @json($attendanceData['monthly_trends']);
        new Chart(monthlyAttendanceCtx, {
            type: 'line',
            data: {
                labels: monthlyAttData.map(d => d.month),
                datasets: [{
                    label: 'Attendance',
                    data: monthlyAttData.map(d => d.count),
                    borderColor: colors.info,
                    backgroundColor: colors.info + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Service Type Chart
    const serviceTypeCtx = document.getElementById('serviceTypeChart');
    if (serviceTypeCtx) {
        const serviceTypes = @json($attendanceData['service_types']);
        new Chart(serviceTypeCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(serviceTypes),
                datasets: [{
                    data: Object.values(serviceTypes),
                    backgroundColor: [colors.primary, colors.success, colors.info, colors.warning]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Monthly Events Chart
    const monthlyEventsCtx = document.getElementById('monthlyEventsChart');
    if (monthlyEventsCtx) {
        const monthlyEventsData = @json($eventData['monthly_trends']);
        new Chart(monthlyEventsCtx, {
            type: 'bar',
            data: {
                labels: monthlyEventsData.map(d => d.month),
                datasets: [
                    {
                        label: 'Events',
                        data: monthlyEventsData.map(d => d.events),
                        backgroundColor: colors.primary
                    },
                    {
                        label: 'Celebrations',
                        data: monthlyEventsData.map(d => d.celebrations),
                        backgroundColor: colors.success
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>

<style>
/* Hero */
.analytics-hero{
    background: white;
}
.analytics-hero .hero-icon{
    width:48px; height:48px; background: rgba(0,123,255,.1); color:#007bff; font-size:20px; border: 2px solid #007bff;
}
.text-white-75{
    color: rgba(255,255,255,.75) !important;
}

/* Section headers */
.section-header-primary{ position:relative; background: linear-gradient(135deg, #4e73df 0%, #3b5bcc 100%) !important; color:#fff !important; }
.section-header-success{ position:relative; background: linear-gradient(135deg, #1cc88a 0%, #16a36f 100%) !important; color:#fff !important; }
.section-header-info{ position:relative; background: linear-gradient(135deg, #36b9cc 0%, #2aa2b3 100%) !important; color:#fff !important; }
.section-header-warning{ position:relative; background: linear-gradient(135deg, #f6c23e 0%, #d6a62f 100%) !important; color:#fff !important; }

.section-header-primary::before,
.section-header-success::before,
.section-header-info::before{
    content:''; position:absolute; inset:0; background: rgba(0,0,0,.06);
    border-top-left-radius: .5rem; border-top-right-radius: .5rem;
}

.section-header-primary h6,
.section-header-success h6,
.section-header-info h6,
.section-header-warning h6{ 
    color:#fff !important; 
}

.section-header-primary h6 *,
.section-header-success h6 *,
.section-header-info h6 *,
.section-header-warning h6 * {
    color:#fff !important;
}

.section-header-primary,
.section-header-success,
.section-header-info,
.section-header-warning {
    color:#fff !important;
}

.section-header-primary *,
.section-header-success *,
.section-header-info *,
.section-header-warning * {
    color:#fff !important;
}

.section-header-primary i,
.section-header-success i,
.section-header-info i,
.section-header-warning i {
    color:#fff !important;
}

/* Ensure text in headers is white, overriding any other styles */
.card-header.section-header-primary,
.card-header.section-header-success,
.card-header.section-header-info,
.card-header.section-header-warning {
    color: #fff !important;
}

.card-header.section-header-primary h6,
.card-header.section-header-success h6,
.card-header.section-header-info h6,
.card-header.section-header-warning h6 {
    color: #fff !important;
}

.card-header.section-header-primary h6.fw-semibold,
.card-header.section-header-success h6.fw-semibold,
.card-header.section-header-info h6.fw-semibold,
.card-header.section-header-warning h6.fw-semibold {
    color: #fff !important;
}

/* Force all text content to be white */
.section-header-primary,
.section-header-success,
.section-header-info,
.section-header-warning {
    color: #ffffff !important;
}

.section-header-primary h6,
.section-header-success h6,
.section-header-info h6,
.section-header-warning h6 {
    color: #ffffff !important;
}

/* Target the actual text nodes */
.section-header-primary h6,
.section-header-success h6,
.section-header-info h6,
.section-header-warning h6 {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}

/* Override any Bootstrap card-header default styles */
.card-header.section-header-primary *,
.card-header.section-header-success *,
.card-header.section-header-info *,
.card-header.section-header-warning * {
    color: #ffffff !important;
}

.section-body{ background:#fff; }

/* KPI cards */
.analytics-kpi{ border-radius: .75rem; transition: transform .15s ease, box-shadow .15s ease; }
.analytics-kpi:hover{ transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
.analytics-kpi .h5{ letter-spacing: .2px; color: #212529; font-weight: 700; }
.analytics-kpi .text-xs{ font-size: 0.7rem; font-weight: 600; letter-spacing: 0.5px; }
.analytics-kpi .text-muted{ font-size: 0.75rem; }
.kpi-net.text-success{ color:#1cc88a !important; }
.kpi-net.text-danger{ color:#e74a3b !important; }

/* Cards */
.card.rounded-3{ border-radius: .75rem; }
.card .card-header{ border-top-left-radius: .75rem !important; border-top-right-radius: .75rem !important; }

/* Charts */
.chart-container{ position: relative; height: 320px; }

/* Light header overlay for sub-card headers */
.header-overlay{ 
    position: relative; 
    background-color: #f8f9fa !important;
}
.header-overlay::before{
    content:''; position:absolute; inset:0; z-index: 0;
    background: linear-gradient(180deg, rgba(0,0,0,.03), rgba(0,0,0,.01));
    border-top-left-radius: .75rem; border-top-right-radius: .75rem;
}
.header-overlay h6,
.header-overlay h6 * {
    position: relative;
    z-index: 1;
    color: #212529 !important;
    font-weight: 700 !important;
    font-size: 1rem !important;
    letter-spacing: 0.01em;
}
.header-overlay i {
    position: relative;
    z-index: 1;
    color: #495057 !important;
    opacity: 0.9;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    /* Hero section mobile */
    .analytics-hero .card-body {
        padding: 1rem !important;
    }
    
    .analytics-hero .h5 {
        font-size: 1rem !important;
    }
    
    .analytics-hero .small {
        font-size: 0.8rem !important;
    }
    
    .hero-icon {
        width: 40px !important;
        height: 40px !important;
        font-size: 18px !important;
    }
    
    /* Section headers mobile */
    .section-header-primary,
    .section-header-success,
    .section-header-info,
    .section-header-warning {
        padding: 0.75rem !important;
    }
    
    .section-header-primary h6,
    .section-header-success h6,
    .section-header-info h6,
    .section-header-warning h6 {
        font-size: 0.9rem !important;
        margin-bottom: 0 !important;
    }
    
    /* Card body padding */
    .card-body {
        padding: 1rem !important;
    }
    
    .section-body {
        padding: 1rem !important;
    }
    
    /* KPI Cards - ensure full width on mobile */
    .col-lg-3.col-md-6,
    .col-lg-4.col-md-6 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        margin-bottom: 1rem;
    }
    
    /* KPI card content */
    .analytics-kpi .card-body {
        padding: 1rem !important;
    }
    
    .analytics-kpi .h5 {
        font-size: 1.25rem !important;
    }
    
    .analytics-kpi .text-xs {
        font-size: 0.7rem !important;
    }
    
    /* Icons */
    .fa-2x {
        font-size: 1.5em !important;
    }
    
    /* Charts */
    .chart-container{ 
        height: 280px !important;
    }
    
    /* Chart columns - stack on mobile */
    .col-lg-6 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        margin-bottom: 1.5rem;
    }
    
    /* Tables */
    .table-responsive {
        font-size: 0.875rem;
        -webkit-overflow-scrolling: touch;
        overflow-x: auto;
        overflow-y: auto;
    }
    
    .table th,
    .table td {
        padding: 0.5rem 0.5rem;
        white-space: normal;
        min-width: 80px;
    }
    
    .table th {
        font-size: 0.8rem;
        font-weight: 600;
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
    }
    
    /* Header overlays */
    .header-overlay h6 {
        font-size: 0.9rem !important;
    }
    
    /* General text adjustments */
    h6 {
        font-size: 0.9rem;
    }
    
    .h5 {
        font-size: 1.15rem !important;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    /* Hero section extra small */
    .analytics-hero .card-body {
        padding: 0.75rem !important;
    }
    
    .analytics-hero .h5 {
        font-size: 0.95rem !important;
    }
    
    .analytics-hero .small {
        font-size: 0.75rem !important;
    }
    
    .hero-icon {
        width: 36px !important;
        height: 36px !important;
        font-size: 16px !important;
    }
    
    /* Section headers */
    .section-header-primary,
    .section-header-success,
    .section-header-info,
    .section-header-warning {
        padding: 0.625rem !important;
    }
    
    .section-header-primary h6,
    .section-header-success h6,
    .section-header-info h6,
    .section-header-warning h6 {
        font-size: 0.85rem !important;
    }
    
    .section-header-primary i,
    .section-header-success i,
    .section-header-info i,
    .section-header-warning i {
        font-size: 0.9rem !important;
    }
    
    /* Card body padding */
    .card-body {
        padding: 0.75rem !important;
    }
    
    .section-body {
        padding: 0.75rem !important;
    }
    
    /* Full width cards on mobile */
    .col-lg-3,
    .col-lg-4,
    .col-lg-6,
    .col-md-6 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        margin-bottom: 1rem;
    }
    
    /* KPI Cards - better mobile layout */
    .analytics-kpi .card-body {
        padding: 0.875rem !important;
    }
    
    .analytics-kpi .d-flex {
        flex-wrap: wrap;
    }
    
    .analytics-kpi .flex-grow-1 {
        flex: 1 1 70% !important;
        min-width: 0;
    }
    
    .analytics-kpi .ms-3 {
        margin-left: 0.5rem !important;
        flex: 0 0 auto;
    }
    
    .analytics-kpi .h5 {
        font-size: 1.1rem !important;
        line-height: 1.3;
    }
    
    .analytics-kpi .text-xs {
        font-size: 0.65rem !important;
        line-height: 1.4;
    }
    
    .analytics-kpi small.text-muted {
        font-size: 0.7rem !important;
        display: block;
        margin-top: 0.25rem;
    }
    
    /* Icons smaller */
    .fa-2x {
        font-size: 1.25em !important;
    }
    
    /* Charts */
    .chart-container{ 
        height: 240px !important;
    }
    
    /* Tables */
    .table {
        font-size: 0.75rem;
    }
    
    .table-responsive {
        max-height: 350px;
        overflow-x: auto;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table th,
    .table td {
        padding: 0.5rem 0.375rem;
        font-size: 0.75rem;
        min-width: 70px;
    }
    
    .table th {
        font-size: 0.7rem;
        white-space: nowrap;
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
    }
    
    .table td {
        word-break: break-word;
    }
    
    /* Badges in tables */
    .table .badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Header overlays */
    .header-overlay {
        padding: 0.625rem !important;
    }
    
    .header-overlay h6 {
        font-size: 0.85rem !important;
    }
    
    .header-overlay i {
        font-size: 0.8rem !important;
    }
    
    /* General text */
    .h5 {
        font-size: 1.05rem !important;
    }
    
    .h6 {
        font-size: 0.85rem !important;
    }
    
    .text-xs {
        font-size: 0.65rem !important;
    }
    
    /* Border left cards */
    .border-left-primary,
    .border-left-success,
    .border-left-info,
    .border-left-warning,
    .border-left-danger {
        border-left-width: 0.2rem !important;
    }
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.text-xs {
    font-size: 0.7rem;
}

.card-header { font-weight: 600; }

/* Ensure charts are responsive */
.chart-container {
    position: relative;
    height: 300px;
}

@media (max-width: 576px) {
    .chart-container {
        height: 250px;
    }
}
</style>
@endsection
