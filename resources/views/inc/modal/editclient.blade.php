<div class="modal fade client" id="editModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <form action="{{route('Client-Update-Submit', $data->id)}}" method="post">
                @csrf
                <div class="modal-header">
                    <div class="col-10">
                        <h2>Редактировать клиента</h2>
                    </div>                    

                        <div class="col-2 form-check form-switch">
                            <div class="float-end">
                                <input class="form-check-input status-client" type="checkbox" name="status" id="status" value="1"
                                    @if ($data->status == 1) checked @endif>
                                <label class="form-check-label" for="status">В работе</label>
                            </div>
                        </div>                    
                </div>

                <div class ="modal-body">
                    <div class="row g-3 mb-1">
                        <div class="col">
                            <label for="name">ФИО  <span class="text-danger">*</span></label>
                            <input type="text" name="name" placeholder="Иван Васильевич" id="name" value='{{$data->name}}' class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="phone">телефон <span class="text-danger">* </span></label>
                            <input type="phone" name="phone" placeholder="+7" id="phone" value='{{$data->phone}}' class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="email">email</label>
                            <input type="email" name="email" placeholder="ivanov@yandex.ru" id="email" value='{{$data->email}}' class="form-control">
                        </div>
                    </div>
                
                    
                    <div class="row g-3 mb-1">
                        <div class="col">
                            <label for="address">адрес</label>
                            <input type="text" name="address" value='{{$data->address}}' id="address" class="form-control">
                            <div class="form-text">индекс облегчает работу юристам</div>
                        </div>
                        <div class="col">
                            <label for="address">Ссылка на диск</label>
                            <input type="text" name="url" value="{{ $data->url }}" placeholder="Не копируйте ссылку из браузера" id="url" class="form-control">
                            <div class="form-text">В яндекс диске нажмите поделиться</div>
                        </div>
                    </div>
         
                    <div class="form-group mb-1 form-floating">
                        <textarea type="text" name="description" id="description" class="form-control">{{ $data->description }}</textarea>
                        <label for="description">Введите описание</label>                    
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label for="source">источник</label>
                            <select class="form-select" name="source" value="{{ old('source') }}" id="source" class="form-control">
                                @foreach ($datasource as $el)
                                    <option value="{{$el->name}}" @if($data->source == $el->name) selected @endif>{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="lawyer">юрист</label>
                            <select class="form-select" name="lawyer" id="lawyer">
                                @foreach ($datalawyers as $el)
                                    <option value="{{$el->id}}"  @if ($data->lawyer == $el->id) selected @endif>{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            {!! \App\Helpers\ClientHelper::typeList($data->casettype) !!}
                        </div>
                    </div>

                    <div class="row g-3 mb-1 align-items-center">
                        <div class="col-8 rating-list">
                            @php $i = 0; @endphp
                            <label style="display: block">рейтинг</label>
                            @foreach (\App\Models\Enums\Clients\Rating::cases() as $rating)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input rating" type="radio" name="rating" id="rating{{ ++$i }}"
                                        value="{{ lcfirst($rating->name) }}" @if (lcfirst($rating->name) == $data->rating) checked @endif>
                                    <label class="form-check-label" for="rating{{ $i }}">{{ $rating->value }}</label>
                                </div>
                            @endforeach
                            <div class="form-text">У положительных будем просить отзывы</div>
                        </div>

                        <div class="col-4">
                            <div class="float-end">
                                <button type="submit" class="btn btn-primary">Обновить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
