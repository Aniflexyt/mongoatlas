<?php

date_default_timezone_set('America/Bogota');
$hoy = date("Y-m-d H:i:s");

require 'vendor/autoload.php'; // Cargar Composer

try {
    // Conexión a tu clúster de Mongo Atlas
    $cliente = new MongoDB\Client("mongodb+srv://johancardenas619_db_user:nwH1EiuQn4AZizRt@cluster0.vx98nac.mongodb.net/?appName=Cluster0");
    $db = $cliente->pruebas;      
    $coleccion = $db->gustos;     

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resultado = $coleccion->insertOne([
            "apellidos" => $_POST["apellidos"],
            "nombres"   => $_POST["nombres"],
            "color"     => $_POST["color"],
            "comida"    => $_POST["comida"],
            "pelicula"  => $_POST["pelicula"],
            "registro"  => $hoy
        ]);

        echo "<center><div class='alert alert-success m-0 rounded-0 fw-bold' role='alert'>¡Documento insertado con éxito! ID asignado: " . $resultado->getInsertedId() . "</div></center>";
    }

    // NUEVA CONSULTA CON TYPEMAP: Evita el Fatal Error transformando todo a arreglos de PHP
    $documentos = $coleccion->find([], [
        'typeMap' => [
            'root' => 'array',
            'document' => 'array',
            'array' => 'array'
        ]
    ]);

} catch (Exception $e) {
    die("<div class='alert alert-danger text-center m-5 fw-bold'>Error crítico en la conexión: " . $e->getMessage() . "</div>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Registros - Mongo Atlas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light p-4">

    <div class="container card p-4 shadow-sm mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="text-primary fw-bold m-0">📋 Documentos en la Colección "Gustos"</h2>
            <a href="index.html" class="btn btn-secondary fw-semibold">← Volver al Formulario</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle border">
                <thead class="table-dark">
                    <tr>
                        <th>Apellidos</th>
                        <th>Nombres</th>
                        <th>Color Favorito</th>
                        <th>Comida Favorita</th>
                        <th>Cine / Literatura</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_registros = 0;
                    foreach ($documentos as $doc): 
                        $total_registros++;
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($doc['apellidos'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo htmlspecialchars($doc['nombres'] ?? 'N/A'); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($doc['color'] ?? 'N/A'); ?></span></td>
                            <td><?php echo htmlspecialchars($doc['comida'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($doc['pelicula'] ?? 'N/A'); ?></td>
                            <td class="text-muted small"><?php echo htmlspecialchars($doc['registro'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($total_registros === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">No se encontraron documentos registrados en esta colección.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
