<?php 
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();

        if (password_verify($senha, $hash)) {
            $_SESSION['usuario_id'] = $id;
            header("Location: index.php");
            exit();
        }
    }

    $erro = "E-mail ou senha inválidos.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
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
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
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
            margin-bottom: 30px;
            text-align: center;
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
        <h2>Login</h2>
        <form method="POST" class="mb-3">
            <input type="email" name="email" class="form-control mb-3" placeholder="E-mail" required />
            <input type="password" name="senha" class="form-control mb-3" placeholder="Senha" required />
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Cadastro realizado com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

        <?php if (isset($erro)) : ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>
        <p class="text-center"><a href="cadastro.php">Criar nova conta</a></p>
    </div>
</body>
</html>