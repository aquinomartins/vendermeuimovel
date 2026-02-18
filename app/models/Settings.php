<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

class Settings
{
    public static function all(): array
    {
        return db()->query('SELECT id, `key`, `value`, updated_at FROM site_settings ORDER BY `key`')->fetchAll();
    }

    public static function map(): array
    {
        $rows = self::all();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    public static function get(string $key, string $default = ''): string
    {
        $stmt = db()->prepare('SELECT `value` FROM site_settings WHERE `key` = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? (string) $value : $default;
    }

    public static function upsert(string $key, string $value): void
    {
        $stmt = db()->prepare('INSERT INTO site_settings (`key`, `value`, updated_at) VALUES (:key, :value, NOW()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()');
        $stmt->execute(['key' => $key, 'value' => $value]);
    }

    public static function delete(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM site_settings WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
