<?php
require '../conexao.php';

try{
    $conn = getConnection();

    $stmt = $conn->prepare("SELECT * FROM categoria");
    $stmt->execute();
    $result = $stmt->get_result();

    $categorias = [];
    while($linha = $result->fetch_assoc()){
        $categorias[] = $linha;
    }

    echo json_encode($categorias);
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no servidor: '. $e->getMessage()]);
}
?>