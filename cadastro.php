<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha);
    
    if ($stmt->execute()) {
    header("Location: login.php?sucesso=1");
        exit();
    } else {
        $erro = "Erro ao cadastrar usuário.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Cadastro</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #e6f2ff;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 400px;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        h1.logo {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 900;
        font-size: 3rem;
        color: #3399ff;
        letter-spacing: 4px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        text-align: center;
        margin-bottom: 30px;
      }
      h1.logo span {
        color: #0056b3;
      }

        h2 {
            color: #0056b3;
            text-align: center;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: #3399ff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #287acc;
        }

        a {
            color: #0056b3;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .alert {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="logo">Task <span>Manager</span></h1>
        <h2>Cadastro</h2>
        <form method="POST" class="mb-3">
            <input type="text" name="nome" class="form-control mb-3" placeholder="Nome completo" required />
            <input type="email" name="email" class="form-control mb-3" placeholder="E-mail" required />
            <input type="password" name="senha" class="form-control mb-3" placeholder="Senha" required />
            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
        </form>
        <?php if (isset($erro)) : ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>
        <p class="text-center"><a href="login.php">Já tem conta? Faça login</a></p>
    </div>
</body>
</html>