<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/db.php';

final class HomeItems
{
    public static function groups(): array
    {
        return ['typeChips', 'metrics', 'readyCards', 'launchCards', 'regionChips', 'testimonials', 'pinsList', 'footer_links'];
    }

    public static function all(?string $group = null): array
    {
        if ($group === null) {
            return db()->query('SELECT * FROM home_items ORDER BY group_key, sort_order, id')->fetchAll();
        }

        $stmt = db()->prepare('SELECT * FROM home_items WHERE group_key = :group_key ORDER BY sort_order, id');
        $stmt->execute(['group_key' => $group]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): void
    {
        $sql = 'INSERT INTO home_items (group_key,title,`text`,image_url,link_url,badge,price,sort_order,is_visible,updated_at)
                VALUES (:group_key,:title,:text,:image_url,:link_url,:badge,:price,:sort_order,:is_visible,NOW())';
        db()->prepare($sql)->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $sql = 'UPDATE home_items SET group_key=:group_key,title=:title,`text`=:text,image_url=:image_url,link_url=:link_url,badge=:badge,price=:price,sort_order=:sort_order,is_visible=:is_visible,updated_at=NOW() WHERE id=:id';
        db()->prepare($sql)->execute($data);
    }

    public static function delete(int $id): void
    {
        db()->prepare('DELETE FROM home_items WHERE id = :id')->execute(['id' => $id]);
    }
}
