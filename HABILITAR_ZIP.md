# Habilitar extensión ZIP en Laragon

## Pasos:

1. **Abre Laragon**
2. **Click derecho en el icono de Laragon** (bandeja del sistema)
3. **PHP** → **php.ini**
4. **Busca la línea** (Ctrl+F):
   ```
   ;extension=zip
   ```
5. **Quita el punto y coma** para que quede:
   ```
   extension=zip
   ```
6. **Guarda el archivo** (Ctrl+S)
7. **Reinicia Laragon**:
   - Click derecho en Laragon
   - **Apache** → **Reload**
   - O simplemente **Stop All** → **Start All**

## Verificar que funcionó:

Ejecuta en PowerShell:
```powershell
php -m | findstr zip
```

Debería mostrar:
```
zip
```

## Ahora instala phpspreadsheet:

```powershell
composer require phpoffice/phpspreadsheet
```

---

## Alternativa rápida (si no encuentras la línea):

Agrega esta línea al final de la sección `[PHP]` en `php.ini`:
```
extension=zip
```
