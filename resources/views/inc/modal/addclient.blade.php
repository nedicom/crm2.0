<div class="modal fade client" id="myModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <form action="{{ route('add-client') }}" method="post">
                @csrf
                <div class="modal-header">
                    <div class="col-10">
                        <h2>Добавить клиента</h2>
                    </div>
                    <div class="col-2 form-check form-switch">
                        <div class="float-end">
                            <input class="form-check-input status-client" type="checkbox" name="status" id="status"
                                value="1" checked>
                            <label class="form-check-label" for="status">В работе</label>
                        </div>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-1">
                        <div class="col">
                            <label for="name">ФИО <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="Иван Васильевич" id="name" class="form-control" reqired>
                        </div>
                        <div class="col">
                            <label for="phone">телефон <span class="text-danger">*</span></label>
                            <input type="phone" name="phone" value="{{ old('phone') }}" placeholder="+7"
                                id="phone" class="form-control" reqired>
                        </div>
                        <div class="col">
                            <label for="email">email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="ivanov@yandex.ru" id="email" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-1">
                        <div class="col">
                            <label for="address">адрес</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                placeholder="295000, Симферополь, ул. Кирова, 15" id="address" class="form-control">
                            <div class="form-text">индекс облегчает работу юристам</div>
                        </div>
                        <div class="col">
                            <label for="address">Ссылка на диск</label>
                            <input type="text" name="url" value="{{ old('url') }}"
                                placeholder="Не копируйте ссылку из браузера" id="url" class="form-control">
                            <div class="form-text">В яндекс диске нажмите поделиться</div>
                        </div>
                    </div>

                    <div class="form-floating">
                        <textarea class="form-control" name="description" id="description">{{ old('description') }}</textarea>
                        <label for="description">описание</label>
                    </div>



                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label for="source">источник</label>
                            <select class="form-select" name="source" value="{{ old('source') }}" id="source"
                                class="form-control">
                                @foreach ($datasource as $el)
                                    <option value="{{ $el->name }}">{{ $el->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="lawyer">юрист</label>
                            <select class="form-select" name="lawyer" id="lawyer">
                                @foreach ($datalawyers as $el)
                                    <option value="{{ $el->id }}">{{ $el->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="city_id">город</label>
                            <select class="form-select" name="city_id" id="city_id" class="form-control">
                                <option value=null>не выбрано</option>
                                @foreach ($cities as $el)
                                    <option value="{{ $el->id }}">{{ $el->city }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            {!! \App\Helpers\ClientHelper::typeList(0) !!}
                        </div>
                    </div>




                    <div class="row g-3 mb-1 align-items-center">
                        <div class="col-8 rating-list">
                            @php $i = 0; @endphp
                            <label style="display: block">рейтинг</label>
                            @foreach (\App\Models\Enums\Clients\Rating::cases() as $rating)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input rating" type="radio" name="rating"
                                        id="rating{{ ++$i }}" value="{{ lcfirst($rating->name) }}"
                                        @if (lcfirst($rating->name) == 'neutral') checked @endif>
                                    <label class="form-check-label"
                                        for="rating{{ $i }}">{{ $rating->value }}</label>
                                </div>
                            @endforeach
                            <div class="form-text">у положительных будем просить отзывы</div>
                        </div>
                        <div class="col-4">
                            <div class="float-end">
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        </div>
                    </div>

                </div>



            </form>
        </div>
    </div>
</div>
