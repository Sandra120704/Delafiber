# Delafiber

Requisitos
- Windows
- XAMPP (Apache + MySQL)
- PHP 8.x compatible con CodeIgniter 4
- Composer (https://getcomposer.org)
- Git (opcional pero recomendable)

Pasos de instalación (rápidos)

1) Clonar el repositorio (si no lo tienes)

   git clone <tu-repo-url> Delafiber
   cd Delafiber

2) Instalar dependencias PHP

   composer install

3) Configurar XAMPP
- Asegúrate de que Apache y MySQL están corriendo.
- Si usas XAMPP, coloca el proyecto en tu `htdocs` (ejemplo: `C:\xampp\htdocs\Delafiber`).

4) Crear base de datos
- Abre phpMyAdmin (http://localhost/phpmyadmin) o usa la línea de comandos.
- Crea una base de datos llamada `delafiber` (o el nombre que prefieras).

5) Importar esquema y datos
- Desde phpMyAdmin: importa `database/delafiber.sql` (si el archivo es muy grande, usa la línea de comandos).
- O con la línea de comandos (PowerShell):


6) Configurar variables de entorno
- Copia `env` a `.env` (archivo base del framework) y ajusta:
  - app.baseURL = 'http://localhost/Delafiber/'
  - Database.default.hostname = '127.0.0.1'
  - Database.default.database = 'delafiber'
  - Database.default.username = 'root'
  - Database.default.password = '' (o la que tengas)

7) Permisos (Windows)
- Asegúrate que la carpeta `writable/` es escribible por PHP (XAMPP normalmente lo permite).

8) Acceder a la aplicación
- Abre en el navegador: http://localhost/Delafiber/public
- Usuario admin de prueba: `admin@delafiber.com` / `password123` (si importaste seeds)

Verificación rápida antes de una exposición (clonar en otra máquina)

1) Clonar y preparar proyecto

```powershell
git clone <tu-repo-url> Delafiber
cd Delafiber
composer install 

```

4) Configurar VirtualHost (opcional pero recomendado)

Agrega en `C:\\xampp\\apache\\conf\\extra\\httpd-vhosts.conf` (ejemplo):

```apache
<VirtualHost *:80>
    ServerName delafiber.local
    DocumentRoot "C:\\xampp\\htdocs\\Delafiber\\public"
    <Directory "C:\\xampp\\htdocs\\Delafiber\\public">
        Require all granted
        AllowOverride All
    </Directory>
</VirtualHost>
```

Y en `C:\\Windows\\System32\\drivers\\etc\\hosts` añade:

```
127.0.0.1    delafiber.local
```

Reinicia Apache desde el Panel de Control de XAMPP.

