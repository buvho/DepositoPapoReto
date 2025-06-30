let produtosSelecionados = [];
let produtoAtual = null;

document.addEventListener("DOMContentLoaded", () => {
  carregarProdutos();
  carregarPedidosPendentes();

  document.getElementById("btn-finalizar-pedido").addEventListener("click", () => {
    console.log(produtosSelecionados)
    if (produtosSelecionados.length > 0) {
      document.getElementById("modal-finalizar").style.display = "block";
    } else {
      alert("Selecione pelo menos um produto para continuar.");
    }
  });
});

function carregarProdutos() {
  fetch("php/produtos/buscarTodos.php")
    .then(response => response.json())
    .then(produtos => {
      console.log(produtos)
      const container = document.getElementById("produtos-container");
      container.innerHTML = "";
      produtos.dados
        .forEach(prod => {
          const div = document.createElement("div");
          div.classList.add("produto-item");

          div.innerHTML = `
            <img src="imagens/${prod.imagem}" width="100"><br>
            <strong>${prod.nome}</strong><br>
            <p>Estoque: ${prod.quantidade}</p>
            <p>R$ ${parseFloat(prod.preco).toFixed(2)}</p>
            <button class="btn" onclick='selecionarProduto(${JSON.stringify(prod)})'>Selecionar</button>
          `;
        container.appendChild(div);
      });
    });
}

function selecionarProduto(prod) {
  produtoAtual = prod;
  document.getElementById("modal-quantidade").style.display = "block";
}

function confirmarQuantidade() {
  const qtd = parseInt(document.getElementById("quantidade-retirada").value);
  if (isNaN(qtd) || qtd <= 0 || qtd > produtoAtual.quantidade) {
    alert("Quantidade inválida");
    return;
  }

  produtosSelecionados.push({
    ...produtoAtual,
    quantidade: qtd
  });

  fecharModalQuantidade();
}

function fecharModalQuantidade() {
  document.getElementById("modal-quantidade").style.display = "none";
  document.getElementById("quantidade-retirada").value = "";
}

function fecharModalFinalizar() {
  document.getElementById("modal-finalizar").style.display = "none";
}

function salvarParaDepois() {
  const nome = document.getElementById("nome-comprador").value;
  if (!nome) {
    alert("Informe o nome do comprador");
    return;
  }
  console.log(JSON.stringify({ nome, itens: produtosSelecionados, status: "pendente" }))
  fetch("php/pedido/adicionar.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ nome, itens: produtosSelecionados, status: "pendente" })
  })
    .then(() => location.reload())
    .catch(err => console.error(err));
}

function confirmarPedido() {
  const nome = document.getElementById("nome-comprador").value;
  if (!nome) {
    alert("Informe o nome do comprador");
    return;
  }
  console.log(JSON.stringify({ nome, itens: produtosSelecionados }))
  fetch("php/pedido/finalizarPedidoNaHora.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ nome, itens: produtosSelecionados })
  })
   .then(() => location.reload())
    .catch(err => console.error(err));
}

function carregarPedidosPendentes() {
  fetch("php/pedido/buscarTodos.php")
    .then(res => res.json())
    .then(pedidos => {
      const container = document.getElementById("pedidos-pendentes");
      container.innerHTML = "";
      console.log(pedidos);
      pedidos.dados.forEach(pedido => {
        const div = document.createElement("div");
        div.classList.add("pedido-pendente");
        const itens = pedido.produtos.map(i => `${i.nome} (x${i.quantidade})`).join(", ");
        const itensPedido = pedido.produtos.map(i => ({ id: i.ID_Produto, quantidade: i.quantidade}));
        const stringfy = JSON.stringify({ id: pedidos.ID_Pedido, p: itensPedido});
        div.innerHTML = `
          <strong>${pedido.nome_cliente}</strong><br>
          <p><strong>Produtos:</strong> ${itens}</p>
          <p><strong>Total:</strong>R$ ${pedido.valor} </p>
          <div class="botoes-card">
            <button class="btn" onclick="confirmarPedido(${pedido.ID_Pedido})">Confirmar Pagamento</button>
            <button class="btn" onclick="cancelarPedido(${pedido.ID_Pedido})">Cancelar</button>
          </div>
        `;

        container.appendChild(div);
      });
    });
}

function confirmarPedido(id) {
  fetch("php/pedido/remover.php", {
    method: "POST",
    body: JSON.stringify({ ID_Pedido: id })
  })
    .then(() => location.reload());
}

function cancelarPedido(id) {
 if (confirm("Tem certeza que deseja cancelar este pedido?")) {
    fetch("php/pedido/cancelar.php", {
      method: "POST",
      body: JSON.stringify({ ID_Pedido: id})
    })
      .then(() => location.reload());
  }
}
