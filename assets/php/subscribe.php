<?php
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$bairroImovel = isset($_POST['bairroImovel']) ? trim($_POST['bairroImovel']) : '';

$to = 'hithemestarz@gmail.com';
$subject = 'You Have new subscriber!';

$body = "";
$body .= "Name: ";
$body .= $name;
$body .= "\n\n";

$body .= "";
$body .= "Email: ";
$body .= $email;
$body .= "\n\n";

$body .= "";
$body .= "Phone no.: ";
$body .= $phone;
$body .= "\n\n";

$body .= "Bairro do Imóvel: ";
$body .= $bairroImovel;
$body .= "\n";

$headers = 'From: ' .$email . "\r\n";

if ($bairroImovel === '') {
    echo '<span id="invalid">Por favor, selecione o bairro do imóvel.</span>';
    exit;
}

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
mail($to, $subject, $body, $headers);
echo '<span id="valid">Thank you for your subscription! We will notice you as soon as possible</span>';
}else{
echo '<span id="invalid">Something gets wrong. Please try again.</span>';
}
