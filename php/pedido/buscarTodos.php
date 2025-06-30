<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();

try {
    $stmt = $conexao->prepare("
        SELECT 
            pedido.ID_Pedido,
            pedido.status,
            pedido.valor,
            pedido_produto.quantidade,
            pedido.nome_cliente,
            produto.ID_Produto,
            produto.nome
        FROM pedido 
        JOIN pedido_produto ON pedido.ID_Pedido = pedido_produto.ID_Pedido 
        JOIN produto ON pedido_produto.ID_Produto = produto.ID_Produto 
        ORDER BY pedido.ID_Pedido
    ");
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dados_brutos = $resultado->fetch_all(MYSQLI_ASSOC);

    $pedidos = [];

    foreach ($dados_brutos as $linha) {
        $idPedido = $linha['ID_Pedido'];

        if (!isset($pedidos[$idPedido])) {
            $pedidos[$idPedido] = [
                'ID_Pedido' => $idPedido,
                'status' => $linha['status'],
                'nome_cliente' => $linha['nome_cliente'],
                'valor' => $linha['valor'],
                'produtos' => []
            ];
        }
        $pedidos[$idPedido]['produtos'][] = [
            'ID_Produto' => $linha['ID_Produto'],
            'nome' => $linha['nome'],
            'quantidade' => $linha['quantidade'],
        ];
    }

    $pedidos = array_values($pedidos);

    echo json_encode(['dados' => $pedidos], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['mensagem' => 'algo deu errado: ' . $e->getMessage()]);
}
