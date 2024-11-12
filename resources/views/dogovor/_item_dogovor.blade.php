<div class="col-12 col-md-4 my-3">
     <div class="card shadow">
         <div class="card-header d-flex align-items-center">
             <span>
                 <img src="{{ $el->userFunc->avatar }}" style="width: 40px; height:40px" class="rounded-circle">
             </span>
             <span class="mx-3 text-truncate">{{ $el->name }}</span>
         </div>
         <div class="card-body">
             <div class="d-flex align-items-center justify-content-between">
                 <h2 class="px-2 text-truncate text-center" tabindex="0" data-bs-toggle="popover"
                     data-bs-trigger="hover focus" title="предмет" data-bs-content="{{ $el->subject }}">
                     <strong>{{ $el->allstoimost }}</strong>
                 </h2>
             </div>
             <p class="px-2">{{ $el->created_at }}</p>
             <div class="d-flex justify-content-between">

                 <div class="px-2 d-flex flex-column">
                     <div class="text-truncate text-left text-muted"><small>
                             источник</small>
                     </div>
                     <div class="text-truncate text-left">
                         @if ($el->clientFunc)
                             {{ $el->clientFunc->source }}
                         @endif
                     </div>
                 </div>

                 <div class="px-2 d-flex flex-column">
                     <div class="text-truncate text-right text-muted"><small>
                             тип дела</small>
                     </div>
                     <div class="text-truncate text-right">
                         @if ($el->clientFunc)
                             {{ $el->clientFunc->casettype }}
                         @endif
                     </div>
                 </div>

                 <div class="px-2 d-flex flex-column">
                    <div class="text-truncate text-right text-muted"><small>
                            город</small>
                    </div>
            
                    <div class="text-truncate text-right">
                        @if ($el->city)
                            {{ $el->city->city }}
                        @endif
                    </div>
                </div>
             </div>
             <div class="d-flex justify-content-between">
                 <span class="px-2 text-truncate text-center">
                     @if ($el->clientFunc)
                         <a href="/clients/{{ $el->clientFunc->id }}" target="_blank">{{ $el->clientFunc->name }}</a>
                     @endif
                 </span>

                 @if ($el->url)
                     <a href="/{{ $el->url }}" class="btn btn-primary">скачать</a>
                 @else
                     <a href="#" class="btn btn-secondary disabled">скачать</a>
                 @endif
             </div>
         </div>
     </div>
 </div>
