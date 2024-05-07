<div class="my-3 d-inline-block shadow m-1" style="width: 20rem;">
          <div class="card border-light">
              <div class="card-body">
                <div class ="d-flex justify-content-between">
                  <h6 class="card-title text-truncate">{{$el -> name}}</h6>
                  <div>
                    <span class="badge
                    @if ($el -> status == 'поступил') badge-postupil
                    @elseif ($el -> status == 'в работе') badge-vrabote
                    @elseif ($el -> status == 'конвертирован') badge-convertirovan
                    @else badge-udalen
                    @endif">{{$el -> status}} </span>
                  </div>
                </div>

               <h4 class="header-title mb-3">{{$el -> phone}}</h4>

               <div class ="d-flex justify-content-between">
                 <p class ="">{{$el -> created_at}}</p>
                 <p class ="">{{$el -> source}}</p>
               </div>



               <div class="mt-1 d-flex justify-content-between">
                <div class="col-4">
                  <a class="btn btn-light w-100" href="{{ route ('showLeadById', $el->id) }}">
                  <i class="bi-three-dots"></i></a>
                </div>
                <div class="col-4">
                        @foreach($datalawyers as $ellawyer)
                          @if ($ellawyer -> id == $el -> responsible)  
                            <img src="{{$ellawyer -> avatar}}" style="width: 40px;  height:40px" class="rounded-circle" 
                            data-toggle="tooltip" title="{{$ellawyer -> name}}">
                          @endif
                        @endforeach
                </div>
                <div class="col-4">
                @if($el -> action)<a class ="btn" data-toggle="tooltip" title="{{$el -> action}}">
                   <i class="bi bi-chat-right-text" style="color: red "></i></a>
                @endif
                </div>
              </div>
                </div>
              </div>
          </div>
