    <script>
        $(document).ready(function(){

            $('#client').keyup(function(){
              var query = $(this).val();
              var quantity = $(this).val().length;
                if(quantity > 2){
                  var _token = $('input[name="_token"]').val();
                  $.ajax({
                  url:"{{ route('getclient') }}",
                  method:"POST",
                  data:{query:query, _token:_token},
                  success:function(data){
                    $('#clientList').fadeIn();
                    $('#clientList').html(data);
                  }
                  });
                }
            });

            $(document).on('click', '.clientList', function(){
              $('#adress').val($(this).attr( "address" ));
              $('#phone').val($(this).attr( "phone" ));
              $('#clientidinput').val($(this).val());
              $('#client').val($(this).attr( "name" ));
              $('#clientList').fadeOut();
              var x = document.getElementById("clientidinput").value;
                if(x!==''){
                  $('#submit').prop('disabled', false);
                  $('#newclient').html( 'Ок. Клиент существует.' );
                  $('#newclient').css('color', 'green');;
                }
            });

        });
    </script>
  
  <div class="modal fade" id="dogovorModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class ="modal-header">
            <h2>Добавить договор</h2>
          </div>

          <div class ="modal-body d-flex justify-content-center">

          <div class ="col-10">
            <form action="{{route('adddogovor')}}" autocomplete="off" method="post">
              @csrf

              <div class="form-group mb-3">
                <label for="name">Укажите название<span class="text-danger">*</span></label>
                <input type = "text" name="name" placeholder="Мой самый успешный договор" id="name" value="{{ old('name') }}" class="form-control" required>
                <div id="name" class="form-text">Название поможет Вам найти договор в общем списке</div>
              </div>



              <div class="">
                  <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseService" 
                    role="button" aria-expanded="false" aria-controls="collapseService">
                      выбрать из списка  
                  </a>

                <div class ="collapse" id="collapseService">
                    @foreach ($dataservice as $el)
                        @php
                        $input = ["primary", "secondary", "danger", "success", "info", "dark"];
                        $rand_key =rand(0, 4);
                        @endphp
                      <span onclick="addServiceFunc(this)" data-price = "{{$el -> price}}"
                      class="badge bg-{{$input[$rand_key]}} shadow m-1">{{$el -> name}} ({{$el -> price}})</span>
                    @endforeach
                </div>
              </div>

              <div class="row">
                <div class="form-group mb-3 col-9">
                  <label for="subject">Укажите предмет (услуги)<span class="text-danger">*</span></label>
                  <textarea rows="5" name="subject" value="{{ old('subject') }}"
                  placeholder="Исковое заявление о признании права собственности, участие в судебном заседании..." 
                  id="subject" class="form-control" required></textarea>
                </div>

                <div class="form-group mb-3 col-3">
                  <label for="allstoimost">Cтоимость <span class="text-danger">*</span></label>
                  <input type = "text" name="allstoimost" placeholder="" value="{{ old('allstoimost') }}" id="allstoimost" class="form-control" required>
                </div>
              </div>



              <div class="">
                  <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseServicePred" 
                    role="button" aria-expanded="false" aria-controls="collapseServicePred">
                      выбрать из списка  
                  </a>

                <div class ="collapse" id="collapseServicePred">
                    @foreach ($dataservice as $el)
                        @php
                        $input = ["primary", "secondary", "danger", "success", "info", "dark"];
                        $rand_key =rand(0, 4);
                        @endphp
                      <span onclick="addServiceFuncPred(this)" data-price = "{{$el -> price}}"
                      class="badge bg-{{$input[$rand_key]}} shadow m-1">{{$el -> name}} ({{$el -> price}})</span>
                    @endforeach
                </div>
              </div>

              <div class="row">
                <div class="form-group mb-3 col-9">
                  <label for="preduslugi">Укажите какие услуги предоплачены<span class="text-danger">*</span></label>
                  <textarea rows="3" name="preduslugi" value="{{ old('preduslugi') }}"
                  placeholder="Например, исковое заявление о признании права собственности." id="preduslugi" class="form-control" required></textarea>
                </div>
                <div class="form-group mb-3 col-3">
                  <label for="predoplata">Предоплата<span class="text-danger">*</span></label>
                  <input type = "text" name="predoplata" value="{{ old('predoplata') }}" placeholder="" id="predoplata" class="form-control" required>
                </div>
              </div>

              <div class="row">
                <div class="form-group mb-3 col-5">
                  <label for="client">Укажите клиента (<a href="clients" target="_blank">создать нового</a>)<span class="text-danger">*</span></label>
                  <input type = "text" name="client" value="{{ old('client') }}" id="client" class="form-control" required>                
                  <div id="clientList">
                  </div>
                  <div id="client" class="form-text">Вы не можете создавать нового клиента, но можете изменить имя клиента, если в нем ошибка</div>
                </div>
          
                <div class="form-group mb-3 col-5">
                  <label for="adress">Укажите адрес клиента<span class="text-danger">*</span></label>
                  <input type = "text" name="adress" value="{{ old('adress') }}" placeholder="" id="adress" class="form-control" required>
                  <div class="form-text">Вы можете добавить или поменять адрес клиента</div>
                </div>

                <div class="form-group mb-3 col-2">
                  <label for="phone">Телефон<span class="text-danger">*</span></label>
                  <input type = "text" name="phone" value="{{ old('phone') }}" placeholder="" id="phone" class="form-control" required>
                  <div class="form-text">Или телефон</div>
                </div>
              </div>

              <input type="hidden" name="clientidinput" id="clientidinput" class="form-control">

              <input type="hidden" name="ispolnitelinput" id="ispolnitelinput" class="form-control">

              <div class="d-flex align-items-center">
                <p><button type="submit" id='submit' class="btn btn-primary" disabled>Сохранить</button></p>  
                <p class="small mx-3" id="newclient" style="color:red;">Нового клиента добавлять нельзя</p>             
              </div>

            </form>
          </div>
        </div>

        </div>
      </div>
    </div>

    <script>
      function checkIspolnitel(e){
        document.getElementById("ispolnitelinput").value = e;
      }

      function addServiceFunc(e) {
        document.getElementById("subject").value += e.innerHTML +"\n";
        let price = e.getAttribute("data-price");
        let price2 = parseInt(price);
        var commonprice = document.getElementById("allstoimost").value;
          if(commonprice==""){
            var commonprice2 = 0;
            var allprice = price2 + commonprice2;
          }
          else{
            commonprice2 = parseInt(commonprice);
            var allprice = price2 + commonprice2;
          }       
        document.getElementById("allstoimost").value = allprice;        
      }

      function addServiceFuncPred(e) {
        document.getElementById("preduslugi").value += e.innerHTML +"\n";
        let price = e.getAttribute("data-price");
        let price2 = parseInt(price);
        var commonprice = document.getElementById("predoplata").value;
          if(commonprice==""){
            var commonprice2 = 0;
            var allprice = price2 + commonprice2;
          }
          else{
            commonprice2 = parseInt(commonprice);
            var allprice = price2 + commonprice2;
          }       
        document.getElementById("predoplata").value = allprice;        
      }      
    </script>
