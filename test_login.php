<?php
// Script de prueba para verificar login
require __DIR__ . '/vendor/autoload.php';

// Cargar configuración de CodeIgniter
$pathsConfig = new \Config\Paths();
$bootstrap = \CodeIgniter\Boot::bootWeb($pathsConfig);

$db = \Config\Database::connect();

echo "<h2>🔍 Diagnóstico de Login</h2>";
echo "<hr>";

// 1. Verificar si existe el usuario
echo "<h3>1. Verificando usuario 'jperes':</h3>";
$query = $db->query("SELECT idusuario, usuario, clave, idpersona, idrol, activo FROM usuarios WHERE usuario = 'jperes'");
$user = $query->getRowArray();

if ($user) {
    echo "✅ Usuario encontrado:<br>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
    // 2. Verificar contraseña
    echo "<h3>2. Verificando contraseña:</h3>";
    $password = '123456';
    echo "Contraseña ingresada: <strong>$password</strong><br>";
    echo "Contraseña en BD: <strong>{$user['clave']}</strong><br>";
    
    if ($user['clave'] === $password) {
        echo "✅ Contraseña coincide (texto plano)<br>";
    } elseif (password_verify($password, $user['clave'])) {
        echo "✅ Contraseña coincide (hash)<br>";
    } else {
        echo "❌ Contraseña NO coincide<br>";
        echo "Sugerencia: Actualiza la contraseña con este SQL:<br>";
        echo "<code>UPDATE usuarios SET clave = '123456' WHERE usuario = 'jperes';</code><br>";
    }
    
    // 3. Verificar estado activo
    echo "<h3>3. Verificando estado:</h3>";
    if ($user['activo'] == 1) {
        echo "✅ Usuario ACTIVO<br>";
    } else {
        echo "❌ Usuario INACTIVO<br>";
        echo "Sugerencia: Activa el usuario con este SQL:<br>";
        echo "<code>UPDATE usuarios SET activo = 1 WHERE usuario = 'jperes';</code><br>";
    }
    
    // 4. Verificar persona asociada
    echo "<h3>4. Verificando persona asociada:</h3>";
    if ($user['idpersona']) {
        $persona = $db->query("SELECT * FROM personas WHERE idpersona = ?", [$user['idpersona']])->getRowArray();
        if ($persona) {
            echo "✅ Persona encontrada: {$persona['nombres']} {$persona['apellidos']}<br>";
        } else {
            echo "⚠️ idpersona existe pero no se encuentra la persona<br>";
        }
    } else {
        echo "⚠️ Usuario sin persona asociada (NULL)<br>";
    }
    
    // 5. Verificar rol
    echo "<h3>5. Verificando rol:</h3>";
    if ($user['idrol']) {
        $rol = $db->query("SELECT * FROM roles WHERE idrol = ?", [$user['idrol']])->getRowArray();
        if ($rol) {
            echo "✅ Rol encontrado: {$rol['nombre']}<br>";
        } else {
            echo "❌ idrol existe pero no se encuentra el rol<br>";
        }
    } else {
        echo "❌ Usuario sin rol asignado<br>";
        echo "Sugerencia: Asigna un rol con este SQL:<br>";
        echo "<code>UPDATE usuarios SET idrol = 1 WHERE usuario = 'jperes';</code><br>";
    }
    
    // 6. Probar query completo del login
    echo "<h3>6. Probando query completo de login:</h3>";
    $loginQuery = "
        SELECT u.idusuario, u.usuario, u.clave, u.activo,
               COALESCE(CONCAT(p.nombres, ' ', p.apellidos), u.usuario) as nombre_completo,
               p.correo, 
               COALESCE(r.nombre, 'Usuario') as rol
        FROM usuarios u
        LEFT JOIN personas p ON u.idpersona = p.idpersona
        LEFT JOIN roles r ON u.idrol = r.idrol
        WHERE u.usuario = 'jperes' AND u.activo = 1
    ";
    
    $loginResult = $db->query($loginQuery)->getRowArray();
    
    if ($loginResult) {
        echo "✅ Query de login exitoso:<br>";
        echo "<pre>";
        print_r($loginResult);
        echo "</pre>";
    } else {
        echo "❌ Query de login falló<br>";
    }
    
} else {
    echo "❌ Usuario 'jperes' NO encontrado en la base de datos<br>";
    echo "<h3>Usuarios disponibles:</h3>";
    $allUsers = $db->query("SELECT idusuario, usuario, activo FROM usuarios")->getResultArray();
    echo "<pre>";
    print_r($allUsers);
    echo "</pre>";
    
    echo "<h3>Solución:</h3>";
    echo "Crea el usuario con este SQL:<br>";
    echo "<code>
    INSERT INTO usuarios (usuario, clave, idrol, activo) 
    VALUES ('jperes', '123456', 1, 1);
    </code>";
}

echo "<hr>";
echo "<h3>✅ Diagnóstico completado</h3>";
echo "<p><a href='/auth'>Volver al login</a></p>";
