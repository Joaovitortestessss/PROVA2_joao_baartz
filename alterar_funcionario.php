<?php
session_start();
require_once 'conexao.php';

// Verifica permissão de ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

$funcionario = null;
$mensagem = "";

// Buscar funcionário por ID ou Nome
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['busca_funcionario'])) {
    $busca = trim($_POST['busca_funcionario']);
    if (!empty($busca)) {
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }
        $stmt->execute();
        $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$funcionario) {
            $mensagem = "Funcionário não encontrado!";
        }
    }
}

// Alterar funcionário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['alterar_funcionario'])) {
    $id = $_POST['id_funcionario'];
    $nome = trim($_POST['nome_funcionario']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);

    if (empty($nome) || empty($email)) {
        $mensagem = "Nome e Email são obrigatórios!";
    } else {
        // Verifica duplicidade de email
        $sqlCheck = "SELECT id_funcionario FROM funcionario WHERE email = :email AND id_funcionario <> :id";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        if ($stmtCheck->fetch()) {
            $mensagem = "E-mail já cadastrado para outro funcionário!";
        } else {
            $sql = "UPDATE funcionario SET nome_funcionario = :nome, endereco = :endereco, telefone = :telefone, email = :email WHERE id_funcionario = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':endereco', $endereco, PDO::PARAM_STR);
            $stmt->bindParam(':telefone', $telefone, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "<script>alert('Funcionário alterado com sucesso!');window.location.href='alterar_funcionario.php';</script>";
                exit();
            } else {
                $mensagem = "Erro ao alterar funcionário!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Funcionário</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    function somenteLetras(e) {
        let char = String.fromCharCode(e.which);
        if (!/[a-zA-ZÀ-ÿ\s]/.test(char) && e.which !== 8 && e.which !== 0) {
            e.preventDefault();
        }
    }
    function somenteNumeros(e) {
        let char = String.fromCharCode(e.which);
        if (!/[0-9]/.test(char) && e.which !== 8 && e.which !== 0) {
            e.preventDefault();
        }
    }
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
    function validarAlteracaoFuncionario() {
        var nome = document.getElementById('nome_funcionario').value.trim();
        var email = document.getElementById('email').value.trim();
        if (nome === '' || email === '') {
            alert('Nome e Email são obrigatórios!');
            return false;
        }
        return true;
    }
    function limparCampos() {
        document.getElementById('formAlterarFuncionario').reset();
    }
    </script>
</head>
<body>
<div style="text-align:center; font-weight:bold; font-size:22px; margin-top:10px;">
    João Vitor Luçolli Baartz
</div>
    <h2>Alterar Funcionário</h2>
    <?php if (!empty($mensagem)): ?>
        <script>
            alert("<?= htmlspecialchars($mensagem) ?>");
        </script>
        <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form action="alterar_funcionario.php" method="POST">
        <label for="busca_funcionario">Digite o ID ou Nome do funcionário:</label>
        <input type="text" id="busca_funcionario" name="busca_funcionario" required>
        <button type="submit">Buscar</button>
    </form>

    <?php if ($funcionario): ?>
        <form id="formAlterarFuncionario" action="alterar_funcionario.php" method="POST" onsubmit="return validarAlteracaoFuncionario();">
            <input type="hidden" name="id_funcionario" value="<?= htmlspecialchars($funcionario['id_funcionario']) ?>">

            <label for="nome_funcionario">Nome:</label>
            <input type="text" id="nome_funcionario" name="nome_funcionario"
                   value="<?= htmlspecialchars($funcionario['nome_funcionario']) ?>" required
                   pattern="[A-Za-zÀ-ÿ\s]+"
                   title="Digite apenas letras"
                   onkeypress="somenteLetras(event)"
                   onpaste="validarNomeOnPaste(event)">

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($funcionario['endereco']) ?>">

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone"
                   value="<?= htmlspecialchars($funcionario['telefone']) ?>"
                   pattern="[0-9]+"
                   title="Digite apenas números"
                   onkeypress="somenteNumeros(event)"
                   onpaste="validarTelefoneOnPaste(event)">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($funcionario['email']) ?>" required>

            <button type="submit" name="alterar_funcionario">Alterar</button>
            <button type="button" onclick="limparCampos()">Cancelar</button>
        </form>
    <?php endif; ?>

    <a href="principal.php">Voltar</a>
</body>
</html>