# 📋 INSTRUCCIONES: Agregar Botones de Asignación en Vista de Leads

## ✅ YA IMPLEMENTADO

### 1. **Notificaciones en el Navbar** ✅
- **Archivo:** `app/Views/Layouts/header.php` (líneas 69-101)
- **Estado:** ✅ COMPLETADO
- **Características:**
  - Badge con contador de notificaciones
  - Dropdown con lista de notificaciones
  - Botón "Marcar todas como leídas"
  - Actualización automática cada 30 segundos

### 2. **JavaScript de Notificaciones** ✅
- **Archivo:** `public/js/notificaciones/notificaciones-sistema.js`
- **Cargado en:** `app/Views/Layouts/footer.php` (línea 93)
- **Estado:** ✅ COMPLETADO

### 3. **Estilos CSS** ✅
- **Ubicación:** `app/Views/Layouts/footer.php` (líneas 99-167)
- **Estado:** ✅ COMPLETADO

---

## 🔧 FALTA AGREGAR: Botones en Vista de Lead

### **Archivo a Modificar:** `app/Views/leads/view.php`

Necesitas agregar estos elementos:

### **1. Cargar JavaScript de Asignación**

Agregar al final del archivo (antes de `<?= $this->endSection() ?>`):

```php
<?= $this->section('scripts') ?>
<!-- Sistema de Asignación de Leads -->
<script src="<?= base_url('js/leads/asignacion-leads.js') ?>"></script>
<?= $this->endSection() ?>
```

### **2. Agregar Botones en el Header del Lead**

Busca la sección donde está el encabezado del lead (probablemente cerca del título con el nombre del cliente) y agrega:

```php
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0">
                    <i class="ti-user text-primary"></i>
                    <?= esc($lead['nombres']) ?> <?= esc($lead['apellidos']) ?>
                </h4>
                <small class="text-muted">
                    Lead #<?= $lead['idlead'] ?> | 
                    <span class="badge bg-<?= $lead['etapa_color'] ?? 'secondary' ?>">
                        <?= esc($lead['etapa_nombre'] ?? 'Sin etapa') ?>
                    </span>
                </small>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group" role="group">
                    <!-- Botón Reasignar -->
                    <button type="button" class="btn btn-primary btn-sm btn-reasignar-lead" 
                            data-idlead="<?= $lead['idlead'] ?>">
                        <i class="ti-reload"></i> Reasignar
                    </button>
                    
                    <!-- Botón Solicitar Apoyo -->
                    <button type="button" class="btn btn-warning btn-sm btn-solicitar-apoyo" 
                            data-idlead="<?= $lead['idlead'] ?>">
                        <i class="ti-help-alt"></i> Solicitar Apoyo
                    </button>
                    
                    <!-- Botón Programar Seguimiento -->
                    <button type="button" class="btn btn-success btn-sm btn-programar-seguimiento" 
                            data-idlead="<?= $lead['idlead'] ?>">
                        <i class="ti-alarm-clock"></i> Programar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Resto del contenido del lead -->
    </div>
</div>
```

### **3. Alternativa: Botones en Sección de Acciones**

Si prefieres agregar los botones en una sección de acciones separada:

```php
<!-- Sección de Acciones Rápidas -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="ti-settings"></i> Acciones Rápidas</h5>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-4">
                <button type="button" class="btn btn-primary w-100 btn-reasignar-lead" 
                        data-idlead="<?= $lead['idlead'] ?>">
                    <i class="ti-reload"></i><br>
                    <strong>Reasignar Lead</strong><br>
                    <small>Transferir a otro usuario</small>
                </button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-warning w-100 btn-solicitar-apoyo" 
                        data-idlead="<?= $lead['idlead'] ?>">
                    <i class="ti-help-alt"></i><br>
                    <strong>Solicitar Apoyo</strong><br>
                    <small>Pedir ayuda a un compañero</small>
                </button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-success w-100 btn-programar-seguimiento" 
                        data-idlead="<?= $lead['idlead'] ?>">
                    <i class="ti-alarm-clock"></i><br>
                    <strong>Programar Seguimiento</strong><br>
                    <small>Agendar llamada o visita</small>
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 🎨 EJEMPLO COMPLETO DE VISTA

```php
<?= $this->extend('Layouts/base') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <!-- Header con Botones -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0">
                            <i class="ti-user text-primary"></i>
                            <?= esc($lead['nombres']) ?> <?= esc($lead['apellidos']) ?>
                        </h4>
                        <small class="text-muted">
                            Lead #<?= $lead['idlead'] ?> | 
                            <span class="badge bg-primary"><?= esc($lead['etapa_nombre'] ?? 'Sin etapa') ?></span>
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary btn-sm btn-reasignar-lead" 
                                    data-idlead="<?= $lead['idlead'] ?>">
                                <i class="ti-reload"></i> Reasignar
                            </button>
                            <button type="button" class="btn btn-warning btn-sm btn-solicitar-apoyo" 
                                    data-idlead="<?= $lead['idlead'] ?>">
                                <i class="ti-help-alt"></i> Solicitar Apoyo
                            </button>
                            <button type="button" class="btn btn-success btn-sm btn-programar-seguimiento" 
                                    data-idlead="<?= $lead['idlead'] ?>">
                                <i class="ti-alarm-clock"></i> Programar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Lead -->
        <div class="card">
            <div class="card-body">
                <!-- Tu contenido actual aquí -->
                <p><strong>Teléfono:</strong> <?= esc($lead['telefono']) ?></p>
                <p><strong>Email:</strong> <?= esc($lead['correo'] ?? 'No especificado') ?></p>
                <!-- etc... -->
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Sistema de Asignación de Leads -->
<script src="<?= base_url('js/leads/asignacion-leads.js') ?>"></script>
<?= $this->endSection() ?>
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### **Notificaciones (Ya Completado)** ✅
- [x] Dropdown de notificaciones en navbar
- [x] JavaScript de polling automático
- [x] Estilos CSS
- [x] Badge con contador
- [x] Botón "Marcar todas como leídas"

### **Botones de Asignación (Por Hacer)** ⏳
- [ ] Agregar JavaScript en vista de lead
- [ ] Agregar botones en header o sección de acciones
- [ ] Probar funcionalidad de reasignación
- [ ] Probar solicitud de apoyo
- [ ] Probar programación de seguimientos

---

## 🧪 CÓMO PROBAR

### **1. Probar Notificaciones:**
```
1. Abrir el sistema en el navegador
2. Verificar que aparece el ícono de campana en el navbar
3. Hacer clic en la campana
4. Debe aparecer "Cargando notificaciones..."
5. Si hay notificaciones, deben aparecer en la lista
6. El badge debe mostrar el número correcto
```

### **2. Probar Reasignación:**
```
1. Ir a /leads/view/1 (o cualquier lead)
2. Click en botón "Reasignar"
3. Debe abrir modal con lista de usuarios
4. Seleccionar usuario
5. Agregar motivo
6. Marcar "Crear tarea" (opcional)
7. Click "Reasignar Lead"
8. Verificar:
   - Lead cambia de usuario
   - Aparece en seguimientos
   - Usuario recibe notificación
```

### **3. Probar Solicitud de Apoyo:**
```
1. En vista de lead, click "Solicitar Apoyo"
2. Seleccionar usuario
3. Escribir mensaje
4. Marcar "URGENTE" (opcional)
5. Click "Enviar Solicitud"
6. Usuario seleccionado debe recibir notificación
7. Lead NO debe cambiar de usuario
```

### **4. Probar Programación:**
```
1. Click "Programar"
2. Seleccionar fecha y hora
3. Seleccionar tipo (Llamada, WhatsApp, etc.)
4. Agregar notas
5. Configurar recordatorio
6. Click "Programar"
7. Debe crear tarea automática
8. Debe aparecer en lista de tareas
```

---

## 📞 SOPORTE

Si algo no funciona:

1. **Verificar consola del navegador** (F12) para errores JavaScript
2. **Verificar logs de CodeIgniter** en `writable/logs/`
3. **Verificar que las rutas estén cargadas** en `app/Config/Routes.php`
4. **Verificar que los controladores existan:**
   - `app/Controllers/LeadAsignacion.php` ✅
   - `app/Controllers/Notificaciones.php` ✅

---

## 🎯 RESULTADO ESPERADO

Una vez implementado, tendrás:

✅ **Notificaciones en tiempo real** en el navbar
✅ **Botones de acción** en vista de lead
✅ **Modales interactivos** para reasignar, solicitar apoyo y programar
✅ **Sistema completo de comunicación** entre usuarios
✅ **Seguimiento continuo** de leads entre turnos

---

*Última actualización: 12 de Octubre, 2025*
