<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de ADM ou secretaria
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2){
    echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
    exit();
}

$mensagem = "";
$funcionarios = []; // Inicializa a variável

// Processa busca
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    // Validação
    if ($busca === "") {
        $mensagem = "Digite um nome ou ID para pesquisar, ou deixe em branco para listar todos.";
    }

    // Busca por ID ou nome
    if ($busca !== "") {
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca ORDER BY nome_funcionario ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome ORDER BY nome_funcionario ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }
    } else {
        $sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
    }
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($funcionarios)) {
        $mensagem = "Nenhum funcionário encontrado.";
    }
} else {
    // Primeira exibição: mostra todos
    $sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Buscar Funcionário</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
<div style="text-align:center; font-weight:bold; font-size:22px; margin-top:10px;">
    João Vitor Luçolli Baartz
</div>
    <h2>Lista de Funcionários</h2>
    <?php if (!empty($mensagem)): ?>
    <script>
        alert("<?= htmlspecialchars($mensagem) ?>");
    </script>
<?php endif; ?>
    <!-- Formulário para buscar funcionário -->
    <form action="buscar_funcionario.php" method="POST">
        <label for="busca">Digite o ID ou nome (opcional):</label>
        <input type="text" id="busca" name="busca" value="<?= isset($_POST['busca']) ? htmlspecialchars($_POST['busca']) : '' ?>">
        <button type="submit">Pesquisar</button>
    </form>
    
    <?php if ($mensagem): ?>
        <p><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

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
                        <a href="alterar_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>">Alterar</a>
                        <a href="excluir_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>" onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif (!$mensagem): ?>
        <p>Nenhum funcionário encontrado.</p>
    <?php endif; ?>
    <a href="principal.php">Voltar</a>
</body>
</html>