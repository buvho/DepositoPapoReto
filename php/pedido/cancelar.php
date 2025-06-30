<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();

try {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['ID_Pedido'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'ID do pedido está faltando']);
        exit;
    }

    $conexao->begin_transaction();

    $stmt = $conexao->prepare("SELECT ID_Produto, quantidade FROM pedido_produto WHERE ID_Pedido = ?");
    $stmt->bind_param("i", $dados['ID_Pedido']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $produtos = [];

    while ($row = $resultado->fetch_assoc()) {
        $produtos[] = $row;
    }

    $stmt->close();

    foreach ($produtos as $produto) {
        $stmt = $conexao->prepare("UPDATE produto SET quantidade = quantidade + ? WHERE ID_Produto = ?");
        $stmt->bind_param("ii", $produto['quantidade'], $produto['ID_Produto']);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conexao->prepare("DELETE FROM pedido_produto WHERE ID_Pedido = ?");
    $stmt->bind_param("i", $dados['ID_Pedido']);
    $stmt->execute();
    $stmt->close();

    $stmt = $conexao->prepare("DELETE FROM pedido WHERE ID_Pedido = ?");
    $stmt->bind_param("i", $dados['ID_Pedido']);
    $stmt->execute();
    $stmt->close();

    $conexao->commit();
    http_response_code(200);
    echo json_encode(['mensagem' => 'Pedido deletado e estoque atualizado com sucesso']);

} catch (Exception $e) {
    $conexao->rollback();
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro ao deletar o pedido: ' . $e->getMessage()]);
}
?>
