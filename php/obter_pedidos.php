<?php
require 'conexao.php';

$sql = "
SELECT p.id, p.nome_comprador, i.produto_id, i.quantidade, i.preco_unitario, pr.nome
FROM pedidos p
JOIN itens_pedido i ON p.id = i.pedido_id
JOIN produtos pr ON pr.id = i.produto_id
WHERE p.status = 'pendente'
ORDER BY p.id DESC
";

$result = $conn->query($sql);
$pedidos = [];

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    if (!isset($pedidos[$id])) {
        $pedidos[$id] = [
            'id' => $id,
            'nome_comprador' => $row['nome_comprador'],
            'itens' => [],
            'total' => 0
        ];
    }

    $pedidos[$id]['itens'][] = [
        'nome' => $row['nome'],
        'quantidade' => $row['quantidade'],
        'preco_unitario' => $row['preco_unitario']
    ];

    $pedidos[$id]['total'] += $row['quantidade'] * $row['preco_unitario'];
}

echo json_encode(array_values($pedidos));
?>
