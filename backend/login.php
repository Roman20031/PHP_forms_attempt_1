<?php
// login.php
session_start();
require_once 'db.php';

function redirect_with_error($msg) {
    $_SESSION['flash_error'] = $msg;
    header('Location: /login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_error('Neplatná žádost.');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    redirect_with_error('Vyplňte email i heslo.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_with_error('Neplatný email.');
}

// Najdi uživatele
$stmt = $pdo->prepare('SELECT id, name, password_hash FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    redirect_with_error('Uživatel nenalezen.');
}

// Ověření hesla
if (!password_verify($password, $user['password_hash'])) {
    redirect_with_error('Špatné heslo.');
}

// Úspěšné přihlášení:
// Uložíme do session nezbytná data
session_regenerate_id(true); // bezpečnost - nová session id
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];

// Přesměruj na chráněnou domovskou stránku
header('Location: /home.php');
exit;