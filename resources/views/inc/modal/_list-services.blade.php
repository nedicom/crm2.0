@foreach ($services as $service)
    <li class="list-group-item serviceList serviceIndex" data-service-id="{{ $service->id }}">
        <a href="#" class="text-decoration-none">
            <span class="service-name">{{ $service->name }}</span>
            @if ($service->execution_time) (<span class="service-execution-time">
                Время выполнения: {{ \App\Helpers\TaskHelper::transformDuration($service->execution_time, $service->type_execution_time)['hours'] }} час
                {{ \App\Helpers\TaskHelper::transformDuration($service->execution_time, $service->type_execution_time)['minutes'] }} мин.) </span>
            @endif
        </a>
    </li>
@endforeach
