<ul class="list-group">
    @foreach ($query as $task)
        <li class="list-group-item taskList taskIndex" data-task-id="{{ $task->id }}">
            <a href="#" class="text-decoration-none">
                <span class="name-client">{{ $task->client }}</span> - {{ $task->name }} - {{ $task->created_at }}
            </a>
        </li>
    @endforeach
</ul>
