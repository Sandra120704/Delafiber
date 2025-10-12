# 🔍 Diagnóstico: Verificación de Cobertura No Aparece

## 🚨 Problema Reportado
La verificación de cobertura **antes aparecía** al seleccionar un distrito, pero **ahora ya no aparece**.

## 🛠️ Herramienta de Diagnóstico Instalada

He agregado un script de prueba que te ayudará a identificar exactamente dónde está el problema.

## 📋 Pasos para Diagnosticar

### 1. Abrir el Formulario
```
http://localhost/Delafiber/leads/create
```

### 2. Abrir la Consola del Navegador
- Presiona **F12**
- Ve a la pestaña **Console**

### 3. Ejecutar Tests Automáticos
En la consola, escribe:
```javascript
testCobertura.runAll()
```

Esto ejecutará una serie de tests y te mostrará:
- ✅ Si SweetAlert está cargado
- ✅ Si el elemento distrito existe
- ✅ Si PersonaManager está disponible
- ✅ Si el endpoint responde correctamente
- ✅ Un toast de prueba

### 4. Interpretar Resultados

#### Escenario A: Todo ✅ pero el toast no aparece al seleccionar distrito

**Diagnóstico**: El evento `change` no se está registrando correctamente.

**Solución**: Ejecuta en consola:
```javascript
testCobertura.testCambioDistrito()
```

Esto simulará la selección de un distrito. Si aparece el toast, el problema es que el evento no se dispara manualmente.

#### Escenario B: SweetAlert ❌

**Diagnóstico**: La librería no se cargó.

**Solución**: 
1. Verifica tu conexión a internet
2. Revisa en la pestaña **Network** si `sweetalert2` se descargó
3. Si falla, descarga la librería localmente

#### Escenario C: Elemento distrito ❌

**Diagnóstico**: Estás en el Paso 1, no en el Paso 2.

**Solución**: 
1. Completa el Paso 1
2. Click en "Siguiente"
3. Ejecuta los tests nuevamente

#### Escenario D: PersonaManager ❌

**Diagnóstico**: El script `create.js` no se cargó o tiene errores.

**Solución**:
1. Revisa la pestaña **Console** en busca de errores en rojo
2. Verifica que `create.js` se descargue en la pestaña **Network**
3. Limpia el caché del navegador (Ctrl + Shift + Delete)

#### Escenario E: Endpoint retorna error

**Diagnóstico**: Problema en el servidor.

**Solución**:
1. Verifica que XAMPP esté corriendo
2. Abre directamente: `http://localhost/Delafiber/leads/verificar-cobertura?distrito=1`
3. Deberías ver un JSON con la respuesta

## 🧪 Tests Individuales Disponibles

### Test 1: Verificar SweetAlert
```javascript
testCobertura.testSweetAlert()
```
**Resultado esperado**: Aparece un toast que dice "Test exitoso"

### Test 2: Verificar Elemento Distrito
```javascript
testCobertura.testDistritoElement()
```
**Resultado esperado**: Muestra información del elemento en consola

### Test 3: Verificar PersonaManager
```javascript
testCobertura.testPersonaManager()
```
**Resultado esperado**: Muestra si está inicializado

### Test 4: Simular Cambio de Distrito
```javascript
testCobertura.testCambioDistrito()
```
**Resultado esperado**: Selecciona un distrito y dispara el evento

### Test 5: Probar Endpoint Directamente
```javascript
testCobertura.testEndpoint(1)
```
**Resultado esperado**: Muestra la respuesta JSON del servidor

### Test 6: Mostrar Toast Manualmente
```javascript
// Con cobertura
testCobertura.testMostrarToast(true)

// Sin cobertura
testCobertura.testMostrarToast(false)
```
**Resultado esperado**: Aparece el toast en la esquina superior derecha

## 🔍 Verificación Manual Paso a Paso

Si los tests automáticos no ayudan, prueba esto:

### 1. Verificar que estás en el Paso 2
```javascript
document.getElementById('paso2').style.display
// Debería retornar: "block" (no "none")
```

### 2. Verificar que el evento está registrado
```javascript
// Obtener el elemento
const distrito = document.getElementById('iddistrito');

// Ver sus event listeners (Chrome DevTools)
getEventListeners(distrito)
// Debería mostrar un listener de tipo "change"
```

### 3. Disparar evento manualmente
```javascript
const distrito = document.getElementById('iddistrito');
distrito.value = '1'; // O cualquier ID válido
distrito.dispatchEvent(new Event('change'));
```

### 4. Verificar logs en consola
Después de seleccionar un distrito, deberías ver:
```
🔔 Evento change disparado en distrito
📌 Valor seleccionado: 1
🌐 Verificando cobertura para distrito: 1
🔗 URL completa: http://localhost/Delafiber/leads/verificar-cobertura?distrito=1
📥 Response status: 200
📡 Resultado cobertura completo: {...}
✅ Mostrando alerta de cobertura...
🚀 Ejecutando Swal.fire para cobertura positiva...
✅ Swal.fire ejecutado
```

## 🐛 Problemas Comunes y Soluciones

### Problema 1: "testCobertura is not defined"
**Causa**: El script de prueba no se cargó.

**Solución**:
1. Refresca la página (Ctrl + F5)
2. Verifica en Network que `test-cobertura.js` se descargó
3. Si no aparece, verifica que agregaste la línea en `create.php`

### Problema 2: Los logs aparecen pero el toast no
**Causa**: Problema con los estilos CSS o configuración de SweetAlert.

**Solución**:
```javascript
// Probar un toast básico sin estilos personalizados
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Prueba básica',
    showConfirmButton: false,
    timer: 3000
});
```

Si este funciona pero el de cobertura no, el problema está en los estilos personalizados.

### Problema 3: "coberturaInicializada is false"
**Causa**: La función `initVerificarCobertura()` no se ejecutó.

**Solución**:
```javascript
// Ejecutar manualmente
window.personaManager.initVerificarCobertura()
```

Luego intenta seleccionar un distrito nuevamente.

### Problema 4: Error 404 en el endpoint
**Causa**: La ruta no está registrada o BASE_URL es incorrecta.

**Solución**:
1. Verifica BASE_URL en consola: `console.log(BASE_URL)`
2. Debería ser: `http://localhost/Delafiber`
3. Verifica que la ruta existe en `app/Config/Routes.php`

## 📊 Reporte de Diagnóstico

Después de ejecutar los tests, copia y pega este reporte con los resultados:

```
=== REPORTE DE DIAGNÓSTICO ===
Fecha: [fecha]
Navegador: [Chrome/Firefox/Edge]

Test 1 - SweetAlert: [✅/❌]
Test 2 - Elemento Distrito: [✅/❌]
Test 3 - PersonaManager: [✅/❌]
Test 4 - Cambio Distrito: [✅/❌]
Test 5 - Endpoint: [✅/❌]
Test 6 - Toast Manual: [✅/❌]

Logs en consola al seleccionar distrito:
[Pegar logs aquí]

Errores (si hay):
[Pegar errores aquí]

Observaciones adicionales:
[Describe qué ves o no ves]
```

## ✅ Siguiente Paso

Una vez que identifiques cuál test falla, comparte el reporte y podré darte la solución específica.

---

**Nota**: Este script de prueba es temporal. Una vez solucionado el problema, puedes remover la línea:
```html
<script src="<?= base_url('js/leads/test-cobertura.js') ?>"></script>
```

Del archivo `app/Views/leads/create.php` (línea 333).
