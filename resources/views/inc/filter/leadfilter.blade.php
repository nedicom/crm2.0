<div class="row p-4">
    <div class="col-10 d-flex justify-content-center">
        <form class="row gx-3 gy-2 align-items-center d-flex justify-content-between" action="{{route('leads')}}" method="GET">
            <div class="col-2">
                {!! \App\Helpers\LeadHelper::statusList() !!}
            </div>
            <div class="col-2">
                <select class="form-select" name="checkedsources" id="checkedsources">
                    <option value="">Источник</option>
                    @foreach($datasource as $el)
                        <option value="{{$el -> name}}" @if (($el -> name) == (app('request')->input('checkedsources'))) selected @endif>
                            {{$el -> name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-2">
                <select class="form-select" name="checkedlawyer" id="checkedlawyer">
                    <option value="">Привлек лид</option>
                    @foreach($datalawyers as $el)
                        <option value="{{$el -> id}}" @if (($el -> id) == (app('request')->input('checkedlawyer'))) selected @endif>
                            {{$el -> name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-2">
                <select class="form-select" name="checkedresponsible" id="checkedresponsible">
                    <option value="">Ответственный</option>
                    @foreach($datalawyers as $el)
                        <option value="{{$el -> id}}" @if (($el -> id) == (app('request')->input('checkedresponsible'))) selected @endif>
                            {{$el -> name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-primary">Применить</button>
                <a href='{{ route('leads') }}' class='button btn btn-secondary'>Сбросить</a>
            </div>
        </form>
    </div>
</div>
