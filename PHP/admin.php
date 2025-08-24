<?php
session_start();

// 1. Configurações do Banco de Dados
require_once __DIR__ . '/config.php';

// 2. Configuração de Autenticação
define('ADMIN_PASS', 'sua_senha_segura_aqui'); // Altere para uma senha forte

// 3. Processamento do Login
if (isset($_POST['login'])) {
    if ($_POST['senha'] === ADMIN_PASS) {
        $_SESSION['admin_logado'] = true;
    } else {
        $erro = "Senha incorreta";
    }
}

// 4. Processamento do Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// 5. Verificação de Login
if (!isset($_SESSION['admin_logado'])) {
    // Exibir formulário de login
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login Admin</title>
        <style>
            body { 
                font-family: 'Poppins', sans-serif; 
                background: #2D2D2D; 
                color: #EEE; 
            }
            .login-box { 
                max-width: 400px; 
                margin: 100px auto; 
                padding: 30px; 
                background: #434648; 
                border-radius: 8px;
                box-shadow: 0 0 20px rgba(0,0,0,0.5);
            }
            input { 
                width: 100%; 
                padding: 12px; 
                margin: 10px 0; 
                background: #2D2D2D; 
                border: 1px solid #555; 
                color: #EEE;
                border-radius: 4px;
            }
            button { 
                background: #f7bd00fb; 
                color: #2D2D2D; 
                border: none; 
                padding: 12px 20px; 
                cursor: pointer;
                width: 100%;
                font-weight: 600;
                border-radius: 4px;
                margin-top: 10px;
            }
            h2 {
                text-align: center;
                margin-bottom: 20px;
            }
            .error {
                color: #f7bd00fb;
                text-align: center;
                margin-bottom: 15px;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>Área Administrativa</h2>
            <?php if (isset($erro)) echo "<p class='error'>$erro</p>"; ?>
            <form method="post">
                <input type="password" name="senha" placeholder="Digite sua senha" required>
                <button type="submit" name="login">Entrar</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 6. Conexão com o Banco de Dados e Busca de Registros
try {
    $registros = $pdo->query("SELECT * FROM registros ORDER BY data DESC LIMIT 50")->fetchAll();
} catch(PDOException $e) {
    die("<div style='color:red;padding:20px;'>Erro ao acessar o banco de dados: " . $e->getMessage() . "</div>");
}

// 7. Exibição do Painel Administrativo
?>
<!DOCTYPE html>
<html>
<head>
    <title>Painel Admin - Radioativo210</title>
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #2D2D2D; 
            color: #EEE; 
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #f7bd00fb;
            border-bottom: 2px solid #434648;
            padding-bottom: 10px;
        }
        h2 {
            color: #EEE;
            margin-top: 30px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
        }
        th, td { 
            padding: 12px 15px; 
            border: 1px solid #555; 
            text-align: left; 
        }
        th { 
            background: #434648; 
            color: #f7bd00fb; 
            font-weight: 600;
        }
        tr:nth-child(even) { 
            background: #434648; 
        }
        tr:hover {
            background: #3a3a3a;
        }
        a { 
            color: #f7bd00fb; 
            text-decoration: none;
            font-weight: 600;
        }
        .logout {
            float: right;
            font-size: 14px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #888;
        }
    </style>
</head>
<body>
    <h1>Painel Administrativo <a href="admin.php?logout" class="logout">(Sair)</a></h1>
    
    <h2>Últimos Registros</h2>
    <?php if (empty($registros)): ?>
        <p class="no-data">Nenhum registro encontrado.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Detalhes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $reg): ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($reg['data'])) ?></td>
                <td><?= $reg['tipo'] ?></td>
                <td><?= htmlspecialchars($reg['nome']) ?></td>
                <td><?= htmlspecialchars($reg['email']) ?></td>
                <td>
                    <?php if ($reg['tipo'] === 'compra'): ?>
                        <strong>Compra:</strong> <?= htmlspecialchars($reg['produto']) ?>
                    <?php else: ?>
                        <?= strlen($reg['mensagem']) > 50 ? 
                            htmlspecialchars(substr($reg['mensagem'], 0, 50)).'...' : 
                            htmlspecialchars($reg['mensagem']) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html>