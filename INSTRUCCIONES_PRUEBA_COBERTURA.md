# 🧪 Instrucciones de Prueba - Verificación de Cobertura

## 📋 Preparación

1. **Limpiar caché del navegador**:
   - Presiona `Ctrl + Shift + Delete`
   - Selecciona "Imágenes y archivos en caché"
   - Click en "Borrar datos"

2. **Abrir DevTools**:
   - Presiona `F12`
   - Ve a la pestaña **Console**
   - Asegúrate de que no haya filtros activos

## 🚀 Pasos de Prueba

### 1. Abrir el Formulario
```
http://localhost/Delafiber/leads/create
```

### 2. Verificar Logs Iniciales
Deberías ver en consola:
```
✅ PersonaManager inicializado
✅ Wizard inicializado correctamente
✅ Event listeners configurados
🚀 Inicializando campos dinámicos de origen...
📋 Elementos encontrados: Object
✅ Event listener agregado correctamente
```

### 3. Completar Paso 1
- **Nombres**: Juan
- **Apellidos**: Pérez  
- **Teléfono**: 987654321
- Click en botón **"Siguiente"**

### 4. Verificar Logs del Paso 2
Deberías ver:
```
✅ Navegado al Paso 2
🔄 Inicializando verificación de cobertura en Paso 2...
✅ Verificación de cobertura inicializada en Paso 2
📍 Elemento distrito encontrado: <select...>
🔗 URL base: http://localhost/Delafiber
```

### 5. Seleccionar un Distrito
- Abre el dropdown de **"Distrito"**
- Selecciona cualquier distrito (ej: Chincha Alta)

### 6. Verificar Logs del Evento Change
Deberías ver INMEDIATAMENTE:
```
🔔 Evento change disparado en distrito
📌 Valor seleccionado: [número del distrito]
🌐 Verificando cobertura para distrito: [número]
🔗 URL completa: http://localhost/Delafiber/leads/verificar-cobertura?distrito=[número]
📥 Response status: 200
📡 Resultado cobertura completo: {success: true, tiene_cobertura: true, ...}
🔍 typeof Swal: function
✅ Mostrando alerta de cobertura...
🎨 mostrarAlertaCobertura llamada con: {...}
🔍 tiene_cobertura: true
✅ Mostrando alerta de COBERTURA POSITIVA
🚀 Ejecutando Swal.fire para cobertura positiva...
✅ Swal.fire ejecutado
```

### 7. Verificar Toast Notification
Deberías ver en la **esquina superior derecha**:
- Un toast verde con el mensaje: **"✅ ¡Tenemos cobertura!"**
- Nombre del distrito
- Número de zonas activas
- Nombres de las zonas

## 🐛 Diagnóstico de Problemas

### Problema 1: No aparece "🔔 Evento change disparado"
**Causa**: El evento no se está registrando correctamente

**Solución**:
1. Verifica que veas: `✅ Verificación de cobertura inicializada en Paso 2`
2. Si no aparece, el `setTimeout` en wizard.js no está funcionando
3. Prueba aumentar el delay a 500ms en `wizard.js` línea 215

### Problema 2: Aparece "⚠️ No hay distrito seleccionado"
**Causa**: El valor del select está vacío

**Solución**:
1. Verifica que el select tenga opciones
2. En consola ejecuta: `document.getElementById('iddistrito').value`
3. Debería retornar un número, no vacío

### Problema 3: Error HTTP 404
**Causa**: La ruta no está registrada o hay problema con BASE_URL

**Solución**:
1. Verifica la URL completa en el log `🔗 URL completa:`
2. Copia esa URL y pégala en el navegador
3. Deberías ver un JSON con la respuesta
4. Si da 404, verifica `app/Config/Routes.php` línea 42

### Problema 4: "❌ SweetAlert2 no está cargado!"
**Causa**: La librería SweetAlert2 no se cargó

**Solución**:
1. Verifica en `app/Views/leads/create.php` línea 327
2. Debería tener: `<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>`
3. En consola ejecuta: `typeof Swal`
4. Debería retornar: `"function"`

### Problema 5: Logs se detienen en "📥 Response status:"
**Causa**: Error al parsear el JSON de respuesta

**Solución**:
1. Abre la pestaña **Network** en DevTools
2. Busca la petición `verificar-cobertura`
3. Click en ella
4. Ve a la pestaña **Response**
5. Verifica que sea un JSON válido

## 🔍 Verificación Manual del Endpoint

Abre esta URL en tu navegador (cambia el número del distrito):
```
http://localhost/Delafiber/leads/verificar-cobertura?distrito=1
```

**Respuesta esperada**:
```json
{
  "success": true,
  "tiene_cobertura": true,
  "distrito_nombre": "Chincha Alta",
  "zonas_activas": 3,
  "zonas": [
    {
      "id_zona": 1,
      "nombre_zona": "Zona Centro Chincha",
      "campania_nombre": "Campaña Verano 2025"
    }
  ],
  "mensaje": "¡Excelente! Tenemos 3 zona(s) activa(s) en campañas"
}
```

## 📸 Captura de Pantalla

Si el problema persiste, toma captura de:
1. **Console completa** con todos los logs
2. **Network tab** mostrando la petición `verificar-cobertura`
3. **Response** de esa petición

## ✅ Checklist de Verificación

- [ ] Caché del navegador limpiado
- [ ] DevTools abierto en pestaña Console
- [ ] Formulario cargado correctamente
- [ ] Paso 1 completado y avanzado a Paso 2
- [ ] Logs de inicialización aparecen
- [ ] Distrito seleccionado del dropdown
- [ ] Logs del evento change aparecen
- [ ] Petición AJAX se ejecuta (Network tab)
- [ ] Respuesta JSON es válida
- [ ] SweetAlert está cargado (`typeof Swal === "function"`)
- [ ] Toast notification aparece en pantalla

## 🎯 Resultado Esperado

Al seleccionar un distrito, deberías ver:
1. **En consola**: Todos los logs mencionados arriba
2. **En pantalla**: Toast notification en esquina superior derecha
3. **Duración**: El toast se muestra por 5 segundos (con cobertura) o 4 segundos (sin cobertura)

---

**Nota**: Si después de seguir todos estos pasos el toast NO aparece pero SÍ ves todos los logs incluyendo "✅ Swal.fire ejecutado", entonces el problema está en los estilos CSS o en la configuración de SweetAlert2.

En ese caso, prueba ejecutar manualmente en consola:
```javascript
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Prueba',
    showConfirmButton: false,
    timer: 3000
});
```

Si esto funciona, el problema está en los parámetros específicos del toast de cobertura.
