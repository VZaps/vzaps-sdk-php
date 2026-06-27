<?php

declare(strict_types=1);

namespace VZaps\Sdk\Json;

use JsonException;

final class VZapsJson
{
    /**
     * @param mixed $value
     */
    public static function encode(mixed $value): string
    {
        return json_encode(self::normalizeOutgoing($value), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return mixed
     */
    public static function decode(string $json): mixed
    {
        if (trim($json) === '') {
            return null;
        }

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function normalizeOutgoing(mixed $value): mixed
    {
        if ($value instanceof \JsonSerializable) {
            $value = $value->jsonSerialize();
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            $value = $value->toArray();
        }

        if (!is_array($value)) {
            return $value;
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            $outKey = is_string($key) ? self::toSnakeCase($key) : $key;
            $normalized[$outKey] = self::normalizeOutgoing($item);
        }

        return $normalized;
    }

    public static function toSnakeCase(string $key): string
    {
        $key = preg_replace('/(?<!^)[A-Z]/', '_$0', $key) ?? $key;

        return strtolower($key);
    }

    /**
     * @throws JsonException
     */
    public static function tryDecode(string $json): mixed
    {
        return self::decode($json);
    }
}
