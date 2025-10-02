# Delafiber CRM

## Requisitos

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Servidor web (Laragon, XAMPP, WAMP, etc.)

## Instalación

1. **Clona el repositorio**
   ```sh
   git clone https://github.com/tuusuario/Delafiber.git
   cd Delafiber
   ```

2. **Instala las dependencias de Composer**
   ```sh
   composer install
   ```

3. **Configura el archivo de entorno**
   - Copia `.env.example` a `.env` si existe, o crea uno nuevo.
   - Edita `.env` con tus datos de base de datos y configuración local.

4. **Crea la base de datos**
   - Crea una base de datos en MySQL/MariaDB.
   - Importa el archivo de estructura si existe (`database.sql`).

5. **Permisos de carpetas (Linux/Mac)**
   ```sh
   chmod -R 775 writable
   ```
   *(En Windows no es necesario)*

6. **Inicia el servidor**
   ```sh
   php spark serve
   ```
   O usa Laragon/XAMPP y accede por el navegador.

7. **Accede a la aplicación**
   - Abre `http://delafiber.test` o la URL configurada.

## Notas

- No subas archivos sensibles ni la carpeta `writable/` completa al repositorio.
- Si usas librerías externas (ejemplo: PhpSpreadsheet), ejecuta `composer install` después de clonar.
- Si tienes archivos de assets o uploads necesarios, súbelos manualmente si no están en el repositorio.

## Actualización

Para actualizar tu copia local:
```sh
git pull origin master
composer install
```

## Soporte

Si tienes problemas, revisa los logs en `writable/logs/` y verifica la configuración en `.env`.

## Backup (Respaldo)

Para hacer un respaldo de tu proyecto:

1. **Respalda la base de datos**
   - Usando MySQL/MariaDB:
     ```sh
     mysqldump -u usuario -p nombre_base_de_datos > respaldo.sql
     ```
   - Guarda el archivo `respaldo.sql` en un lugar seguro.

2. **Respalda archivos importantes**
   - Copia las carpetas y archivos personalizados que no están en el repositorio, por ejemplo:
     - `writable/uploads/`
     - `public/uploads/`
     - Archivos de configuración local (`.env`)

3. **Respalda el código fuente**
   - Si usas Git, tu código ya está respaldado en GitHub.

**Recomendación:**  
Guarda los respaldos en una carpeta segura y realiza backups periódicos.

## Restaurar backup

1. **Restaura la base de datos**
   ```sh
   mysql -u usuario -p nombre_base_de_datos < respaldo.sql
   ```

2. **Restaura archivos importantes**
   - Copia los archivos y carpetas respaldados a su ubicación original.
