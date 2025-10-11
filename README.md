# 🌐 Delafiber CRM

Sistema de gestión de relaciones con clientes (CRM) diseñado específicamente para empresas de telecomunicaciones y fibra óptica. Desarrollado con CodeIgniter 4 y optimizado para equipos de ventas que trabajan en campo.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.1+-purple.svg)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## 📋 Tabla de Contenidos

- [Características Principales](#-características-principales)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Uso](#-uso)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Módulos](#-módulos)
- [API Endpoints](#-api-endpoints)
- [Contribuir](#-contribuir)
- [Licencia](#-licencia)

## ✨ Características Principales

### 🎯 Gestión de Leads
- **Pipeline Visual**: Seguimiento de leads a través de etapas (Captación → Interés → Cotización → Negociación → Cierre)
- **Múltiples Orígenes**: Facebook, WhatsApp, Referidos, Publicidad, Web, Llamadas
- **Asignación Inteligente**: Distribución automática por zonas y turnos
- **Historial Completo**: Registro de todos los cambios de etapa

### 📍 Geolocalización y Campañas
- **Zonas de Campaña**: Definición de polígonos en mapa para campañas territoriales
- **Múltiples Direcciones**: Una persona puede solicitar servicios en diferentes ubicaciones
- **Visualización en Mapa**: Integración con mapas para ver leads por zona
- **Asignación por Territorio**: Vendedores asignados a zonas específicas

### 📝 Cotizaciones
- **Generación Automática**: Cálculo de precios con descuentos e instalación
- **Múltiples Servicios**: Internet (50/100/200 Mbps), Cable TV, Streaming
- **Seguimiento de Estado**: Borrador, Enviada, Aceptada, Rechazada
- **Historial de Precios**: Registro de todas las cotizaciones por lead

### ✅ Tareas y Seguimientos
- **Calendario Integrado**: Visualización de tareas por día/semana/mes
- **Recordatorios**: Notificaciones automáticas de tareas pendientes
- **Modalidades**: Llamada, WhatsApp, Email, Visita, Messenger
- **Prioridades**: Baja, Media, Alta, Urgente

### 👥 Gestión de Usuarios
- **Roles y Permisos**: Administrador, Supervisor, Vendedor
- **Turnos**: Mañana, Tarde, Completo
- **Auditoría**: Registro de todas las acciones importantes
- **Dashboard Personalizado**: Métricas según el rol del usuario

### 📊 Reportes y Análisis
- **Métricas en Tiempo Real**: Leads activos, conversiones, tareas pendientes
- **Actividad Reciente**: Últimas interacciones con clientes
- **Leads Calientes**: Identificación automática de oportunidades prioritarias
- **Estadísticas por Vendedor**: Rendimiento individual y de equipo

## 💻 Requisitos del Sistema

### Requisitos Mínimos
- **PHP**: 8.1 o superior
- **MySQL**: 5.7 o superior (recomendado 8.0+)
- **Composer**: 2.0 o superior
- **Servidor Web**: Apache 2.4+ o Nginx 1.18+

### Extensiones PHP Requeridas
```
php-json
php-mbstring
php-mysqlnd
php-xml
php-intl
php-curl
```

### Herramientas Recomendadas
- **XAMPP** 8.1+ (para desarrollo local)
- **Git** (para control de versiones)
- **Node.js** (opcional, para gestión de assets frontend)

## 🚀 Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/SandraGeraldine/Delafiber.git
cd Delafiber
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configurar Base de Datos

#### Crear la base de datos:
```bash
mysql -u root -p
```
```sql
CREATE DATABASE delafiber CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### Importar el esquema:
```bash
mysql -u root -p delafiber < database/delafiber.sql
```

### 4. Configurar Variables de Entorno

Copiar el archivo de ejemplo:
```bash
cp env .env
```

Editar `.env` con tus credenciales:
```ini
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = delafiber
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'http://delafiber.test/'
app.indexPage = ''
```

### 5. Configurar Permisos (Linux/Mac)
```bash
chmod -R 777 writable/
```

### 6. Iniciar el Servidor

#### Opción A: Con XAMPP
1. Colocar el proyecto en `C:\xampp\htdocs\Delafiber`
2. Iniciar Apache y MySQL desde XAMPP Control Panel
3. Acceder a: `http://localhost/Delafiber` o `http://delafiber.test`

#### Opción B: Servidor integrado de PHP
```bash
php spark serve
```
Acceder a: `http://localhost:8080`

## 🔐 Credenciales por Defecto

Después de la instalación, puedes acceder con:

| Rol | Email | Contraseña |
|-----|-------|------------|
| **Administrador** | admin@delafiber.com | password123 |
| **Supervisor** | carlos@delafiber.com | password123 |
| **Vendedor** | maria@delafiber.com | password123 |

> ⚠️ **IMPORTANTE**: Cambia estas contraseñas inmediatamente en producción.

## ⚙️ Configuración

### Configuración de Mapas (Opcional)

Si deseas usar la funcionalidad de mapas, necesitas una API key de Google Maps:

1. Obtén una API key en [Google Cloud Console](https://console.cloud.google.com/)
2. Edita `app/Views/mapa/index.php` y reemplaza `YOUR_API_KEY`

### Configuración de Email (Opcional)

Para enviar cotizaciones por email, configura en `.env`:
```ini
email.fromEmail = noreply@delafiber.com
email.fromName = Delafiber CRM
email.SMTPHost = smtp.gmail.com
email.SMTPUser = tu-email@gmail.com
email.SMTPPass = tu-password
email.SMTPPort = 587
```

## 📖 Uso

### Flujo de Trabajo Típico

#### 1. **Crear un Nuevo Lead**
```
Dashboard → Nuevo Lead → Completar formulario
```
- Ingresar datos del cliente (nombre, teléfono, DNI)
- Seleccionar origen del lead (Facebook, WhatsApp, etc.)
- Agregar dirección de instalación del servicio
- Asignar a campaña (opcional)

#### 2. **Realizar Seguimiento**
```
Leads → Ver Lead → Agregar Seguimiento
```
- Registrar llamadas, mensajes, visitas
- Actualizar etapa del lead
- Programar próxima tarea

#### 3. **Crear Cotización**
```
Cotizaciones → Nueva Cotización → Seleccionar Lead
```
- Elegir servicio (Internet, Cable TV, etc.)
- Aplicar descuentos si corresponde
- Generar PDF o enviar por WhatsApp

#### 4. **Gestionar Tareas**
```
Tareas → Nueva Tarea
```
- Asignar a lead específico
- Definir fecha y hora
- Establecer prioridad
- Marcar como completada

### Atajos de Teclado

| Atajo | Acción |
|-------|--------|
| `Ctrl + N` | Nuevo Lead |
| `Ctrl + K` | Búsqueda rápida |
| `Ctrl + D` | Ir al Dashboard |

## 📁 Estructura del Proyecto

```
Delafiber/
├── app/
│   ├── Controllers/        # Controladores MVC
│   │   ├── Auth.php       # Autenticación
│   │   ├── Leads.php      # Gestión de leads
│   │   ├── Cotizaciones.php
│   │   ├── Tareas.php
│   │   └── ...
│   ├── Models/            # Modelos de datos
│   │   ├── LeadModel.php
│   │   ├── PersonaModel.php
│   │   └── ...
│   ├── Views/             # Vistas (HTML/PHP)
│   │   ├── Layouts/       # Plantillas base
│   │   ├── leads/
│   │   ├── cotizaciones/
│   │   └── ...
│   └── Filters/           # Filtros (Auth, Permisos)
├── public/
│   ├── css/               # Estilos
│   ├── js/                # JavaScript
│   │   ├── leads/
│   │   ├── cotizaciones/
│   │   └── config/
│   └── assets/            # Librerías externas
├── database/
│   └── delafiber.sql      # Esquema de BD
├── writable/              # Logs y cache
└── vendor/                # Dependencias Composer
```

## 🧩 Módulos

### Dashboard
Panel principal con resumen del día:
- Tareas pendientes y vencidas
- Leads calientes que requieren atención
- Actividad reciente
- Acciones rápidas

### Leads
Gestión completa del ciclo de vida del cliente:
- Creación y edición de leads
- Pipeline visual por etapas
- Filtros avanzados (etapa, origen, campaña)
- Historial de cambios

### Personas
Directorio de contactos:
- Información personal y de contacto
- Múltiples direcciones por persona
- Conversión a lead
- Búsqueda y filtros

### Cotizaciones
Sistema de cotizaciones:
- Catálogo de servicios
- Cálculo automático con descuentos
- Generación de PDF
- Seguimiento de estado

### Tareas
Gestión de actividades:
- Calendario integrado
- Recordatorios automáticos
- Asignación por lead
- Filtros por estado y prioridad

### Campañas
Organización territorial:
- Definición de zonas en mapa
- Asignación de vendedores
- Metas y seguimiento
- Estadísticas por zona

### Reportes
Análisis y métricas:
- Conversiones por período
- Rendimiento por vendedor
- Efectividad por origen
- Exportación a Excel

### Usuarios
Administración del equipo:
- Gestión de roles y permisos
- Asignación de zonas
- Control de turnos
- Auditoría de acciones

## 🔌 API Endpoints

### Autenticación
```
POST   /auth/login          # Iniciar sesión
POST   /auth/logout         # Cerrar sesión
```

### Leads
```
GET    /leads               # Listar leads
GET    /leads/view/{id}     # Ver detalle
POST   /leads/store         # Crear lead
PUT    /leads/update/{id}   # Actualizar lead
DELETE /leads/delete/{id}   # Eliminar lead
POST   /leads/cambiarEtapa  # Cambiar etapa
```

### Cotizaciones
```
GET    /cotizaciones                    # Listar cotizaciones
POST   /cotizaciones/store              # Crear cotización
GET    /cotizaciones/generarPDF/{id}    # Generar PDF
POST   /cotizaciones/enviar/{id}        # Enviar cotización
```

### Tareas
```
GET    /tareas                      # Listar tareas
POST   /tareas/store                # Crear tarea
PUT    /tareas/completar/{id}       # Completar tarea
GET    /tareas/calendario           # Vista calendario
```

> 📝 **Nota**: Todos los endpoints requieren autenticación. Incluye el token CSRF en las peticiones POST/PUT/DELETE.

## 🛠️ Desarrollo

### Ejecutar en Modo Desarrollo
```bash
php spark serve --host=localhost --port=8080
```

### Ver Logs
```bash
tail -f writable/logs/log-*.log
```

### Limpiar Cache
```bash
php spark cache:clear
```

### Ejecutar Migraciones (si las agregas)
```bash
php spark migrate
```

## 🧪 Testing

Aunque el proyecto tiene configurado PHPUnit, actualmente no hay tests implementados. Para agregar tests:

```bash
# Ejecutar tests
./vendor/bin/phpunit

# Ejecutar con cobertura
./vendor/bin/phpunit --coverage-html coverage/
```

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add: nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Convenciones de Código
- Seguir PSR-12 para PHP
- Usar camelCase para JavaScript
- Comentar funciones complejas
- Mantener consistencia con el código existente

## 📝 Changelog

### [1.0.0] - 2025-10-11
#### Agregado
- Sistema completo de gestión de leads
- Módulo de cotizaciones
- Gestión de tareas y calendario
- Sistema de campañas con mapas
- Dashboard con métricas
- Roles y permisos
- Auditoría de cambios

## 🐛 Problemas Conocidos

- [ ] La búsqueda global en el header aún no está implementada
- [ ] Falta integración con WhatsApp Business API
- [ ] Los reportes avanzados están en desarrollo
- [ ] Notificaciones push pendientes de implementar

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👥 Autores

- **Sandra Geraldine** - *Desarrollo inicial* - [SandraGeraldine](https://github.com/SandraGeraldine)

## 🙏 Agradecimientos

- CodeIgniter 4 Framework
- Bootstrap 5
- DataTables
- Select2
- SweetAlert2
- Font Awesome

## 📞 Soporte

Si tienes preguntas o necesitas ayuda:

- 📧 Email: soporte@delafiber.com
- 🐛 Issues: [GitHub Issues](https://github.com/SandraGeraldine/Delafiber/issues)
- 📖 Documentación: [Wiki del Proyecto](https://github.com/SandraGeraldine/Delafiber/wiki)

---

**Hecho con ❤️ para equipos de ventas que trabajan en campo**
