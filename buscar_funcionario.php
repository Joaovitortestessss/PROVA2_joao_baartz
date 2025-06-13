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
    <style>
        body {
    background: #f6f6f6;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    text-align: center;
    min-height: 100vh;
}

body {
    background: #f6f6f6;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    text-align: center;
    min-height: 100vh;
    font-size: 15px;
}

/* ===== Cabeçalho Usuário e Título ===== */
body > div[style*="font-weight:bold"] {
    margin-top: 28px !important;
    margin-bottom: 0 !important;
    font-size: 1.23rem !important;
    font-weight: 800 !important;
    color: #23272b !important;
    letter-spacing: 0.01em;
}

h2 {
    font-size: 1.37rem;
    font-weight: bold;
    margin: 16px 0 24px 0;
    letter-spacing: 0.08em;
    color: #f04c3d;
    text-shadow: 0 2px 8px #f04c3d10;
}

/* ===== Card do formulário de busca/alteração ===== */
form[action="buscar_funcionario.php"],
form[action="alterar_funcionario.php"] {
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    padding: 18px 16px 16px 16px;
    margin: 0 auto 30px auto;
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(44,62,80,0.09);
    max-width: 340px;
    min-width: 180px;
    border: none;
    font-weight: 500;
    position: relative;
}

form[action="buscar_funcionario.php"] label,
form[action="alterar_funcionario.php"] label {
    font-weight: bold;
    color: #23272b;
    margin-bottom: 6px;
    font-size: 1rem;
    align-self: flex-start;
    letter-spacing: 0.01em;
}

form[action="buscar_funcionario.php"] input[type="text"],
form[action="alterar_funcionario.php"] input[type="text"] {
    width: 100%;
    padding: 9px 10px;
    border: 1.2px solid #e3e6ed;
    border-radius: 6px;
    font-size: 1rem;
    background: #fafcfd;
    color: #23272b;
    transition: border-color 0.16s, box-shadow 0.16s, background 0.11s;
    box-sizing: border-box;
    margin-bottom: 7px;
}
form[action="buscar_funcionario.php"] input[type="text"]:focus,
form[action="alterar_funcionario.php"] input[type="text"]:focus {
    border-color: #f04c3d;
    background: #fff7f6;
    box-shadow: 0 0 0 2px #f04c3d18;
    outline: none;
}

form[action="buscar_funcionario.php"] button,
form[action="alterar_funcionario.php"] button[type="submit"] {
    width: 100%;
    padding: 9px 0;
    background: #f04c3d;
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: 1.03rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.16s, box-shadow 0.13s, transform 0.11s, color 0.17s;
    box-shadow: 0 2.5px 10px #f04c3d10;
    letter-spacing: 0.02em;
    margin-top: 7px;
}
form[action="buscar_funcionario.php"] button:hover,
form[action="alterar_funcionario.php"] button[type="submit"]:hover {
    background: #d83c2c;
    color: #fff;
    transform: translateY(-1px) scale(1.01);
}

/* ===== Tabela Moderna e Proporcional ===== */
table {
    border-collapse: separate;
    border-spacing: 0;
    margin: 10px auto 0 auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(44,62,80,0.10);
    overflow: hidden;
    min-width: 600px;
    max-width: 95vw;
    font-size: 0.97rem;
}
th, td {
    padding: 7px 8px;
    text-align: left;
}
th {
    background: #393e42;
    color: #fff;
    font-weight: 700;
    font-size: 1.01rem;
    border: none;
    letter-spacing: 0.18px;
}
th:first-child {
    border-top-left-radius: 10px;
}
th:last-child {
    border-top-right-radius: 10px;
}
td {
    color: #23272b;
    vertical-align: middle;
    background: #fff;
    border-bottom: 1px solid #f0f3f7;
}
tr:nth-child(even) td {
    background: #f6f9fb;
}
tr:hover td {
    background-color: #fbeaea;
}

/* ===== Botão Excluir na tabela ===== */
td a[href*="excluir_funcionario"] {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #f04c3d;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    padding: 3px 10px;
    text-decoration: none;
    transition: background 0.12s, color 0.11s, box-shadow 0.13s;
    box-shadow: 0 1.5px 6px #f04c3d13;
    margin: 0 auto 3px auto;
    line-height: 1.08;
}
td a[href*="excluir_funcionario"]:before {
    content: "🗑️";
    font-size: 1em;
    margin-right: 1px;
}
td a[href*="excluir_funcionario"]:hover {
    background: #d83c2c;
    color: #fff;
}

/* ===== Botão Alterar na tabela ===== */
td a[href*="alterar_funcionario"] {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #fff;
    color: #f04c3d;
    border: 1.2px solid #f04c3d;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    padding: 3px 10px;
    text-decoration: none;
    transition: 
        background 0.12s, 
        color 0.11s, 
        border 0.11s, 
        box-shadow 0.13s;
    box-shadow: 0 1.5px 6px #f04c3d13;
    margin: 0 auto 3px auto;
    line-height: 1.08;
}
td a[href*="alterar_funcionario"]:before {
    content: "✏️";
    font-size: 1em;
    margin-right: 1px;
}
td a[href*="alterar_funcionario"]:hover {
    background: #f04c3d;
    color: #fff;
    border-color: #f04c3d;
}

/* ===== Responsivo (mantém proporcional em telas menores) ===== */
@media (max-width: 900px) {
    th, td {
        font-size: 0.83rem;
        padding: 5px 2px;
    }
    table {
        min-width: 96vw;
        max-width: 98vw;
    }
    td a[href*="excluir_funcionario"], td a[href*="alterar_funcionario"] {
        font-size: 0.77rem;
        padding: 2px 7px;
    }
}
@media (max-width: 650px) {
    table {
        min-width: 0;
        max-width: 99vw;
        font-size: 0.80rem;
    }
    th, td {
        padding: 3px 1px;
    }
    form[action="buscar_funcionario.php"], form[action="alterar_funcionario.php"] {
        max-width: 96vw;
        padding: 7px 2vw 7px 2vw;
    }
    h2 {
        font-size: 0.96rem;
    }
}

/* ===== Botão Voltar ===== */
body {
    background: #f6f6f6;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    text-align: center;
    min-height: 100vh;
    font-size: 15px;
}

/* ===== Cabeçalho Usuário e Título ===== */
body > div[style*="font-weight:bold"] {
    margin-top: 28px !important;
    margin-bottom: 0 !important;
    font-size: 1.23rem !important;
    font-weight: 800 !important;
    color: #23272b !important;
    letter-spacing: 0.01em;
}

h2 {
    font-size: 1.37rem;
    font-weight: bold;
    margin: 16px 0 24px 0;
    letter-spacing: 0.08em;
    color: #f04c3d;
    text-shadow: 0 2px 8px #f04c3d10;
}

/* ===== Card do formulário de busca/alteração ===== */
form[action="buscar_funcionario.php"],
form[action="alterar_funcionario.php"] {
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    padding: 18px 16px 16px 16px;
    margin: 0 auto 30px auto;
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(44,62,80,0.09);
    max-width: 340px;
    min-width: 180px;
    border: none;
    font-weight: 500;
    position: relative;
}

form[action="buscar_funcionario.php"] label,
form[action="alterar_funcionario.php"] label {
    font-weight: bold;
    color: #23272b;
    margin-bottom: 6px;
    font-size: 1rem;
    align-self: flex-start;
    letter-spacing: 0.01em;
}

form[action="buscar_funcionario.php"] input[type="text"],
form[action="alterar_funcionario.php"] input[type="text"] {
    width: 100%;
    padding: 9px 10px;
    border: 1.2px solid #e3e6ed;
    border-radius: 6px;
    font-size: 1rem;
    background: #fafcfd;
    color: #23272b;
    transition: border-color 0.16s, box-shadow 0.16s, background 0.11s;
    box-sizing: border-box;
    margin-bottom: 7px;
}
form[action="buscar_funcionario.php"] input[type="text"]:focus,
form[action="alterar_funcionario.php"] input[type="text"]:focus {
    border-color: #f04c3d;
    background: #fff7f6;
    box-shadow: 0 0 0 2px #f04c3d18;
    outline: none;
}

form[action="buscar_funcionario.php"] button,
form[action="alterar_funcionario.php"] button[type="submit"] {
    width: 100%;
    padding: 9px 0;
    background: #f04c3d;
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: 1.03rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.16s, box-shadow 0.13s, transform 0.11s, color 0.17s;
    box-shadow: 0 2.5px 10px #f04c3d10;
    letter-spacing: 0.02em;
    margin-top: 7px;
}
form[action="buscar_funcionario.php"] button:hover,
form[action="alterar_funcionario.php"] button[type="submit"]:hover {
    background: #d83c2c;
    color: #fff;
    transform: translateY(-1px) scale(1.01);
}

/* ===== Tabela Moderna e Proporcional ===== */
table {
    border-collapse: separate;
    border-spacing: 0;
    margin: 10px auto 0 auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(44,62,80,0.10);
    overflow: hidden;
    min-width: 600px;
    max-width: 95vw;
    font-size: 0.97rem;
}
th, td {
    padding: 7px 8px;
    text-align: left;
}
th {
    background: #393e42;
    color: #fff;
    font-weight: 700;
    font-size: 1.01rem;
    border: none;
    letter-spacing: 0.18px;
}
th:first-child {
    border-top-left-radius: 10px;
}
th:last-child {
    border-top-right-radius: 10px;
}
td {
    color: #23272b;
    vertical-align: middle;
    background: #fff;
    border-bottom: 1px solid #f0f3f7;
}
tr:nth-child(even) td {
    background: #f6f9fb;
}
tr:hover td {
    background-color: #fbeaea;
}

/* ===== Botão Excluir na tabela ===== */
td a[href*="excluir_funcionario"] {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #f04c3d;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    padding: 3px 10px;
    text-decoration: none;
    transition: background 0.12s, color 0.11s, box-shadow 0.13s;
    box-shadow: 0 1.5px 6px #f04c3d13;
    margin: 0 auto 3px auto;
    line-height: 1.08;
}
td a[href*="excluir_funcionario"]:before {
    content: "🗑️";
    font-size: 1em;
    margin-right: 1px;
}
td a[href*="excluir_funcionario"]:hover {
    background: #d83c2c;
    color: #fff;
}

/* ===== Botão Alterar na tabela ===== */
td a[href*="alterar_funcionario"] {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #fff;
    color: #f04c3d;
    border: 1.2px solid #f04c3d;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    padding: 3px 10px;
    text-decoration: none;
    transition: 
        background 0.12s, 
        color 0.11s, 
        border 0.11s, 
        box-shadow 0.13s;
    box-shadow: 0 1.5px 6px #f04c3d13;
    margin: 0 auto 3px auto;
    line-height: 1.08;
}
td a[href*="alterar_funcionario"]:before {
    content: "✏️";
    font-size: 1em;
    margin-right: 1px;
}
td a[href*="alterar_funcionario"]:hover {
    background: #f04c3d;
    color: #fff;
    border-color: #f04c3d;
}

/* ===== Responsivo (mantém proporcional em telas menores) ===== */
@media (max-width: 900px) {
    th, td {
        font-size: 0.83rem;
        padding: 5px 2px;
    }
    table {
        min-width: 96vw;
        max-width: 98vw;
    }
    td a[href*="excluir_funcionario"], td a[href*="alterar_funcionario"] {
        font-size: 0.77rem;
        padding: 2px 7px;
    }
}
@media (max-width: 650px) {
    table {
        min-width: 0;
        max-width: 99vw;
        font-size: 0.80rem;
    }
    th, td {
        padding: 3px 1px;
    }
    form[action="buscar_funcionario.php"], form[action="alterar_funcionario.php"] {
        max-width: 96vw;
        padding: 7px 2vw 7px 2vw;
    }
    h2 {
        font-size: 0.96rem;
    }
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
        </style>
</body>
</html>