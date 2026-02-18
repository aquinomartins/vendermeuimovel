<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/db.php';

$sql = file_get_contents(__DIR__ . '/schema.sql');
db()->exec($sql ?: '');

$adminName = env_value('ADMIN_NAME', 'Administrador');
$adminEmail = env_value('ADMIN_EMAIL', 'admin@local.test');
$adminPass = env_value('ADMIN_PASSWORD', 'admin123');

$stmt = db()->prepare('INSERT INTO users (name,email,password_hash,role,created_at) VALUES (:name,:email,:password_hash,:role,NOW()) ON DUPLICATE KEY UPDATE name=VALUES(name), password_hash=VALUES(password_hash), role=VALUES(role)');
$stmt->execute([
    'name' => $adminName,
    'email' => $adminEmail,
    'password_hash' => password_hash($adminPass, PASSWORD_DEFAULT),
    'role' => 'admin',
]);

$pageStmt = db()->prepare('INSERT INTO pages (slug,title,meta_title,meta_description,is_published,updated_at) VALUES (:slug,:title,:meta_title,:meta_description,1,NOW()) ON DUPLICATE KEY UPDATE title=VALUES(title), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description), is_published=1, updated_at=NOW()');
$pageStmt->execute([
    'slug' => 'home',
    'title' => 'Aurora Imóveis',
    'meta_title' => 'Aurora Imóveis | Seu próximo endereço começa aqui',
    'meta_description' => 'Atendimento consultivo para compra, venda e investimento.',
]);

$pageId = (int) db()->query("SELECT id FROM pages WHERE slug='home' LIMIT 1")->fetchColumn();

db()->prepare('DELETE FROM sections WHERE page_id = :page_id')->execute(['page_id' => $pageId]);

$sectionTypes = ['hero', 'features', 'testimonials', 'faq', 'cta', 'footer'];
$sectionInsert = db()->prepare('INSERT INTO sections (page_id,type,title,subtitle,sort_order,is_visible,updated_at) VALUES (:page_id,:type,:title,:subtitle,:sort_order,1,NOW())');
$itemInsert = db()->prepare('INSERT INTO section_items (section_id,title,`text`,image_url,link_url,sort_order,is_visible,updated_at) VALUES (:section_id,:title,:text,:image_url,:link_url,:sort_order,1,NOW())');

$seedContent = [
    'hero' => [['title' => 'Descubra seu novo lar', 'text' => 'Curadoria local e atendimento próximo para compra e venda.', 'image_url' => '', 'link_url' => '#lead-form']],
    'features' => [['title' => 'Atendimento consultivo', 'text' => 'Equipe especialista no mercado local.', 'image_url' => '', 'link_url' => ''], ['title' => 'Negociação segura', 'text' => 'Processos transparentes do início ao fim.', 'image_url' => '', 'link_url' => '']],
    'testimonials' => [['title' => 'Ana e Carlos', 'text' => 'Encontramos o imóvel ideal com suporte total.', 'image_url' => '', 'link_url' => '']],
    'faq' => [['title' => 'Quais documentos preciso?', 'text' => 'Depende da negociação; nossa equipe orienta todo o processo.', 'image_url' => '', 'link_url' => '']],
    'cta' => [['title' => 'Quer vender seu imóvel?', 'text' => 'Anuncie com quem entende de performance imobiliária.', 'image_url' => '', 'link_url' => '#lead-form']],
    'footer' => [['title' => 'Contato', 'text' => 'Fale pelo WhatsApp e redes sociais.', 'image_url' => '', 'link_url' => '']],
];

foreach ($sectionTypes as $index => $type) {
    $sectionInsert->execute([
        'page_id' => $pageId,
        'type' => $type,
        'title' => ucfirst($type),
        'subtitle' => 'Seção editável no admin',
        'sort_order' => $index,
    ]);
    $sectionId = (int) db()->lastInsertId();

    foreach ($seedContent[$type] as $itemIndex => $item) {
        $itemInsert->execute([
            'section_id' => $sectionId,
            'title' => $item['title'],
            'text' => $item['text'],
            'image_url' => $item['image_url'],
            'link_url' => $item['link_url'],
            'sort_order' => $itemIndex,
        ]);
    }
}

$settings = [
    'site_name' => 'Aurora Imóveis',
    'whatsapp' => 'https://wa.me/5511999999999',
    'logo' => '/assets/placeholders.svg',
    'social_instagram' => 'https://instagram.com',
    'social_facebook' => 'https://facebook.com',
    'footer_text' => 'Aurora Imóveis conecta você ao imóvel ideal.',
];

$settingStmt = db()->prepare('INSERT INTO site_settings (`key`,`value`,updated_at) VALUES (:key,:value,NOW()) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), updated_at=NOW()');
foreach ($settings as $key => $value) {
    $settingStmt->execute(['key' => $key, 'value' => $value]);
}

echo "Seed finalizado. Admin: {$adminEmail} / {$adminPass}" . PHP_EOL;
