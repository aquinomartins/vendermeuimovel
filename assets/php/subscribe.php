<?php
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$whatsapp = isset($_POST['email']) ? trim($_POST['email']) : '';
$tipoImovel = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$bairroImovel = isset($_POST['bairroImovel']) ? trim($_POST['bairroImovel']) : '';

$to = 'hithemestarz@gmail.com';
$subject = 'Nova solicitação de avaliação de imóvel';

if ($name === '' || $whatsapp === '' || $tipoImovel === '' || $bairroImovel === '') {
    echo '<span id="invalid">Por favor, preencha todos os campos obrigatórios.</span>';
    exit;
}

$body = "";
$body .= "Nome: ";
$body .= $name;
$body .= "\n\n";

$body .= "WhatsApp: ";
$body .= $whatsapp;
$body .= "\n\n";

$body .= "Tipo de Imóvel: ";
$body .= $tipoImovel;
$body .= "\n\n";

$body .= "Bairro do Imóvel: ";
$body .= $bairroImovel;
$body .= "\n";

$headers = 'From: noreply@vendermeuimovel.com.br' . "\r\n";
$headers .= 'Reply-To: noreply@vendermeuimovel.com.br' . "\r\n";

mail($to, $subject, $body, $headers);
echo '<span id="valid">Obrigado! Recebemos seus dados e entraremos em contato.</span>';
