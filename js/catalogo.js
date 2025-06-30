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
                    <img src="imagens/${prod.imagem}">
                    <p><b>Quantidade:</b> ${prod.quantidade}</p>
                    <p><b>Preço:</b> R$ ${parseFloat(prod.preco).toFixed(2)}</p>
                    <div class="botoes-card">
                      <button onclick='editarProduto(${JSON.stringify(prod)})'>Editar</button>
                      <button onclick='removerProduto(${prod.ID_Produto})'>Remover</button>
                    </div>
                  `;

          container.appendChild(div);
        });
    });
}


function abrirFormulario() {
  document.getElementById("formularioProduto").style.display = "block";
  document.getElementById("formProduto").reset();
  document.getElementById("ID_Produto").value = "";
  document.getElementById("titulo-form").innerText = "Adcionar Produto";
}

function fecharFormulario() {
  document.getElementById("formProduto").reset();
  document.getElementById("ID_Produto").value = ""; // limpa ID se for edição
  document.getElementById("formularioProduto").style.display = "none";
}

async function salvarProduto() {
  const form = document.getElementById("formProduto");
  const dados = new FormData(form);
  const jsonData = {};
  dados.forEach((value, key) => {
    if (key != "imagem")
      jsonData[key] = value;
  })
  const imagem = dados.get("imagem")
  if (imagem && imagem.size > 0) {
    const imagemForm = new FormData();
    imagemForm.append("imagem", imagem);

    const res = await fetch("php/produtos/uploadImagem.php", {
      method: "POST",
      body: imagemForm
    });

    const data = await res.json();
    if (data.caminho) {
      jsonData["imagem"] = data.caminho;
    } else {
      alert("Erro ao enviar imagem");
      return;
    }
  }
  const rota = jsonData.ID_Produto ? "php/produtos/editar.php" : "php/produtos/adicionar.php";
  const metodo = jsonData.ID_Produto ? "PUT" : "POST";

  console.log("depois: "+ JSON.stringify(jsonData))
  fetch(rota, {
    method: metodo,
    body: JSON.stringify(jsonData)
  })
  .then(res => res.text())
  .then(() => {
    carregarProdutos();
    fecharFormulario();
  });
}

function editarProduto(produto) {
  abrirFormulario();
    console.log(produto)
    document.getElementById("titulo-form").innerText = "Editar Produto";
    document.getElementById("ID_Produto").value = produto.ID_Produto;
    document.getElementById("nome").value = produto.nome;
    //document.getElementById("categoria").value = produto.categoria;
    document.getElementById("quantidade").value = produto.quantidade;
    document.getElementById("preco").value = produto.preco;
}

function removerProduto(id) {
  if (confirm("Tem certeza que deseja remover este produto?")) {
    fetch("php/produtos/remover.php", {
      method: "POST",
      body: JSON.stringify({ ID_Produto: id })
    })
    .then(res => res.text())
    .then(() => carregarProdutos());
  }
}
