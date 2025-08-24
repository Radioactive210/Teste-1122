<?php
session_start();

// Configurações básicas
define('DB_HOST', 'localhost');
define('DB_NAME', 'radioativo_db');
define('DB_USER', 'usuario');
define('DB_PASS', 'senha_segura');
define('SITE_EMAIL', 'contato@radioativo210.com');

// Conexão com banco de dados
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão com o banco de dados.");
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proteção básica contra spam
    if (!empty($_POST['url'])) {
        die();
    }

    // Dados comuns
    $nome = substr(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING), 0, 100);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Validação mínima
    if (empty($nome) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Dados inválidos.");
    }

    // Determinar tipo de registro
    if (isset($_POST['produto'])) { // Formulário de compra
        $produto = substr($_POST['produto'], 0, 100);
        $stmt = $pdo->prepare("INSERT INTO registros (tipo, nome, email, produto, data) VALUES ('compra', ?, ?, ?, NOW())");
        $stmt->execute([$nome, $email, $produto]);
        
        // Redirecionamento específico para compras
        header('Location: obrigado_compra.html');
    } else { // Formulário de contato
        $mensagem = substr(filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING), 0, 2000);
        $stmt = $pdo->prepare("INSERT INTO registros (tipo, nome, email, mensagem, data) VALUES ('contato', ?, ?, ?, NOW())");
        $stmt->execute([$nome, $email, $mensagem]);
        
        // Redirecionamento para contato
        header('Location: obrigado.html');
    }
    exit;
}
?>