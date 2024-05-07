<div class="modal fade" id="leadsModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>Добавить лид</h2>
            </div>
            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('addlead')}}" class='' autocomplete="off" method="post">
                        @csrf

                        <div class="row g-3 mb-1">
                            <div class="col">
                                <label for="name">ФИО <span class="text-danger">*</span></label>
                                <input type = "text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                             </div>
                            <div class="col">
                                <label for="phone">Введите телефон <span class="text-danger">*</span></label>
                                <input type = "phone" name="phone" placeholder="+7" id="phone" value="{{ old('phone') }}" class="form-control" required>
                            </div>                            
                        </div>


                        <div class="form-floating my-3">                            
                            <textarea rows="3" name="description" placeholder="Не увольняют военнослужащего" id="description" class="form-control" required>{{ old('description') }}</textarea>
                            <label for="description">Описание проблемы</label>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col">
                                {!! \App\Helpers\ClientHelper::typeList(0) !!}
                            </div>

                            <div class="col">
                                <label for="source">источник</label>
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
                        </div>                   -->

                            <div class="col">
                                <label for="lawyer">привлек</label>
                                <select class="form-select" name="lawyer" id="lawyer" class="form-control">                                     
                                    @foreach($datalawyers as $el)                                           
                                        <option value="{{$el->id}}">
                                            {{$el->name}}                                            
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">
                                <label for="responsible">ответсвенный</label>
                                <select class="form-select" name="responsible" id="responsible" class="form-control">
                                    @foreach($datalawyers as $el)
                                        <option value="{{$el->id}}">{{$el->name}}</option>
                                    @endforeach
                                </select>
                            </div>
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
                    <div class="row  g-3 float-end">
                        <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
