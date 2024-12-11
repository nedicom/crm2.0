<div class="modal fade" id="modalleadfail">
    <div class="modal-dialog">
      <div class="modal-content">
          <div class ="modal-header">
            <h2>Провален</h2>
          </div>

          <div class ="modal-body d-flex justify-content-center">

          <div class ="col-10">
            <form action="{{route('lead.fail', $data -> id)}}" class='' autocomplete="off" method="post">
              @csrf

              <div class="form-group mb-3">
                <label for="defeatreason">Почему лид провалился?</label>
                <textarea rows="3" name="defeatreason"
                placeholder="Не пришел, дорого, проблема не юридическая, проблема не решаемая, клиент неадекватный" id="defeatreason" class="form-control" required></textarea>
              </div>

              <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
            </form>
          </div>
        </div>

        </div>
      </div>
    </div>
