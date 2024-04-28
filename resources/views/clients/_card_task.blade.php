<div class="col-12 col-md-3 px-3 py-3 border border-4 border-light" style="background-color:
    @if($task->status == 'просрочена') LightCoral
    @elseif($task->status == 'в работе') MediumAquaMarine
    @elseif($task->status == 'ожидает') Cornsilk
    @else Cornsilk  @endif;">
    <div class="d-flex justify-content-between bg-white px-2 pt-1">
        <span class="fw-normal" style="font-size: 14px;!important" title="{{ $task->status }}">{{ $task->date['value'] }}</span>
        @if ($task->type == 'консультация')
            <i class="bi bi-chat-dots"  data-bs-toggle="tooltip" data-bs-placement="top" title="это консультация"></i>
        @elseif ($task->type == 'заседание')
            <i class="bi bi bi-briefcase"  data-bs-toggle="tooltip" data-bs-placement="top" title="это заседание"></i>
        @elseif ($task->type == 'допрос')
            <i class="bi bi bi-emoji-neutral"  data-bs-toggle="tooltip" data-bs-placement="top" title="это допрос"></i>
        @elseif ($task->type == 'звонок')
            <i class="bi bi-phone"  data-bs-toggle="tooltip" data-bs-placement="top" title="это звонок"></i>
        @else
            <i class="bi bi-clipboard"  data-bs-toggle="tooltip" data-bs-placement="top" title="это обычная задача"></i>
        @endif
        <input class="form-check-input checkedvipolnena" autocomplete="off" type="checkbox" value="" id="{{ $task->id }}"
            data-bs-toggle="tooltip" data-bs-placement="top" title="выполнена">
    </div>
    <div class="px-1 fw-normal bg-white border border-white"  style="min-height: 80px;overflow: hidden; position: relative;">
        <a href="/tasks/{{ $task->id }}" style="font-size: 14px;!important" target="_blank">{{ $task->name }}</a>
    </div>
</div>
