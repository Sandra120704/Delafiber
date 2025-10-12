# 🐛 DEBUG: Botones No Abren Modales

## 🔍 PASOS PARA DEPURAR

### **1. Abrir Consola del Navegador**
```
1. Presiona F12 en tu navegador
2. Ve a la pestaña "Console" (Consola)
3. Recarga la página (F5)
4. Busca estos mensajes:
```

**Mensajes esperados:**
```
🚀 Sistema de Asignación de Leads cargado
📦 Bootstrap disponible: true
📦 jQuery disponible: true
📦 Swal disponible: true
🔧 Inicializando eventos de asignación...
📌 Botones reasignar encontrados: 1
📌 Botones solicitar apoyo encontrados: 1
📌 Botones programar encontrados: 1
✅ Eventos inicializados correctamente
✅ Usuarios disponibles cargados: X
```

### **2. Hacer Click en un Botón**

Cuando hagas click en "Reasignar", deberías ver:
```
🔄 Click en Reasignar, Lead ID: 1
```

---

## ❌ PROBLEMAS COMUNES Y SOLUCIONES

### **Problema 1: "Bootstrap disponible: false"**

**Causa:** Bootstrap no está cargado o hay conflicto de versiones

**Solución:** Verificar que en `app/Views/Layouts/footer.php` esté:
```php
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

**O cambiar el modal para usar jQuery:**
```javascript
// En lugar de:
const modal = new bootstrap.Modal(document.getElementById('modalReasignar'));
modal.show();

// Usar:
$('#modalReasignar').modal('show');
```

---

### **Problema 2: "Botones encontrados: 0"**

**Causa:** Los botones no existen en el DOM cuando se ejecuta el script

**Solución:** Verificar que los botones tengan las clases correctas:
```html
<button class="btn btn-primary btn-sm btn-reasignar-lead" data-idlead="<?= $lead['idlead'] ?>">
```

**Verificar en consola:**
```javascript
document.querySelectorAll('.btn-reasignar-lead')
// Debe retornar: NodeList [button]
```

---

### **Problema 3: Click no hace nada**

**Causa:** Evento no se está disparando

**Solución temporal - Agregar eventos inline:**

En `app/Views/leads/view.php`, cambiar los botones a:

```php
<button type="button" class="btn btn-primary btn-sm" 
        onclick="mostrarModalReasignar(<?= $lead['idlead'] ?>)">
    <i class="ti-reload"></i> Reasignar
</button>
<button type="button" class="btn btn-warning btn-sm" 
        onclick="mostrarModalSolicitarApoyo(<?= $lead['idlead'] ?>)">
    <i class="ti-help-alt"></i> Solicitar Apoyo
</button>
<button type="button" class="btn btn-success btn-sm" 
        onclick="mostrarModalProgramarSeguimiento(<?= $lead['idlead'] ?>)">
    <i class="ti-alarm-clock"></i> Programar
</button>
```

---

### **Problema 4: Modal no se muestra**

**Causa:** Conflicto entre Bootstrap 5 y versiones anteriores

**Solución:** Usar jQuery en lugar de Bootstrap 5 API

Modificar en `asignacion-leads.js` la función `mostrarModalReasignar`:

```javascript
// Cambiar ESTA línea (línea ~140):
const modal = new bootstrap.Modal(document.getElementById('modalReasignar'));
modal.show();

// POR ESTA:
$('#modalReasignar').modal('show');
```

Hacer lo mismo en:
- `mostrarModalSolicitarApoyo` (línea ~200)
- `mostrarModalProgramarSeguimiento` (línea ~260)

---

## 🔧 SOLUCIÓN RÁPIDA (Si nada funciona)

### **Opción 1: Usar eventos inline (más simple)**

Reemplaza los botones en `app/Views/leads/view.php` con:

```php
<!-- Botones de Asignación y Comunicación -->
<div class="btn-group" role="group">
    <button type="button" class="btn btn-primary btn-sm" 
            onclick="window.mostrarModalReasignar(<?= $lead['idlead'] ?>)">
        <i class="ti-reload"></i> Reasignar
    </button>
    <button type="button" class="btn btn-warning btn-sm" 
            onclick="window.mostrarModalSolicitarApoyo(<?= $lead['idlead'] ?>)">
        <i class="ti-help-alt"></i> Solicitar Apoyo
    </button>
    <button type="button" class="btn btn-success btn-sm" 
            onclick="window.mostrarModalProgramarSeguimiento(<?= $lead['idlead'] ?>)">
        <i class="ti-alarm-clock"></i> Programar
    </button>
</div>
```

---

### **Opción 2: Usar jQuery en lugar de Bootstrap 5**

En `asignacion-leads.js`, buscar y reemplazar TODAS las líneas que digan:

```javascript
const modal = new bootstrap.Modal(document.getElementById('modalXXX'));
modal.show();
```

Por:

```javascript
$('#modalXXX').modal('show');
```

---

## 📋 CHECKLIST DE VERIFICACIÓN

- [ ] Consola muestra "Sistema de Asignación de Leads cargado"
- [ ] Bootstrap disponible: true
- [ ] jQuery disponible: true
- [ ] Swal disponible: true
- [ ] Botones encontrados: 1 (o más)
- [ ] Click en botón muestra mensaje en consola
- [ ] Modal se crea en el DOM (inspeccionar elemento)
- [ ] Modal se muestra visualmente

---

## 🆘 SI AÚN NO FUNCIONA

Copia y pega esto en la consola del navegador:

```javascript
// Test 1: Verificar que el botón existe
console.log('Botones:', document.querySelectorAll('.btn-reasignar-lead'));

// Test 2: Verificar que la función existe
console.log('Función existe:', typeof mostrarModalReasignar);

// Test 3: Ejecutar función manualmente
mostrarModalReasignar(1);

// Test 4: Verificar que el modal se creó
console.log('Modal en DOM:', document.getElementById('modalReasignar'));
```

Si el Test 3 funciona, el problema es el evento click. Usa la **Opción 1** (eventos inline).

---

## 📞 INFORMACIÓN PARA SOPORTE

Si necesitas ayuda, proporciona:

1. **Mensajes de la consola** (captura de pantalla)
2. **Versión de Bootstrap** que estás usando
3. **Errores en consola** (si los hay)
4. **Resultado de los tests** de arriba

---

*Documento de depuración - Octubre 2025*
