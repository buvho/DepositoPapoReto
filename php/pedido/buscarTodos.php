<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();

try {
    $stmt = $conexao->prepare("
        SELECT 
            pedido.ID_Pedido,
            pedido.Status, 
            pedido_produto.quantidade AS quantidade_pedida,
            pedido.nome_cliente,
            produto.ID_Produto,
            produto.nome,
            produto.imagem,
            produto.quantidade AS estoque
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
                'Status' => $linha['Status'],
                'produtos' => []
            ];
        }
        $pedidos[$idPedido]['produtos'][] = [
            'ID_Produto' => $linha['ID_Produto'],
            'nome' => $linha['nome'],
            'imagem' => $linha['imagem'],
            'quantidade_pedida' => $linha['quantidade_pedida'],
            'estoque' => $linha['estoque']
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
