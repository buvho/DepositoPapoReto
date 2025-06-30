<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';/* Do caio
$conexao = getConnection();
try{
    $dados = json_decode(file_get_contents('php://input'), true);
    if (empty($dados['nome']) || empty($dados['quantidade']) || empty($dados['preco'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'algum valor esta faltando']);
        exit;
    }
    
    $stmt = $conexao->prepare("INSERT INTO produto (nome,quantidade,preco,imagem) VALUES (?,?,?,?)");
    $stmt->bind_param("siis", $dados['nome'],$dados['quantidade'],$dados['preco'],$dados['imagem']);

    if($stmt->execute()){
        http_response_code(201);
        echo json_encode(['mensagem' => 'valor inserido com sucesso']);
    } else {
        http_response_code(422);
        echo json_encode(['mensagem' => 'algo deu errado :/']);
    }

    $stmt->close();
    $conexao->close();
    
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['mensagem' => 'algo deu errado ' . $e ]);
}*/

/*try { //primeira tentativa minha (daniel)
    $conn = getConnection();

    // Lê os dados JSON do frontend
    $data = json_decode(file_get_contents("php://input"), true);

    $nome = $data["nome"];
    $imagem = $data["imagem"] ?? null;
    $quantidade = $data["quantidade"];
    $preco = $data["preco"];
    $idCategoria = $data["ID_Categoria"];

    // Insere o produto
    $stmt = $conn->prepare("INSERT INTO produto (nome, imagem, quantidade, preco) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $nome, $imagem, $quantidade, $preco);
    $stmt->execute();

    // Obtém o ID do produto recém-criado
    $idProduto = $conn->insert_id;

    // Relaciona o produto com a categoria
    $stmt = $conn->prepare("INSERT INTO produto_categoria (ID_Produto, ID_Categoria) VALUES (?, ?)");
    $stmt->bind_param("ii", $idProduto, $idCategoria);
    $stmt->execute();

    echo json_encode(["sucesso" => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["erro" => $e->getMessage()]);
}*/

try {
    $conn = getConnection();
    $data = json_decode(file_get_contents("php://input"), true);

    $nome = $data["nome"];
    $imagem = $data["imagem"] ?? null;
    $quantidade = $data["quantidade"];
    $preco = $data["preco"];
    $idCategoria = isset($data["ID_Categoria"]) ? (int)$data["ID_Categoria"] : null;

    $stmt = $conn->prepare("INSERT INTO produto (nome, imagem, quantidade, preco) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $nome, $imagem, $quantidade, $preco);
    $stmt->execute();

    $idProduto = $conn->insert_id;

    // Só insere a relação se a categoria for válida
    if ($idCategoria && $idCategoria > 0) {
        $stmt = $conn->prepare("INSERT INTO produto_categoria (ID_Produto, ID_Categoria) VALUES (?, ?)");
        $stmt->bind_param("ii", $idProduto, $idCategoria);
        $stmt->execute();
    }

    echo json_encode(["sucesso" => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["erro" => $e->getMessage()]);
}