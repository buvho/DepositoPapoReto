const senhaPadrao = "paporeto123"; // Troque aqui pela senha real

document.getElementById("loginForm").addEventListener("submit", function(event) {
  event.preventDefault();

  const senhaDigitada = document.getElementById("senha").value;

  if (senhaDigitada === senhaPadrao) {
    window.location.href = "catalogo.html";
  } else {
    document.getElementById("mensagemErro").style.display = "block";
  }
});
