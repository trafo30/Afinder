<?php
header('Content-Type: application/json; charset=utf-8');

$conexion = new mysqli("localhost", "root", "", "autofinder");
if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "db_error"]);
    exit();
}

$nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$precio = isset($_GET['precio']) ? floatval($_GET['precio']) : 0;

if ($nombre === '' || $precio <= 0) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT id_producto 
        FROM productos 
        WHERE nombre = ? AND ABS(precio - ?) < 0.01
        ORDER BY id_producto DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sd", $nombre, $precio);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

echo json_encode($res ?: []);
