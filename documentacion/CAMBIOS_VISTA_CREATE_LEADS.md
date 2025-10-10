# 📝 CAMBIOS EN LA VISTA `create.php` - MÚLTIPLES SOLICITUDES

## ✅ MODIFICACIONES REALIZADAS

### **1. Sección de Búsqueda de Cliente (NUEVO)**

Se agregó una sección completa para buscar clientes existentes por teléfono:

```php
<!-- Buscar por Teléfono (NUEVO) -->
<div class="form-group">
    <label for="buscar_telefono">Buscar por Teléfono</label>
    <div class="input-group">
        <input type="text" class="form-control" id="buscar_telefono" 
               placeholder="Ingrese teléfono (9 dígitos)" maxlength="9">
        <div class="input-group-append">
            <button class="btn btn-success" type="button" id="btnBuscarTelefono">
                <i class="icon-search"></i> Buscar Cliente
            </button>
        </div>
    </div>
</div>

<!-- Resultado de búsqueda -->
<div id="resultado-busqueda" style="display:none;"></div>

<!-- Campo oculto para ID de persona -->
<input type="hidden" id="idpersona" name="idpersona">
```

---

### **2. Campos de Dirección de Servicio (NUEVO)**

Se agregaron campos específicos para la dirección donde se instalará el servicio:

```php
<!-- Tipo de Instalación -->
<div class="form-group">
    <label for="tipo_solicitud">Tipo de Instalación *</label>
    <select class="form-control" id="tipo_solicitud" name="tipo_solicitud" required>
        <option value="">Seleccione</option>
        <option value="Casa">🏠 Casa / Hogar</option>
        <option value="Negocio">🏢 Negocio / Empresa</option>
        <option value="Oficina">🏛️ Oficina</option>
        <option value="Otro">📍 Otro</option>
    </select>
</div>

<!-- Distrito de Instalación -->
<div class="form-group">
    <label for="distrito_servicio">Distrito de Instalación *</label>
    <select class="form-control" id="distrito_servicio" name="distrito_servicio" required>
        <option value="">Seleccione distrito</option>
        <!-- Opciones de distritos -->
    </select>
</div>

<!-- Dirección de Instalación -->
<div class="form-group">
    <label for="direccion_servicio">Dirección de Instalación del Servicio *</label>
    <input type="text" class="form-control" id="direccion_servicio" 
           name="direccion_servicio" required
           placeholder="Ej: Jr. Comercio 456, Chincha Alta">
    <small class="text-muted">
        Esta dirección puede ser diferente a la dirección personal del cliente
    </small>
</div>
```

---

### **3. JavaScript para Búsqueda (NUEVO ARCHIVO)**

**Archivo:** `public/js/leads/buscar-cliente.js`

Funcionalidades:
- ✅ Buscar cliente por teléfono
- ✅ Autocompletar datos si existe
- ✅ Mostrar solicitudes activas del cliente
- ✅ Permitir crear nueva solicitud
- ✅ Validaciones en tiempo real

---

## 🎯 FLUJO DE TRABAJO EN LA VISTA

### **CASO 1: Cliente Nuevo**

```
1. Usuario ingresa teléfono → Buscar
2. Sistema: "Cliente no encontrado"
3. Usuario completa todos los datos
4. Llena dirección de servicio
5. Guarda → Se crea persona + lead
```

### **CASO 2: Cliente Existente - Primera Solicitud**

```
1. Usuario ingresa teléfono → Buscar
2. Sistema: "Cliente encontrado - 0 solicitudes activas"
3. Datos personales se autocompletan (bloqueados)
4. Usuario solo llena:
   - Tipo de solicitud (Casa)
   - Dirección de servicio
   - Distrito de servicio
5. Guarda → Se crea solo el lead (persona ya existe)
```

### **CASO 3: Cliente Existente - Segunda Solicitud**

```
1. Usuario ingresa teléfono → Buscar
2. Sistema: "Cliente encontrado - 1 solicitud activa"
3. Datos personales se autocompletan (bloqueados)
4. Usuario llena NUEVA ubicación:
   - Tipo de solicitud (Negocio)
   - Dirección de servicio (DIFERENTE)
   - Distrito de servicio (DIFERENTE)
5. Guarda → Se crea segundo lead para el mismo cliente
```

---

## 📊 COMPARACIÓN ANTES vs DESPUÉS

| Aspecto | ANTES | DESPUÉS |
|---------|-------|---------|
| Búsqueda de cliente | Solo por DNI | Por teléfono + DNI |
| Múltiples solicitudes | ❌ No permitido | ✅ Permitido |
| Dirección de servicio | Una sola (personal) | Múltiples (por lead) |
| Tipo de instalación | No especificado | Casa/Negocio/Oficina |
| Validación duplicados | Por teléfono | Por teléfono + dirección |
| Experiencia usuario | Confusa | Clara y guiada |

---

## 🎨 MEJORAS VISUALES

### **Alertas Informativas**

```html
<!-- Alerta en búsqueda -->
<div class="alert alert-info">
    <i class="icon-info"></i> <strong>¿Cliente existente?</strong> 
    Busca primero por teléfono para evitar duplicados.
</div>

<!-- Alerta en dirección de servicio -->
<div class="alert alert-warning">
    <i class="icon-info"></i> <strong>Importante:</strong> 
    Un mismo cliente puede tener múltiples solicitudes en diferentes ubicaciones.
</div>
```

### **Resultado de Búsqueda - Cliente Encontrado**

```html
<div class="alert alert-success">
    <h5><i class="icon-check"></i> Cliente Encontrado</h5>
    <hr>
    <p><strong>Nombre:</strong> Juan Pérez García</p>
    <p><strong>Teléfono:</strong> 987654321</p>
    <p><strong>Solicitudes activas:</strong> 1</p>
    <small class="text-muted">
        Puedes crear una nueva solicitud de servicio en una ubicación diferente.
    </small>
</div>
```

### **Resultado de Búsqueda - Cliente No Encontrado**

```html
<div class="alert alert-warning">
    <h5><i class="icon-info"></i> Cliente No Encontrado</h5>
    <p>No se encontró ningún cliente con el teléfono <strong>987654321</strong></p>
    <p class="mb-0">
        <i class="icon-arrow-down"></i> Completa los datos del nuevo cliente a continuación.
    </p>
</div>
```

---

## 🔧 ARCHIVOS MODIFICADOS

### **1. Vista PHP**
- **Archivo:** `app/Views/leads/create.php`
- **Cambios:**
  - Agregada sección de búsqueda por teléfono
  - Agregados campos de dirección de servicio
  - Agregado campo tipo de solicitud
  - Agregado campo distrito de servicio
  - Reorganizada estructura del formulario

### **2. JavaScript**
- **Archivo:** `public/js/leads/buscar-cliente.js` (NUEVO)
- **Funciones:**
  - `buscarClientePorTelefono()`
  - `mostrarClienteEncontrado()`
  - `mostrarClienteNoEncontrado()`
  - `limpiarBusqueda()`

### **3. Controlador (Próximo paso)**
- **Archivo:** `app/Controllers/Leads.php`
- **Método a modificar:** `store()`
- **Cambios necesarios:**
  - Detectar si `idpersona` viene lleno (cliente existente)
  - No crear persona si ya existe
  - Validar que no haya lead duplicado para la misma dirección
  - Guardar campos de dirección de servicio

---

## ✅ VALIDACIONES IMPLEMENTADAS

### **En el Frontend (JavaScript)**
1. ✅ Teléfono debe tener 9 dígitos
2. ✅ Campos obligatorios marcados con *
3. ✅ Tipo de solicitud requerido
4. ✅ Dirección de servicio requerida
5. ✅ Distrito de servicio requerido

### **En el Backend (PHP - Próximo paso)**
1. ⏳ Validar que idpersona exista si viene lleno
2. ⏳ Validar que no haya lead duplicado (mismo cliente + misma dirección)
3. ⏳ Validar formato de teléfono (9 dígitos)
4. ⏳ Validar que tipo_solicitud sea válido
5. ⏳ Geocodificar dirección_servicio

---

## 📝 PRÓXIMOS PASOS

1. ✅ Vista modificada
2. ✅ JavaScript creado
3. ⏳ Modificar controlador `Leads.php` método `store()`
4. ⏳ Probar flujo completo
5. ⏳ Agregar validación de leads duplicados
6. ⏳ Implementar geocodificación automática

---

## 🎯 RESULTADO ESPERADO

Cuando un cliente (Juan Pérez) contacta dos veces:

**Primera vez:**
```
Teléfono: 987654321
Tipo: Casa
Dirección: Av. Benavides 123, Grocio Prado
→ Se crea: Persona + Lead #1
```

**Segunda vez:**
```
Buscar: 987654321 → Cliente encontrado
Tipo: Negocio
Dirección: Jr. Comercio 456, Chincha Alta
→ Se crea: Solo Lead #2 (persona ya existe)
```

**Resultado final:**
- 1 Persona (Juan Pérez)
- 2 Leads (Casa + Negocio)
- 2 Direcciones diferentes
- 2 Seguimientos independientes

---

**Fecha de Implementación:** Enero 2025  
**Estado:** ✅ Vista completada - Falta modificar controlador
