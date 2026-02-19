<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

final class Settings
{
    public static function all(): array
    {
        if (!table_exists('site_settings')) {
            return [];
        }

        return db()->query('SELECT * FROM site_settings ORDER BY `key`')->fetchAll();
    }

    public static function map(): array
    {
        $map = [];
        foreach (self::all() as $row) {
            $map[$row['key']] = $row['value'];
        }

        return $map;
    }

    public static function upsert(string $key, string $value): void
    {
        if (!table_exists('site_settings')) {
            return;
        }

        $sql = 'INSERT INTO site_settings (`key`,`value`,updated_at) VALUES (:key,:value,NOW())
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()';
        db()->prepare($sql)->execute(['key' => $key, 'value' => $value]);
    }

    public static function delete(int $id): void
    {
        if (!table_exists('site_settings')) {
            return;
        }

        db()->prepare('DELETE FROM site_settings WHERE id = :id')->execute(['id' => $id]);
    }
}
