#Delafiber CRM

Sistema de gestión de relaciones con clientes (CRM) Desarrollado con CodeIgniter 4.


## Características Principales

###  Gestión de Leads
- **Pipeline Visual**: Seguimiento de leads a través de etapas (Captación → Interés → Cotización → Negociación → Cierre)
- **Múltiples Orígenes**: Facebook, WhatsApp, Referidos, Publicidad, Web, Llamadas
- **Asignación Inteligente**: Distribución automática por zonas y turnos
- **Historial Completo**: Registro de todos los cambios de etapa

### Geolocalización y Campañas
- **Zonas de Campaña**: Definición de polígonos en mapa para campañas territoriales
- **Múltiples Direcciones**: Una persona puede solicitar servicios en diferentes ubicaciones
- **Visualización en Mapa**: Integración con mapas para ver leads por zona
- **Asignación por Territorio**: Vendedores asignados a zonas específicas

### Cotizaciones
- **Generación Automática**: Cálculo de precios con descuentos e instalación
- **Múltiples Servicios**: Internet (50/100/200 Mbps), Cable TV, Streaming
- **Seguimiento de Estado**: Borrador, Enviada, Aceptada, Rechazada
- **Historial de Precios**: Registro de todas las cotizaciones por lead

### Tareas y Seguimientos
- **Calendario Integrado**: Visualización de tareas por día/semana/mes
- **Recordatorios**: Notificaciones automáticas de tareas pendientes
- **Modalidades**: Llamada, WhatsApp, Email, Visita, Messenger
- **Prioridades**: Baja, Media, Alta, Urgente

### Gestión de Usuarios
- **Roles y Permisos**: Administrador, Supervisor, Vendedor
- **Turnos**: Mañana, Tarde, Completo
- **Auditoría**: Registro de todas las acciones importantes
- **Dashboard Personalizado**: Métricas según el rol del usuario

###  Reportes y Análisis
- **Métricas en Tiempo Real**: Leads activos, conversiones, tareas pendientes
- **Actividad Reciente**: Últimas interacciones con clientes
- **Leads Calientes**: Identificación automática de oportunidades prioritarias
- **Estadísticas por Vendedor**: Rendimiento individual y de equipo

## Requisitos del Sistema

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

## Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/SandraGeraldine/Delafiber.git
cd Delafiber
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configurar Variables de Entorno

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

```

### 5. Configurar Permisos (Linux/Mac)
```bash
chmod -R 777 writable/
```

### 6. Iniciar el Servidor

#### Opción A: Con XAMPP
1. Colocar el proyecto en `C:\xampp\htdocs\Delafiber`
2. Iniciar Apache y MySQL desde XAMPP Control Panel
3. Acceder a: `http://delafiber.test`

#### Opción B: Servidor integrado de PHP
```bash
php spark serve
```
Acceder a: `http://delafiber.test`

## Credenciales por Defecto

Después de la instalación, puedes acceder con:

| Rol | Email | Contraseña |
|-----|-------|------------|
| **Administrador** | admin@delafiber.com | password123 |
| **Supervisor** | carlos@delafiber.com | password123 |
| **Vendedor** | maria@delafiber.com | password123 |

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


##  Módulos

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


#### Agregado
- Sistema completo de gestión de leads
- Módulo de cotizaciones
- Gestión de tareas y calendario
- Sistema de campañas con mapas
- Dashboard con métricas
- Roles y permisos
- Auditoría de cambios

## Problemas Conocidos

- [ ] La búsqueda global en el header aún no está implementada
- [ ] Falta integración con WhatsApp Business API
- [ ] Los reportes avanzados están en desarrollo
- [ ] Notificaciones push pendientes de implementar

## Autor

- **Sandra Geraldine** - *Desarrollo inicial* - 
