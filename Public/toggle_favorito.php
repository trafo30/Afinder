<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["status" => "error", "msg" => "not_logged"]);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_producto = intval($_POST['id_producto'] ?? 0);

if (!$id_producto) {
    echo json_encode(["status" => "error", "msg" => "id_producto_missing"]);
    exit();
}

$conexion = new mysqli("localhost", "root", "", "autofinder");

$sql_check = "SELECT * FROM favoritos WHERE id_usuario = ? AND id_producto = ?";
$stmt = $conexion->prepare($sql_check);
$stmt->bind_param("ii", $id_usuario, $id_producto);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Ya existe: eliminar
    $sql_del = "DELETE FROM favoritos WHERE id_usuario = ? AND id_producto = ?";
    $stmt2 = $conexion->prepare($sql_del);
    $stmt2->bind_param("ii", $id_usuario, $id_producto);
    $stmt2->execute();
    echo json_encode(["status" => "ok", "action" => "removed"]);
} else {
    // No existe: insertar
    $sql_ins = "INSERT INTO favoritos (id_usuario, id_producto, fecha_guardado)
                VALUES (?, ?, NOW())";
    $stmt3 = $conexion->prepare($sql_ins);
    $stmt3->bind_param("ii", $id_usuario, $id_producto);
    $stmt3->execute();
    echo json_encode(["status" => "ok", "action" => "added"]);
}

?>
