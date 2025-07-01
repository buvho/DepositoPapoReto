<?php
require_once '../conexao.php';

try{
    $conn = getConnection();

    $stmt = $conn->prepare("SELECT * FROM registros");
    $stmt->execute();
    $result = $stmt->get_result();

    $registros = [];
    while($linha = $result->fetch_assoc()){
        $registros[] = $linha;
    }

    echo json_encode($registros);
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no servidor: '. $e->getMessage()]);
}