<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

final class HomeSections
{
    public static function all(): array
    {
        if (!table_exists('home_sections')) {
            return [];
        }

        return db()->query('SELECT * FROM home_sections ORDER BY sort_order, id')->fetchAll();
    }

    public static function mapByKey(): array
    {
        $out = [];
        foreach (self::all() as $section) {
            $out[$section['section_key']] = $section;
        }

        return $out;
    }

    public static function upsert(array $data): void
    {
        if (!table_exists('home_sections')) {
            return;
        }

        $sql = 'INSERT INTO home_sections (section_key,title,subtitle,body,image_url,button_label,button_url,is_visible,sort_order,updated_at)
            VALUES (:section_key,:title,:subtitle,:body,:image_url,:button_label,:button_url,:is_visible,:sort_order,NOW())
            ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), body=VALUES(body), image_url=VALUES(image_url), button_label=VALUES(button_label), button_url=VALUES(button_url), is_visible=VALUES(is_visible), sort_order=VALUES(sort_order), updated_at=NOW()';
        db()->prepare($sql)->execute($data);
    }
}
