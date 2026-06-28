<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Sessions;

use VZaps\Sdk\Models\Common\VZapsModel;

final class SessionBusinessCategory implements VZapsModel
{
    public function __construct(
        public readonly string $id = '',
        public readonly string $name = '',
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}

final class SessionBusinessProfile implements VZapsModel
{
    /**
     * @param list<SessionBusinessCategory>|null $categories
     * @param array<string, string>|null $profileOptions
     */
    public function __construct(
        public readonly ?string $businessHoursTimezone = null,
        public readonly ?array $categories = null,
        public readonly ?array $profileOptions = null,
        public readonly ?string $address = null,
        public readonly ?string $email = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $categories = null;
        if (isset($data['categories']) && is_array($data['categories'])) {
            $categories = [];
            foreach ($data['categories'] as $item) {
                if (is_array($item)) {
                    $categories[] = SessionBusinessCategory::fromArray($item);
                }
            }
        }

        $profileOptions = null;
        if (isset($data['profile_options']) && is_array($data['profile_options'])) {
            $profileOptions = [];
            foreach ($data['profile_options'] as $key => $value) {
                $profileOptions[(string) $key] = (string) $value;
            }
        }

        return new self(
            businessHoursTimezone: isset($data['business_hours_timezone']) ? (string) $data['business_hours_timezone'] : null,
            categories: $categories,
            profileOptions: $profileOptions,
            address: isset($data['address']) ? (string) $data['address'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'business_hours_timezone' => $this->businessHoursTimezone,
            'categories' => $this->categories !== null
                ? array_map(static fn (SessionBusinessCategory $item): array => $item->toArray(), $this->categories)
                : null,
            'profile_options' => $this->profileOptions,
            'address' => $this->address,
            'email' => $this->email,
        ], static fn ($value): bool => $value !== null);
    }
}

final class SessionStatusData implements VZapsModel
{
    public function __construct(
        public readonly bool $connected,
        public readonly ?string $phone = null,
        public readonly ?string $whatsappJid = null,
        public readonly ?string $pushName = null,
        public readonly ?string $businessName = null,
        public readonly ?SessionBusinessProfile $businessProfile = null,
        public readonly ?string $profilePictureId = null,
        public readonly ?string $profilePictureUrl = null,
        public readonly ?string $profileUrl = null,
        public readonly ?string $verifiedName = null,
        public readonly ?string $about = null,
        public readonly ?string $website = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            connected: (bool) ($data['connected'] ?? false),
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            whatsappJid: isset($data['whatsapp_jid']) ? (string) $data['whatsapp_jid'] : null,
            pushName: isset($data['push_name']) ? (string) $data['push_name'] : null,
            businessName: isset($data['business_name']) ? (string) $data['business_name'] : null,
            businessProfile: isset($data['business_profile']) && is_array($data['business_profile'])
                ? SessionBusinessProfile::fromArray($data['business_profile'])
                : null,
            profilePictureId: isset($data['profile_picture_id']) ? (string) $data['profile_picture_id'] : null,
            profilePictureUrl: isset($data['profile_picture_url']) ? (string) $data['profile_picture_url'] : null,
            profileUrl: isset($data['profile_url']) ? (string) $data['profile_url'] : null,
            verifiedName: isset($data['verified_name']) ? (string) $data['verified_name'] : null,
            about: isset($data['about']) ? (string) $data['about'] : null,
            website: isset($data['website']) ? (string) $data['website'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'connected' => $this->connected,
            'phone' => $this->phone,
            'whatsapp_jid' => $this->whatsappJid,
            'push_name' => $this->pushName,
            'business_name' => $this->businessName,
            'business_profile' => $this->businessProfile?->toArray(),
            'profile_picture_id' => $this->profilePictureId,
            'profile_picture_url' => $this->profilePictureUrl,
            'profile_url' => $this->profileUrl,
            'verified_name' => $this->verifiedName,
            'about' => $this->about,
            'website' => $this->website,
        ], static fn ($value): bool => $value !== null);
    }
}

final class SessionStatusResponse implements VZapsModel
{
    public function __construct(
        public readonly int $code,
        public readonly bool $success,
        public readonly SessionStatusData $data,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $data = isset($payload['data']) && is_array($payload['data'])
            ? SessionStatusData::fromArray($payload['data'])
            : new SessionStatusData(connected: false);

        return new self(
            code: (int) ($payload['code'] ?? 0),
            success: (bool) ($payload['success'] ?? false),
            data: $data,
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'success' => $this->success,
            'data' => $this->data->toArray(),
        ];
    }
}
