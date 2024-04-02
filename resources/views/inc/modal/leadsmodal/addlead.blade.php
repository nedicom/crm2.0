<div class="modal fade" id="leadsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>Добавить лид</h2>
            </div>
            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('addlead')}}" class='' autocomplete="off" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Введите ФИО</label>
                            <input type = "text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone">Введите телефон</label>
                            <input type = "phone" name="phone" placeholder="+7" id="phone" value="{{ old('phone') }}" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description">Описание проблемы</label>
                            <textarea rows="3" name="description" placeholder="Не увольняют военнослужащего" id="description" class="form-control" required>{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="source">Укажите источник</label>
                            <select class="form-select" name="source" id="source" class="form-control">
                                @foreach($datasource as $el)
                                    <option value="{{$el->name}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>

                    <!--
                        <div class="form-group mb-3">
                            <label for="service">Что можно предложить</label>
                            <select class="form-select" name="service" id="service" class="form-control">
                                @foreach($dataservices as $el)
                                    <option value="{{$el->id}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>

                    -->
                        <div class="form-group mb-3">
                            <label for="lawyer">Укажите кто привлек лид</label>
                            <select class="form-select" name="lawyer" id="lawyer" class="form-control">
                                @foreach($datalawyers as $el)
                                    <option value="{{$el->id}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="responsible">Укажите кто ответсвенный за лид</label>
                            <select class="form-select" name="responsible" id="responsible" class="form-control">
                                @foreach($datalawyers as $el)
                                    <option value="{{$el->id}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!--
                        <div class="form-group mb-3">
                            <label for="status">Укажите статус</label>
                            <select class="form-select" name="status" id="status" class="form-control">
                                <option value="поступил">поступил</option>
                                <option value="в работе">в работе</option>
                            </select>
                        </div>
                        -->
                        <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
