@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-success"></i>Church Elder Tasks</h1>
                            <p class="text-muted mb-0">Tasks reported by Church Elders in {{ $campus->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>All Church Elder Tasks</h5>
                </div>
                <div class="card-body">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Task Title</th>
                                        <th>Church Elder</th>
                                        <th>Community</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>
                                                <strong>{{ $task->task_title }}</strong>
                                                @if($task->pastor_comments)
                                                    <i class="fas fa-comment text-primary ms-2" title="Pastor has commented"></i>
                                                @endif
                                            </td>
                                            <td>{{ $task->churchElder->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $task->community->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $task->task_type_display }}</span>
                                            </td>
                                            <td>{{ $task->task_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('evangelism-leader.church-elder-tasks.show', $task->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No tasks reported by Church Elders yet
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



