  <div class="modal fade" id="modalleaddelete">
    <div class="modal-dialog">
      <div class="modal-content">
          <div class ="modal-header">
            <h2>В брак</h2>
          </div>

          <div class ="modal-body d-flex justify-content-center">

          <div class ="col-10">
            <form action="{{route('leadDelete', $data -> id)}}" class='' autocomplete="off" method="post">
              @csrf

              <div class="form-group mb-3">
                <label for="failurereason">Что не так с лидом?</label>
                <textarea rows="3" name="failurereason"
                placeholder="Дубль, нет телефона, спам и так далее" id="failurereason" class="form-control" required>{{$data->failurereason}}</textarea>
              </div>

              <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
            </form>
          </div>
        </div>

        </div>
      </div>
    </div>
