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
    window.onload = function() {
        document.getElementById('telefone').addEventListener('input', mascaraTelefone);
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
               maxlength="15"
               pattern="\(\d{2}\)\s*\d{4,5}-\d{4}"
               title="Digite no formato (99) 99999-9999"
               onkeypress="somenteNumeros(event)"
               onpaste="validarTelefoneOnPaste(event)">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>
    <a href="principal.php">Voltar</a>

    <style>
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

/* ===== Card do formulário de cadastro ===== */
form[action="cadastro_funcionario.php"] {
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

form[action="cadastro_funcionario.php"] label {
    font-weight: bold;
    color: #23272b;
    margin-bottom: 6px;
    font-size: 1rem;
    align-self: flex-start;
    letter-spacing: 0.01em;
}

form[action="cadastro_funcionario.php"] input[type="text"],
form[action="cadastro_funcionario.php"] input[type="email"] {
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
form[action="cadastro_funcionario.php"] input[type="text"]:focus,
form[action="cadastro_funcionario.php"] input[type="email"]:focus {
    border-color: #f04c3d;
    background: #fff7f6;
    box-shadow: 0 0 0 2px #f04c3d18;
    outline: none;
}

form[action="cadastro_funcionario.php"] button[type="submit"] {
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
form[action="cadastro_funcionario.php"] button[type="submit"]:hover {
    background: #d83c2c;
    color: #fff;
    transform: translateY(-1px) scale(1.01);
}
form[action="cadastro_funcionario.php"] button[type="reset"] {
    width: 100%;
    padding: 9px 0;
    background: #fff;
    color: #f04c3d;
    border: 1.2px solid #f04c3d;
    border-radius: 7px;
    font-size: 1.03rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.16s, color 0.16s, border 0.16s, box-shadow 0.13s;
    box-shadow: 0 2px 5px #f04c3d0b;
    letter-spacing: 0.02em;
    margin-bottom: 3px;
    margin-top: 1px;
}
form[action="cadastro_funcionario.php"] button[type="reset"]:hover {
    background: #f04c3d;
    color: #fff;
    border-color: #f04c3d;
}

/* ===== Botão Voltar igual às outras telas ===== */
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

/* ===== Responsivo (mantém proporcional em telas menores) ===== */
@media (max-width: 650px) {
    form[action="cadastro_funcionario.php"] {
        max-width: 96vw;
        padding: 7px 2vw 7px 2vw;
    }
    h2 {
        font-size: 0.96rem;
    }
    .btn-voltar {
        padding: 8px 7vw;
        font-size: 1rem;
    }
}

/* ===== Mensagens de alerta (se precisar) ===== */
.mensagem, p.aviso {
    color: #e74c3c;
    background: #fff3f2;
    border: 1px solid #ffdada;
    border-radius: 6px;
    margin: 16px auto 14px auto;
    padding: 9px 16px;
    max-width: 320px;
    font-size: 1rem;
}
        </style>
</body>
</html>