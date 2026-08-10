<?php

namespace NotificationSystem\DTOs;

/**
 * Immutable data transfer object representing a resolved notification recipient.
 *
 * Created from Eloquent models or raw arrays via the {@see fromModel()} factory.
 *
 * @see \NotificationSystem\Resolvers\RecipientResolver
 */
readonly class RecipientData
{
    /**
     * @param  string|int|null  $id        The recipient's primary key.
     * @param  string|null      $type      The recipient's model class name (or 'array').
     * @param  string|null      $name      Display name.
     * @param  string|null      $email     Email address.
     * @param  string|null      $phone     Phone number (for WhatsApp/SMS channels).
     * @param  string|null      $fcmToken  FCM device token (for push notifications).
     * @param  string|null      $locale    Preferred locale.
     * @param  mixed            $rawModel  The original model or array for channel access.
     */
    public function __construct(
        public string|int|null $id = null,
        public ?string $type = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $fcmToken = null,
        public ?string $locale = null,
        public mixed $rawModel = null
    ) {}

    /**
     * Create a RecipientData from an Eloquent model or associative array.
     *
     * Automatically extracts common fields (name, email, phone, fcm_token, locale)
     * with sensible fallback attribute names.
     *
     * @param  mixed  $model  An Eloquent model, associative array, or null.
     */
    public static function fromModel(mixed $model): self
    {
        if ($model instanceof self) {
            return $model;
        }

        if (is_null($model)) {
            return new self();
        }

        if (is_array($model)) {
            return new self(
                id: $model['id'] ?? null,
                type: $model['type'] ?? 'array',
                name: $model['name'] ?? null,
                email: $model['email'] ?? null,
                phone: $model['phone'] ?? null,
                fcmToken: $model['fcm_token'] ?? null,
                locale: $model['locale'] ?? $model['lang'] ?? null,
                rawModel: $model
            );
        }

        $id = method_exists($model, 'getKey') ? $model->getKey() : ($model->id ?? null);
        $type = get_class($model);
        $name = $model->name ?? $model->first_name ?? null;
        $email = $model->email ?? null;
        $phone = $model->phone ?? $model->phone_number ?? $model->mobile ?? null;
        $fcmToken = $model->fcm_token ?? $model->device_token ?? null;

        $locale = method_exists($model, 'preferredLocale')
            ? $model->preferredLocale()
            : ($model->lang ?? $model->locale ?? null);

        return new self(
            id: $id,
            type: $type,
            name: $name,
            email: $email,
            phone: $phone,
            fcmToken: $fcmToken,
            locale: $locale,
            rawModel: $model
        );
    }

    /**
     * Export the recipient data as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'fcm_token' => $this->fcmToken,
            'locale' => $this->locale,
        ];
    }
}
