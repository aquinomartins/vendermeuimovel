<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

class Pages
{
    public static function all(): array
    {
        return db()->query('SELECT * FROM pages ORDER BY id')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM pages WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function bySlug(string $slug): ?array
    {
        $stmt = db()->prepare('SELECT * FROM pages WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function save(array $data, ?int $id = null): int
    {
        if ($id) {
            $stmt = db()->prepare('UPDATE pages SET slug=:slug,title=:title,meta_title=:meta_title,meta_description=:meta_description,is_published=:is_published,updated_at=NOW() WHERE id=:id');
            $stmt->execute($data + ['id' => $id]);
            return $id;
        }

        $stmt = db()->prepare('INSERT INTO pages (slug,title,meta_title,meta_description,is_published,updated_at) VALUES (:slug,:title,:meta_title,:meta_description,:is_published,NOW())');
        $stmt->execute($data);
        return (int) db()->lastInsertId();
    }
}
