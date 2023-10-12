<div class="modal fade" id="addUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class ="modal-header">
                <h2>Добавить пользователя</h2>
            </div>
            <div class ="modal-body d-flex justify-content-center">
                <div class ="col-10">
                    <form action="{{route('users.store')}}" autocomplete="off" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Введите имя</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Введите email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Введите пароль</label>
                            <input type="password" name="password" id="password" value="{{ old('password') }}" class="form-control" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password-confirm">Подвтердите пароль</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="role">Выбрать роль</label>
                            <select id="role" name="role" class="form-control form-select">
                                @foreach (\App\Models\User::rolesList() as $name => $value)
                                    <option value="{{ $name }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input status-client" type="checkbox" name="status" id="status" value="1" checked>
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
