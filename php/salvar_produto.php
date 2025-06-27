<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido";
    exit;
}
require 'conexao.php';

$id = $_POST['id'] ?? '';
$nome = $_POST['nome'];
$categoria = $_POST['categoria'];
$quantidade = $_POST['quantidade'];
$preco = $_POST['preco'];
$imagem = '';

// Processar imagem se houver
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $pasta = "../imagens/";
    $nomeImagem = uniqid() . "_" . $_FILES['imagem']['name'];
    $caminho = $pasta . $nomeImagem;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
        $imagem = $nomeImagem;
    }
}

if ($id) {
    if ($imagem) {
        $stmt = $conn->prepare("UPDATE produtos SET nome=?, categoria=?, quantidade=?, preco=?, imagem=? WHERE id=?");
        $stmt->bind_param("ssidsi", $nome, $categoria, $quantidade, $preco, $imagem, $id);
    } else {
        $stmt = $conn->prepare("UPDATE produtos SET nome=?, categoria=?, quantidade=?, preco=? WHERE id=?");
        $stmt->bind_param("ssidi", $nome, $categoria, $quantidade, $preco, $id);
    }
} else {
    $stmt = $conn->prepare("INSERT INTO produtos (nome, categoria, quantidade, preco, imagem) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $nome, $categoria, $quantidade, $preco, $imagem);
}

$stmt->execute();
