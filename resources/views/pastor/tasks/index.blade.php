@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>All Tasks</h1>
                            <p class="text-muted mb-0">View all tasks from Evangelism Leaders and Church Elders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Evangelism Leader Tasks -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Evangelism Leader Tasks</h5>
                </div>
                <div class="card-body">
                    @if($evangelismTasks->count() > 0)
                        <div class="list-group">
                            @foreach($evangelismTasks as $task)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $task->task_title }}</h6>
                                            <p class="mb-1 small text-muted">{{ Str::limit($task->description, 150) }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>{{ $task->evangelismLeader->name ?? 'N/A' }}
                                                @if($task->community)
                                                    | <i class="fas fa-map-marker-alt me-1"></i>{{ $task->community->name }}
                                                @endif
                                                @if($task->member)
                                                    | <i class="fas fa-user-circle me-1"></i>{{ $task->member->full_name }}
                                                @endif
                                                | <i class="fas fa-calendar me-1"></i>{{ $task->task_date->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }} mb-2 d-block">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            <a href="{{ route('pastor.tasks.show-evangelism', $task->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $evangelismTasks->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No tasks from Evangelism Leaders</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Church Elder Tasks -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>Church Elder Tasks</h5>
                </div>
                <div class="card-body">
                    @if($churchElderTasks->count() > 0)
                        <div class="list-group">
                            @foreach($churchElderTasks as $task)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $task->task_title }}</h6>
                                            <p class="mb-1 small text-muted">{{ Str::limit($task->description, 150) }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>{{ $task->churchElder->name ?? 'N/A' }}
                                                @if($task->community)
                                                    | <i class="fas fa-map-marker-alt me-1"></i>{{ $task->community->name }}
                                                @endif
                                                @if($task->member)
                                                    | <i class="fas fa-user-circle me-1"></i>{{ $task->member->full_name }}
                                                @endif
                                                | <i class="fas fa-calendar me-1"></i>{{ $task->task_date->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }} mb-2 d-block">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            <a href="{{ route('pastor.tasks.show-elder', $task->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $churchElderTasks->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No tasks from Church Elders</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

