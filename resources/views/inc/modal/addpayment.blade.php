<div class="modal fade" id="paymentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>Добавить платеж</h2>
            </div>

            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('addpayment')}}" class='' autocomplete="off" method="post">
                        @csrf

                        <label for="summ" class="form-label">Введите сумму</label>
                        <div class = "row d-flex align-items-center mb-3">
                            <div class = "col-6">
                                <div class="input-group d-flex align-items-center">
                                    <span class="input-group-text">ru</span>
                                    <input type = "number" name="summ" placeholder="" value="{{ old('summ') }}" id="summ" class="form-control" value='' required
                                        style="input::-webkit-inner-spin-button: -webkit-appearance: none; margin: 0; -moz-appearance: textfield;">
                                    <span class="input-group-text">.00</span>
                                </div>
                            </div>
                            <div class="form-check col-6">
                                <input class="form-check-input" type="checkbox" value="predoplata" id="predoplata"
                                       data-bs-toggle="collapse" href="#predoplatadiv" aria-expanded="false" aria-controls="predoplatadiv">
                                <label class="form-check-label" for="predoplata">
                                    Частичный платеж
                                </label>
                            </div>
                        </div>
                        <div class="row mb-3 collapse" id="predoplatadiv">
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text">ru</span>
                                    <input type = "number" name="sellsumm" placeholder="" value="{{ old('sellsumm') }}"
                                        style="input::-webkit-inner-spin-button: -webkit-appearance: none; margin: 0; -moz-appearance: textfield;" id="sellsumm" class="form-control" value=''>
                                    <span class="input-group-text">.00</span>
                                </div>
                            </div>
                            <div class = "col-6 d-flex align-items-center">
                                <span class="">За сколько продана услуга?</span>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="client">Укажите клиента</label>
                            <input type = "text" name="client" id="client" class="form-control">
                            <div id="clientList"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="service">Укажите услугу</label>
                            <select class="form-select" name="service" id="service" class="form-control">
                                @foreach($dataservices as $el)
                                    <option value="{{$el->id}}">{{$el->name}} ({{$el->price}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nameOfAttractioner">Укажите кто привлек клиента</label>
                            <select class="form-select" name="nameOfAttractioner" id="nameOfAttractioner" class="form-control">
                                @foreach($datalawyers as $el)
                                    <option value="{{$el->id}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nameOfSeller">Укажите кто продал услугу</label>
                            <select class="form-select" name="nameOfSeller" id="nameOfSeller" class="form-control">
                                @foreach($datalawyers as $el)
                                    <option value="{{$el->id}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--
                        <div class="form-group mb-3">
                            <label for="directionDevelopment">Укажите кто развивал направление</label>
                            <select class="form-select" name="directionDevelopment" id="directionDevelopment" class="form-control">
                                @foreach($datalawyers as $el)
                                    <option value="{{$el->id}}">{{$el->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    -->
                        <div class="form-group mb-3">
                            <label for="calculation">Куда поступили деньги</label>
                            <select class="form-select" name="calculation" id="calculation" aria-label="Default select example">
                                <option value="РНКБ" >РНКБ</option>
                                <option value="СБЕР">СБЕР</option>
                                <option value="БАНКРОССИИ" selected>БАНКРОССИИ</option>
                                <option value="ГЕНБАНК">ГЕНБАНК</option>
                                <option value="НАЛИЧНЫЕ">НАЛИЧНЫЕ</option>
                            </select>
                        </div>
                        <!-- Задачи -->
                        <div class="form-group tasksIndex">
                            <label>Задачи</label>
                            <table class="table table-bordered" id="tasksTable">
                                <tr>
                                    <td width="200">
                                        <div class="task-input-block">
                                            <input type="text" name="taskClient[]" placeholder="Введите имя клиента" class="task-input form-control" />
                                            <input type="hidden" name="taskID[]" class="task-id" value="">
                                            <!-- Список платежей -->
                                            <div class="tasksList" style="display:none"></div>
                                        </div>
                                    </td>
                                    <td class="info-task"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <button style="margin-bottom:15px;width:170px" type="button" name="add-task" id="add-task" class="btn btn-success">Добавить задачу</button>
                        </div>

                        <input type="hidden" name="clientidinput" id="clientidinput" class="form-control">
                        <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#predoplata").change(function() {
        if (this.checked) {
            $("#predoplatadiv").fadeIn("slow");
        } else{
            $("#predoplatadiv").fadeOut("slow");
        }
    });
</script>

@section('footerscript')
    <script type="text/javascript" src="{{ asset('/resources/js/payment.js') }}"></script>
@endsection
