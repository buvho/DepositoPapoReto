<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: index.html");
    exit;
}
include 'catalogo.html'; // carrega o HTML da interface
