document.addEventListener("DOMContentLoaded", function () {
  carregarProdutos();

  document.getElementById("formProduto").addEventListener("submit", function (e) {
    e.preventDefault();
    salvarProduto();
  });

  // Filtro por categoria (corrigido)
  document.querySelectorAll("#filtro-categorias li").forEach(li => {
    li.addEventListener("click", () => {
      const categoria = li.dataset.categoria;
      carregarProdutos(categoria);
    });
  });
});

function carregarProdutos(categoriaSelecionada = "Todos") {
  fetch("php/produtos/buscarTodos.php")
    .then(response => response.json())
    .then(produtos => {
      console.log(produtos)
      const container = document.getElementById("lista-produtos");
      container.innerHTML = "";
      //.filter(prod => categoriaSelecionada === "Todos" || prod.categoria === categoriaSelecionada)
      produtos.dados
        .forEach(prod => {
          const div = document.createElement("div");
                div.classList.add("produto-card");
                  div.innerHTML = `
                    <strong>${prod.nome}</strong>
                    <p><b>Categoria:</b> ${prod.categoria}</p>
                    <p><b>Quantidade:</b> ${prod.quantidade}</p>
                    <p><b>Preço:</b> R$ ${parseFloat(prod.preco).toFixed(2)}</p>
                    <div class="botoes-card">
                      <button onclick='editarProduto(${JSON.stringify(prod)})'>Editar</button>
                      <button onclick='removerProduto(${prod.id})'>Remover</button>
                    </div>
                  `;

          container.appendChild(div);
        });
    });
}


function abrirFormulario() {
  document.getElementById("formularioProduto").style.display = "block";
  document.getElementById("formProduto").reset();
  document.getElementById("produtoId").value = "";
  document.getElementById("titulo-form").innerText = "Novo Produto";
}

function fecharFormulario() {
  document.getElementById("formProduto").reset();
  document.getElementById("produtoId").value = ""; // limpa ID se for edição
  document.getElementById("formularioProduto").style.display = "none";
}

function salvarProduto() {
  const form = document.getElementById("formProduto");
  const dados = new FormData(form);

  fetch("php/produtos/adicionar.php", {
    method: "POST",
    body: dados
  })
  .then(res => res.text())
  .then(() => {
    carregarProdutos();
    fecharFormulario();
  });
}

function editarProduto(produto) {
  abrirFormulario();
  document.getElementById("titulo-form").innerText = "Editar Produto";
  document.getElementById("produtoId").value = produto.id;
  document.getElementById("nome").value = produto.nome;
  document.getElementById("categoria").value = produto.categoria;
  document.getElementById("quantidade").value = produto.quantidade;
  document.getElementById("preco").value = produto.preco;
}

function removerProduto(id) {
  if (confirm("Tem certeza que deseja remover este produto?")) {
    fetch("php/remover_produto.php", {
      method: "POST",
      body: new URLSearchParams({ id })
    })
    .then(res => res.text())
    .then(() => carregarProdutos());
  }
}
