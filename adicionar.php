<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

$titulo = $_POST['titulo'];
$prioridade = $_POST['prioridade'];
$data = $_POST['data'];
$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("INSERT INTO tarefas (titulo, prioridade, data, usuario_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $titulo, $prioridade, $data, $usuario_id);
$stmt->execute();

header("Location: index.php");
exit();