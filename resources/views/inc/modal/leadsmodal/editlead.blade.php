<div class="modal fade" id="editleadModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>Обработать лид</h2>
            </div>
            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('LeadUpdateSubmit', $data -> id)}}" class='' autocomplete="off" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Введите ФИО</label>
                            <input type = "text" name="name" id="name" value='{{$data->name}}' class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone">Введите телефон</label>
                            <input type = "phone" name="phone" placeholder="+7" id="phone" value='{{$data->phone}}' class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="description">Описание проблемы</label>
                            <textarea rows="3" name="description" placeholder="Не увольняют военнослужащего" id="phone" class="form-control" required>{{$data->description}}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="source">Укажите источник</label>
                            <select class="form-select" name="source" id="source" class="form-control">
                                @foreach($datasource as $el)
                                    <option value="{{$el->name}}" @if($data->source == $el->name) selected @endif>{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!--
                        <div class="form-group mb-3">
                            <label for="service">Что можно предложить</label>
                            <select class="form-select" name="service" id="service" class="form-control">
                                @foreach($dataservices as $el)
                                    <option value="{{$el->id}}" @if($data->service == $el->id) selected @endif>{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                            -->
                        <div class="form-group mb-3">
                            <label for="lawyer">Кто привлек лид</label>
                            <select class="form-select" name="lawyer" id="lawyer" class="form-control">
                                @foreach($datalawyers as $el)
                                    <option value="{{$el->id}}"  @if($data->lawyer == $el->id) selected @endif>{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="responsible">Кто ответсвенный за лид</label>
                            <select class="form-select" name="responsible" id="responsible" class="form-control">
                                @foreach($datalawyers as $el)
                                    <option value="{{$el -> id}}" @if($data->responsible == $el -> id) selected @endif>{{$el -> name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            @if ($data->ring_recording_url !== null)
                                <a href="{{ $data->ring_recording_url }}" class="btn btn-primary">Ссылка на запись разговора</a>
                            @endif
                        </div>
                        <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
