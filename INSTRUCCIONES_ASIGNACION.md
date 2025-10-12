# 📋 Sistema de Asignación de Leads - Delafiber CRM

## 🎯 Flujo de Trabajo Implementado

### **Escenario Real:**

**Turno Mañana (9:00 AM):**
- **Juan** (vendedor turno mañana) recibe llamada de un cliente
- Cliente dice: *"Estoy interesado, pero llámame por la tarde porque consultaré con mi esposa"*
- **Juan** registra el lead y lo asigna a **María** (vendedor turno tarde)

**Resultado:**
- ✅ Lead guardado con `idusuario_registro = Juan` (quien lo creó)
- ✅ Lead guardado con `idusuario = María` (quien dará seguimiento)
- ✅ María ve el lead en su lista cuando inicia sesión por la tarde
- ✅ María recibe notificación: "Juan te ha asignado un nuevo lead"
- ✅ El sistema registra que Juan fue quien captó el lead (para métricas)

---

## 🗄️ PASO 1: Actualizar Base de Datos

### **Opción A: Ejecutar migración (RECOMENDADO si ya tienes datos)**

```bash
mysql -u root -p delafiber < database/migracion_campo_usuario_registro.sql
```

Este script:
- ✅ Agrega el campo `idusuario_registro`
- ✅ Copia los datos existentes de `idusuario` a `idusuario_registro`
- ✅ No pierde ningún dato
- ✅ Agrega índices y foreign keys

### **Opción B: Recrear base de datos completa (si es desarrollo limpio)**

```bash
mysql -u root -p delafiber < database/delafiber.sql
```

---

## 📊 Estructura de Campos en `leads`

| Campo | Descripción | ¿Cambia? | Ejemplo |
|-------|-------------|----------|---------|
| `idusuario_registro` | Usuario que **REGISTRÓ** el lead | ❌ NO | Juan (turno mañana) |
| `idusuario` | Usuario **ASIGNADO** para seguimiento | ✅ SÍ | María (turno tarde) |

### **Ejemplo de Registro:**

```sql
INSERT INTO leads (
    idpersona, 
    idusuario_registro,  -- Juan (quien registró)
    idusuario,           -- María (quien dará seguimiento)
    idetapa, 
    idorigen
) VALUES (
    123,
    5,  -- ID de Juan
    8,  -- ID de María
    1,
    2
);
```

---

## 🔄 Flujos de Asignación

### **Caso 1: Mismo Usuario (Yo daré seguimiento)**

```
Juan registra lead → Selecciona "Yo mismo"
Resultado:
- idusuario_registro = Juan
- idusuario = Juan
- Juan ve el lead en su lista
```

### **Caso 2: Asignación por Turno**

```
Juan (turno mañana) registra lead → Cliente pide llamada tarde
Juan selecciona "María - Vendedor (Turno: tarde)"
Resultado:
- idusuario_registro = Juan (métricas: Juan captó el lead)
- idusuario = María (María ve el lead y debe llamar)
- María recibe notificación
```

### **Caso 3: Supervisor Asigna**

```
Carlos (supervisor) registra lead → Asigna a Juan
Resultado:
- idusuario_registro = Carlos
- idusuario = Juan
- Juan ve el lead
- Juan recibe notificación
```

### **Caso 4: Reasignación (Futuro)**

```
María tiene lead pero está ocupada
Supervisor reasigna a Pedro
Resultado:
- idusuario_registro = Juan (NO cambia, Juan lo captó)
- idusuario = Pedro (CAMBIA, ahora Pedro da seguimiento)
- Se registra en historial_leads
```

---

## 👀 Quién Ve Qué

### **Vendedor (Nivel 3):**
```sql
-- Solo ve leads donde ÉL es el asignado
SELECT * FROM leads WHERE idusuario = {id_vendedor}
```

**Ejemplo:** María solo ve leads donde `idusuario = María`

### **Supervisor (Nivel 2):**
```sql
-- Ve TODOS los leads
SELECT * FROM leads
```

**Ejemplo:** Carlos ve todos los leads del sistema

### **Administrador (Nivel 1):**
```sql
-- Ve TODOS los leads
SELECT * FROM leads
```

---

## 📝 Cambios en el Formulario

### **Antes:**
```
[ Datos del Cliente ]
[ Guardar ] → Lead asignado automáticamente a quien lo crea
```

### **Ahora:**
```
[ Datos del Cliente ]

┌─────────────────────────────────────────┐
│ ℹ️ Registrado por: Juan (Vendedor)     │
│    Este dato quedará guardado automát. │
└─────────────────────────────────────────┘

Asignar Seguimiento a: *
┌─────────────────────────────────────────┐
│ ○ Yo mismo (daré seguimiento)          │
│ ○ María - Vendedor (Turno: tarde)      │
│ ○ Pedro - Vendedor (Turno: mañana)     │
│ ○ Carlos - Supervisor (Turno: completo)│
└─────────────────────────────────────────┘
💡 Si el cliente dice "llámame por la tarde",
   asigna a un vendedor del turno tarde

[ Guardar ]
```

---

## 🔔 Sistema de Notificaciones

### **Cuándo se crea notificación:**

```php
if (usuario_asignado != usuario_que_registra) {
    // Crear notificación
    "Juan te ha asignado un nuevo lead: Carlos Pérez"
}
```

### **Tabla `notificaciones`:**

| Campo | Ejemplo |
|-------|---------|
| `idusuario` | 8 (María) |
| `tipo` | lead_asignado |
| `titulo` | Nuevo lead asignado |
| `mensaje` | Juan te ha asignado un nuevo lead: Carlos Pérez |
| `url` | /leads/view/123 |
| `leida` | 0 |

---

## 📊 Reportes y Métricas

### **Leads Captados por Usuario:**
```sql
SELECT 
    u.nombre,
    COUNT(*) as leads_captados
FROM leads l
JOIN usuarios u ON l.idusuario_registro = u.idusuario
GROUP BY u.idusuario
ORDER BY leads_captados DESC;
```

### **Leads Asignados por Usuario:**
```sql
SELECT 
    u.nombre,
    COUNT(*) as leads_asignados
FROM leads l
JOIN usuarios u ON l.idusuario = u.idusuario
WHERE l.estado = 'Activo'
GROUP BY u.idusuario;
```

### **Rendimiento por Turno:**
```sql
SELECT 
    u.turno,
    COUNT(*) as leads_captados,
    SUM(CASE WHEN l.estado = 'Convertido' THEN 1 ELSE 0 END) as conversiones
FROM leads l
JOIN usuarios u ON l.idusuario_registro = u.idusuario
GROUP BY u.turno;
```

---

## 🧪 Pruebas

### **Test 1: Asignación Básica**
```
1. Login: juan@delafiber.com (password123)
2. Ir a /leads/create
3. Crear lead: "Cliente Prueba"
4. Asignar a: "María - Vendedor (Turno: tarde)"
5. Guardar
6. Logout
7. Login: maria@delafiber.com
8. Ir a /leads
9. ✅ Verificar que aparece "Cliente Prueba"
```

### **Test 2: Verificar Registro**
```
1. Como Admin, ir a base de datos
2. SELECT * FROM leads WHERE idlead = [último_id]
3. ✅ Verificar:
   - idusuario_registro = ID de Juan
   - idusuario = ID de María
```

### **Test 3: Notificación**
```
1. Después del Test 1
2. Login como María
3. Ir a /notificaciones (o dashboard)
4. ✅ Verificar: "Juan te ha asignado un nuevo lead"
```

### **Test 4: Permisos**
```
1. Login: pedro@delafiber.com (otro vendedor)
2. Ir a /leads
3. ✅ Verificar que NO ve el lead asignado a María
```

---

## 🚀 Funcionalidades Futuras

### **1. Reasignación de Leads**
Agregar botón en vista de detalle:
```
[ Reasignar Lead ]
→ Modal con lista de vendedores
→ Actualiza solo `idusuario`
→ Mantiene `idusuario_registro` original
→ Registra en historial
```

### **2. Filtros Avanzados**
```
- "Leads que registré yo"
- "Leads asignados a mí"
- "Leads por turno"
```

### **3. Dashboard Mejorado**
```
- Leads captados hoy: 15
- Leads asignados a mí: 8
- Leads que registré: 12
```

---

## ✅ Checklist de Implementación

- [x] Campo `idusuario_registro` agregado en BD
- [x] Migración SQL creada
- [x] Model actualizado con nuevo campo
- [x] Controller guarda ambos campos
- [x] Vista muestra quién registra
- [x] Vista permite asignar a otro usuario
- [x] Notificaciones implementadas
- [x] Turnos visibles en selector
- [ ] Ejecutar migración en BD
- [ ] Probar flujo completo
- [ ] Verificar notificaciones

---

## 📞 Comandos Útiles

### **Ver estructura de tabla:**
```sql
DESCRIBE leads;
```

### **Ver últimos leads creados:**
```sql
SELECT 
    l.idlead,
    CONCAT(p.nombres, ' ', p.apellidos) as cliente,
    u_reg.nombre as registrado_por,
    u_asig.nombre as asignado_a
FROM leads l
JOIN personas p ON l.idpersona = p.idpersona
LEFT JOIN usuarios u_reg ON l.idusuario_registro = u_reg.idusuario
LEFT JOIN usuarios u_asig ON l.idusuario = u_asig.idusuario
ORDER BY l.created_at DESC
LIMIT 10;
```

### **Limpiar caché de CodeIgniter:**
```bash
php spark cache:clear
```

---

**Fecha:** 12 de Octubre, 2025  
**Versión:** 2.0  
**Sistema:** Delafiber CRM
