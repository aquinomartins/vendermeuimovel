<?php
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$whatsapp = isset($_POST['email']) ? trim($_POST['email']) : '';
$propertyType = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$propertyNeighborhood = isset($_POST['property_neighborhood']) ? trim($_POST['property_neighborhood']) : '';

$to = 'hithemestarz@gmail.com';
$subject = 'Nova solicitação de avaliação de imóvel';

$body = "";
$body .= "Nome: ";
$body .= $name;
$body .= "\n\n";

$body .= "WhatsApp: ";
$body .= $whatsapp;
$body .= "\n\n";

$body .= "Tipo de imóvel: ";
$body .= $propertyType;
$body .= "\n\n";

$body .= "Bairro do imóvel: ";
$body .= $propertyNeighborhood;
$body .= "\n";

$headers = 'From: noreply@vendermeuimovel.com.br' . "\r\n";
$headers .= 'Reply-To: noreply@vendermeuimovel.com.br' . "\r\n";

if ($name !== '' && $whatsapp !== '' && $propertyType !== '' && $propertyNeighborhood !== '') {
    mail($to, $subject, $body, $headers);
    echo '<span id="valid">Obrigado! Recebemos seus dados e entraremos em contato em breve.</span>';
} else {
    echo '<span id="invalid">Por favor, preencha todos os campos obrigatórios.</span>';
}
