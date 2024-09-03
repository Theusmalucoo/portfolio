<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Credenciais corretas (substitua com as suas credenciais)
    $valid_user = 'Theusmalucoo';
    $valid_password = 'Pt04052002';

    if ($username === $valid_user && $password === $valid_password) {
        $_SESSION['user'] = $username;
        header('Location: admin.php');
    } else {
        $error = 'Credenciais incorretas';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
</head>
<body>
    <form action="login.php" method="POST">
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Entrar</button>
    </form>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</body>
</html>
