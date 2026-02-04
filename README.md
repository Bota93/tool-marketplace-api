# Tool Marketplace API

Backend **API-first** para una plataforma privada de módulos/herramientas de desarrollo.
Diseñado como base de un SaaS tipo *marketplace*, con control de acceso por usuario,
roles administrativos y soporte para contenido multimedia asociado a cada módulo.

> ⚠️ Este repositorio **no sirve vistas**. Es un backend puro pensado para ser consumido
> por un frontend web o móvil.

---

## 🧠 Visión general

La plataforma permite:

- Autenticación mediante tokens (Laravel Sanctum)
- Usuarios normales y administradores (`is_admin`)
- Creación de módulos privados (solo administradores)
- Concesión de acceso a módulos por usuario (grants)
- Ocultación de recursos no autorizados (404)
- Asociación de media (imágenes por URL) a los módulos
- Entorno de desarrollo reproducible mediante seeders
- Smoke test automático para validación end-to-end

---

## 🧱 Stack tecnológico

- **PHP 8.3**
- **Laravel 11**
- **PostgreSQL**
- **Laravel Sanctum** (autenticación por tokens)
- **Eloquent ORM**
- **WSL2 / Docker** para entorno local

---

## 📁 Estructura de carpetas

```
tool-marketplace-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── ModuleController.php
│   │   │       └── Admin/
│   │   │           └── ModuleAdminController.php
│   │   └── Middleware/
│   │       └── EnsureAdmin.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Module.php
│   │   ├── ModuleAccess.php
│   │   └── ModuleMedia.php
│   ├── Policies/
│   │   └── ModulePolicy.php
│   └── Providers/
│       └── AuthServiceProvider.php
│
├── database/
│   ├── migrations/
│   │   ├── 2026_01_18_203923_create_modules_table.php
│   │   ├── 2026_01_18_204500_create_module_accesses_table.php
│   │   └── 2026_01_25_081926_create_module_media_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── DevSeeder.php
│
├── routes/
│   ├── api.php
│   ├── web.php
│   └── console.php
│
├── scripts/
│   └── smoke.sh
│
├── public/
├── storage/
├── tests/
├── vendor/
│
├── artisan
├── composer.json
├── composer.lock
├── phpunit.xml
├── README.md
└── .env (no versionar)
```

---

## 🔐 Autenticación

La autenticación se realiza mediante **tokens** usando Laravel Sanctum.

### Login
```
POST /api/auth/login
```

El token devuelto debe enviarse en todas las peticiones protegidas:

```
Authorization: Bearer <token>
```

---

## 👥 Roles y autorización

- **Usuario normal**
  - Puede listar y ver únicamente los módulos a los que tiene acceso.
- **Administrador (`is_admin = true`)**
  - Puede crear módulos.
  - Puede conceder acceso a usuarios.
  - Puede añadir media a los módulos.

Los endpoints administrativos están protegidos por middleware `admin` y
se **ocultan** a usuarios no autorizados devolviendo **404**.

---

## 📦 Módulos

Un **módulo** representa una herramienta, script o recurso privado.

Características:
- Privado por defecto.
- Puede ser gratuito o de pago (estructura preparada).
- Accesible solo si existe un acceso válido en `module_accesses`.
- Puede tener múltiples elementos multimedia asociados.

### Endpoints principales

```
GET /api/modules
GET /api/modules/{slug}
```

> Si el usuario no tiene acceso al módulo, el endpoint responde **404**
> para no revelar su existencia.

---

## 🖼 Media de módulos

Cada módulo puede tener media asociada (imágenes por URL).

Por ahora:
- Solo URLs externas (por ejemplo GitHub raw).
- No se gestionan uploads directos.

Endpoint admin:
```
POST /api/admin/modules/{id}/media
```

---

## 🛡 Seguridad

- Policies centralizadas (`ModulePolicy`).
- Middleware `admin` para rutas administrativas.
- Ocultación de recursos no autorizados (404).
- Respuestas API unificadas (401 / 404 / 422).

---

## 🌱 Entorno de desarrollo (Seeder)

El proyecto incluye un **DevSeeder** que crea automáticamente:

- Usuario admin: `admin@example.com` / `password123`
- Usuario normal: `user@example.com` / `password123`
- Módulo publicado `qr-generator-2`
- Acceso concedido al usuario
- Media de ejemplo asociada al módulo

### Comando
```
php artisan migrate:fresh --seed
```

> ⚠️ Este seeder es **solo para desarrollo**.

---

## 🧪 Smoke test

Existe un script de validación rápida del sistema:

```
./scripts/smoke.sh
```

Valida automáticamente:
- Login admin y usuario.
- Rol de administrador correcto.
- Ocultación de endpoints admin.
- Acceso a módulos según grants.
- Presencia de media en el detalle del módulo.

Flujo recomendado:
```
php artisan migrate:fresh --seed
./scripts/smoke.sh
```

---

## 🚀 Estado del proyecto

✔ Backend core estable  
✔ Autenticación y roles funcionales  
✔ Control de acceso validado  
✔ Entorno reproducible  
✔ Smoke test end-to-end  

### Próximos pasos previstos
- Endurecer contrato API (Resources, paginación).
- Frontend MVP (React o Vue).
- Tests automatizados con PHPUnit.
- Preparación para monetización futura.

---

## 📌 Notas finales

- El frontend se desarrollará en un repositorio independiente.
- No versionar `.env`.
- Proyecto orientado a portfolio y evolución a SaaS.

---
