<?php

declare(strict_types=1);

require __DIR__ . '/content_manager.php';

$template = file_get_contents(getTemplatePath());
if ($template === false) {
    http_response_code(500);
    echo 'Não foi possível carregar o template da página inicial.';
    exit;
}

$defaults = extractEditableContent($template);
$stored = loadStoredContent();
$effective = buildEffectiveContent($defaults, $stored);

echo renderTemplateWithContent($template, $effective);
