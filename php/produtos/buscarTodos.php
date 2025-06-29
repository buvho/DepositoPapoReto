<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();

try{
    $stmt = $conexao->prepare(
    "SELECT 
        produto.ID_Produto,
        produto.nome,
        produto.preco,
        produto.quantidade,
        produto.imagem,
        categoria.nome AS categoria
    FROM produto
    JOIN categoria ON produto.ID_Categoria = categoria.ID_Categoria");

    $stmt->execute();
    $resultado = $stmt->get_result();
    $produtos = $resultado->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['dados' => $produtos]);

    $stmt->close();
    $conexao->close();
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['mensagem' => 'algo deu errado']);
}