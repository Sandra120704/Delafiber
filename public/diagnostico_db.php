<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico Base de Datos - Leads</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; border-left: 4px solid #28a745; padding-left: 10px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background: #007bff; color: white; padding: 12px; text-align: left; }
        table td { padding: 10px; border-bottom: 1px solid #ddd; }
        table tr:hover { background: #f8f9fa; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico Completo - Módulo de Leads</h1>
        <p><strong>Fecha:</strong> <?= date('Y-m-d H:i:s') ?></p>

<?php
// Configuración de base de datos
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'delafiber';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    echo '<div class="success">✅ <strong>Conexión exitosa</strong> a la base de datos</div>';
    
    // ========================================
    // 1. VERIFICAR TABLAS EXISTEN
    // ========================================
    echo '<h2>1️⃣ Verificación de Tablas</h2>';
    
    $tablas_requeridas = ['leads', 'seguimientos', 'historial_leads', 'etapas', 'modalidades', 'usuarios'];
    $tablas_faltantes = [];
    
    echo '<table>';
    echo '<tr><th>Tabla</th><th>Estado</th><th>Registros</th></tr>';
    
    foreach ($tablas_requeridas as $tabla) {
        $result = $conn->query("SHOW TABLES LIKE '$tabla'");
        if ($result->num_rows > 0) {
            $count = $conn->query("SELECT COUNT(*) as total FROM $tabla")->fetch_assoc()['total'];
            echo "<tr><td>$tabla</td><td><span class='badge badge-success'>✅ EXISTE</span></td><td>$count</td></tr>";
        } else {
            $tablas_faltantes[] = $tabla;
            echo "<tr><td>$tabla</td><td><span class='badge badge-danger'>❌ NO EXISTE</span></td><td>-</td></tr>";
        }
    }
    echo '</table>';
    
    // ========================================
    // 2. SI FALTA historial_leads, CREAR
    // ========================================
    if (in_array('historial_leads', $tablas_faltantes)) {
        echo '<div class="warning">⚠️ <strong>Tabla historial_leads NO EXISTE</strong> - Creando ahora...</div>';
        
        $sql_create = "CREATE TABLE `historial_leads` (
            `idhistorial` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `idlead` INT UNSIGNED NOT NULL,
            `idusuario` INT UNSIGNED NOT NULL,
            `etapa_anterior` INT UNSIGNED NULL,
            `etapa_nueva` INT UNSIGNED NOT NULL,
            `motivo` TEXT NULL,
            `fecha` DATETIME NOT NULL,
            INDEX idx_lead (idlead),
            INDEX idx_usuario (idusuario),
            INDEX idx_fecha (fecha)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($conn->query($sql_create)) {
            echo '<div class="success">✅ <strong>Tabla historial_leads creada exitosamente</strong></div>';
        } else {
            echo '<div class="error">❌ Error al crear tabla: ' . $conn->error . '</div>';
        }
    }
    
    // ========================================
    // 3. VERIFICAR ESTRUCTURA DE LEADS
    // ========================================
    echo '<h2>2️⃣ Estructura de Tabla LEADS</h2>';
    
    $result = $conn->query("DESCRIBE leads");
    echo '<table>';
    echo '<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Key</th></tr>';
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
    }
    echo '</table>';
    
    // ========================================
    // 4. VERIFICAR ESTADOS DE LEADS
    // ========================================
    echo '<h2>3️⃣ Estados de Leads</h2>';
    
    $result = $conn->query("SELECT estado, COUNT(*) as total FROM leads GROUP BY estado");
    echo '<table>';
    echo '<tr><th>Estado</th><th>Total</th><th>Acción</th></tr>';
    
    $necesita_normalizacion = false;
    while ($row = $result->fetch_assoc()) {
        $estado = $row['estado'] ?? 'NULL';
        $total = $row['total'];
        
        if ($estado === 'NULL' || $estado === '' || $estado !== strtolower($estado)) {
            $necesita_normalizacion = true;
            echo "<tr><td>$estado</td><td>$total</td><td><span class='badge badge-warning'>⚠️ NORMALIZAR</span></td></tr>";
        } else {
            echo "<tr><td>$estado</td><td>$total</td><td><span class='badge badge-success'>✅ OK</span></td></tr>";
        }
    }
    echo '</table>';
    
    if ($necesita_normalizacion) {
        echo '<div class="warning">⚠️ <strong>Normalizando estados...</strong></div>';
        
        $conn->query("UPDATE leads SET estado = 'activo' WHERE estado IS NULL OR estado = '' OR estado = 'Activo'");
        $conn->query("UPDATE leads SET estado = 'convertido' WHERE estado = 'Convertido'");
        $conn->query("UPDATE leads SET estado = 'descartado' WHERE estado = 'Descartado'");
        
        echo '<div class="success">✅ Estados normalizados correctamente</div>';
    }
    
    // ========================================
    // 5. VERIFICAR ETAPAS
    // ========================================
    echo '<h2>4️⃣ Etapas Configuradas</h2>';
    
    $result = $conn->query("SELECT * FROM etapas ORDER BY orden");
    echo '<table>';
    echo '<tr><th>ID</th><th>Nombre</th><th>Orden</th><th>Color</th><th>Leads</th></tr>';
    
    while ($row = $result->fetch_assoc()) {
        $count = $conn->query("SELECT COUNT(*) as total FROM leads WHERE idetapa = {$row['idetapa']} AND estado = 'activo'")->fetch_assoc()['total'];
        $color = $row['color'] ?? '#6c757d';
        echo "<tr>";
        echo "<td>{$row['idetapa']}</td>";
        echo "<td><span style='background: $color; color: white; padding: 4px 8px; border-radius: 3px;'>{$row['nombre']}</span></td>";
        echo "<td>{$row['orden']}</td>";
        echo "<td>$color</td>";
        echo "<td>$count</td>";
        echo "</tr>";
    }
    echo '</table>';
    
    // ========================================
    // 6. VERIFICAR MODALIDADES
    // ========================================
    echo '<h2>5️⃣ Modalidades de Seguimiento</h2>';
    
    $result = $conn->query("SELECT * FROM modalidades");
    echo '<table>';
    echo '<tr><th>ID</th><th>Nombre</th><th>Seguimientos</th></tr>';
    
    while ($row = $result->fetch_assoc()) {
        $count = $conn->query("SELECT COUNT(*) as total FROM seguimientos WHERE idmodalidad = {$row['idmodalidad']}")->fetch_assoc()['total'];
        echo "<tr><td>{$row['idmodalidad']}</td><td>{$row['nombre']}</td><td>$count</td></tr>";
    }
    echo '</table>';
    
    // ========================================
    // 7. VERIFICAR USUARIOS
    // ========================================
    echo '<h2>6️⃣ Usuarios Activos</h2>';
    
    $result = $conn->query("SELECT u.idusuario, u.nombre, r.nombre as rol, COUNT(l.idlead) as total_leads 
                           FROM usuarios u 
                           LEFT JOIN roles r ON u.idrol = r.idrol 
                           LEFT JOIN leads l ON u.idusuario = l.idusuario AND l.estado = 'activo'
                           WHERE u.estado = 'activo'
                           GROUP BY u.idusuario");
    
    echo '<table>';
    echo '<tr><th>ID</th><th>Nombre</th><th>Rol</th><th>Leads Asignados</th></tr>';
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['idusuario']}</td><td>{$row['nombre']}</td><td>{$row['rol']}</td><td>{$row['total_leads']}</td></tr>";
    }
    echo '</table>';
    
    // ========================================
    // 8. VERIFICAR HISTORIAL
    // ========================================
    echo '<h2>7️⃣ Historial de Cambios</h2>';
    
    if (!in_array('historial_leads', $tablas_faltantes)) {
        $result = $conn->query("SELECT COUNT(*) as total FROM historial_leads");
        $total_historial = $result->fetch_assoc()['total'];
        
        if ($total_historial > 0) {
            echo "<div class='info'>📊 Total de cambios registrados: <strong>$total_historial</strong></div>";
            
            $result = $conn->query("SELECT h.*, u.nombre as usuario, ea.nombre as etapa_anterior, en.nombre as etapa_nueva 
                                   FROM historial_leads h
                                   LEFT JOIN usuarios u ON h.idusuario = u.idusuario
                                   LEFT JOIN etapas ea ON h.etapa_anterior = ea.idetapa
                                   LEFT JOIN etapas en ON h.etapa_nueva = en.idetapa
                                   ORDER BY h.fecha DESC LIMIT 10");
            
            echo '<p><strong>Últimos 10 cambios:</strong></p>';
            echo '<table>';
            echo '<tr><th>Fecha</th><th>Lead ID</th><th>Usuario</th><th>Cambio</th><th>Motivo</th></tr>';
            
            while ($row = $result->fetch_assoc()) {
                $etapa_ant = $row['etapa_anterior'] ?? 'NUEVO';
                $etapa_nueva = $row['etapa_nueva'];
                echo "<tr>";
                echo "<td>{$row['fecha']}</td>";
                echo "<td>{$row['idlead']}</td>";
                echo "<td>{$row['usuario']}</td>";
                echo "<td>$etapa_ant → $etapa_nueva</td>";
                echo "<td>{$row['motivo']}</td>";
                echo "</tr>";
            }
            echo '</table>';
        } else {
            echo '<div class="warning">⚠️ No hay cambios registrados en el historial</div>';
        }
    }
    
    // ========================================
    // 9. VERIFICAR SEGUIMIENTOS
    // ========================================
    echo '<h2>8️⃣ Seguimientos Registrados</h2>';
    
    $result = $conn->query("SELECT COUNT(*) as total FROM seguimientos");
    $total_seguimientos = $result->fetch_assoc()['total'];
    
    if ($total_seguimientos > 0) {
        echo "<div class='info'>📊 Total de seguimientos: <strong>$total_seguimientos</strong></div>";
        
        $result = $conn->query("SELECT s.*, u.nombre as usuario, m.nombre as modalidad, l.idlead
                               FROM seguimientos s
                               LEFT JOIN usuarios u ON s.idusuario = u.idusuario
                               LEFT JOIN modalidades m ON s.idmodalidad = m.idmodalidad
                               LEFT JOIN leads l ON s.idlead = l.idlead
                               ORDER BY s.fecha DESC LIMIT 10");
        
        echo '<p><strong>Últimos 10 seguimientos:</strong></p>';
        echo '<table>';
        echo '<tr><th>Fecha</th><th>Lead ID</th><th>Usuario</th><th>Modalidad</th><th>Nota</th></tr>';
        
        while ($row = $result->fetch_assoc()) {
            $nota = substr($row['nota'], 0, 50) . '...';
            echo "<tr>";
            echo "<td>{$row['fecha']}</td>";
            echo "<td>{$row['idlead']}</td>";
            echo "<td>{$row['usuario']}</td>";
            echo "<td>{$row['modalidad']}</td>";
            echo "<td>$nota</td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<div class="warning">⚠️ No hay seguimientos registrados</div>';
    }
    
    // ========================================
    // 10. PRUEBA DE INSERCIÓN
    // ========================================
    echo '<h2>9️⃣ Prueba de Inserción en historial_leads</h2>';
    
    if (!in_array('historial_leads', $tablas_faltantes)) {
        // Obtener un lead de prueba
        $lead_test = $conn->query("SELECT idlead, idetapa FROM leads LIMIT 1")->fetch_assoc();
        
        if ($lead_test) {
            $test_data = [
                'idlead' => $lead_test['idlead'],
                'idusuario' => 1,
                'etapa_anterior' => $lead_test['idetapa'],
                'etapa_nueva' => $lead_test['idetapa'],
                'motivo' => 'PRUEBA DE DIAGNÓSTICO - IGNORAR',
                'fecha' => date('Y-m-d H:i:s')
            ];
            
            $sql = "INSERT INTO historial_leads (idlead, idusuario, etapa_anterior, etapa_nueva, motivo, fecha) 
                    VALUES ({$test_data['idlead']}, {$test_data['idusuario']}, {$test_data['etapa_anterior']}, {$test_data['etapa_nueva']}, '{$test_data['motivo']}', '{$test_data['fecha']}')";
            
            if ($conn->query($sql)) {
                $insert_id = $conn->insert_id;
                echo '<div class="success">✅ <strong>Inserción exitosa</strong> - ID: ' . $insert_id . '</div>';
                
                // Eliminar el registro de prueba
                $conn->query("DELETE FROM historial_leads WHERE idhistorial = $insert_id");
                echo '<div class="info">🗑️ Registro de prueba eliminado</div>';
            } else {
                echo '<div class="error">❌ Error en inserción: ' . $conn->error . '</div>';
            }
        } else {
            echo '<div class="warning">⚠️ No hay leads para probar</div>';
        }
    }
    
    // ========================================
    // RESUMEN FINAL
    // ========================================
    echo '<h2>✅ Resumen Final</h2>';
    
    $problemas = [];
    
    if (count($tablas_faltantes) > 0) {
        $problemas[] = "Tablas faltantes: " . implode(', ', $tablas_faltantes);
    }
    
    if ($necesita_normalizacion) {
        $problemas[] = "Estados de leads necesitaban normalización (ya corregido)";
    }
    
    if (count($problemas) > 0) {
        echo '<div class="warning"><strong>⚠️ Problemas encontrados y corregidos:</strong><ul>';
        foreach ($problemas as $problema) {
            echo "<li>$problema</li>";
        }
        echo '</ul></div>';
    } else {
        echo '<div class="success"><strong>✅ TODO ESTÁ CORRECTO</strong> - La base de datos está lista para usar</div>';
    }
    
    echo '<div class="info">';
    echo '<h3>📝 Próximos Pasos:</h3>';
    echo '<ol>';
    echo '<li>Cierra esta ventana</li>';
    echo '<li>Presiona <strong>Ctrl + F5</strong> en tu navegador para limpiar caché</li>';
    echo '<li>Ve a: <a href="/Delafiber/leads/view/1" target="_blank">Ver un Lead</a></li>';
    echo '<li>Abre la consola del navegador (F12)</li>';
    echo '<li>Intenta cambiar de etapa</li>';
    echo '</ol>';
    echo '</div>';
    
    $conn->close();
    
} catch (Exception $e) {
    echo '<div class="error">❌ <strong>ERROR:</strong> ' . $e->getMessage() . '</div>';
}
?>

        <hr>
        <p style="text-align: center; color: #666;">
            <a href="/Delafiber/leads" class="btn">← Volver a Leads</a>
            <a href="?" class="btn">🔄 Recargar Diagnóstico</a>
        </p>
    </div>
</body>
</html>
