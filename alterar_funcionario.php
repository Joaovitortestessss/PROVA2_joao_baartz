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
    // Máscara para telefone (formato (99) 99999-9999)
    function mascaraTelefone(e) {
        let input = e.target;
        let value = input.value.replace(/\D/g, '').slice(0, 11); // Limita a 11 dígitos
        if (value.length > 0) {
            value = '(' + value;
        }
        if (value.length > 3) {
            value = value.slice(0, 3) + ') ' + value.slice(3);
        }
        if (value.length > 10) {
            value = value.slice(0, 10) + '-' + value.slice(10);
        }
        input.value = value;
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
    window.onload = function() {
        let tel = document.getElementById('telefone');
        if (tel) {
            tel.addEventListener('input', mascaraTelefone);
        }
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
                   maxlength="100"
                   onkeypress="somenteLetras(event)"
                   onpaste="validarNomeOnPaste(event)">

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($funcionario['endereco']) ?>">

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone"
                   value="<?= htmlspecialchars($funcionario['telefone']) ?>"
                   maxlength="15"
                   pattern="\(\d{2}\)\s*\d{4,5}-\d{4}"
                   title="Digite no formato (99) 99999-9999"
                   onkeypress="somenteNumeros(event)"
                   onpaste="validarTelefoneOnPaste(event)">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($funcionario['email']) ?>" required>

            <button type="submit" name="alterar_funcionario">Alterar</button>
            <button type="button" onclick="limparCampos()">Cancelar</button>
        </form>
    <?php endif; ?>

    <a href="principal.php">Voltar</a>
    <style>
/* ===== Cabeçalho Usuário e Título ===== */
body > div[style*="font-weight:bold"] {
    margin-top: 32px !important;
    margin-bottom: 0 !important;
    font-size: 1.45rem !important;
    font-weight: 800 !important;
    color: #222831 !important;
    letter-spacing: 0.01em;
}

h2 {
    font-size: 2rem;
    font-weight: bold;
    margin: 24px 0 18px 0;
    letter-spacing: 0.08em;
    color: #e74c3c;
    text-shadow: 0 2px 8px #e74c3c18;
}

/* ===== Card do formulário - igual o card da tabela de exclusão ===== */
form[action="alterar_funcionario.php"], 
#formAlterarFuncionario {
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 28px 32px 22px 32px;
    margin: 0 auto 32px auto;
    border-radius: 16px;
    box-shadow: 0 6px 32px rgba(44,62,80,0.10);
    max-width: 420px;
    min-width: 220px;
    border: none;
    transition: box-shadow 0.23s, border-color 0.19s;
}

#formAlterarFuncionario {
    margin-top: 38px;
}

/* ===== Labels e Inputs ===== */
form label {
    font-weight: 700;
    color: #222831;
    margin-bottom: 7px;
    font-size: 1.05rem;
    align-self: flex-start;
    letter-spacing: 0.01em;
}
form input[type="text"],
form input[type="email"],
form input[type="password"],
form select {
    width: 100%;
    padding: 11px 12px;
    border: 1.5px solid #e3e6ed;
    border-radius: 7px;
    font-size: 1.07rem;
    background: #f7fafc;
    margin-bottom: 13px;
    color: #23272b;
    transition: border-color 0.16s, box-shadow 0.16s, background 0.11s;
    box-sizing: border-box;
}
form input:focus, form select:focus {
    border-color: #e74c3c;
    background: #fcf1ef;
    box-shadow: 0 0 0 2px #e74c3c22;
    outline: none;
}

/* ===== Botões ===== */
form button[type="submit"], form button[type="button"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 10px;
    margin-top: 5px;
    background: #e74c3c;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1.09rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.16s, box-shadow 0.13s, transform 0.11s, color 0.17s;
    box-shadow: 0 3px 10px #e74c3c22;
    letter-spacing: 0.04em;
}
form button[type="button"] {
    background: #f6f8fa;
    color: #e74c3c;
    border: 1.2px solid #e3e6ed;
    font-weight: 700;
    margin-top: 0;
    box-shadow: 0 1.5px 6px #e74c3c0a;
}
form button[type="submit"]:hover {
    background: #c0392b;
    color: #fff;
    transform: translateY(-2px) scale(1.01);
}
form button[type="button"]:hover {
    background: #fbeaea;
    color: #c0392b;
    border-color: #e74c3c;
    transform: scale(1.01);
}

/* ===== Mensagem de erro/alerta ===== */
.mensagem {
    color: #e74c3c;
    background: #fff3f2;
    border: 1.2px solid #ffdada;
    border-radius: 6px;
    margin: 16px auto 14px auto;
    padding: 11px 18px;
    max-width: 420px;
    font-size: 1.06rem;
    font-weight: 600;
    box-shadow: 0 1px 6px #e74c3c0c;
}

/* ===== Botão Voltar ===== */
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

/* ===== Responsivo ===== */
@media (max-width: 600px) {
    form[action="alterar_funcionario.php"],
    #formAlterarFuncionario {
        padding: 13px 4vw 9px 4vw;
        min-width: 0;
        max-width: 98vw;
    }
    #formAlterarFuncionario {
        margin-top: 18px;
    }
    h2 {
        font-size: 1.18rem;
    }
    a[href="principal.php"] {
        padding: 8px 7vw;
        font-size: 1rem;
    }
}
    </style>
</body>
</html>