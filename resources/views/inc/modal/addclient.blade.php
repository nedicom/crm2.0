<div class="modal fade client" id="myModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <form action="{{route('add-client')}}" method="post">
                @csrf
                <div class="modal-header">
                    <h2>Добавить клиента</h2>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label for="name">Введите Имя <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Иван Васильевич" id="name" class="form-control" reqired>
                        </div>
                        <div class="col">
                            <label for="phone">Введите телефон <span class="text-danger">*</span></label>
                            <input type="phone" name="phone" value="{{ old('phone') }}" placeholder="+7" id="phone" class="form-control" reqired>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label for="email">Введите email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="ivanov@yandex.ru" id="email" class="form-control">
                            <div class="form-text">для заключения договора дистанционно</div>
                        </div>
                        <div class="col">
                            <label for="address">Введите адрес</label>
                            <input type="text" name="address" value="{{ old('address') }}" placeholder="295000, Симферополь, ул. Кирова, 15" id="address" class="form-control">
                            <div class="form-text">индекс облегчает работу юристам</div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description">Введите описание</label>
                        <textarea type="text" name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                    </div>



                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label for="source">Укажите источник</label>
                            <select class="form-select" name="source" value="{{ old('source') }}" id="source" class="form-control">
                                @foreach($datasource as $el)
                                <option value="{{$el->name}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="lawyer">Укажите юриста</label>
                            <select class="form-select" name="lawyer" id="lawyer">
                                @foreach($datalawyers as $el)
                                <option value="{{$el->id}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            {!! \App\Helpers\ClientHelper::typeList(0) !!}
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="address">Ссылка на диск</label>
                        <input type="text" name="url" value="{{ old('url') }}" placeholder="не копируйте ссылку из браузера" id="url" class="form-control">
                        <div class="form-text">В яндекс диске нажмите поделиться</div>
                    </div>

                    <div class="form-group mb-3 rating-list">
                        @php $i = 0; @endphp
                        <label style="display: block">Рейтинг</label>
                        @foreach (\App\Models\Enums\Clients\Rating::cases() as $rating)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input rating" type="radio" name="rating" id="rating{{ ++$i }}" value="{{ lcfirst($rating->name) }}" @if (lcfirst($rating->name) == 'neutral') checked @endif>
                            <label class="form-check-label" for="rating{{ $i }}">{{ $rating->value }}</label>
                        </div>
                        @endforeach
                        <div class="form-text">У положительных будем просить отзывы</div>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input status-client" type="checkbox" name="status" id="status" value="1" checked>
                            <label class="form-check-label" for="status">В работе</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>