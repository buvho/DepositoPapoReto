<?php
// finalizar_pedido.php
require 'conexao.php';

$id = $_POST['id'] ?? null;
$acao = $_POST['acao'] ?? null; // 'confirmar' ou 'cancelar'

if (!$id || !$acao) {
    http_response_code(400);
    echo "Dados incompletos.";
    exit;
}

if ($acao === 'confirmar') {
    $stmt = $conn->prepare("UPDATE pedidos SET status = 'confirmado' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "Pedido confirmado.";

} elseif ($acao === 'cancelar') {
    // Repor estoque antes de cancelar
    $itens = $conn->prepare("SELECT produto_id, quantidade FROM itens_pedido WHERE pedido_id = ?");
    $itens->bind_param("i", $id);
    $itens->execute();
    $result = $itens->get_result();

    while ($row = $result->fetch_assoc()) {
        $produto_id = $row['produto_id'];
        $qtd = $row['quantidade'];
        
        $update = $conn->prepare("UPDATE produtos SET quantidade = quantidade + ? WHERE id = ?");
        $update->bind_param("ii", $qtd, $produto_id);
        $update->execute();
    }

    // Agora muda status
    $stmt = $conn->prepare("UPDATE pedidos SET status = 'cancelado' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "Pedido cancelado e estoque revertido.";

} else {
    http_response_code(400);
    echo "Ação inválida.";
}
?>

