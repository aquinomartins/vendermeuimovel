<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

final class Settings
{
    public static function all(): array
    {
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
        $sql = 'INSERT INTO site_settings (`key`,`value`,updated_at) VALUES (:key,:value,NOW())
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()';
        db()->prepare($sql)->execute(['key' => $key, 'value' => $value]);
    }

    public static function delete(int $id): void
    {
        db()->prepare('DELETE FROM site_settings WHERE id = :id')->execute(['id' => $id]);
    }
}
