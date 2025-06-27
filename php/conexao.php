<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "estoque_bebidas";

$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
