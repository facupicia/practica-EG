<?php
session_start();

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'formularios_db'; 
$username = 'root'; 
$password = '1234'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validar que todos los campos requeridos estén presentes
    $campos_requeridos = ['nombre', 'email', 'producto', 'mes', 'cantidad', 'terminos'];
    $errores = [];
    
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            $errores[] = "El campo $campo es requerido";
        }
    }
    
    // Validar que los términos y condiciones estén aceptados
    if (!isset($_POST['terminos'])) {
        $errores[] = "Debe aceptar los términos y condiciones";
    }
    
    // Si no hay errores, procesar los datos
    if (empty($errores)) {
        
        // Obtener la fecha y hora actual
        $fecha_hora = date('Y-m-d H:i:s');
        
        // Preparar los datos para insertar
        $datos_formulario = [
            'nombre' => $_POST['nombre'],
            'email' => $_POST['email'],
            'producto' => $_POST['producto'],
            'mes' => $_POST['mes'],
            'cantidad' => $_POST['cantidad'],
            'fecha_hora' => $fecha_hora
        ];
        
        try {
            // Insertar en la base de datos
            $sql = "INSERT INTO formularios (nombre, email, producto, mes, cantidad, fecha_hora) 
                    VALUES (:nombre, :email, :producto, :mes, :cantidad, :fecha_hora)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($datos_formulario);
            
            // Guardar en sesión agrupado por fecha-hora
            if (!isset($_SESSION['formularios_enviados'])) {
                $_SESSION['formularios_enviados'] = [];
            }
            
            // Usar fecha-hora como clave para agrupar
            $_SESSION['formularios_enviados'][$fecha_hora][] = $datos_formulario;
            
            $mensaje_exito = "Formulario enviado correctamente. Fecha: " . $fecha_hora;
            
        } catch(PDOException $e) {
            $errores[] = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
}

// Función para limpiar la sesión
limpiar_sesion();
function limpiar_sesion() {
    if (isset($_POST['limpiar_sesion'])) {
        unset($_SESSION['formularios_enviados']);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>