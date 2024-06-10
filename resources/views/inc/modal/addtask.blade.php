<div class="modal fade taskModal" id="taskModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>+ <span id="taskname">задачу</span></h2>
            </div>
            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('addtask')}}" autocomplete="off" method="post">
                        @csrf
                        <div class="form-group mb-3 hideme">
                            <label for="nameoftask">Укажите название задачи <span class="text-danger">*</span></label>
                            <input type="text" name="nameoftask" placeholder="Получить решение по делу" value="{{ old('nameoftask') }}"
                                id="nameoftask" class="field-name-task form-control" required>
                            <!-- Связанная услуга -->
                            <span style="display: none" class="service_ref_name">
                                <strong style="color: red;">Закрепленная услуга: </strong>
                                <span class="service_ref_val"></span>
                            </span>
                            <!-- Выпадающий блок списка услуг -->
                            <div style="display:none" class="popup-list-services">
                                <!-- Generate content from ajax request -->
                            </div>
                            <input type="hidden" name="service_id" value="">
                        </div>
                        <div class="form-group mb-3">
                            <label for="description">Описание</label>
                            <textarea rows="3" name="description" placeholder="Немного подробнее, если это нужно" id="description" class="form-control">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group mb-3 hideme">
                            <label for="name">Яндекс-диск</label>
                            <input type="url" name="hrftodcm" placeholder="https://disk.yandex.ru" id="hrftodcm" class="form-control">
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="date">Время начала: <span class="text-danger">*</span></label>
                                <input type="text" id="date" class="form-control" name="date" min="{{ date('Y-m-d H:i') }}" required>
                            </div>
                            <div class="col-md-8 form-group mb-3">
                                <span>Продолжительность<span class="text-danger">*</span>
                                <div class="row">
                                    <div class="col-6">
                                        <!-- Продолжительность в часах -->
                                        <div class="input-group">
                                            <label class="input-group-text" for="duration_h"><i class="bi bi-stopwatch"></i></label>
                                            <input @cannot('manage-services') readonly @endcannot type="number" name="duration[hours]"
                                                value="{{ \App\Helpers\TaskHelper::transformDuration(0)['hours'] }}" min="0" max="24" step="1" id="duration_h" class="form-control" 
                                                 />
                                            <span class="input-group-text">час</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <!-- Продолжительность в минутах -->
                                        <div class="input-group">
                                            <label class="input-group-text" for="duration_m"><i class="bi bi-stopwatch"></i></label>
                                            <input @cannot('manage-services') readonly @endcannot type="number" name="duration[minutes]"
                                            value="{{ \App\Helpers\TaskHelper::transformDuration(0)['minutes'] }}" min="0" max="60" step="1" id="duration_m" class="form-control" />
                                            <span class="input-group-text">мин</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="client">Клиент<span class="text-danger">*</span></label>
                                <input type="text" name="client" id="client" class="form-control" @if(isset($data->name) ) value="{{$data->name}}" @endif required>
                                <div id="clientList"></div>                                 
                            </div>
                            <div class="col-md-3 form-group mb-3 hideme">
                                <label for="tag">Сделайте отметку</label>
                                <select class="form-select" name="tag" id="tag">
                                    <option value="неважно">неважно</option>
                                    <option value="перенос">перенос</option>
                                    <option value="срочно">срочно</option>
                                    <option value="приоритет">приоритет</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group mb-3 hideme">
                                <label for="status">Cтатус</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="в работе">в работе</option>
                                    <option value="просрочена">просрочена</option>
                                    <option value="выполнена">выполнена</option>
                                    <option value="ожидает" selected>ожидает</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3 hideme">
                                <label for="type">Тип</label>
                                <select class="form-select" name="type" id="type">
                                    @foreach (\App\Models\Enums\Tasks\Type::cases() as $type)
                                        <option value="{{ $type->value }}">{{ $type->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="lawyer">Укажите исполнителя <span class="text-danger">*</span></label>
                                <select name="lawyer" id="lawyer" class="form-select">
                                    @foreach ($datalawyers as $el)
                                        <option value="{{$el->id}}" @if ((Auth::user()->id) == $el->id) selected @endif>{{$el->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3 hideme">
                                <label for="soispolintel">Укажите соИсполнителя</label>
                                <select class="form-select" name="soispolintel" id="soispolintel">
                                    @foreach ($datalawyers as $el)
                                        <option value="{{$el->id}}" @if ((Auth::user()->id) == $el->id) selected @endif>{{$el->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Согласовано начальниками -->
                        <div class="row">
                            <label>Согласовано</label>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="lawyer_agree" id="lawyer_agree" @cannot('manage-users') disabled @endcan>
                                    <label class="form-check-label" for="lawyer_agree">Начальником юр. отдела</label>
                                </div>
                            </div>
                            <div class="col-md- 6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="sales_agree" id="sales_agree" @cannot('manage-users') disabled @endcan>
                                    <label class="form-check-label" for="sales_agree">Начальником отдела продаж</label>
                                </div>
                            </div>
                        </div>
                        <!-- Список дел -->
                        <div class="list-deals-block"></div>
                        <!-- Платежи -->
                        <div class="row">
                            <div class="col-12 form-group paymentsIndex">
                                <label>Платежи</label>
                                <table class="table table-bordered" id="paymentsTable">
                                    <tr>
                                        <td width="300">
                                            <div class="payment-input-block">
                                                <input type="text" name="payClient[]" placeholder="Введите имя клиента" class="payment-input form-control" />
                                                <input type="hidden" name="payID[]" class="payment-id" value="">
                                                <!-- Список платежей -->
                                                <div class="paymentsList" style="display:none"></div>
                                            </div>
                                        </td>
                                        <td class="info-payment"></td>
                                    </tr>
                                </table>
                            </div>
                                <button style="margin-bottom:15px;" type="button" name="add-payment" id="add-payment" class="btn btn-success col-md-4 col-12">Добавить платеж</button>
                        </div>

                        <input type="hidden" name="clientidinput" id="clientidinput" class="form-control" @if(isset($data->id)) value="{{$data->id}}" @endif>
                        <input type="hidden" name="type" value="задача" id="tasktype">
                        <div class="row">
                            <button type="submit" id='submit' class="btn btn-primary col-md-4">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


