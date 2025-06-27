let produtosSelecionados = [];
let produtoAtual = null;

document.addEventListener("DOMContentLoaded", () => {
  carregarProdutos();
  carregarPedidosPendentes();

  document.getElementById("btn-finalizar-pedido").addEventListener("click", () => {
    if (produtosSelecionados.length > 0) {
      document.getElementById("modal-finalizar").style.display = "block";
    } else {
      alert("Selecione pelo menos um produto para continuar.");
    }
  });
});

function carregarProdutos() {
  fetch("php/obter_produtos.php")
    .then(res => res.json())
    .then(produtos => {
      const container = document.getElementById("produtos-container");
      container.innerHTML = "";

      produtos.forEach(prod => {
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

  fetch("php/salvar_pedido.php", {
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

  fetch("php/finalizar_pedido.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ nome, itens: produtosSelecionados })
  })
    .then(() => location.reload())
    .catch(err => console.error(err));
}

function carregarPedidosPendentes() {
  fetch("php/obter_pedidos.php")
    .then(res => res.json())
    .then(pedidos => {
      const container = document.getElementById("pedidos-pendentes");
      container.innerHTML = "";

      pedidos.forEach(p => {
        const div = document.createElement("div");
        div.classList.add("pedido-pendente");
        const itens = p.itens.map(i => `${i.nome} (x${i.quantidade})`).join(", ");

        div.innerHTML = `
          <strong>${p.nome_comprador}</strong><br>
          <p><strong>Produtos:</strong> ${itens}</p>
          <p><strong>Total:</strong> R$ ${p.total.toFixed(2)}</p>
          <div class="botoes-card">
            <button class="btn" onclick="confirmarPagamento(${p.id})">Confirmar Pagamento</button>
            <button class="btn" onclick="cancelarPedido(${p.id})">Cancelar</button>
          </div>
        `;

        container.appendChild(div);
      });
    });
}

function confirmarPagamento(id) {
  fetch("php/confirmar_pagamento.php", {
    method: "POST",
    body: new URLSearchParams({ id })
  })
    .then(() => location.reload());
}

function cancelarPedido(id) {
  if (confirm("Tem certeza que deseja cancelar este pedido?")) {
    fetch("php/cancelar_pedido.php", {
      method: "POST",
      body: new URLSearchParams({ id })
    })
      .then(() => location.reload());
  }
}
