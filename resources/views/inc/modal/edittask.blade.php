<div class="modal fade taskModal" id="edittaskModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>Изменить <span id="taskname">задачу</span></h2>
            </div>
            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('editTaskById', $data->id)}}" autocomplete="off" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="nameoftask">Укажите название<span class="text-danger">*</span></label>
                            <input type="text" name="nameoftask" placeholder="" id="nameoftask" value="{{$data->name}}" class="field-name-task form-control" required>
                            <!-- Связанная услуга -->
                            <span @if (!$data->service) style="display: none" @endif class="service_ref_name">
                                <strong style="color: red;">Закрепленная услуга: </strong>
                                <span class="service_ref_val">@if ($data->service) {{ $data->service->name }} @endif</span>
                            </span>
                            <!-- Выпадающий блок списка услуг -->
                            <div style="display:none" class="popup-list-services">
                                <!-- Generate content from ajax request -->
                            </div>
                            <input type="hidden" name="service_id" value="">
                        </div>
                        <div class="form-group mb-3">
                            <label for="description">Описание</label>
                            <textarea rows="3" name="description" placeholder="Немного подробнее о задаче (необязательно)" id="description" class="form-control">{{$data->description}}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Яндекс-диск</label>
                            <input type = "url" name="hrftodcm" placeholder="https://disk.yandex.ru" id="hrftodcm"
                            value="{{$data->hrftodcm}}" class="form-control">
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="date">Время начала:<span class="text-danger">*</span></label>
                                <input type="text" id="date" value="{{$data->date['value']}}" class="form-control"
                                    name="date" @if ($data->isAtDepartment()) disabled @endif>
                            </div>
                            <div class="col-md-8 form-group mb-3">
                                <span>Продолжительность<span class="text-danger">*</span></span>
                                <div class="row">
                                    <div class="col-6">
                                        <!-- Продолжительность в часах -->
                                        <div class="input-group">
                                            <label class="input-group-text" for="duration_h"><i class="bi bi-stopwatch"></i></label>
                                            <input @cannot('manage-services') readonly @endcannot type="number" name="duration[hours]"
                                                value="{{ \App\Helpers\TaskHelper::transformDuration($data->duration, $data->type_duration)['hours'] }}" min="0" max="24" step="1" id="duration_h" class="form-control" />
                                            <span class="input-group-text">час</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <!-- Продолжительность в минутах -->
                                        <div class="input-group">
                                            <label class="input-group-text" for="duration_m"><i class="bi bi-stopwatch"></i></label>
                                            <input @cannot('manage-services') readonly @endcannot type="number" name="duration[minutes]"
                                                value="{{ \App\Helpers\TaskHelper::transformDuration($data->duration, $data->type_duration)['minutes'] }}" min="0" max="60" step="1" id="duration_m" class="form-control" />
                                            <span class="input-group-text">мин</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="client">Клиент<span class="text-danger">*</span></label>
                                <input type = "text" name="client" id="client" value="{{$data->client}}" class="form-control">
                                <div id="clientList">
                                </div>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="tag">Отметка</label>
                                <select class="form-select" name="tag" id="tag">
                                    <option value="неважно"  @if ($data->tag == "в работе") selected @endif>неважно</option>
                                    <option value="перенос"  @if ($data->tag == "перенос") selected @endif>перенос</option>
                                    <option value="срочно"  @if ($data->tag == "срочно") selected @endif>срочно</option>
                                    <option value="приоритет"  @if ($data->tag == "приоритет") selected @endif>приоритет</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="status">Cтатус</label>
                                {!! \App\Helpers\TaskHelper::statusList($data, $data->isOverdueAtDepartment()) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="lawyer">Исполнитель<span class="text-danger">*</span></label>
                                <select class="form-select" name="lawyer" id="lawyer" class="form-control">
                                    @foreach ($datalawyers as $el)
                                        <option value="{{$el->id}}"  @if ($data->lawyer == $el->id) selected @endif>{{$el->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="soispolintel">соИсполнитель</label>
                                <select class="form-select" name="soispolintel" id="soispolintel" class="form-control">
                                    @foreach ($datalawyers as $el)
                                        <option value="{{$el->id}}" @if ($data->soispolintel == $el->id) selected @endif>{{$el->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="type">Тип</label>
                                <select class="form-select" name="type" id="type">
                                    @foreach (\App\Models\Enums\Tasks\Type::cases() as $type)
                                        <option value="{{ $type->value }}"  @if ($data->type == $type->value) selected @endif>{{ $type->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Согласовано начальниками -->
                        <div class="row">
                            <label>Согласовано</label>
                            <div class="col-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="lawyer_agree" id="lawyer_agree"
                                        @cannot('manage-users') disabled @endcan @if($data->lawyer_agree) checked @endif>
                                    <label class="form-check-label" for="lawyer_agree">Начальником юр. отдела</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="sales_agree" id="sales_agree"
                                        @cannot('manage-users') disabled @endcan @if($data->sales_agree) checked @endif>
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
                                    @foreach ($data->payments()->get() as $payment)
                                        <tr>
                                            <td width="300">
                                                <div class="payment-input-block">
                                                    <input type="text" name="payClient[]" placeholder="Введите имя клиента" class="payment-input form-control" value="{{ $payment->client }}" />
                                                    <input type="hidden" name="payID[]" class="payment-id" value="{{ $payment->id }}" />
                                                    <div class="paymentsList" style="display:none"></div>
                                                </div>
                                            </td>
                                            <td class="info-payment">
                                                {{ $payment->client  . ' - ' . $payment->serviceFunc->name . ' - ' . $payment->created_at->format('d.m.Y') }}
                                            </td>
                                            <td><button type="button" class="btn btn-danger remove-tr">Удалить</button></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                           
                                <button style="margin-bottom:15px;" type="button" name="add-payment" id="add-payment" class="btn btn-success col-md-4 col-12">Добавить платеж</button>
                           
                        </div>

                        <input type="hidden" name="clientidinput" id="clientidinput" class="form-control" @if ($data->clientsModel) value="{{ $data->clientsModel->id }}" @endif>
                        
                        <div class="row"><button type="submit" id='submit' class="btn btn-primary col-12 col-md-4">обновить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
