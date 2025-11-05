<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const ROLE_USER = 'user'; // Юрист
    public const ROLE_ADMIN = 'admin'; // Администратор
    public const ROLE_MODERATOR = 'moderator'; // Модератор
    public const ROLE_USER_SERVICE_CLIENTS = 'user_service_clients'; // Юрист по работе с клиентами
    public const ROLE_HEAD_LAWYER = 'head_lawyer'; // Начальник юр. отдела
    public const ROLE_HEAD_SALES = 'head_sales'; // Начальник отдела продаж
    public const ROLE_LEAD_HANDLER = 'lead_handler'; // Лидменеджер

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'tg_id', 'status', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Список ролей
     * @return string[]
     */
    public static function rolesList(): array
    {
        return [
            self::ROLE_USER => 'Пользователь (юрист)',
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_MODERATOR => 'Модератор',
            self::ROLE_USER_SERVICE_CLIENTS => 'Юрист по работе с клиентами',
            self::ROLE_HEAD_LAWYER => 'Начальник юридического отдела',
            self::ROLE_HEAD_SALES => 'Начальник отдела продаж',
            self::ROLE_LEAD_HANDLER => 'Лидменеджер',
        ];
    }

    /**
     * Список статусов
     * @return string[]
     */
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
    public static function generateRandomAvatar(): string
    {
        $imgNames = ["rabbit.png", "bear.png", "cat.png"];
        $randKey = rand(0, 2);
        $avatar = '/avatars/' . $imgNames[$randKey]; // public_path('/avatars' . $imgNames[$randKey]);

        return $avatar;
    }

    public function getAvatar(): ?string
    {
        return (!empty($this->avatar))
            ? Str::replaceFirst('/public/', '/', $this->avatar)
            : null;
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
        return $this->role === self::ROLE_MODERATOR
            || $this->role === self::ROLE_HEAD_SALES
            || $this->role === self::ROLE_HEAD_LAWYER
        ;
    }

        public function isHeadSales(): bool
    {
        return $this->role === self::ROLE_ADMIN
            || $this->role === self::ROLE_HEAD_SALES
        ;
    }

    public function isUserServiceClients(): bool
    {
        return $this->role === self::ROLE_USER_SERVICE_CLIENTS;
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
