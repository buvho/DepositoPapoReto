<?php
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "Produto removido com sucesso.";
    } else {
        http_response_code(400);
        echo "ID inválido.";
    }
} else {
    http_response_code(405);
    echo "Método não permitido.";
}
?>
