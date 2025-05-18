<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($nome_usuario);
$stmt->fetch();
$stmt->close();

$prioridadeFiltro = isset($_GET['prioridade']) && in_array($_GET['prioridade'], ['baixa', 'media', 'alta']) ? $_GET['prioridade'] : '';
$statusFiltro = isset($_GET['status']) && ($_GET['status'] === '0' || $_GET['status'] === '1') ? $_GET['status'] : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Tarefas</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6f2ff;
        }

        .container {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
        }

        .btn-success {
            background-color: #3399ff;
            border: none;
        }

        .btn-success:hover {
            background-color: #287acc;
        }

        .btn-link {
            color: #0056b3;
        }

        .done {
            text-decoration: line-through;
            color: gray;
        }
    </style>
</head>
<body class="py-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold" style="color: #0056b3;">Olá, <?php echo htmlspecialchars($nome_usuario); ?>!</h2>
        <p style="color: #000; font-weight: 500; font-size: 1.1rem; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            Crie e gerencie suas tarefas com praticidade e organização.
        </p>
    </div>

    <div class="container">
        <h1 class="logo">Task <span>Manager</span></h1>
        <h2 class="mb-4">Minhas Tarefas</h2>

        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="sucesso-alert">
                ✅ Cadastro realizado com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <form action="adicionar.php" method="POST" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="titulo" class="form-control" placeholder="Nova tarefa" required>
                </div>
                <div class="col-md-3">
                    <select name="prioridade" class="form-select">
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="data" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100" type="submit">Adicionar</button>
                </div>
            </div>
        </form>

        <form method="GET" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="prioridade" class="form-label">Filtrar por Prioridade:</label>
                    <select name="prioridade" id="prioridade" class="form-select">
                        <option value="">Todas</option>
                        <option value="baixa" <?= $prioridadeFiltro == 'baixa' ? 'selected' : '' ?>>Baixa</option>
                        <option value="media" <?= $prioridadeFiltro == 'media' ? 'selected' : '' ?>>Média</option>
                        <option value="alta" <?= $prioridadeFiltro == 'alta' ? 'selected' : '' ?>>Alta</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Filtrar por Status:</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todas</option>
                        <option value="0" <?= $statusFiltro === '0' ? 'selected' : '' ?>>Pendentes</option>
                        <option value="1" <?= $statusFiltro === '1' ? 'selected' : '' ?>>Concluídas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Filtrar</button>
                </div>
            </div>
        </form>

        <ul class="list-group">
            <?php
            $sql = "SELECT * FROM tarefas WHERE usuario_id = ?";
            $params = [$usuario_id];
            $tipos = "i";

            if ($prioridadeFiltro && $statusFiltro !== '') {
                $sql .= " AND prioridade = ? AND status = ?";
                $tipos .= "si";
                $params[] = $prioridadeFiltro;
                $params[] = (int)$statusFiltro;
            } elseif ($prioridadeFiltro) {
                $sql .= " AND prioridade = ?";
                $tipos .= "s";
                $params[] = $prioridadeFiltro;
            } elseif ($statusFiltro !== '') {
                $sql .= " AND status = ?";
                $tipos .= "i";
                $params[] = (int)$statusFiltro;
            }

            $sql .= " ORDER BY criado_em DESC";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($tipos, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $cor = match ($row['prioridade']) {
                    'alta' => 'danger',
                    'media' => 'warning',
                    default => 'secondary',
                };

                $data_formatada = isset($row['data']) ? date('d/m/Y', strtotime($row['data'])) : 'Sem data';

                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                    <span>
                        <span class='badge bg-$cor me-2 text-uppercase'>{$row['prioridade']}</span>
                        " . ($row['status'] ? "<span class='done'>{$row['titulo']}</span>" : $row['titulo']) . "
                        <br><small class='text-muted'>Prazo: $data_formatada</small>
                    </span>
                    <span>
                        " . ($row['status'] == 0 ? "<a href='concluir.php?id={$row['id']}' class='btn btn-sm btn-success me-1'>Concluir</a>" : "") . "
                        <a href='excluir.php?id={$row['id']}' class='btn btn-sm btn-danger'>Excluir</a>
                    </span>
                </li>";
            }
            ?>
        </ul>

        <a href="logout.php" class="btn btn-primary mt-3">Sair</a>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            const alert = document.getElementById('sucesso-alert');
            if (alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        }, 3000);
    </script>
</body>
</html>
