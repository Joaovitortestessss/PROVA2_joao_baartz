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
$sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
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
<div style="text-align:center; font-weight:bold; font-size:22px; margin-top:10px;">
    João Vitor Luçolli Baartz
</div>
    <h2>Excluir Funcionário</h2>

    <?php if (!empty($funcionarios)): ?>
        <table border="1">
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
                    <td>
                        <a href="excluir_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>" onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum funcionário encontrado.</p>
    <?php endif; ?>

    <a href="principal.php">Voltar</a>
</body>
</html>