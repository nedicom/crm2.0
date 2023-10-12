<div class="modal fade" id="editUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>Редактировать пользователя</h2>
            </div>
            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('users.update', $user)}}" autocomplete="off" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">Введите имя</label>
                            <input type="text" name="name" id="name" value='{{ $user->name }}' class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Введите email</label>
                            <input type="email" name="email" id="email" value='{{ $user->email }}' class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="role">Выбрать роль</label>
                            <select id="role" name="role" class="form-control form-select">
                                @foreach (\App\Models\User::rolesList() as $name => $value)
                                    <option value="{{ $name }}" @if ($user->role === $name) selected @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input status-client" type="checkbox" name="status" id="status" value="1"
                                    @if ($user->isActive()) checked @endif>
                                <label class="form-check-label" for="status">Активен</label>
                            </div>
                        </div>
                        <button type="submit" id='submit' class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
