<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/db.php';

$pdo = db();
$schema = file_get_contents(__DIR__ . '/schema.sql');
$pdo->exec($schema ?: '');

$adminEmail = getenv('ADMIN_EMAIL') ?: 'admin@aurora.local';
$adminPass = getenv('ADMIN_PASSWORD') ?: '123456';
$hash = password_hash($adminPass, PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO users (name,email,password_hash,role) VALUES ("Administrador",:email,:password_hash,"admin") ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)')
    ->execute(['email' => $adminEmail, 'password_hash' => $hash]);

$settings = [
    'site_title' => 'Aurora Imóveis | Seu próximo endereço começa aqui',
    'meta_description' => 'Aurora Imóveis conecta você ao imóvel ideal para morar, investir ou vender com segurança.',
    'brand_name' => 'Aurora Imóveis',
    'whatsapp_url' => 'https://wa.me/5511999999999',
];
$stmtSetting = $pdo->prepare('INSERT INTO site_settings (`key`,`value`) VALUES (:key,:value) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
foreach ($settings as $k => $v) {
    $stmtSetting->execute(['key' => $k, 'value' => $v]);
}

$sections = [
    ['hero', 'Atendimento consultivo para compra, venda e investimento', 'Descubra seu novo lar ou o investimento certo para crescer com segurança.', 'Na Aurora Imóveis, você encontra curadoria local, negociação transparente e suporte do primeiro clique até a assinatura.', '', '', '', 1, 1],
    ['finance', 'Consórcio e financiamento sem complicação', '', 'Análise consultiva, comparação de condições e acompanhamento até a assinatura do contrato.', '', 'Saiba mais', '#consorcio', 1, 2],
    ['sell_cta', 'Venda seu imóvel com estratégia', '', 'Posicionamento profissional, fotos que valorizam e divulgação multicanal para acelerar sua negociação.', '/uploads/placeholders.svg', 'Quero anunciar', 'https://wa.me/5511999999999', 1, 3],
    ['work_cta', 'Trabalhe conosco', '', 'Buscamos talentos em atendimento, comercial e marketing para construir experiências imobiliárias excepcionais.', '/uploads/placeholders.svg', 'Quero me candidatar', '#contato', 1, 4],
];
$stmtSection = $pdo->prepare('INSERT INTO home_sections (section_key,title,subtitle,body,image_url,button_label,button_url,is_visible,sort_order) VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE title=VALUES(title),subtitle=VALUES(subtitle),body=VALUES(body),image_url=VALUES(image_url),button_label=VALUES(button_label),button_url=VALUES(button_url),is_visible=VALUES(is_visible),sort_order=VALUES(sort_order)');
foreach ($sections as $s) {
    $stmtSection->execute($s);
}

$pdo->exec('DELETE FROM home_items');
$items = [
    ['typeChips','Apartamento','','','','','','1',1],['typeChips','Casa','','','','','','2',1],['typeChips','Cobertura','','','','','','3',1],
    ['metrics','12000','imóveis em carteira ativa','','','+','',1,1],['metrics','500000','clientes atendidos em todo o DF','','','+','',2,1],['metrics','18','de experiência no mercado imobiliário','','','',' anos',3,1],
    ['readyCards','Residencial Horizonte','Águas Claras · Brasília · 88m² · 3 quartos · 2 vagas','','#','Apartamento','R$ 780.000',1,1],
    ['readyCards','Casa Jardim Lumière','Lago Sul · Brasília · 240m² · 4 quartos · 3 vagas','','#','Casa','R$ 2.490.000',2,1],
    ['launchCards','Aurora Park Residence','Noroeste, Brasília · 67 a 118 m² · 2 e 3 quartos','','#','','',1,1],
    ['launchCards','Viva Eixo Smart Homes','Asa Norte, Brasília · 34 a 56 m² · Studios e 1 quarto','','#','','',2,1],
    ['regionChips','Asa Sul','','','','','','1',1],['regionChips','Asa Norte','','','','','','2',1],['regionChips','Águas Claras','','','','','','3',1],
    ['testimonials','Mariana Costa','A equipe da Aurora foi precisa em cada etapa da compra.','','','','',1,1],['testimonials','Henrique Prado','Excelente suporte na venda do meu imóvel.','','','','',2,1],
    ['pinsList','📍 4 imóveis disponíveis na Asa Sul','','','','','','1',1],['pinsList','📍 3 oportunidades de lançamento em Águas Claras','','','','','','2',1],
];
$stmtItem = $pdo->prepare('INSERT INTO home_items (group_key,title,`text`,image_url,link_url,badge,price,sort_order,is_visible) VALUES (?,?,?,?,?,?,?,?,?)');
foreach ($items as $item) {
    $stmtItem->execute($item);
}

echo "Seed concluído. Admin: {$adminEmail} / {$adminPass}\n";
