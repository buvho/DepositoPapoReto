<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();

try{
    $stmt = $conexao->prepare("SELECT 
        pedido.ID_Pedido,
        pedido.Status, 
        pedido_produto.quantidade,
        produto.nome,
        produto.imagem,
        produto.quantidade
    FROM Pedido 
    join pedido_produto on pedido.ID_Pedido = pedido_produto.ID_Pedido 
    join produto on pedido_produto.ID_Produto = produto.ID_Produto 
    ORDER BY pedido.ID_Pedido");
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    $pedidos = $resultado->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['dados' => $pedidos]);

    $stmt->close();
    $conexao->close();
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['mensagem' => 'algo deu errado']);
}