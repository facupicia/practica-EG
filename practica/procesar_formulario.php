<?php
session_start();

// 🔧 Config DB
$host = 'localhost';
$dbname = 'formularios_db';
$user = 'root';
$pass = '1234';

// 🔗 Conexión
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 🧹 Limpiar sesión si se presiona el botón
if (isset($_POST['limpiar_sesion'])) {
    unset($_SESSION['formularios_enviados']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 📩 Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['limpiar_sesion'])) {
    $campos = ['nombre', 'email', 'producto', 'mes', 'cantidad', 'terminos'];
    $errores = [];

    // Validaciones
    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            $errores[] = "El campo '$campo' es obligatorio.";
        }
    }

    if (empty($errores)) {
        $fecha_hora = date('Y-m-d H:i:s');
        $datos = [
            'nombre' => $_POST['nombre'],
            'email' => $_POST['email'],
            'producto' => $_POST['producto'],
            'mes' => $_POST['mes'],
            'cantidad' => $_POST['cantidad'],
            'fecha_hora' => $fecha_hora
        ];

        try {
            $stmt = $pdo->prepare("INSERT INTO formularios 
                (nombre, email, producto, mes, cantidad, fecha_hora)
                VALUES (:nombre, :email, :producto, :mes, :cantidad, :fecha_hora)");
            $stmt->execute($datos);

            $_SESSION['formularios_enviados'][$fecha_hora] = $datos;
            $mensaje_exito = "Formulario enviado correctamente a las $fecha_hora.";
        } catch (PDOException $e) {
            $errores[] = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<h1>Lista de formularios enviados</h1>

<?php if (!empty($mensaje_exito)): ?>
    <p style="color: green;"><?= htmlspecialchars($mensaje_exito) ?></p>
<?php endif; ?>

<?php if (!empty($errores)): ?>
    <ul style="color: red;">
        <?php foreach ($errores as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p>
    ID de sesión: <b><?= session_id() ?></b><br>
    <?php if (!empty($_SESSION['formularios_enviados'])): ?>
        Primera fecha registrada: <b><?= array_key_first($_SESSION['formularios_enviados']) ?></b>
    <?php else: ?>
        No hay formularios registrados.
    <?php endif; ?>
</p>

<?php if (!empty($_SESSION['formularios_enviados'])): ?>
<table border="1" style="border-collapse: collapse; margin: 20px 0;">
    <tr>
        <th>Fecha-Hora</th><th>Nombre</th><th>Email</th>
        <th>Producto</th><th>Mes</th><th>Cantidad</th>
    </tr>
    <?php foreach ($_SESSION['formularios_enviados'] as $fecha => $f): ?>
    <tr>
        <td><?= htmlspecialchars($fecha) ?></td>
        <td><?= htmlspecialchars($f['nombre']) ?></td>
        <td><?= htmlspecialchars($f['email']) ?></td>
        <td><?= htmlspecialchars($f['producto']) ?></td>
        <td><?= htmlspecialchars($f['mes']) ?></td>
        <td><?= htmlspecialchars($f['cantidad']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<form method="post">
    <button type="submit" name="limpiar_sesion">🧹 Limpiar sesión</button>
</form>
