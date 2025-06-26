<style>
    .text-truncate-container {
        -webkit-line-clamp: 2;
        line-clamp: 2;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<div class="modal fade" id="taskconsModal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>+ <span id="taskname">задачу</span></h2>
            </div>
            <div class="modal-body d-flex justify-content-center">
                <div class="col-10">
                    <form action="{{ route('add.task.lead') }}" autocomplete="off" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="description">Описание</label>
                            <textarea rows="3" name="description" placeholder="Немного подробнее, если это нужно" id="description"
                                class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-4 form-group mb-3">
                                <label for="date">Время начала: <span class="text-danger">*</span></label>
                                <input type="text" id="date" class="form-control" name="date"
                                 required>
                            </div>
                            <div class="col-4 form-group mb-3">
                                <label for="lawyer">Укажите исполнителя <span class="text-danger">*</span></label>
                                <select name="lawyer" id="lawyer" class="taskforlawyer form-select">
                                    @foreach ($datalawyers as $el)
                                    @if ($el->status == 'active')
                                    <option value="{{ $el->id }}"
                                        @if (Auth::user()->id == $el->id) selected @endif>{{ $el->name }}
                                    </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="container" style="font-size: 0.8rem;">
                            @php
                            $firstweek = $start + 14;
                            $i = 0;
                            @endphp

                            <div class="row" style="min-height: 100px;">
                                @for ($start; $start < $firstweek; $start++)
                                    @php $i++ @endphp
                                    <div class="col rounded @if ($start == $today) bg-light border @endif">
                                    {{ $start }}
                                    <div id="weekday{{ $start }}" class="weekday text-xs"></div>
                            </div>
                            @if ($i == 7)
                        </div>
                        <div class="row" style="min-height: 100px;">
                            @endif
                            @endfor
                        </div>
                        <div class="result">
                        </div>
                </div>

                <input type="hidden" name="casettype" value="{{ $data->casettype }}" id="casettype">
                <input type="hidden" name="leadname" value="{{ $data->name }}" id="leadname">
                <input type="hidden" name="nameoftask" value="консультация" id="nameoftask">
                <input type="hidden" name="type" value="консультация" id="tasktype">
                <input type="hidden" data-user-id="1" id="soispolintel">

                <input type="hidden" name="type" value="консультация" id="type">
                <input type="hidden" name="lead_id" id="lead_id" value="{{ $data->id }}">
                <input type="hidden" name="lead_phone" id="lead_phone" value="{{ $data->phone }}">
                <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
                <meta name="lawyerforform" content="{{ Auth::user()->id }}">
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    let select = document.querySelector('.taskforlawyer');

    async function asyncCall(lawyerid) {
        let response = await fetch("/lawyertaskfetch/" + lawyerid);
        let commits = await response.json();

        for (key in commits) {
            selector = "weekday" + commits[key].date.currentDay;
            let result = document.getElementById(selector);
            if (result) {
                result.innerHTML += "<div class='text-truncate-container'>" + commits[key].name + "</div> <div class='text-truncate-container' style='font-size: 0.8rem;'>" + commits[key].date.currentTime + "</div>";
            }
        };
    };

    select.addEventListener("change", (event) => {
        let weekdays = document.getElementsByClassName("weekday");
        for (let i = 0; i < weekdays.length; i++) {
            weekdays[i].innerHTML = "";
        }
        asyncCall(select.value);
    });

    lawyerid = $('meta[name="lawyerforform"]').attr('content');
    asyncCall(lawyerid);
</script>