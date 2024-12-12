<div class='col-md-12 my-2 leadcard'>
  <div class='card border-1 shadow'>

    <div class="d-flex justify-content-left align-items-center m-1">

      <a class="btn btn-primary col-md-1 text-truncate" href="{{ route ('showLeadById', $el->id) }}" style="font-size: 0.7rem;">
        {{$el->status}}</a>
      @if ($countTasks = $el->tasks()->where('status', '!=', \App\Models\Tasks::STATUS_COMPLETE)->count() > 0)
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        {{$el->tasks()->where('status', '!=', \App\Models\Tasks::STATUS_COMPLETE)->count()}}
      </span>
      @endif

      <span class="col-md-1 d-flex align-items-center px-1">
        <span class="badge d-flex align-items-center p-1 border border-dark-subtle rounded-pill"
          data-toggle="tooltip" data-placement="top" title="{{$el -> userFunc -> name}}">
          <img class="rounded-circle" width="24" height="24" src="https://crm.nedicom.ru/{{ $el -> userFunc -> avatar }}" alt="">
        </span>
        <span class="ps-1"
          data-toggle="tooltip" data-placement="top" title="Поступил">
          {{$el -> created_at->format('d.m.Y H:i')}}
        </span>
      </span>

      @if($el->name)
      <span class="text-truncate col-md-1 text-center" data-toggle="tooltip" data-placement="top" title="{{$el -> name}}">
        {{$el -> name}}
      </span>
      @endif


      @if($el->resptasks)
      {{$el->resptasks}}
      @endif

      <span class="col-md-1 d-flex align-items-center px-1">
        <span class="badge d-flex align-items-center p-1 border border-dark-subtle rounded-pill"
          data-toggle="tooltip" data-placement="top" title="{{$el -> responsibleFunc -> name}}">
          <img class="rounded-circle" width="24" height="24" src="https://crm.nedicom.ru/{{ $el -> responsibleFunc -> avatar }}" alt="">
        </span>
        @if ($el->updated_at !== null)
        <span class="ps-1"
          data-toggle="tooltip" data-placement="top" title="Поступил">
          {{$el -> updated_at->format('d.m.Y H:i')}}
        </span>
        @else
        <span class="ps-1">
          ждет
        </span>
        @endif
      </span>

      <div class="row col-md-1">
        <p class="mb-0 text-muted" style="font-size: 0.9rem;">
          {{$el -> phone}}
        </p>
        @if($el->ring_recording_url)
        <p class="mb-0 text-muted col" style="display: -webkit-box;  -webkit-line-clamp: 1;  -webkit-box-orient: vertical;  overflow: hidden;">
          <a href="{{ $el->ring_recording_url }}"><i class="bi bi-headset"></i></a>
        </p>
        @endif
      </div>

      <span class="text-truncate col-md-3"
        data-toggle="tooltip" data-placement="top" title="{{$el -> description}}">
        {{$el -> description}}
      </span>

      @if($el->city)
      <span class="text-truncate col-md-1 text-center"
        data-toggle="tooltip" data-placement="top" title="город">
        {{$el -> city -> city}}
      </span>
      @else
      <span class="col-md-1 text-center"
        data-toggle="tooltip" data-placement="top" title="город">
        -
      </span>
      @endif

      @if($el->casettype)
      <span class="text-truncate col-md-1 text-center"
        data-toggle="tooltip" data-placement="top" title="тип дела">
        {{$el -> casettype}}
      </span>
      @else
      <span class="col-md-1 text-center"
        data-toggle="tooltip" data-placement="top" title="тип дела">
        -
      </span>
      @endif

      @if($el->source)
      <span class="text-truncate col-md-1 text-center"
        data-toggle="tooltip" data-placement="top" title="источник">
        {{$el -> source}}
      </span>
      @else
      <span class="col-md-1 text-center"
        data-toggle="tooltip" data-placement="top" title="источник">
        -
      </span>
      @endif

      @if($el->state)
      <span class="text-truncate col-md-1 text-center"
        data-toggle="tooltip" data-placement="top" title="онлайн/офлайн">
        {{$el -> state}}
      </span>
      @else
      <span class="col-md-1 text-center"
        data-toggle="tooltip" data-placement="top" title="онлайн/офлайн">
        -
      </span>
      @endif

    </div>
  </div>
</div>

<style>
  .leadcard {
    font-size: 0.75rem;
  }
</style>