# Laravel Mining Operations REST API

A **Laravel REST API** built with **clean architecture patterns** to model and manage mining operations, including vehicles, trips, diesel allocations, ore, users, and branches.  

This API leverages **Repository & Service layers**, **Laravel Policies** for authorization, **queued Jobs** and **Firebase Cloud Messaging (FCM)** for notifications, and **API Resources** for consistent JSON responses — making it ideal for integration with mobile or web clients.

---

## Architecture Overview

**Framework:** Laravel (PHP) — MVC + ecosystem features (Jobs, Events, Policies, Resources, Middleware)  
**API:** RESTful, versioned (V1, see `api.php`) — JSON responses via API Resources  
**Persistence:** Eloquent models map domain entities (e.g., `Trip`, `Vehicle`, `DieselAllocation`) to database tables; migrations, seeds, and factories used for schema and test data  

### Layering

| Layer | Responsibility |
|-------|----------------|
| **Controllers** | Orchestrate requests, validate input via Form Requests, and return API Resources |
| **Filters** | Encapsulate query filtering for list endpoints (lightweight query transformer/criteria layer) |
| **Repositories** | Abstract data access and queries from controllers for testability |
| **Services** | Implement business logic spanning models/repositories (e.g., allocation calculations, state changes) |
| **Policies** | Authorization logic for model-level access control using Laravel Gate/Policy system |
| **Jobs** | Background tasks (e.g., `CleanupInvalidFcmTokens`) run via Laravel Queues for non-blocking operations |
| **Helpers** | Centralized notification logic (FCM integration via `firebase-credentials.json`) |

---

## Key Components & Locations

- **Models:** `app/Models/*` — domain entities  
- **Controllers:** `app/Http/Controllers/*` — request endpoints  
- **Requests/Validation:** `app/Http/Requests/*` — input validation  
- **API Resources:** `app/Http/Resources/*` — response shaping  
- **Repositories:** `app/Repositories/*` — data access abstraction  
- **Services:** `app/Services/*` — business rules  
- **Filters:** `app/Filters/V1/*` — query parameter handling and filtering  
- **Policies:** `app/Policies/*` — authorization  
- **Jobs:** `app/Jobs/*` — queued background tasks  
- **Tests:** `tests/Feature` and `tests/Unit` — PHPUnit tests  

---

## Design Principles & Methodologies

- **Separation of Concerns:** Controllers orchestrate, Repositories handle DB access, Services encapsulate business rules, Policies manage authorization  
- **Repository Pattern:** Isolates Eloquent access for easier mocking and unit testing  
- **Service Layer:** Centralizes complex business logic to keep controllers thin  
- **API Resource Pattern:** Consistent JSON responses with transformation logic separated from models  
- **Versioned API & Filters:** Supports API evolution and structured query filters  
- **Declarative Validation:** Form Requests for clean validation rules  
- **Policy-based Authorization:** Centralized per model/action using Laravel Policies  
- **Background Processing & Resilience:** Jobs + Queue workers handle long-running or failure-prone tasks  
- **Push Notifications:** FCM integration for mobile/web notifications  
- **Config-driven & 12-factor friendly:** Environment variables for credentials (e.g., `firebase-credentials.json`)  
- **Test-first Mindset:** PHPUnit tests for main API flows  
- **PSR & Laravel Conventions:** Consistent code style and maintainability  

---

## Security & Operational Notes

- **Authentication:** Token-based (likely via Laravel Sanctum). Emphasize token revocation and scopes  
- **Sensitive Files:** Remove/rotate `firebase-credentials.json` before making repo public  
- **Queues:** Supports `sync`, `database`, `redis`; Redis recommended for production  
- **Rate Limiting & CORS:** Configured via `Kernel.php` and `cors.php` using standard Laravel middleware  

---

## Installation & Setup

1. Clone the repository:

