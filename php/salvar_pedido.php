<?php
require 'conexao.php';

$data = json_decode(file_get_contents("php://input"), true);
$nome = $data["nome"];
$status = $data["status"];
$itens = $data["itens"];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO pedidos (nome_comprador, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $nome, $status);
    $stmt->execute();
    $pedido_id = $stmt->insert_id;

    $stmt_item = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");

    foreach ($itens as $item) {
        $stmt_item->bind_param("iiid", $pedido_id, $item["id"], $item["quantidade"], $item["preco"]);
        $stmt_item->execute();

        // Atualiza o estoque
        $stmt_update = $conn->prepare("UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?");
        $stmt_update->bind_param("ii", $item["quantidade"], $item["id"]);
        $stmt_update->execute();
    }

    $conn->commit();
    echo "Pedido salvo com sucesso.";
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo "Erro ao salvar pedido: " . $e->getMessage();
}
?>

