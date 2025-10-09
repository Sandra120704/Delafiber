# 🚀 Guía de Instalación - Delafiber CRM

## Requisitos Previos
- PHP 8.2 o superior
- MySQL 5.7 o superior
- Composer
- XAMPP o servidor web similar

## Pasos de Instalación

### 1. Clonar el Repositorio
```bash
git clone <url-del-repositorio>
cd Delafiber
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configurar Base de Datos

#### Crear la base de datos:
```sql
mysql -u root -p
CREATE DATABASE delafiber;
exit;
```

#### Importar el esquema:
```bash
mysql -u root -p delafiber < database/delafiber.sql
```

### 4. Configurar Variables de Entorno

Copiar el archivo de configuración:
```bash
copy env .env
```

Editar `.env` y configurar:
```ini
database.default.hostname = localhost
database.default.database = delafiber
database.default.username = root
database.default.password = tu_password
database.default.DBDriver = MySQLi
```

### 5. Configurar Permisos (Linux/Mac)
```bash
chmod -R 777 writable/
```

### 6. Iniciar el Servidor

#### Opción 1: Con XAMPP
- Colocar el proyecto en `C:\xampp\htdocs\Delafiber`
- Iniciar Apache y MySQL desde XAMPP Control Panel
- Acceder a: `http://localhost/Delafiber`

#### Opción 2: Servidor integrado de PHP
```bash
php spark serve
```
Acceder a: `http://localhost:8080`

## Credenciales por Defecto

**Usuario:** admin@delafiber.com  
**Contraseña:** password123

## Verificación

Acceder a la aplicación y verificar que:
- ✅ Login funciona correctamente
- ✅ Dashboard carga sin errores
- ✅ Módulos de Leads, Campañas y Mapa están disponibles

## Solución de Problemas Comunes

### Error de conexión a base de datos
- Verificar credenciales en `.env`
- Confirmar que MySQL está corriendo
- Verificar que la base de datos existe

### Error 500
- Verificar permisos en carpeta `writable/`
- Revisar logs en `writable/logs/`

### Composer no encontrado
```bash
# Instalar Composer globalmente
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

## Soporte
Para más información, revisar la documentación en `/docs` o contactar al equipo de desarrollo.
