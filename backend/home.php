<?php
// home.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Domů</title></head>
<body>
  <h1>Vítejte, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
  <p>Toto je chráněná stránka.</p>
  <a href="/logout.php">Odhlásit</a>
</body>
</html>