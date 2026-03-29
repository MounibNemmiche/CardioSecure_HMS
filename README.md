# CardioSecure

**Secure Cardiologist Clinic Information System**

A GDPR-compliant MVP built for the *Healthcare Information Systems Security* course at the University of Padova. CardioSecure demonstrates secure handling of patient health data through role-based access control, mandatory two-factor authentication, AES-256 encryption of sensitive fields, immutable medical records, and comprehensive audit logging.

---

## Table of Contents

- [Quick Start](#quick-start)
- [Demo Credentials](#demo-credentials)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Project Structure](#project-structure)
- [User Roles & Permissions](#user-roles--permissions)
- [Core Modules](#core-modules)
- [Security Features](#security-features)
- [Database Schema](#database-schema)
- [Development Guide](#development-guide)
- [Troubleshooting](#troubleshooting)
- [Production Deployment](#production-deployment)

---

## Quick Start

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running
- Git

### Setup (3 commands)

```bash
# 1. Clone the repository
git clone <repo-url> cardiosecure
cd cardiosecure

# 2. Copy environment file
cp .env.example .env

# 3. Start Docker containers
docker compose up -d
```

Wait for all 3 containers to become healthy (about 30 seconds), then run:

```bash
# 4. Install dependencies & set up the database
docker compose exec laravel.test bash -c "
  composer install &&
  php artisan key:generate &&
  php artisan migrate --seed &&
  npm install --legacy-peer-deps
"

# 5. Start the frontend development server
docker compose exec -d laravel.test bash -c "cd /var/www/html && npm run dev"
```

### Open in browser

| Service | URL |
|---------|-----|
| **Application** | [http://localhost:8080](http://localhost:8080) |
| **Mailpit** (email testing) | [http://localhost:8025](http://localhost:8025) |

> **Note:** The app runs on port **8080** (not 80) to avoid conflicts with other services. If port 8080 is also taken on your machine, change `APP_PORT` in `.env` and restart Docker.

---

## Demo Credentials

All demo accounts use the same password: **`CardioSecure2026!`**

| Role | Name | Email |
|------|------|-------|
| **Admin** | Alessandro Bianchi | `admin@cardiosecure.it` |
| **Staff** | Giulia Marino | `giulia.marino@cardiosecure.it` |
| **Staff** | Marco Esposito | `marco.esposito@cardiosecure.it` |
| **Doctor** | Dott. Roberto Conti | `roberto.conti@cardiosecure.it` |
| **Doctor** | Dott.ssa Elena Russo | `elena.russo@cardiosecure.it` |
| **Doctor** | Dott. Francesco Moretti | `francesco.moretti@cardiosecure.it` |
| **Patient** | Maria Rossi | `maria.rossi@example.it` |
| **Patient** | Giuseppe Verdi | `giuseppe.verdi@example.it` |
| **Patient** | Anna Ferrari | `anna.ferrari@example.it` |
| **Patient** | Luca Romano | `luca.romano@example.it` |
| **Patient** | Sofia Colombo | `sofia.colombo@example.it` |

> **Important:** On first login, every user must set up **Two-Factor Authentication (2FA)** using an authenticator app (Google Authenticator, Microsoft Authenticator, or Authy). This is a mandatory security requirement , no user can access the dashboard without completing MFA setup.

### First Login Flow

1. Go to [http://localhost:8080/login](http://localhost:8080/login)
2. Enter email and password from the table above
3. You will be redirected to the MFA setup page
4. Click "Set up 2FA" to enable two-factor authentication
5. Scan the QR code with your authenticator app
6. Enter the 6-digit code to verify
7. Save the recovery codes shown (one-time display)
8. You are now on your role-specific dashboard

---

## Tech Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Backend** | Laravel 12, PHP 8.2+ | MVC framework, routing, middleware |
| **Authentication** | Laravel Fortify | Session-based auth, email verification, TOTP 2FA |
| **Frontend** | Vue 3 + TypeScript | Reactive UI components |
| **SPA Bridge** | Inertia.js v3 | Server-driven single-page app (no REST API needed) |
| **Styling** | Tailwind CSS 4 | Utility-first CSS framework |
| **Database** | MySQL 8.4 | Relational data storage |
| **RBAC** | Spatie laravel-permission v7 | Role and permission management |
| **Encryption** | Laravel Encrypted Casts | AES-256-CBC for sensitive fields |
| **Build Tool** | Vite 8 | Frontend asset bundling + HMR |
| **PWA** | vite-plugin-pwa | Service worker, manifest, install prompt |
| **Push Notifications** | minishlink/web-push | VAPID-based Web Push API |
| **Email (dev)** | Mailpit | Local email testing UI |
| **Routing (JS)** | Ziggy | Named Laravel routes in JavaScript |
| **Dev Environment** | Docker Compose (Laravel Sail) | MySQL, PHP, Mailpit containers |

---

## Architecture

CardioSecure follows a **monolithic Laravel + Inertia.js** architecture. There is no separate API layer , all data flows through Inertia controllers.

### Request Flow

```
Browser Request
    |
    v
Laravel Router
    |
    v
Middleware Stack [Auth -> Verified -> MFA -> Role]
    |
    v
Controller (PHP)
    |
    v
Eloquent ORM -> MySQL Database
    |
    v
Inertia::render('Page', $props)
    |
    v
Vue Component (client-side render with props)
    |
    v
HTML Response to Browser
```

### Key Architectural Decisions

- **Inertia.js** eliminates the need for a REST/GraphQL API. The frontend receives props directly from PHP controllers, with full-page type safety.
- **Persistent layouts** (`defineOptions({ layout: XLayout })`) prevent sidebar re-renders on navigation.
- **Server-side authorization** via Laravel Policies prevents IDOR attacks. Every model access is checked against the authenticated user.
- **Encrypted Eloquent casts** transparently encrypt/decrypt sensitive insurance data at the model layer.

---

## Project Structure

```
cardiosecure/
|
+-- app/
|   +-- Http/
|   |   +-- Controllers/
|   |   |   +-- Admin/              # DashboardController, UserManagementController,
|   |   |   |                       # InsurancePlanController
|   |   |   +-- Doctor/             # DashboardController
|   |   |   +-- Patient/            # DashboardController, InsuranceController
|   |   |   +-- Staff/              # DashboardController
|   |   |   +-- Shared/             # ProfileController (used by all roles)
|   |   +-- Middleware/
|   |       +-- EnsureMfaIsSetup.php  # Redirects to MFA setup if not configured
|   |       +-- HandleInertiaRequests.php  # Shares auth + flash data to frontend
|   +-- Models/                     # 12 Eloquent models
|   +-- Actions/Fortify/            # CreateNewUser (patient registration)
|   +-- Policies/                   # Authorization policies (IDOR prevention)
|   +-- Services/                   # AuditService, WebPushService
|
+-- database/
|   +-- migrations/                 # 15 migration files (14 app tables + permissions)
|   +-- seeders/                    # Demo data (Italian names, credentials)
|
+-- resources/js/
|   +-- app.js                      # Inertia app bootstrap
|   +-- components/                 # Reusable Vue components
|   |   +-- FlashMessage.vue        # Auto-dismissing success/error notifications
|   |   +-- StatCard.vue            # Dashboard stat card
|   |   +-- ProfileForm.vue         # Shared profile edit form
|   +-- layouts/                    # Persistent sidebar layouts
|   |   +-- PatientLayout.vue       # Blue accent, patient nav links
|   |   +-- DoctorLayout.vue        # Emerald accent, doctor nav links
|   |   +-- StaffLayout.vue         # Violet accent, staff nav links
|   |   +-- AdminLayout.vue         # Red accent, admin nav links
|   +-- pages/                      # Inertia pages (mapped to routes)
|   |   +-- Auth/                   # Login, Register, MfaSetup, VerifyEmail, etc.
|   |   +-- Patient/               # Dashboard, Profile, Insurance
|   |   +-- Doctor/                # Dashboard, Profile
|   |   +-- Staff/                 # Dashboard, Profile
|   |   +-- Admin/                 # Dashboard, Profile, Users/, InsurancePlans/
|   |   +-- Welcome.vue            # Landing page
|   +-- types/                     # TypeScript interfaces
|       +-- index.ts               # AuthUser, Profile, InsurancePlan, etc.
|
+-- routes/
|   +-- web.php                    # All routes grouped by role middleware
|
+-- config/
|   +-- fortify.php                # Auth configuration (2FA enabled)
|
+-- compose.yaml                   # Docker services (Laravel, MySQL, Mailpit)
+-- vite.config.js                 # Vite + Tailwind + Vue + PWA config
+-- .env.example                   # Environment template
```

---

## User Roles & Permissions

CardioSecure implements four distinct roles, each with specific permissions enforced at both the middleware and policy level.

### Role Matrix

| Permission | Admin | Staff | Doctor | Patient |
|-----------|:-----:|:-----:|:------:|:-------:|
| Manage users & roles | x | | | |
| Manage insurance plan master list | x | | | |
| View audit logs | x | | | |
| Manage all appointments | | x | | |
| View all appointments | | x | | |
| View patient profiles | | x | | |
| View patient insurance (read-only) | | x | | |
| View own appointments | | | x | x |
| View assigned patients | | | x | |
| Create medical records | | | x | |
| View medical records | | | x | x* |
| Upload medical files | | | x | |
| Book appointments | | | | x |
| Manage own insurance | | | | x |
| Manage medication reminders | | | | x |

*Patient sees only their own records. Doctor sees only records for their assigned patients.

### How Roles are Enforced

1. **Route middleware** (`role:admin`, `role:patient`, etc.) blocks access to entire route groups
2. **Laravel Policies** check ownership on individual records (prevents IDOR)
3. **Spatie permissions** provide granular permission checks in controllers

### Creating New Users

- **Patients** self-register at `/register` (public)
- **Doctors, Staff, Admins** are created by an Admin via the User Management panel (`/admin/users/create`)
- All admin-created users get `email_verified_at` set automatically

---

## Core Modules

### Module 1: Authentication & MFA (Phase 1)

- **Registration:** Patient self-registration with name, email, password, phone, date of birth, codice fiscale
- **Email Verification:** Fortify-managed verification emails (intercepted by Mailpit in dev)
- **Two-Factor Authentication:** Mandatory TOTP 2FA for all users. QR code scan + 6-digit verification + recovery codes
- **Login Flow:** Email/password -> 2FA challenge -> Role-based dashboard redirect

### Module 2: Dashboards & Profile Management (Phase 2)

- **Patient Dashboard:** Next appointment, medical records count, active medication reminders, insurance status
- **Doctor Dashboard:** Today's appointments, total patients, records created, upcoming appointments table
- **Staff Dashboard:** Today's/weekly appointment counts, total patients, upcoming appointments table
- **Admin Dashboard:** User counts by role, recent audit log entries
- **Profile:** All users can edit personal info (phone, address, city, province, codice fiscale, date of birth)

### Module 3: Insurance Management (Phase 2)

- **Patient:** Select insurance plan, enter policy number (AES-256 encrypted), tessera sanitaria number (encrypted), holder name (encrypted)
- **Admin:** Full CRUD on insurance plan master list (SSN/Tessera Sanitaria, AXA, Allianz, WAI, Other)
- **Staff:** Read-only view of patient insurance info

### Module 4: Appointments (Phase 3)

- Patient booking with 30-minute slots (Mon-Fri, 09:00-17:00)
- Staff manages all appointments (reschedule, cancel)
- Doctor views own schedule
- Google Calendar link generation

### Module 5: Medical Records & Audit Logs (Phase 4)

- Doctor creates immutable cardiology records (vitals, symptoms, diagnosis)
- File uploads (ECG, PDF, images) stored securely outside web root
- Comprehensive audit logging of all critical actions
- Admin audit log viewer with filtering

### Module 6: Medication Reminders & PWA (Phase 5)

- Patient manages medication reminders (name, dosage, time)
- Push notifications via Web Push API (VAPID)
- PWA with install prompt and service worker

---

## Security Features

### Authentication

| Feature | Implementation |
|---------|---------------|
| Password Policy | Min 8 chars, mixed case, numbers, symbols (`Password::min(8)->mixedCase()->numbers()->symbols()`) |
| Two-Factor Auth | Mandatory TOTP via Laravel Fortify (QR code + backup codes) |
| Email Verification | Required before dashboard access |
| Session Security | `secure=true`, `http_only=true`, `same_site=lax` |
| Rate Limiting | 5 login attempts per minute (Laravel throttle middleware) |
| Session Regeneration | Automatic on login (Laravel default) |

### Authorization

| Feature | Implementation |
|---------|---------------|
| Role-Based Access | Spatie laravel-permission (4 roles, 16 permissions) |
| Route Protection | Middleware groups per role (`role:admin`, `role:patient`, etc.) |
| IDOR Prevention | Laravel Policies check record ownership on every model access |
| CSRF Protection | Automatic via Inertia.js X-XSRF-TOKEN header |

### Data Protection

| Feature | Implementation |
|---------|---------------|
| Encryption at Rest | AES-256-CBC via Laravel `encrypted` cast on insurance fields (policy_number, holder_name, tessera_sanitaria_number) |
| Fortify Encryption | 2FA secrets and recovery codes encrypted automatically |
| Secure File Storage | Medical files in `storage/app/medical-files/` (NOT in `public/`), served via authorized controller |
| SQL Injection Prevention | Eloquent ORM with parameterized queries |
| XSS Prevention | Vue template auto-escaping (no `v-html` used) |

### Audit & Compliance

| Feature | Implementation |
|---------|---------------|
| Audit Logging | Append-only `audit_logs` table (no updates/deletes) |
| Logged Actions | login, logout, MFA setup, appointments, medical records, file uploads, user management |
| Privacy | Audit logs never contain medical content , only metadata |
| GDPR | Data minimization, encrypted sensitive fields, purpose-limited access |

---

## Database Schema

### 14 Application Tables + 5 Spatie Permission Tables

```
users                    # Core auth (Fortify 2FA fields included)
  |-- profiles           # Personal info (phone, address, codice_fiscale)
  |-- doctors            # Doctor-specific (specialization, license)
  |-- patients           # Patient-specific (blood type, allergies, emergency contact)
  |     |-- patient_insurance     # 1:1, encrypted fields
  |     |-- appointments          # N, linked to doctor
  |     |-- medical_records       # N, immutable, linked to doctor
  |     |     |-- medical_record_files  # N, secure storage
  |     |-- medication_reminders  # N
  |-- push_subscriptions # Web Push endpoints
  |-- audit_logs         # Append-only activity log

insurance_plans          # Master list (admin-managed)

+ 5 Spatie tables: roles, permissions, model_has_roles,
                   model_has_permissions, role_has_permissions
```

### Key Design Decisions

- **Encrypted fields:** `patient_insurance.policy_number`, `patient_insurance.holder_name`, `patient_insurance.tessera_sanitaria_number` use Laravel's `encrypted` cast (AES-256-CBC with APP_KEY)
- **Immutable records:** `medical_records` have no update endpoint , corrections are added as new addendum records
- **Append-only audit:** `audit_logs` has `created_at` but no `updated_at`; no update/delete routes exist

---

## Development Guide

### Docker Services

| Service | Container | Port | Purpose |
|---------|-----------|------|---------|
| `laravel.test` | PHP 8.2 + Node.js | 8080 (HTTP), 5173 (Vite HMR) | Application server |
| `mysql` | MySQL 8.4 | 3306 | Database |
| `mailpit` | Mailpit | 8025 (UI), 1025 (SMTP) | Email testing |

### Common Commands

```bash
# Start all services
docker compose up -d

# Stop all services
docker compose down

# Run Artisan commands
docker compose exec laravel.test php artisan <command>

# Reset database with fresh seed data
docker compose exec laravel.test php artisan migrate:fresh --seed

# Start Vite dev server (required for frontend)
docker compose exec -d laravel.test bash -c "cd /var/www/html && npm run dev"

# Install new Composer package
docker compose exec laravel.test composer require <package>

# Install new npm package
docker compose exec laravel.test npm install <package> --legacy-peer-deps

# View application logs
docker compose exec laravel.test tail -f storage/logs/laravel.log

# Access container shell
docker compose exec laravel.test bash

# Check container status
docker compose ps
```

### Adding a New Page

1. **Create a Controller** in `app/Http/Controllers/<Role>/`:
   ```php
   namespace App\Http\Controllers\Patient;

   use App\Http\Controllers\Controller;
   use Inertia\Inertia;

   class MyController extends Controller
   {
       public function index()
       {
           return Inertia::render('Patient/MyPage', [
               'data' => $someData,
           ]);
       }
   }
   ```

2. **Create a Vue Page** in `resources/js/pages/<Role>/MyPage.vue`:
   ```vue
   <script setup lang="ts">
   import PatientLayout from '@/layouts/PatientLayout.vue'
   defineOptions({ layout: PatientLayout })
   defineProps<{ data: SomeType }>()
   </script>

   <template>
       <div>
           <h1 class="text-2xl font-bold text-gray-900">My Page</h1>
           <!-- content here -->
       </div>
   </template>
   ```

3. **Add a Route** in `routes/web.php`:
   ```php
   Route::middleware('role:patient')->prefix('patient')->name('patient.')->group(function () {
       Route::get('/my-page', [MyController::class, 'index'])->name('my-page');
   });
   ```

4. **Add Nav Link** in `resources/js/layouts/PatientLayout.vue` `navLinks` array

5. **Restart Vite** (Docker on Windows does not auto-detect new files):
   ```bash
   docker compose exec laravel.test bash -c "pkill -f vite; cd /var/www/html && npm run dev"
   ```

### Adding a New Role Permission

1. Add permission in `database/seeders/RoleAndPermissionSeeder.php`
2. Run `docker compose exec laravel.test php artisan migrate:fresh --seed`
3. Use in controller: `$this->authorize('permission-name')` or `$user->can('permission-name')`

### Working with Encrypted Fields

Fields with the `encrypted` cast are automatically encrypted when saved and decrypted when read:

```php
// In the model:
protected function casts(): array
{
    return [
        'policy_number' => 'encrypted',
    ];
}

// In code , just use normally, encryption is transparent:
$insurance->policy_number = 'ABC123';  // Encrypted in DB
echo $insurance->policy_number;         // Returns 'ABC123'
```

### TypeScript Types

All shared types are in `resources/js/types/index.ts`. When adding new Inertia props, define interfaces there:

```typescript
export interface MyNewType {
    id: number
    name: string
    // ...
}
```

---

## Troubleshooting

### Blank page after starting

Vite must be running for the frontend to work:
```bash
docker compose exec -d laravel.test bash -c "cd /var/www/html && npm run dev"
```

### Port 8080 already in use

Change the port mapping in `compose.yaml`:
```yaml
ports:
  - '${APP_PORT:-9090}:80'  # Change 8080 to any free port
```

### Vite not picking up file changes (Windows + Docker)

Docker volume mounts on Windows don't trigger filesystem events. After creating/renaming Vue files, restart Vite:
```bash
docker compose exec laravel.test bash -c "pkill -f vite; cd /var/www/html && npm run dev"
```

### npm install fails with peer dependency errors

Use the `--legacy-peer-deps` flag:
```bash
docker compose exec laravel.test npm install --legacy-peer-deps
```

### Permission errors (storage/bootstrap)

```bash
docker compose exec laravel.test chmod -R 777 storage bootstrap/cache
docker compose exec laravel.test php artisan view:clear
```

### MFA QR code not showing

Ensure `confirmPassword` is set to `false` in `config/fortify.php`. If it's `true`, Fortify returns HTTP 423 which blocks the QR code generation.

### Email verification not arriving

Emails are captured by Mailpit in development. Open [http://localhost:8025](http://localhost:8025) to view them. No real emails are sent in local development.

### Database connection refused

Wait for the MySQL container to be healthy:
```bash
docker compose ps  # Check STATUS column shows "healthy"
```

---

## Production Deployment

### Hostinger Shared Hosting

```
Hostinger directory structure:
/home/user/
+-- public_html/    -> Points to cardiosecure/public/
+-- cardiosecure/   -> Full Laravel project (above web root)
```

### Steps

1. Upload project to `/home/user/cardiosecure/`
2. Point `public_html` to `cardiosecure/public/` via `.htaccess`
3. Set PHP 8.2+ in Hostinger hPanel
4. Configure `.env` with production values:
   ```
   APP_ENV=production
   APP_DEBUG=false
   SESSION_SECURE_COOKIE=true
   SESSION_SAME_SITE=lax
   ```
5. Run setup commands:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```
6. Build frontend locally, upload `public/build/` directory
7. Add cron job for medication reminders:
   ```
   * * * * * cd /home/user/cardiosecure && php artisan schedule:run >> /dev/null 2>&1
   ```

---

## Course Context

This project was built for the **Healthcare Information Systems Security** course at the **University of Padova** (Dipartimento di Ingegneria dell'Informazione). It demonstrates:

- Secure authentication with mandatory MFA
- Role-based access control with IDOR prevention
- AES-256 encryption of sensitive healthcare data (insurance info)
- Immutable medical records with audit trails
- GDPR-aware data minimization
- Cardiology-specific healthcare workflows
- Progressive Web App with push notifications for medication reminders

### Known Limitations

- PWA push notifications do not work on iOS Safari (documented Apple limitation; works on Android Chrome and desktop browsers)
- No real insurance verification API integration
- No multi-clinic support (single clinic MVP)
- Codice Fiscale is stored as-is without algorithmic validation

---

## License

This project is developed for academic purposes as part of the University of Padova coursework.
