  {{-- start ajax payments--}}

    <script>
    $(document).ready(function(){

     $('#client').keyup(function(){
            var query = $(this).val();
            var quantity = $(this).val().length;
            if(quantity > 2)
            {
             var _token = $('input[name="_token"]').val();
             $.ajax({
              url:"{{ route('getclient') }}",
              method:"POST",
              data:{query:query, _token:_token},
              success:function(data){
               $('#countryList').fadeIn();
                        $('#countryList').html(data);
              }
             });
            }
        });

        $(document).on('click', 'li', function(){
            $('#client').val($(this).text());
            $('#countryList').fadeOut();
        });

    });

    </script>

  {{-- end ajax payments--}}





<div class="col-3 shadow-lg p-3 mb-5 bg-body rounded">
    <div class ="text-center container col-md-9">
      <h2>Добавить платеж</h2>

      <form action="{{route('add-payment')}}" class='' autocomplete="off" method="post">
        @csrf
        <label for="summ" class="form-label">Введите сумму</label>

        <div class="input-group mb-3">
              <span class="input-group-text">ru</span>
                <input type = "text" name="summ" placeholder="" id="summ" class="form-control" value="{{ old('summ') }}" required>
              <span class="input-group-text">.00</span>
          </div>

        <div class="form-group mb-3">
          <label for="client">Укажите клиента</label>
          <input type = "text" name="client" placeholder="" id="client" value="{{ old('client') }}" class="form-control">
          <div id="countryList">
            </div>
        </div>

        <div class="form-group mb-3">
          <label for="service">Укажите услугу</label>
          <input type = "text" name="service" placeholder="" id="service" value="{{ old('service') }}"class="form-control">
        </div>

        <div class="form-group mb-3">
          <label for="nameOfAttractioner">Укажите кто привлек клиента</label>
          <input type = "text" name="nameOfAttractioner" placeholder="" id="nameOfAttractioner" class="form-control">
        </div>

        <div class="form-group mb-3">
          <label for="nameOfSeller">Укажите кто продал услугу</label>
          <input type = "text" name="nameOfSeller" placeholder="" id="nameOfSeller" class="form-control">
        </div>

        <div class="form-group mb-3">
          <label for="calculation">Куда поступили деньги</label>
          <select class="form-select" name="calculation" id="calculation" aria-label="Default select example">
            <option value="РНКБ" selected>РНКБ</option>
            <option value="БАНКРОССИИ">БАНКРОССИИ</option>
            <option value="СБЕР">СБЕР</option>
            <option value="ГЕНБАНК">ГЕНБАНК</option>
            <option value="НАЛИЧНЫЕ">НАЛИЧНЫЕ</option>
          </select>
        </div>

        <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
      </form>
    </div>
  </div>

  <script>

  </script>
