<?php

namespace App\Models;

use App\Models\ClientsModel;
use App\Scopes\UserActiveScope;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property int $created_at
 * @property int $updated_at
 * @property string $role
 * @property string $avatar
 * @property int $tg_id
 * @property string $status
 */
class User extends Authenticatable //implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    // HasFactory

    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MODERATOR = 'moderator';

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'tg_id', 'status', 'role', //'remember_token',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     * @return void
     */
    /*protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UserActiveScope());
    }*/

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Массив именования ролей
     * @return string[]
     */
    public static function rolesList(): array
    {
        return [
            self::ROLE_USER => 'Пользователь (юрист)',
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_MODERATOR => 'Модератор',
        ];
    }

    public static function statusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Активный',
            self::STATUS_INACTIVE => 'Неактивен',
            self::STATUS_WAIT => 'Ожидает',
        ];
    }

    /**
     * Генерация случайного аватара
     * @return string
     */
    private static function generateRandomAvatar(): string
    {
        $imgNames = ["rabbit.png", "bear.png", "cat.png"];
        $randKey = rand(0, 2);
        $avatar = '/avatars/' . $imgNames[$randKey]; // public_path('/avatars' . $imgNames[$randKey]);

        return $avatar;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar
            ? Str::replaceFirst('/public/', '/', $this->avatar)
            : $this->avatar;
    }

    /**
     * Регистрация пользователя
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string|null $role
     * @return static
     */
    public static function register(string $name, string $email, string $password, string $role = null): self
    {
        return static::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'avatar' => static::generateRandomAvatar(),
            'remember_token' => Str::uuid(),
            'role' => ($role) ?? self::ROLE_USER,
            'status' => self::STATUS_WAIT,
        ]);
    }

    /**
     * Создание пользователя через админку
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $role
     * @param int $status
     * @return static
     */
    public static function new(string $name, string $email, string $password, string $role, int $status): self
    {
        return static::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'avatar' => static::generateRandomAvatar(),
            'role' => $role,
            'status' => ($status) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE,
        ]);
    }

    /**
     * Редактирование пользователя
     * @param string $name
     * @param string $email
     * @param string $role
     * @param int $status
     * @return void
     */
    public function edit(string $name, string $email, string $role, int $status)
    {
        $this->name = $name;
        $this->email = $email;
        $this->status = ($status) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
        if ($role !== $this->role) {
            $this->changeRole($role);
        }
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }

    /**
     * Верификация пользователя
     * @return void
     */
    public function verify(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('Пользователь уже проверен.');
        }

        $this->update([
            'status' => self::STATUS_ACTIVE,
            'remember_token' => null,
        ]);
    }

    /**
     * Изменить роль пользователя
     * @param $role
     * @return void
     */
    public function changeRole($role): void
    {
        if (!array_key_exists($role, self::rolesList())) {
            throw new \InvalidArgumentException('Undefined role "' . $role . '"');
        }
        if ($this->role === $role) {
            throw new \DomainException('Role is already assigned.');
        }
        $this->role = $role;
    }

    /**
     * Связь клиентов
     * @return HasMany
     */
    public function clients(): HasMany
    {
        return $this->hasMany(ClientsModel::class);
    }
}
