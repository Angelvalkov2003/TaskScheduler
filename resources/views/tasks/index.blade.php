<x-layout>
    @auth
    <div class="container mt-5">
        <h2 class="text-center">Dashboard</h2>
        <div class="d-flex justify-content-between mb-3">
            <h4>Tasks:</h4>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    New Task
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('decipherExport.createDecipherTask') }}">Decipher Auto Export</a></li>
                    <li><a class="dropdown-item" href="#">Confirmit Auto Export</a></li>
                    <li><a class="dropdown-item" href="#">Task 3</a></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Start date</th>
                                <th>End date</th>
                                <th>Status</th>
                                <th>Created by</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr>
                                <td>{{ $task->id }}</td>
                                <td>{{ $task->type }}</td>
                                <td>{{ $task->name }}</td>
                                <td>{{ $task->start_date->format('d.m.Y') }}</td>
                                <td>{{ $task->end_date->format('d.m.Y') }}</td>
                                <td>
                                    @if($task->archived_at)
                                        <span class="status status-yellow">Archived</span>
                                    @elseif($task->is_active)
                                        <span class="status status-green">Active</span>
                                    @else
                                        <span class="status">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $task->creator->username }}</td>
                                <td>
                                    <a href="{{ route('decipherExport.createDecipherTask', ['task' => $task->id]) }}">
                                        <button class="btn btn-primary btn-sm">View details</button>
                                    </a>
                                    <form action="{{ route('tasks.force', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Force</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endauth
    </div>
</x-layout>