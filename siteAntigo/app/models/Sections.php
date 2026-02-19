<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

class Sections
{
    public static function byPage(int $pageId): array
    {
        $stmt = db()->prepare('SELECT * FROM sections WHERE page_id=:page_id ORDER BY sort_order, id');
        $stmt->execute(['page_id' => $pageId]);
        $sections = $stmt->fetchAll();

        foreach ($sections as &$section) {
            $section['items'] = self::items((int) $section['id']);
        }

        return $sections;
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM sections WHERE id=:id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $section = $stmt->fetch();
        if (!$section) {
            return null;
        }
        $section['items'] = self::items($id);
        return $section;
    }

    public static function save(array $data, ?int $id = null): int
    {
        if ($id) {
            $stmt = db()->prepare('UPDATE sections SET page_id=:page_id,type=:type,title=:title,subtitle=:subtitle,sort_order=:sort_order,is_visible=:is_visible,updated_at=NOW() WHERE id=:id');
            $stmt->execute($data + ['id' => $id]);
            return $id;
        }

        $stmt = db()->prepare('INSERT INTO sections (page_id,type,title,subtitle,sort_order,is_visible,updated_at) VALUES (:page_id,:type,:title,:subtitle,:sort_order,:is_visible,NOW())');
        $stmt->execute($data);
        return (int) db()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        db()->prepare('DELETE FROM section_items WHERE section_id=:id')->execute(['id' => $id]);
        db()->prepare('DELETE FROM sections WHERE id=:id')->execute(['id' => $id]);
    }

    public static function items(int $sectionId): array
    {
        $stmt = db()->prepare('SELECT * FROM section_items WHERE section_id=:section_id ORDER BY sort_order, id');
        $stmt->execute(['section_id' => $sectionId]);
        return $stmt->fetchAll();
    }

    public static function saveItem(array $data, ?int $id = null): int
    {
        if ($id) {
            $stmt = db()->prepare('UPDATE section_items SET section_id=:section_id,title=:title,`text`=:text,image_url=:image_url,link_url=:link_url,sort_order=:sort_order,is_visible=:is_visible,updated_at=NOW() WHERE id=:id');
            $stmt->execute($data + ['id' => $id]);
            return $id;
        }

        $stmt = db()->prepare('INSERT INTO section_items (section_id,title,`text`,image_url,link_url,sort_order,is_visible,updated_at) VALUES (:section_id,:title,:text,:image_url,:link_url,:sort_order,:is_visible,NOW())');
        $stmt->execute($data);
        return (int) db()->lastInsertId();
    }

    public static function deleteItem(int $id): void
    {
        db()->prepare('DELETE FROM section_items WHERE id=:id')->execute(['id' => $id]);
    }
}
