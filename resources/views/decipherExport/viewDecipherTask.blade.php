<x-layout>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-center">{{ $task->name }}</h2>                    
            
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Back to Tasks</a>
        </div>
        @if(isset($taskSettings['survey_path']))
        <div class="datagrid-item">
            <div class="datagrid-title">Decipher path</div>
            <div class="datagrid-content">{{ $taskSettings['server'] }}/survey/{{ $taskSettings['survey_path'] }}</div>
        </div>
    @endif

        <div class="page-wrapper">
            <div class="page-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">ID</div>
                        <div class="datagrid-content">{{ $task->id }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Type</div>
                        <div class="datagrid-content">{{ $task->type }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Created by</div>
                        <div class="datagrid-content">{{ $task->creator->name }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Status</div>
                        @if($task->archived_at)
                            <span class="status status-yellow">Archived</span>
                        @elseif($task->is_active)
                            <span class="status status-green">Active</span>
                        @else
                            <span class="status">Inactive</span>
                        @endif
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Start date</div>
                        <div class="datagrid-content">{{ $task->start_date->format('d.m.Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">End date</div>
                        <div class="datagrid-content">{{ $task->end_date->format('d.m.Y') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Repeat</div>
                        <div class="datagrid-content">{{ $task->repeat }}</div>
                    </div>
                    @if(isset($taskSettings['format']))
                    <div class="datagrid-item">
                        <div class="datagrid-title">Format</div>
                        <div class="datagrid-content">{{ $taskSettings['format'] }}</div>
                    </div>
                    @endif
                    @if(isset($taskSettings['emails']))
                    <div class="datagrid-item">
                        <div class="datagrid-title">Participants</div>
                        <div class="datagrid-content">{{ $taskSettings['emails'] }}</div>
                    </div>
                    @endif
                    @if(isset($taskSettings['layout']))
                    <div class="datagrid-item">
                        <div class="datagrid-title">Layout</div>
                        <div class="datagrid-content">{{ $taskSettings['layout'] }}</div>
                    </div>
                    @endif
                    @if(isset($taskSettings['condition']))
                    <div class="datagrid-item">
                        <div class="datagrid-title">Conditions</div>
                        <div class="datagrid-content">{{ $taskSettings['condition'] }}</div>
                    </div>
                    @endif
                </div>


                <div class="d-flex flex-column flex-md-row justify-content-end align-items-center gap-3 mb-3 pt-5">

            <!-- Is Active Switch -->
            <div class="d-flex flex-column text-center">
                <div class="form-label">Is Active</div>
                <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" {{ $task->is_active ? 'checked' : '' }} disabled />
                </label>
            </div>
        
            <!-- Buttons -->
            <div class="d-flex flex-wrap gap-2">
                <div class="text-end">
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary me-2">Edit Task</a>
                    <form action="{{ route('tasks.force', $task) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Force Run Now</button>
                    </form>
                </div>
                <a href="#" class="btn btn-danger">Delete the task</a>
            </div>
        </div>
    </div>
</x-layout>