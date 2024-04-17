<div class="modal fade" id="taskModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>+ <span id="taskname">задачу</span></h2>
            </div>
            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('add.task.lead')}}" autocomplete="off" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="description">Описание</label>
                            <textarea rows="3" name="description" placeholder="Немного подробнее, если это нужно" id="description" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-4 form-group mb-3">
                                <label for="date">Время начала: <span class="text-danger">*</span></label>
                                <input type="text" id="date" class="form-control" name="date" min="{{ date('Y-m-d H:i') }}" required>
                            </div>
                            <div class="col-4 form-group mb-3">
                                <label for="lawyer">Укажите исполнителя <span class="text-danger">*</span></label>
                                <select name="lawyer" id="lawyer" class="form-select">
                                    @foreach ($datalawyers as $el)
                                        @if($el->status == 'active')
                                            <option value="{{$el->id}}" @if ((Auth::user()->id) == $el->id) selected @endif>{{$el->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>  
                        </div>
                        <input type="hidden" name="nameoftask" value="" id="nameoftask">
                        <input type="hidden" name="soispolintel" value="" id="soispolintel">
                        <input type="hidden" name="type" value="" id="type">
                        <input type="hidden" name="lead_id" id="lead_id" value="{{ $data->id }}">
                        <input type="hidden" name="lead_phone" id="lead_phone" value="{{ $data->phone }}">
                        <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
