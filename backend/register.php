<?php
// register.php
session_start();
require_once 'db.php'; // připojí $pdo

// Jednoduchá funkce pro odeslání chyb a návrat
function redirect_with_error($msg) {
    $_SESSION['flash_error'] = $msg;
    header('Location: /register.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_error('Neplatná žádost.');
}

// Načtení a trim vstupu
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// 1) Validace
if ($name === '' || $email === '' || $password === '') {
    redirect_with_error('Vyplňte všechna pole.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_with_error('Neplatný email.');
}

if (strlen($password) < 8) {
    redirect_with_error('Heslo musí mít alespoň 8 znaků.');
}

// 2) Kontrola, jestli uživatel existuje
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    redirect_with_error('Uživatel s tímto emailem již existuje.');
}

// 3) Hash hesla - používáme PHP funkci, která automaticky přidá salt
$password_hash = password_hash($password, PASSWORD_DEFAULT);
// PASSWORD_DEFAULT používá bezpečný algoritmus (bcrypt/argon2 podle verze PHP)

// 4) Uložení do DB
$stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
try {
    $stmt->execute([$name, $email, $password_hash]);
} catch (Exception $e) {
    // v produkci logovat
    redirect_with_error('Chyba při ukládání do databáze.');
}

// 5) Hotovo - přesměrovat na přihlášení s úspěšnou zprávou
$_SESSION['flash_success'] = 'Registrace proběhla úspěšně. Přihlaste se.';
header('Location: /login.html');
exit;