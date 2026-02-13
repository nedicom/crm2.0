<div class="modal fade client" id="editModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <form action="{{ route('Client-Update-Submit', $data->id) }}" method="post">
                @csrf
                <div class="modal-header">
                    <div class="col-10">
                        <h2>Редактировать клиента</h2>
                    </div>

                    <div class="col-2 form-check form-switch">
                        <div class="float-end">
                            <input class="form-check-input status-client" type="checkbox" name="status" id="status"
                                value="1" @if ($data->status == 1) checked @endif>
                            <label class="form-check-label" for="status">В работе</label>
                        </div>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-1">
                        <div class="col">
                            <label for="name">ФИО <span class="text-danger">*</span></label>
                            <input type="text" name="name" placeholder="Иван Васильевич" id="name"
                                value='{{ $data->name }}' class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="phone">телефон <span class="text-danger">* </span></label>
                            <input type="phone" name="phone" placeholder="+7" id="phone"
                                value='{{ $data->phone }}' class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="email">email</label>
                            <input type="email" name="email" placeholder="ivanov@yandex.ru" id="email"
                                value='{{ $data->email }}' class="form-control">
                        </div>
                    </div>


                    <div class="row g-3 mb-1">
                        <div class="col">
                            <label for="address">адрес</label>
                            <input type="text" name="address" value='{{ $data->address }}' id="address"
                                class="form-control">
                            <div class="form-text">индекс облегчает работу юристам</div>
                        </div>
                        <div class="col">
                            <label for="address">Ссылка на диск</label>
                            <input type="text" name="url" value="{{ $data->url }}"
                                placeholder="Не копируйте ссылку из браузера" id="url" class="form-control">
                            <div class="form-text">В яндекс диске нажмите поделиться</div>
                        </div>
                    </div>

                    <div class="form-group mb-1 form-floating">
                        <textarea type="text" name="description" id="description" class="form-control" style="min-height: 120px; height: 120px;">{{ $data->description }}</textarea>
                        <label for="description">Введите описание</label>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label for="source">источник</label>
                            <select class="form-select" name="source" value="{{ old('source') }}" id="source"
                                class="form-control">
                                @foreach ($datasource as $el)
                                <option value="{{ $el->name }}"
                                    @if ($data->source == $el->name) selected @endif>{{ $el->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="lawyer">юрист</label>
                            <select class="form-select" name="lawyer" id="lawyer">
                                @foreach ($datalawyers as $el)
                                <option value="{{ $el->id }}"
                                    @if ($data->lawyer == $el->id) selected @endif>{{ $el->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="consult" data-bs-toggle="tooltip"
                                title="Консультант отвечает за платежи, автоматически добавляется из ответственного за лид,
                                 консультант подтягивается в платежах в продажи">консультант
                                <i class="bi bi-question-circle" style="cursor: pointer; color: #0d6efd;"></i>
                            </label>
                            <select class="form-select" name="consult" id="consult">
                                @foreach ($datalawyers as $el)
                                <option value="{{ $el->id }}"
                                    @if ($data->consult == $el->id) selected @endif>{{ $el->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="attract" data-bs-toggle="tooltip"
                                title="Подтягивается в платежах в привлечение">привлек
                                <i class="bi bi-question-circle" style="cursor: pointer; color: #0d6efd;"></i>
                            </label>
                            <select class="form-select" name="attract" id="attract">
                                @foreach ($datalawyers as $el)
                                <option value="{{ $el->id }}"
                                    @if ($data->attract == $el->id) selected @endif>{{ $el->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col">
                            <label for="city_id">город</label>
                            <select class="form-select" name="city_id" id="city_id" class="form-control">
                                <option value=null>не выбрано</option>
                                @foreach ($cities as $el)
                                <option value="{{ $el->id }}"
                                    @if ($data->city_id == $el->id) selected @endif>{{ $el->city }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col">
                            {!! \App\Helpers\ClientHelper::typeList($data->casettype) !!}
                        </div>
                    </div>

                    <div class="row g-3 my-3 align-items-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                @php $i = 0; @endphp
                                @foreach (\App\Models\Enums\Clients\Rating::cases() as $rating)
                                <div class="form-check">
                                    <input class="form-check-input rating"
                                        type="radio"
                                        name="rating"
                                        id="rating{{ ++$i }}"
                                        value="{{ lcfirst($rating->name) }}"
                                        @if (lcfirst($rating->name) == $data->rating) checked @endif>
                                    <label class="form-check-label"
                                        for="rating{{ $i }}">{{ $rating->value }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="form-text text-muted text-center">
                                У положительных будем просить отзывы
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-1 align-items-center">
                        <!-- Доступ к ЛК -->
                        <div class="col-12 col-lg-8">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="api_access"
                                    id="api_access" value="1"
                                    @if ($data->api_access == 1) checked @endif>
                                <label class="form-check-label" for="api_access"
                                    data-bs-toggle="tooltip"
                                    title="Разрешить доступ к данным клиента через API">
                                    Доступ к личному кабинету
                                    <i class="bi bi-question-circle" style="cursor: pointer; color: #0d6efd;"></i>
                                </label>
                                <div class="form-text">Если включено, данные будут доступны клиенту на nedicom.ru</div>
                            </div>
                        </div>

                        <!-- Кнопка для десктопа -->
                        <div class="col-12 col-lg-4 d-none d-lg-block">
                            <div class="float-end">
                                <button type="submit" class="btn btn-primary btn-md px-4 py-2">
                                    Обновить
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка для мобильных -->
                    <div class="d-lg-none mt-4">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                                Обновить данные клиента
                            </button>
                        </div>
                    </div>

            </form>
        </div>
    </div>
</div>
</div>