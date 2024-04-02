<div class= 'col-md-4 my-3'>
    <div class= 'card border-secondary'>

      <div class="card-header bg-transparent border-secondary">
          <div class="d-flex justify-content-between align-items-center m-1">  
              <span class=" badge d-flex align-items-center p-1 pe-2 text-dark-emphasis text-dark bg-light-subtle border border-dark-subtle rounded-pill">
                <img class="rounded-circle me-1" width="24" height="24" src="https://crm.nedicom.ru/{{ $el -> userFunc -> avatar }}" alt=""> 
                <span class="text-truncate" style="width: 4rem;">{{$el -> userFunc -> name}}</span>
              </span>
            
              <span class="badge d-flex align-items-center p-1 pe-2 text-dark-emphasis text-dark bg-light-subtle border border-dark-subtle rounded-pill">
                <img class="rounded-circle me-1" width="24" height="24" src="https://crm.nedicom.ru/{{ $el -> responsibleFunc -> avatar }}" alt="">
                <span class="text-truncate" style="width: 4rem;">{{$el -> responsibleFunc -> name}}</span>
              </span>
          </div>
      </div>
      
    <div class="card-body text-center">



        <p class="mb-0 text-muted" 
        style="display: -webkit-box;  -webkit-line-clamp: 1;  -webkit-box-orient: vertical;  overflow: hidden;">
        {{$el -> phone}}</p>

        <hr class="bg-dark-lighten my-3">       
        <p class="mt-3 fw-lighter lh-sm" 
          style="height: 3rem; display: -webkit-box;  -webkit-line-clamp: 3;  -webkit-box-orient: vertical;  overflow: hidden;">
            {{$el -> description}}</p>
        <p class="text-muted">Поступил: <strong>{{$el -> created_at}}</strong> </p>
          @if ($el->updated_at !== null)
            <p class="text-muted">Обработан: <strong>{{$el -> updated_at}}</strong> </p>
            @else
            <p class="text-muted">не обработан</p>
          @endif      

      </div>
        <div class="card-footer bg-transparent border-secondary">
            <div class="row d-flex justify-content-center">
              <div class="my-1 mx-3">
                <a class="btn btn-primary w-100" href="{{ route ('showLeadById', $el->id) }}">
                обработать</a>
                @if ($countTasks = $el->tasks()->where('status', '!=', \App\Models\Tasks::STATUS_COMPLETE)->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{$el->tasks()->where('status', '!=', \App\Models\Tasks::STATUS_COMPLETE)->count()}}
                </span>
            @endif
              </div>
        </div>

      </div>

    </div>
  </div>