<?php
session_start();
require 'conexao.php';

// Verifica se o usuário tem permissão de ADM ou Secretaria
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

// Inicializa variável para armazenar funcionários
$funcionarios = [];

// Busca todos os funcionários cadastrados em ordem alfabética
$sql = "SELECT * FROM funcionario ORDER BY id_funcionario ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se um ID for passado via GET, exclui o funcionário
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_funcionario = $_GET['id'];

    // Exclui o funcionário do banco de dados
    $sql = "DELETE FROM funcionario WHERE id_funcionario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_funcionario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Funcionário excluído com sucesso!'); window.location.href='excluir_funcionario.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir funcionário!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Funcionário</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page-header">
    João Vitor Luçolli Baartz
</div>
<h2 class="page-title">Excluir Funcionário</h2>

<div class="table-container">
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Endereço</th>
            <th>Telefone</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($funcionarios as $funcionario): ?>
            <tr>
                <td><?= htmlspecialchars($funcionario['id_funcionario']) ?></td>
                <td><?= htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                <td><?= htmlspecialchars($funcionario['endereco']) ?></td>
                <td><?= htmlspecialchars($funcionario['telefone']) ?></td>
                <td><?= htmlspecialchars($funcionario['email']) ?></td>
                <td class="actions">
                    <a class="btn-excluir" href="excluir_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>"
                    onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">
                        <!-- Ícone de lixeira SVG -->
                        <svg viewBox="0 0 24 24"><path d="M9 3V4H4V6H5V19A2 2 0 0 0 7 21H17A2 2 0 0 0 19 19V6H20V4H15V3H9M7 6H17V19H7V6Z"/></svg>
                        Excluir
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<style>
    /* ====== TELA DE EXCLUSÃO DE FUNCIONÁRIO ====== */

/* Topo e título com destaque e fontes maiores */
.page-header {
    margin-top: 22px;
    margin-bottom: 4px;
    font-size: 1.23rem;
    color: #23272b;
    font-family: 'Segoe UI', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.35px;
    text-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

.page-title {
    margin-top: 0;
    margin-bottom: 18px;
    color: #e74c3c;
    font-size: 1.48rem;
    font-weight: bold;
    letter-spacing: 0.4px;
    font-family: 'Segoe UI', Arial, sans-serif;
    text-shadow: 0 2px 6px rgba(231,76,60,0.04);
}

/* Container da tabela, maior e com mais espaço */
.table-container {
    display: flex;
    justify-content: center;
    margin: 0 0 18px 0;
}

/* Tabela moderna, mais espaçada e suave */
table {
    border-collapse: collapse;
    margin: 0 auto;
    background: #fff;
    border-radius: 13px 13px 8px 8px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.09);
    overflow: hidden;
    min-width: 620px;
    max-width: 99vw;
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 1.09rem;
}

/* Cabeçalho claro, elegante e visual leve */
th {
    background: #3b4044;
    color: #fff;
    font-weight: 700;
    font-size: 1.13rem;
    border: none;
    letter-spacing: 0.22px;
    border-bottom: 2.5px solid #e3e6ed;
    text-align: left;
}

th:first-child {
    border-top-left-radius: 13px;
}
th:last-child {
    border-top-right-radius: 13px;
}

th, td {
    padding: 13px 16px;
    text-align: left;
}

tr {
    border-bottom: 1px solid #e3e6ed;
    transition: background 0.18s;
}

tr:nth-child(even) {
    background: #f6f9fb;
}
tr:hover {
    background-color: #fbeaea;
}

td {
    color: #23272b;
    vertical-align: middle;
}

/* Botão de excluir maior, moderno e com ícone */
.actions .btn-excluir {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #fff;
    background: linear-gradient(90deg, #e74c3c 80%, #ff6565 100%);
    border: none;
    border-radius: 5px;
    padding: 7px 18px;
    font-size: 1.04rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.19s, box-shadow 0.18s;
    box-shadow: 0 2px 5px rgba(231,76,60,0.09);
    outline: none;
    text-decoration: none;
    border: 1.3px solid #e74c3c;
}
.actions .btn-excluir:hover, .actions .btn-excluir:focus {
    background: linear-gradient(90deg, #c0392b 80%, #e74c3c 100%);
    border-color: #a62c1a;
    color: #fff;
    text-decoration: none;
    box-shadow: 0 3px 9px rgba(231,76,60,0.17);
}
.actions .btn-excluir svg {
    width: 1.2em;
    height: 1.2em;
    fill: currentColor;
}

/* Botão voltar maior e mais confortável */
a.voltar-btn, a[href="principal.php"] {
    display: inline-block;
    margin: 19px 0 0 0;
    color: #007bff;
    background: #f0f8ff;
    padding: 9px 27px;
    border-radius: 6px;
    font-size: 1.08rem;
    font-weight: 500;
    border: 1.2px solid #b5d5ff;
    transition: background 0.17s, color 0.17s, border 0.17s;
    text-decoration: none;
    box-shadow: 0 1.5px 7px rgba(0,0,0,0.03);
}
a.voltar-btn:hover, a[href="principal.php"]:hover {
    color: #fff;
    background: #007bff;
    border-color: #007bff;
}

@media (max-width: 900px) {
    table, th, td {
        font-size: 1rem;
        min-width: unset;
        padding: 8px 4px;
    }
    table {
        min-width: 98vw;
        max-width: 98vw;
    }
    .table-container {
        margin: 8px 0;
    }
}

/* ========== ALTERAR FUNCIONÁRIO - TELA MODERNA ========== */
.form-card {
    background: #fff;
    border-radius: 13px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.09);
    padding: 28px 32px 24px 32px;
    margin: 20px auto 22px auto;
    max-width: 480px;
    min-width: 300px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

form {
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: none;
    box-shadow: none;
    border-radius: 0;
    padding: 0;
}

/* Para o formulário de busca e de alteração ficarem centralizados com espaçamento */
.form-alterar-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 32px;
    margin-top: 42px;
}

/* Labels alinhadas à esquerda, negrito e com espaçamento agradável */
label {
    font-weight: bold;
    margin: 12px 0 4px 0;
    font-size: 1.05rem;
    color: #23272b;
    align-self: flex-start;
    width: 100%;
}

/* Inputs modernos */
input[type="text"],
input[type="email"],
input[type="password"],
select {
    width: 100%;
    padding: 9px 12px;
    margin-bottom: 8px;
    border: 1.2px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
    background: #f7f8fa;
    transition: border-color 0.18s;
    outline: none;
}
input:focus, select:focus {
    border-color: #007bff;
    background: #f0f8ff;
}

/* Botões principais do formulário */
button[type="submit"], button[type="button"] {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    background: linear-gradient(90deg,#007bff,#5dc7ff 80%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1.08rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.18s, box-shadow 0.18s;
    box-shadow: 0 2px 10px rgba(0,123,255,0.04);
}
button[type="button"] {
    background: linear-gradient(90deg,#f0f0f0,#cce2ff 80%);
    color: #007bff;
    border: 1.2px solid #b5d5ff;
    font-weight: 500;
    margin-top: 12px;
}
button[type="submit"]:hover {
    background: linear-gradient(90deg,#0056b3 70%, #43b0e6 100%);
}
button[type="button"]:hover {
    background: #e3f0ff;
    color: #0056b3;
}

/* Mensagem de erro ou alerta */
.mensagem {
    color: #e74c3c;
    background: #fff3f2;
    border: 1px solid #ffdad6;
    border-radius: 5px;
    margin: 16px auto 14px auto;
    padding: 9px 16px;
    max-width: 410px;
    font-size: 1.02rem;
}

/* Nome do usuário - topo */
.usuario-header {
    font-weight: bold;
    font-size: 22px;
    margin-top: 18px;
}

/* Título principal */
h2, .titulo-principal {
    font-size: 2rem;
    font-weight: bold;
    margin: 12px 0 26px 0;
    color: #23272b;
}

/* Botão Voltar */
a.voltar-btn, a[href="principal.php"] {
    display: inline-block;
    margin: 26px 0 0 0;
    color: #007bff;
    background: #fff;
    padding: 10px 32px;
    border-radius: 7px;
    font-size: 1.1rem;
    font-weight: 500;
    border: 1.5px solid #b5d5ff;
    transition: background 0.18s, color 0.18s, border 0.18s;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
a.voltar-btn:hover, a[href="principal.php"]:hover {
    color: #fff;
    background: #007bff;
    border-color: #007bff;
}

/* Para manter o espaço visual na tela quando só aparece o botão voltar */
.voltar-container {
    margin-top: 28px;
}

@media (max-width: 600px) {
    .form-card {
        padding: 18px 6vw 14px 6vw;
        min-width: 0;
        max-width: 98vw;
    }
    .form-alterar-container {
        gap: 16px;
        margin-top: 18px;
    }
    h2, .titulo-principal {
        font-size: 1.25rem;
        margin: 10px 0 18px 0;
    }
    a.voltar-btn, a[href="principal.php"] {
        padding: 8px 7vw;
        font-size: 1rem;
    }
}
</style>

<a href="principal.php" class="voltar-btn">Voltar</a>
</body>
</html>