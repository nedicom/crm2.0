<!-- Список услуг для формы Задач -->
<span class="close" style="color:red; float:right; cursor:pointer">Скрыть список</span>
<div class="holder-services-list">
    <h6>Список услуг</h6>
    <ul class="list-group">
        @include('inc/modal/_list-services', compact('services'))
    </ul>
</div>
