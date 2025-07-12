<div class ="col-10">
    <form action="{{ route('services.update', $service) }}" autocomplete="off" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group mb-3">
            <label for="name" class="form-label">Укажите название</label>
            <input type="text" name="name" placeholder="" id="name" class="form-control"
                value="{{ $service->name }}" required>
        </div>
        <label for="price" class="form-label">Введите стоимость</label>
        <div class="input-group mb-3">
            <span class="input-group-text">ru</span>
            <input type="number" name="price" id="price" class="form-control" value="{{ $service->price }}"
                required>
            <span class="input-group-text">.00</span>
        </div>
        <div class="form-group mb-3">
            <span>Время на выполнение<span class="text-danger">*</span></span>
            <div class="row">
                <div class="col-4">
                    <!-- Продолжительность в часах -->
                    <div class="input-group">
                        <label class="input-group-text" for="execution_time_h"><i class="bi bi-stopwatch"></i></label>
                        <input type="number" name="execution_time[hours]"
                            value="{{ \App\Helpers\TaskHelper::transformDuration($service->execution_time, $service->type_execution_time)['hours'] }}"
                            min="0" max="24" step="1" id="execution_time_h" class="form-control" />
                        <span class="input-group-text">час</span>
                    </div>
                </div>
                <div class="col-4">
                    <!-- Продолжительность в минутах -->
                    <div class="input-group">
                        <label class="input-group-text" for="execution_time_m"><i class="bi bi-stopwatch"></i></label>
                        <input type="number" name="execution_time[minutes]"
                            value="{{ \App\Helpers\TaskHelper::transformDuration($service->execution_time, $service->type_execution_time)['minutes'] }}"
                            min="0" max="60" step="1" id="execution_time_m" class="form-control" />
                        <span class="input-group-text">мин</span>
                    </div>
                </div>

                <div class="col-4">
                    <!-- тип -->
                    <select class="form-select" name="service_type" id="service_type">
                        @foreach (\App\Models\Enums\Tasks\Type::cases() as $type)
                            <option value="{{ $type->value }}" @if ($service->service_type == $type->value) selected @endif>
                                {{ $type->value }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea name="description" id="description" class="form-control">{{ $service->description }}</textarea>
        </div>
        <div class="form-group mb-3">
            <label for="url_disk" class="form-label">Яндекс-диск</label>
            <input type="url" name="url_disk" id="url_disk" placeholder="https://disk.yandex.ru"
                class="form-control" value="{{ $service->url_disk }}">
        </div>
        <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
    </form>
</div>
