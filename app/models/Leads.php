<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

class Leads
{
    public static function all(): array
    {
        return db()->query('SELECT * FROM leads ORDER BY id DESC')->fetchAll();
    }

    public static function create(array $data): void
    {
        $stmt = db()->prepare('INSERT INTO leads (name,email,phone,message,source,created_at) VALUES (:name,:email,:phone,:message,:source,NOW())');
        $stmt->execute($data);
    }
}
