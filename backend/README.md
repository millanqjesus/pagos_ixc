# Pagos IXC - Backend

Este proyecto es el backend para el sistema de pagos IXC, desarrollado con Laravel.

## Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Configuración del Proyecto](#configuración-del-proyecto)
  - [Clonar el Repositorio](#clonar-el-repositorio)
  - [Configurar el Archivo .env](#configurar-el-archivo-env)
  - [Instalar Dependencias de Composer](#instalar-dependencias-de-composer)
  - [Configurar y Levantar Contenedores Docker](#configurar-y-levantar-contenedores-docker)
  - [Generar Clave de Aplicación](#generar-clave-de-aplicación)
  - [Ejecutar Migraciones y Seeders](#ejecutar-migraciones-y-seeders)
- [Ejecutar Tests](#ejecutar-tests)
  - [Ejecutar Todos los Tests](#ejecutar-todos-los-tests)
  - [Ejecutar Tests de Característica Específicos](#ejecutar-tests-de-característica-específicos)
  - [Ejecutar Tests Unitarios Específicos](#ejecutar-tests-unitarios-específicos)
- [Endpoints de la API (Ejemplos)](#endpoints-de-la-api-ejemplos)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

-   **Docker Desktop:** Para gestionar los contenedores Docker.
-   **Composer:** Para gestionar las dependencias de PHP.

## Configuración del Proyecto

Sigue estos pasos para configurar y ejecutar el proyecto localmente.

### Clonar el Repositorio

```bash
git clone https://github.com/millanqjesus/pagos_ixc.git
cd pagos-ixc/backend
```

Configurar el Archivo .env
Copia el archivo de ejemplo .env.example a .env y .env.testing:
```bash
cp .env.example .env
cp .env.example .env.testing
```

Asegúrate de configurar las variables de entorno en .env y .env.testing según tus necesidades. Para el entorno de testing, se recomienda usar una base de datos SQLite en memoria.

Configurar y Levantar Contenedores Docker
Construye y levanta los contenedores Docker definidos en docker-compose.yml:

```bash
docker compose up -d --build
```

Esto iniciará los servicios de la aplicación (PHP, Nginx, MySQL, etc.).
Generar Clave de Aplicación
Genera la clave de aplicación de Laravel:

```bash
docker compose exec app php artisan key:generate
```

Ejecutar Migraciones y Seeders
Ejecuta las migraciones de la base de datos y los seeders para poblar con datos iniciales:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Ejecutar Tests
El proyecto incluye tests de característica y unitarios.

Ejecutar Todos los Tests
Para ejecutar todos los tests del proyecto:

```bash
docker compose exec app php artisan test --env=testing
```

Ejecutar Tests de Característica Específicos
Para ejecutar tests de característica específicos (por ejemplo, TransferTest):

```bash
docker compose exec app php artisan test --filter TransferTest --env=testing
```

Endpoints de la API (Ejemplos)
Aquí hay una breve descripción de algunos endpoints clave:

POST /api/register: Registra un nuevo usuario.
POST /api/login: Autentica un usuario y devuelve un token.
POST /api/transfer: Realiza una transferencia de dinero entre usuarios (requiere autenticación).

Tecnologías Utilizadas
Laravel (PHP Framework)
Docker
MySQL (para entorno de desarrollo/producción)
SQLite (para entorno de testing)
Laravel Sanctum (para autenticación API)