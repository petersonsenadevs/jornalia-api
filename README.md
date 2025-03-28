# API de Jornalia

<div align="center">
  <img src="https://github.com/user-attachments/assets/a8d9fac1-ea64-4a0b-8e8c-e4a73ea03cb5" alt="jornaliaHD" width="200" height="200">
</div>



## Índice

1. [Descripción del Proyecto](#descripción-del-proyecto)
2. [Tecnologías Utilizadas](#tecnologías-utilizadas)
3. [Paquetes Utilizados](#paquetes-utilizados)
4. [Estructura del Proyecto](#estructura-del-proyecto)
5. [Instalación y Configuración](#instalación-y-configuración)
6. [Endpoints](#endpoints)


## Descripción del Proyecto

Jornalia nació de la necesidad personal de simplificar y organizar el registro de horas trabajadas en un entorno logístico. En mi trabajo diario, me encontraba apuntando manualmente mis horas y calculando los totales, lo cual no solo era tedioso, sino también propenso a errores. Al darme cuenta de que muchos de mis compañeros enfrentaban el mismo desafío, decidí crear Jornalia, una solución digital para llevar un control más detallado y eficiente de las jornadas laborales.

Jornalia esta diseñada para optimizar y centralizar el registro de horas trabajadas, así como el cálculo preciso de sueldos. Ya no hace falta depender de papeles o hojas de cálculo que se pueden extraviar; todo está almacenado de manera segura y accesible.

¿Qué hace Jornalia?
Esta plataforma permite:

Registrar el inicio y fin de la jornada laboral de manera precisa, evitando confusiones y errores.
Identificar las horas normales, extras y festivas, garantizando un cálculo justo para el empleado y una gestión transparente para la empresa.
Calcular el sueldo de manera exacta tomando en cuenta las horas trabajadas, incluyendo el pago por horas extras y festivas, ajustado a la tarifa por hora del empleado.
El sistema está diseñado no solo para empleados, sino también para empresas, con el objetivo de optimizar los procesos administrativos. Las organizaciones pueden gestionar horarios variables de manera eficiente, y los empleados pueden estar seguros de que recibirán una compensación justa por su tiempo trabajado.

## Tecnologías Utilizadas

- **Laravel 11**: Framework PHP para el desarrollo backend.
- **PHP 8.2**: Lenguaje de programación base.
- **MySQL**: Base de datos relacional.
- **Redis**: Sistema de caché para mejorar rendimiento y sesiones, y manejar las colas de trabajo.
- **Nginx**: Servidor web y proxy inverso para manejar las solicitudes de la API.
- **JWT (JSON Web Token)**: Autenticación segura.
- **Docker**: Contenedorización para facilitar la implementación.
- **Supervisor**: Gestión de procesos en segundo plano, incluyendo la ejecución de colas.
- **Rate Limit IP**: Restricción de solicitudes para evitar abusos y mejorar la seguridad.
- **Arquitectura MVC con capas**:
  - **DTOs (Data Transfer Objects)**: Para transferir datos entre capas.
  - **Services**: Lógica de negocio.
  - **Traits**: Código reutilizable.
  - **Eventos y Jobs**: Procesamiento asíncrono para tareas como cálculo de sueldos.
 

## Paquetes Utilizados

### Spatie Permission
- **Descripción**: Manejo de roles y permisos en Laravel.

### Swagger
- **Descripción**: Documentación de API con OpenAPI.
- **Publicación de la configuración**:
  ```bash
  php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
  ```
- **Generación de la documentación**:
  ```bash
  php artisan l5-swagger:generate
  ```
## Estructura del Proyecto

La organización del código sigue una arquitectura modular:

```
app/
├── DTOs/
├── Events/
├── Jobs/
├── Listeners/
├── Models/
├── Services/
├── Traits/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Middleware/
```

### Beneficios de esta Arquitectura

1. **Modularidad**: Código organizado y escalable.
2. **Reutilización**: Uso de Traits y DTOs para evitar redundancias.
3. **Asincronía**: Manejo de tareas en segundo plano.
4. **Desacoplamiento**: Capas independientes para facilitar modificaciones.

## Instalación y Configuración

Para instalar y configurar la API de Jornalia, sigue estos pasos:

### Clonación del Repositorio

1. Clona este repositorio en tu máquina local:

```bash
git clone <url-del-repositorio>
```

2. Instalación de Dependencias:

Navega al directorio del proyecto:

```bash
cd jornalia-api
```

3. Configuración del Entorno:

Copia el archivo `.env.example` a `.env` y actualiza las variables según tu configuración:

```bash
cp .env.example .env
```

Asegúrate de configurar las variables de base de datos, correo y Redis según tus necesidades.

En el archivo `.env`, también debes definir el nombre de las variables configuradas en el `docker-compose.yml`:

```bash
       DB_HOST=mysql
       DB_PORT=3306
       DB_DATABASE=jornalia
       DB_USERNAME=root
       DB_PASSWORD=secret
       REDIS_HOST=redis

```

4. Levantar los contenedores con Docker:

Ejecuta el siguiente comando para construir e iniciar los contenedores:

```bash
docker-compose up --build -d
```

Este comando creará y configurará automáticamente los contenedores de la aplicación, incluyendo la base de datos y Redis, sin necesidad de ejecutar migraciones manualmente.


# Endpoints

## Autenticación
### Registro de empleado
**POST** `/register`  
- **Descripción:** Registra un nuevo empleado.  
- **Autenticación:** No requerida.
- **Cuerpo de la solicitud:**
  - `name` (**String, Required**) - Nombre del empleado.  
  - `email` (**String, Required**) - Correo electrónico único del empleado.  
  - `password` (**String, Required**) - Contraseña para autenticación del empleado.  
  - `normal_hourly_rate` (**Decimal, Required**) - Tarifa por hora normal del empleado.  
  - `overtime_hourly_rate` (**Decimal, Required**) - Tarifa por hora extra del empleado.  
  - `holiday_hourly_rate` (**Decimal, Required**) - Tarifa por hora en días festivos.  
  - `irpf` (**Decimal, Optional**) - Porcentaje de retención del IRPF, opcional.

### Inicio de sesión
**POST** `/login`  
- **Descripción:** Inicia sesión para obtener un token JWT.  
- **Autenticación:** No requerida.
- **Cuerpo de la solicitud:**
  - `email` (**String, Required**) - Correo electrónico registrado del usuario.  
  - `password` (**String, Required**) - Contraseña del usuario. 

### Cierre de sesión
**POST** `/logout`  
- **Descripción:** Cierra la sesión del usuario actual.  
- **Autenticación:** Requerida (JWT).  

---

## Rutas de Usuario
### Actualizar Usuario
**PUT** `/user/update`  
- **Descripción:** Actualiza el email del usuario.  
- **Autenticación:** Requerida (JWT).
- **Cuerpo de la solicitud:**
  - `email`  

### Mostrar Usuario
**GET** `/user/show`  
- **Descripción:** Muestra el email del usuario.  
- **Autenticación:** Requerida (JWT).  

### Eliminar Usuario
**POST** `/user/delete`  
- **Descripción:** Elimina al usuario.  
- **Autenticación:** Requerida (JWT).  

---

## Rutas de Empleado
### Mostrar Empleado
**GET** `/employee`  
- **Descripción:** Muestra información del empleado (nombre, nombre de la empresa, tarifa por hora normal, horas extra y horas festivas).  
- **Autenticación:** Requerida (JWT).  

### Actualizar Empleado
**PUT** `/employee`  
- **Descripción:** Actualiza los datos del empleado, puedes enviar uno o varios campos para actualizar.  
- **Autenticación:** Requerida (JWT).
- 

---

## Rutas de Sesión de Horas
### Crear Sesión de Horas
**POST** `/hour_session`  
- **Descripción:** Crea una nueva sesión de horas.  
- **Autenticación:** Requerida (JWT).  
- **Datos requeridos:**  
  - `date` (**String, Required**) - Fecha de la sesión en formato `yyyy-mm-dd`.
  - `start_time` (**String, Required**) - Hora de inicio en formato `HH:mm`.
  - `end_time` (**String, Required**) - Hora de fin en formato `HH:mm`.
  - `planned_hours` (**Integer, Required**) - Número de horas previstas para la sesión.
  - `work_type` (**String, Optional**) - Tipo de trabajo, valores posibles:  
    - `is_normal` (por defecto si no se especifica)  
    - `is_holiday`  
    - `is_overtime` 

### Mostrar Sesión de Horas
**GET** `/hour_session`  
- **Descripción:** Muestra la sesión de horas de una fecha específica.  
- **Autenticación:** Requerida (JWT).  
- **Parámetros de consulta:**  
  - `date` (formato: `yyyy-mm-dd`)

### Actualizar Sesión de Horas
**PUT** `/hour_session`  
- **Descripción:** Actualiza una sesión de horas basada en la fecha.  
- **Autenticación:** Requerida (JWT).  
- **Parámetros de consulta:**  
  - `date` (formato: `yyyy-mm-dd`)

### Eliminar Sesión de Horas
**DELETE** `/hour_session`  
- **Descripción:** Elimina una sesión de horas específica.  
- **Autenticación:** Requerida (JWT).  
- **Parámetros de consulta:**  
  - `date` (formato: `yyyy-mm-dd`)

---

## Dashboard
### Mostrar Dashboard
**GET** `/dashboard`  
- **Descripción:** Muestra datos del mes actual, como la totalidad de horas trabajadas y el sueldo ganado.  
- **Autenticación:** Requerida (JWT).  

---

## Rutas de Salarios
### Mostrar Salario por Mes
**GET** `/salary`  
- **Descripción:** Muestra el salario de un mes específico.  
- **Autenticación:** Requerida (JWT).  
- **Parámetros de consulta:**  
  - `month`  
  - `year`
---


## Licencia  
Este código está bajo una licencia de uso educativo. Consulta [LICENSE.md](./LICENSE.md) para más detalles.


---

Si necesitas ayuda, abre un issue en el repositorio. 🚀

