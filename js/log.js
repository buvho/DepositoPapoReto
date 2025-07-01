fetch("php/log/buscarTodos.php")
  .then(response => {
    if (!response.ok) throw new Error("Erro na requisição");
    return response.json();
  })
  .then(dados => {
    const lista = document.getElementById("lista-logs");

    dados.forEach(log => {
    console.log(log)
      const li = document.createElement("li");
      li.textContent = log.tipo;
      lista.appendChild(li);
    });
  })
  .catch(error => {
    console.error("Erro ao buscar logs:", error);
  });
