@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Tasks</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <a href="{{ route('church-elder.tasks.create', $community->id) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Task
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Task Title</th>
                                        <th>Type</th>
                                        <th>Member</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                    <tr>
                                        <td>{{ Str::limit($task->task_title, 50) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $task->task_type_display }}</span>
                                        </td>
                                        <td>
                                            @if($task->member)
                                                {{ $task->member->full_name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->task_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'pending' ? 'warning' : ($task->status === 'in_progress' ? 'info' : 'secondary')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('church-elder.tasks.show', [$community->id, $task->id]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
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
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tasks created yet.</p>
                            <a href="{{ route('church-elder.tasks.create', $community->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








