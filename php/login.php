<?php
session_start();
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $senha = $_POST['senha'] ?? '';

    // Buscar o funcionário (fixo por enquanto)
    $sql = "SELECT * FROM funcionarios WHERE nome = 'Funcionário Padrão' LIMIT 1";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows === 1) {
        $funcionario = $resultado->fetch_assoc();

        if (password_verify($senha, $funcionario['senha'])) {
            $_SESSION['logado'] = true;
            $_SESSION['funcionario'] = $funcionario['nome'];
            header("Location: catalogo.php");
            exit;
        }
    }

    $_SESSION['erro_login'] = "Senha incorreta!";
    header("Location: index.html");
    exit;
}
