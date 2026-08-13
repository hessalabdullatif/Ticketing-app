# Mini Ticketing App

A small Laravel + React (Inertia) ticketing platform, built step by step to
practice clean layering, background jobs, auth, and testing.

## Architecture

The app follows a layered structure — each layer has exactly one job, so a
change in one rarely forces a change in another.

| Layer | Responsibility |
|---|---|
| **Models** (`app/Models`) | Represent database tables and their relationships (`Event` → `Ticket` → `Order`) |
| **Migrations** | Define table structure; foreign keys enforce relationships at the database level |
| **Factories / Seeders** | Generate realistic fake data for local development and tests |
| **Form Requests** (`app/Http/Requests`) | Validate incoming data before it reaches any business logic. Fields not listed in `rules()` (like `total`) are silently dropped |
| **Resources** (`app/Http/Resources`) | Control exactly what shape of JSON is returned to the client, independent of the database schema |
| **Controllers** (`app/Http/Controllers/Api`) | Thin coordinators: validate → call a Service → return a Resource. No business rules live here |
| **Services** (`app/Services`) | All business logic: availability checks, total calculation, payment, persistence — wrapped in a DB transaction |
| **Payment Gateways** (`app/Services/PaymentGateways`) | A `PaymentGateway` interface + swappable implementations (`FakeMada`, `FakeVisa`), selected via config and bound in `AppServiceProvider`. `OrderService` depends only on the interface, never a concrete gateway |
| **Jobs** (`app/Jobs`) | Slow work (confirmation emails) is queued (`ShouldQueue`) so the API responds instantly and the work happens in a background worker |
| **Auth** | Sanctum token-based authentication protects write/read order routes; orders are scoped to `auth()->id()` so users only ever see their own |
| **Tests** (`tests/Feature`, `tests/Unit`) | Feature tests hit real HTTP endpoints (201 / 422 / 401 cases); a Unit test isolates the total-calculation logic with no database involved |

## Why split it this way

- **Testability** — business logic in Services can be tested without HTTP;
  the total calculation is tested in complete isolation from the database.
- **Swappability** — adding a third payment gateway means adding one class
  and one line in a `match()` statement. `OrderService` never changes.
- **Safety** — the client can never dictate a price (`total` is never an
  accepted input); the total is always computed server-side from real
  ticket data inside a locked, transactional block.
- **Fast responses** — anything that isn't essential to confirming the
  order (sending an email) is deferred to a queue, so the customer isn't
  waiting on a mail server.

## Running locally

\`\`\`bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve      # terminal 1
npm run dev             # terminal 2
php artisan queue:work  # terminal 3
\`\`\`

## Tests

\`\`\`bash
php artisan test
\`\`\`