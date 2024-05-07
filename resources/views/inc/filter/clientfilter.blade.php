<div class="row p-4">
    <div class="col-12 ">
        <form class="row gy-2 align-items-center d-flex justify-content-between" action="{{route('clients')}}" method="GET">
            
            <div class="col-md-3 col-12 d-flex justify-content-center">
                <div class="form-check form-check-inline m-0 p-1">
                    <input class="btn-check" type="radio" name="statustask" id="prosrochka" value="просрочена"
                        @if ((request()->input('statustask')) == \App\Models\Tasks::STATUS_OVERDUE)checked @endif>
                    <label class="btn btn-sm btn-outline-secondary" for="prosrochka">
                        <span class="badge bg-danger">Просрочка {{ \App\Helpers\TaskHelper::countTasksByStatus(\App\Models\Tasks::STATUS_OVERDUE) }}</span>
                    </label>
                </div>
                <div class="form-check form-check-inline m-0 p-1">
                    <input class="btn-check" type="radio" name="statustask" id="Inwork" value="в работе"
                        @if ((request()->input('statustask')) == \App\Models\Tasks::STATUS_IN_WORK)checked @endif>
                    <label class="btn btn-sm btn-outline-secondary" for="Inwork">
                        <span class="badge bg-success">В работе {{ \App\Helpers\TaskHelper::countTasksByStatus(\App\Models\Tasks::STATUS_IN_WORK) }}</span>
                    </label>
                </div>
                <div class="form-check form-check-inline m-0 p-1">
                    <input class="btn-check" type="radio" name="statustask" id="Waiting" value="ожидает"
                        @if ((request()->input('statustask')) == \App\Models\Tasks::STATUS_WAITING)checked @endif>
                    <label class="btn btn-sm btn-outline-secondary" for="Waiting">
                        <span class="badge bg-warning">Ожидают {{ \App\Helpers\TaskHelper::countTasksByStatus(\App\Models\Tasks::STATUS_WAITING) }}</span>
                    </label>
                </div>
            </div>

            <div class="col-md-2 col-12 d-flex justify-content-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="status" id="status" value="1"
                        @if (request()->input('status') !== null) checked @endif>
                    <label class="form-check-label fs-6" for="flexSwitchCheckDefault">в работе</label>
                </div>
            </div>

            <div class="col-md-3 col-12">
                <input type = "text" name="findclient" placeholder="введите клиента"
                    value="@if (!empty(request()->input('findclient'))) {{request()->input('findclient')}} @endif" id="findclient" class="form-control">
            </div>

            @can('manage-clients')
                <div class="col-md-2 col-12">
                    <select class="form-select" name="checkedlawyer" id="checkedlawyer">
                        <option value="">все клиенты</option>
                        @foreach($datalawyers as $el)
                            <option value="{{$el->id}}" @if (($el->id) == request()->input('checkedlawyer'))) selected @endif>
                                {{$el->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endcan

            <div class="col-md-2 col-12 mt-md-0 mt-5">
                <div class="row gx-1">
                    <div class="col-6 text-center">
                        <button type="submit" class="btn btn-sm btn-primary">Применить</button>
                    </div>
                    <div class="col-6 text-center">
                        <a href='clients' class='button btn-sm btn btn-secondary'>Сбросить</a>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
