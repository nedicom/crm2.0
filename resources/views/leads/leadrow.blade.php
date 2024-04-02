<tr class="table-
    @if($el->status == 'поступил') table-danger
    @elseif($el->status == 'в работе') table-success
    @elseif($el->status == 'конвертирован') table-info
    @elseif($el->status == 'ожидает') table-primary
    @elseif($el->status == 'удален') table-secondary
    @else table-light
    @endif
">
    <td></td>
    <td>{{$el->created_at}}</td>
    <td>{{$el->description}}</td>
    <td>{{$el->name}}</td>
    <td>{{$el->phone}}</td>
    <td>консультация</td>
    <td>{{$el->action}}</td>
    <td>
        @foreach ($datalawyers as $ellawyer)
            @if ($ellawyer->id == $el->responsible)
                <img src="{{$ellawyer->avatar}}" style="width: 40px;  height:40px" class="rounded-circle"
                    data-toggle="tooltip" title="{{$ellawyer->name}}">
            @endif
        @endforeach
    </td>
    <td>
        @foreach ($datalawyers as $ellawyer)
            @if ($ellawyer->id == $el->lawyer)
                <img src="{{$ellawyer->avatar}}" style="width: 40px;  height:40px" class="rounded-circle"
                    data-toggle="tooltip" title="{{$ellawyer->name}}">
            @endif
        @endforeach
    </td>
    <td>{{$el->source}}</td>

    <td>
        <a class="btn btn-light w-100" href="{{ route ('showLeadById', $el->id) }}" style="position: relative">
            <i class="bi-three-dots"></i>
            @if ($countTasks = $el->tasks()->where('status', '!=', \App\Models\Tasks::STATUS_COMPLETE)->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{$countTasks}}
                </span>
            @endif
        </a>
    </td>
</tr>
