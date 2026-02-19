<?php
require_once __DIR__ . '/bootstrap.php';
logout_user();
header('Location: /admin/login.php');
