<?php
session_start();
require_once 'conexao.php';

if ($_SESSION['perfil'] != 1){
    echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome = trim($_POST['nome_funcionario']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);

    if (empty($nome) || empty($email)) {
        echo "<script>alert('Nome e Email são obrigatórios!');</script>";
    } else {
        $sql = "SELECT id_funcionario FROM funcionario WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Já existe funcionário com este e-mail!');</script>";
        } else {
            $sql = "INSERT INTO funcionario (nome_funcionario, endereco, telefone, email) VALUES (:nome, :endereco, :telefone, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()){
                echo "<script>alert('Funcionário cadastrado com sucesso!');window.location.href='cadastro_funcionario.php';</script>";
            } else {
                echo "<script>alert('Erro ao cadastrar funcionário!');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Funcionário</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    // Permitir apenas letras no campo nome
    function somenteLetras(e) {
        let char = String.fromCharCode(e.which);
        if (!/[a-zA-ZÀ-ÿ\s]/.test(char) && e.which !== 8 && e.which !== 0) {
            e.preventDefault();
        }
    }
    // Permitir apenas números no campo telefone
    function somenteNumeros(e) {
        let char = String.fromCharCode(e.which);
        if (!/[0-9]/.test(char) && e.which !== 8 && e.which !== 0) {
            e.preventDefault();
        }
    }
    // Bloquear colar caracteres inválidos
    function validarNomeOnPaste(e) {
        let paste = (e.clipboardData || window.clipboardData).getData('text');
        if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(paste)) {
            e.preventDefault();
        }
    }
    function validarTelefoneOnPaste(e) {
        let paste = (e.clipboardData || window.clipboardData).getData('text');
        if (!/^\d+$/.test(paste)) {
            e.preventDefault();
        }
    }
    </script>
</head>
<body>
<div style="text-align:center; font-weight:bold; font-size:22px; margin-top:10px;">
    João Vitor Luçolli Baartz
</div>
    <h2>Cadastrar Funcionário</h2>
    <form action="cadastro_funcionario.php" method="POST" autocomplete="off">
        <label for="nome_funcionario">Nome:</label>
        <input type="text" id="nome_funcionario" name="nome_funcionario" required
               pattern="[A-Za-zÀ-ÿ\s]+"
               title="Digite apenas letras"
               onkeypress="somenteLetras(event)"
               onpaste="validarNomeOnPaste(event)">

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco">

        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone"
               pattern="[0-9]+"
               title="Digite apenas números"
               onkeypress="somenteNumeros(event)"
               onpaste="validarTelefoneOnPaste(event)">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>
    <a href="principal.php">Voltar</a>
</body>
</html>