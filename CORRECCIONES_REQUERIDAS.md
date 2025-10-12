# CORRECCIONES REQUERIDAS - DELAFIBER CRM

## 🔴 CORRECCIONES CRÍTICAS (Implementar inmediatamente)

### 1. ServicioModel - Agregar campo `velocidad`

**Archivo:** `app/Models/ServicioModel.php`

**Problema:** El campo `velocidad` existe en la base de datos pero no está en `$allowedFields`

**Corrección:**
```php
protected $allowedFields = [
    'nombre',
    'descripcion',
    'velocidad',    // ✅ AGREGAR ESTA LÍNEA
    'categoria',
    'precio',
    'estado'
];
```

---

### 2. LeadModel - Agregar campos de dirección de servicio

**Archivo:** `app/Models/LeadModel.php`

**Problema:** Campos de dirección de servicio existen en BD pero no en modelo

**Corrección:**
```php
protected $allowedFields = [
    'idpersona',
    'idusuario',
    'idusuario_registro',
    'idorigen',
    'idetapa',
    'idcampania',
    'nota_inicial',
    'estado',
    'fecha_conversion',
    'motivo_descarte',
    'direccion_servicio',      // ✅ AGREGAR
    'distrito_servicio',        // ✅ AGREGAR
    'coordenadas_servicio',     // ✅ AGREGAR
    'zona_servicio',            // ✅ AGREGAR
    'tipo_solicitud'            // ✅ AGREGAR
];
```

---

### 3. CotizacionModel - Agregar campos faltantes

**Archivo:** `app/Models/CotizacionModel.php`

**Problema:** Múltiples campos en BD no están en `$allowedFields`

**Corrección:**
```php
protected $allowedFields = [
    'idlead',
    'iddireccion',
    'idusuario',
    'numero_cotizacion',
    'subtotal',
    'igv',
    'total',
    'precio_cotizado',
    'descuento_aplicado',
    'precio_instalacion',
    'vigencia_dias',
    'fecha_vencimiento',        // ✅ AGREGAR
    'condiciones_pago',         // ✅ AGREGAR
    'tiempo_instalacion',       // ✅ AGREGAR
    'observaciones',
    'direccion_instalacion',    // ✅ AGREGAR
    'pdf_generado',             // ✅ AGREGAR
    'enviado_por',              // ✅ AGREGAR
    'estado',
    'motivo_rechazo',           // ✅ AGREGAR
    'fecha_envio',
    'fecha_respuesta'
];
```

---

## 🟡 CORRECCIONES RECOMENDADAS (Media prioridad)

### 4. Validar uso de campo `iddireccion` en cotizaciones

**Análisis:**
- Campo existe en BD y modelo
- NO se usa en controladores ni vistas
- Tabla `direcciones` existe pero no se relaciona

**Opciones:**

**Opción A - Eliminar campo (si no se usará):**
```sql
ALTER TABLE cotizaciones DROP FOREIGN KEY fk_cotizacion_direccion;
ALTER TABLE cotizaciones DROP COLUMN iddireccion;
```

**Opción B - Implementar funcionalidad completa:**
1. Agregar selector de dirección en formulario de cotización
2. Relacionar cotización con dirección específica
3. Permitir múltiples direcciones por persona

---

### 5. Actualizar validaciones en PersonaModel

**Archivo:** `app/Models/PersonaModel.php`

**Mejora:** Hacer DNI y teléfono más flexibles

**Corrección sugerida:**
```php
protected $validationRules = [
    'nombres' => 'required|min_length[2]|max_length[100]',
    'apellidos' => 'required|min_length[2]|max_length[100]',
    'dni' => 'permit_empty|min_length[8]|max_length[8]|numeric|is_unique[personas.dni,idpersona,{idpersona}]',
    'correo' => 'permit_empty|valid_email|max_length[150]',
    'telefono' => 'required|min_length[9]|max_length[9]|numeric',  // Más flexible
    'iddistrito' => 'permit_empty|numeric',
    'coordenadas' => 'permit_empty|max_length[100]',
    'id_zona' => 'permit_empty|numeric'
];
```

---

## 🟢 MEJORAS SUGERIDAS (Baja prioridad)

### 6. Agregar índices adicionales para optimización

**Archivo:** Ejecutar en base de datos

```sql
-- Índices para mejorar rendimiento en búsquedas frecuentes
ALTER TABLE personas ADD INDEX idx_nombres_apellidos (nombres, apellidos);
ALTER TABLE leads ADD INDEX idx_fecha_estado (created_at, estado);
ALTER TABLE cotizaciones ADD INDEX idx_estado_fecha (estado, created_at);
ALTER TABLE seguimientos ADD INDEX idx_lead_fecha (idlead, fecha);
```

---

### 7. Agregar campos de auditoría faltantes

**Varios modelos no tienen `updated_at` configurado correctamente**

Verificar en:
- NotificacionModel
- ComentarioModel
- ModalidadModel

---

### 8. Documentar APIs internas

Agregar comentarios PHPDoc en métodos públicos de modelos:

```php
/**
 * Obtener leads con filtros aplicados
 * 
 * @param int|null $userId ID del usuario (null para admin)
 * @param array $filtros Array de filtros ['etapa' => int, 'origen' => int, etc]
 * @return array Lista de leads con información completa
 */
public function getLeadsConFiltros($userId, $filtros = [])
{
    // ...
}
```

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

### Fase 1: Correcciones Críticas (30 minutos)
- [ ] Corregir ServicioModel
- [ ] Corregir LeadModel  
- [ ] Corregir CotizacionModel
- [ ] Probar inserción de datos con nuevos campos
- [ ] Verificar que no se rompan funcionalidades existentes

### Fase 2: Validación (1 hora)
- [ ] Crear servicio con velocidad
- [ ] Crear lead con dirección de servicio
- [ ] Crear cotización con campos adicionales
- [ ] Verificar que datos se guarden correctamente
- [ ] Probar edición de registros existentes

### Fase 3: Actualizar Vistas (2-4 horas)
- [ ] Agregar campo velocidad en formulario de servicios
- [ ] Agregar campos de dirección de servicio en formulario de leads
- [ ] Agregar campos adicionales en formulario de cotizaciones
- [ ] Actualizar vistas de detalle para mostrar nuevos campos
- [ ] Actualizar JavaScript si es necesario

### Fase 4: Pruebas Integrales (1 hora)
- [ ] Probar flujo completo: Lead → Cotización → Cierre
- [ ] Verificar geocodificación con direcciones de servicio
- [ ] Probar asignación automática de zonas
- [ ] Verificar cálculos de cotizaciones
- [ ] Probar búsquedas y filtros

### Fase 5: Optimizaciones (Opcional)
- [ ] Decidir sobre campo `iddireccion`
- [ ] Agregar índices de BD
- [ ] Mejorar validaciones
- [ ] Agregar documentación

---

## 🔧 COMANDOS ÚTILES

### Backup antes de cambios
```bash
# Backup de base de datos
mysqldump -u root delafiber > backup_antes_correcciones.sql

# Backup de modelos
cp -r app/Models app/Models.backup
```

### Restaurar si algo falla
```bash
mysql -u root delafiber < backup_antes_correcciones.sql
```

### Verificar estructura de tablas
```sql
DESCRIBE servicios;
DESCRIBE leads;
DESCRIBE cotizaciones;
```

---

## ⚠️ ADVERTENCIAS

1. **NO eliminar campos de `$allowedFields` existentes** - Solo agregar
2. **Hacer backup antes de modificar** - Siempre tener punto de restauración
3. **Probar en desarrollo primero** - No aplicar directo en producción
4. **Verificar foreign keys** - Al agregar campos con relaciones
5. **Actualizar validaciones** - Si campos son requeridos

---

## 📞 SOPORTE POST-IMPLEMENTACIÓN

### Si algo falla:
1. Revisar logs de CodeIgniter: `writable/logs/`
2. Verificar errores de SQL en consola
3. Usar `var_dump()` para depurar datos
4. Verificar que campos existen en BD: `SHOW COLUMNS FROM tabla`

### Verificar que correcciones funcionan:
```php
// En un controlador temporal o método de prueba
$servicioModel = new \App\Models\ServicioModel();
$data = [
    'nombre' => 'Test',
    'velocidad' => '100 Mbps',  // ✅ Debe funcionar ahora
    'precio' => 80.00,
    'estado' => 'Activo'
];
$id = $servicioModel->insert($data);
var_dump($id); // Debe retornar ID, no false
```

---

*Documento generado: Octubre 2025*
*Prioridad: ALTA - Implementar lo antes posible*
