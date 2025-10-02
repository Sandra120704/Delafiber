<?php
// Script simple para ver usuarios
$host = 'localhost';
$dbname = 'delafiber';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>👥 Usuarios Registrados en el Sistema</h2>";
    echo "<hr>";
    
    // Obtener todos los usuarios
    $stmt = $pdo->query("
        SELECT 
            u.idusuario,
            u.usuario,
            u.clave,
            u.idpersona,
            u.idrol,
            u.activo,
            COALESCE(CONCAT(p.nombres, ' ', p.apellidos), 'Sin persona') as nombre_completo,
            COALESCE(r.nombre, 'Sin rol') as rol
        FROM usuarios u
        LEFT JOIN personas p ON u.idpersona = p.idpersona
        LEFT JOIN roles r ON u.idrol = r.idrol
        ORDER BY u.idusuario
    ");
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($usuarios) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>
                <th>ID</th>
                <th>Usuario</th>
                <th>Contraseña</th>
                <th>Nombre Completo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acción</th>
              </tr>";
        
        foreach ($usuarios as $user) {
            $estado = $user['activo'] == 1 ? '✅ Activo' : '❌ Inactivo';
            $estadoColor = $user['activo'] == 1 ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>{$user['idusuario']}</td>";
            echo "<td><strong>{$user['usuario']}</strong></td>";
            echo "<td><code>{$user['clave']}</code></td>";
            echo "<td>{$user['nombre_completo']}</td>";
            echo "<td>{$user['rol']}</td>";
            echo "<td style='color: $estadoColor;'><strong>$estado</strong></td>";
            echo "<td>
                    <a href='?activar={$user['idusuario']}' style='color: green;'>Activar</a> | 
                    <a href='?reset_pass={$user['idusuario']}' style='color: blue;'>Reset Pass</a>
                  </td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        echo "<br><hr>";
        echo "<h3>📝 Instrucciones:</h3>";
        echo "<ul>";
        echo "<li>Si la contraseña está en <strong>texto plano</strong>, puedes usarla directamente</li>";
        echo "<li>Si la contraseña empieza con <code>\$2y\$</code>, está hasheada</li>";
        echo "<li>Click en <strong>Activar</strong> para activar un usuario inactivo</li>";
        echo "<li>Click en <strong>Reset Pass</strong> para cambiar la contraseña a '123456'</li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>❌ No hay usuarios registrados en la base de datos</p>";
        echo "<h3>Crear usuario de prueba:</h3>";
        echo "<p>Ejecuta este SQL en phpMyAdmin:</p>";
        echo "<pre style='background: #f0f0f0; padding: 15px;'>
INSERT INTO usuarios (usuario, clave, idrol, activo) 
VALUES ('jperes', '123456', 1, 1);
        </pre>";
    }
    
    // Procesar acciones
    if (isset($_GET['activar'])) {
        $id = (int)$_GET['activar'];
        $pdo->exec("UPDATE usuarios SET activo = 1 WHERE idusuario = $id");
        echo "<script>alert('Usuario activado'); window.location.href='ver_usuarios.php';</script>";
    }
    
    if (isset($_GET['reset_pass'])) {
        $id = (int)$_GET['reset_pass'];
        $pdo->exec("UPDATE usuarios SET clave = '123456' WHERE idusuario = $id");
        echo "<script>alert('Contraseña cambiada a: 123456'); window.location.href='ver_usuarios.php';</script>";
    }
    
    echo "<br><hr>";
    echo "<p><a href='/auth' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔐 Ir al Login</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error de conexión</h2>";
    echo "<p>No se pudo conectar a la base de datos: " . $e->getMessage() . "</p>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>Laragon esté ejecutándose</li>";
    echo "<li>MySQL esté activo</li>";
    echo "<li>La base de datos 'delafiber' exista</li>";
    echo "</ul>";
}
?>
