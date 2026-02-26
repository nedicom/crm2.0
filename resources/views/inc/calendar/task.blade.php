  <div class="d-inline-block shadow-sm m-1 taskcard" onmousedown="mouseDown(this.id)" onmouseup="mouseUp(this.id)" 
  date="{{$el['date']['value']}}" id="{{$el -> id}}" style="width: 100%; max-width:180px;">
      <div id="card{{$el -> id}}" class="card" style="
                @if($el -> type == 'задача') border: 2px solid Lightgray; background-color: Ivory;
                @elseif($el -> type == 'консультация') border: 3px solid LightGreen; background-color: WhiteSmoke ;
                @elseif($el -> type == 'звонок') border: 3px solid LightSeaGreen; background-color: Linen;
                @elseif($el -> type == 'заседание') border: 3px solid LightSalmon; background-color: MistyRose;
                @elseif($el -> type == 'допрос') border: 3px solid CornflowerBlue; background-color: LightCyan ;
                @else border: 3px solid Black; background-color: MistyRose;
                @endif">
                
          <div class="task-header d-flex justify-content-between align-items-center"> 
                  <span>
                    @foreach($datalawyers as $ellawyer)
                      @if ($ellawyer -> id == $el -> lawyer)  
                        <a class="w-100" href="{{ route ('showTaskById', $el->id) }}">
                          <img src="{{$ellawyer -> avatar}}" style="width: 20px;" class="rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="исполнитель - {{$ellawyer -> name}}">
                        </a>
                      @endif
                    @endforeach
                  </span>              
                  
                  <div style="font-size: 8px;" class="text-truncate text-left fw-normal w-full" tabindex="0" data-bs-toggle="popover" 
                    data-bs-trigger="hover focus" title="{{$el -> name}}" data-bs-content="{{$el -> client}} - {{$el -> description}}">
                    <strong>{{$el -> name}}</strong>
                  </div>

                  <span  style="">
                      <i class="bi bi-tag-fill" id="tagspan{{$el -> id}}" style="color:  
                      @if($el -> tag == 'неважно') LightGray
                      @elseif($el -> tag == 'перенос') Aquamarine
                      @elseif($el -> tag == 'срочно') Red
                      @elseif($el -> tag == 'приоритет') BlueViolet
                      @else Black
                      @endif                
                      ;"></i>
                  </span>

                  @if($el -> new == 1)
                    <span  style="position: absolute; top:-15px; right:-10px;">
                      <span class="badge rounded-pill bg-danger">
                        new
                      </span>
                    </span>
                  @endif

          </div>                  
      </div>
</div>

