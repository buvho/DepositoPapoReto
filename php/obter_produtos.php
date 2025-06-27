<?php
require 'conexao.php';
$resultado = $conn->query("SELECT * FROM produtos");
$produtos = [];

while ($linha = $resultado->fetch_assoc()) {
    $produtos[] = $linha;
}

echo json_encode($produtos);
