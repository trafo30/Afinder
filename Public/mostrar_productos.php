<?php
$archivo_csv = "productos_llantas.csv";

// Verifica si el archivo existe
if (!file_exists($archivo_csv)) {
    echo "<p>No se encontró el archivo de productos.</p>";
    return;
}

$archivo = fopen($archivo_csv, "r");

// Leer encabezados
$encabezados = fgetcsv($archivo);

echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";

// Leer cada producto
while (($fila = fgetcsv($archivo)) !== false) {
    $producto = array_combine($encabezados, $fila);

    echo "<div style='border: 1px solid #ccc; padding: 10px; width: 250px; border-radius: 8px;'>";
    echo "<img src='" . htmlspecialchars($producto['imagen']) . "' alt='Imagen' width='200' height='200'><br>";
    echo "<h3>" . htmlspecialchars($producto['nombre']) . "</h3>";
    echo "<p><strong>Precio:</strong> " . htmlspecialchars($producto['precio']) . "</p>";
    echo "<p><strong>Vendedor:</strong> " . htmlspecialchars($producto['vendedor']) . "</p>";
    echo "</div>";
}

echo "</div>";

fclose($archivo);
?>
