<?php

namespace App\Services\MyCalls\ValueObject;

/**
 * @property string|null $name
 * @property string $phone
 * @property int $direction входящий - 0, 1 - исходящий
 * @property int $answered  пропущен - 0, 1 - отвечен
 */
class RingDTO
{
    public function __construct(
        private readonly string|null $name,
        private readonly string $clientPhone,
        private readonly string $ownerPhone,
        private readonly int $direction,
        private readonly int $answered,
        private readonly string|null $recordingUrl
    ) {}

    public function getClientName(): ?string
    {
        return $this->name;
    }

    public function getClientPhone(): string
    {
        return $this->clientPhone;
    }

    public function getFormatClientPhone(): string
    {
        return str_replace('+7', '', $this->clientPhone);
    }

    public function getOwnerPhone(): string {
        return $this->ownerPhone;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function getAnswered(): int
    {
        return $this->answered;
    }

    // TODO: дописать проверку на валидацию url
    public function getRecordingUrl(): ?string
    {
        return $this->recordingUrl;
    }
}
