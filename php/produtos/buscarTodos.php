<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");/*

require_once '../conexao.php';
$conexao = getConnection();

try{
    $stmt = $conexao->prepare("SELECT * FROM produto");

    $stmt->execute();
    $resultado = $stmt->get_result();
    $produtos = $resultado->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['dados' => $produtos]);

    $stmt->close();
    $conexao->close();
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['mensagem' => 'algo deu errado']);
}*/

require '../conexao.php';

try {
    $conn = getConnection();

    $sql = "SELECT 
                p.ID_Produto, p.nome, p.imagem, p.quantidade, p.preco,
                pc.ID_Categoria, c.nome AS nome_categoria
            FROM produto p
            LEFT JOIN produto_categoria pc ON p.ID_Produto = pc.ID_Produto
            LEFT JOIN categoria c ON pc.ID_Categoria = c.ID_Categoria";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $produtos = [];
    while ($linha = $result->fetch_assoc()) {
        $produtos[] = $linha;
    }

    echo json_encode(["dados" => $produtos]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["erro" => $e->getMessage()]);
}