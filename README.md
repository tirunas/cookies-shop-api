# Cookie Shop API

## Task

This code has several problems. You can show your experience by fixing them or explaining what would you do differently.
````
Route::get('buy/{cookies}', function ($cookies) {
    $wallet = Auth::user()->wallet;
    Auth::user()->update(['wallet' => $wallet - $cookies * 1]);
    Log:info('User ' . $user->email . ' have bought ' . $cookies . ' cookies'); // we need to log who ordered and how much
    return 'Success, you have bought ' . $cookies . ' cookies!';
});
````

1. Imagine that this is a code for a small shop where for every $1 you can buy 1 cookie.
2. User can order some amount of cookies by entering in the browser: /buy/3 (to buy 3 cookies).
3. Add "wallet" column to the "users" table and set the value to 10 ($10).
4. Copy provided code to the web.php and fix it.
5. Think logically, what else you can do it this code, apply all possible senarios in it.(Main point)

## Questions
1. We all know what the N+1 query problem is. But as an architect, how do you enforce that a large team never accidentally pushes an N+1 query to production in the first place?
2. If our application is ingesting data from external hardware or third-party APIs and that external service suddenly goes down, how do you configure Laravel's queues to handle the failing jobs without losing the data or overwhelming the system?
3. When frontend or mobile teams in different time zones (like Europe and North America) need to build against your backend simultaneously, how do you handle API versioning and documentation to ensure nobody is blocked?
4. Walk me through how you handle database transactions when a user action requires both updating our local database and successfully charging a card via a third-party payment gateway.
5. In a highly dynamic application—like a multi-vendor marketplace—caching is easy, but invalidation is hard. What is your go-to strategy for ensuring users don't see stale data when a vendor updates their inventory?

## Answers

1. Preventing N+1 Queries
   I would use Model::preventLazyLoading() in the service provider. This throws an exception on lazy loading both locally and on the staging server. This forces the developers to use eager loading. This would prevent 90% of the N+1 query problems. In addition to this, the tests would also be run under the same configuration on the CI environment. This would prevent the N+1 query from even making it to the code review.

2. Queue Resilience When External Services Go Down
   The basic idea is to never lose the payload and not bombard the dead service. I would use an exponential backoff for the retries. This would mean a 10-second delay for the first retry, a one-minute delay for the second retry, and a five-minute delay for the third retry. After this, the jobs would be moved to the failed_jobs table where the payload would still be present. In the event the service comes back up, the jobs could be retried in bulk. In the job itself, if a timeout or a 503 response is detected, it would be released back to the queue with a delay instead of failing.

3. API Versioning Across Time Zones
   We actually did this in the project, folder-based versioning where v1 and v2 are completely independent implementations living side by side, where routes are automatically prefixed, so they can run simultaneously. Frontend teams can pin on a version and migrate on their own schedules. No breaking changes in a live version; if you need behavior changes, that's a new version. For documentation, something like Scribe or Scramble can be used to automatically generate OpenAPI specs from code, so you don't have anyone maintaining a wiki, and teams can agree on a contract shape, mock against it, and implement in parallel.

4. Transactions with a Payment Gateway
   You can't really wrap an HTTP call in a database transaction; that's the core problem here. So, you'd want to break this down into recoverable steps: create the order as 'pending,' then call the payment gateway, then update the order as 'paid' or 'failed,' depending on the result. Every call to the payment gateway gets an idempotency key so you can't accidentally double-charge a customer on a retry. Also, listening for the payment gateway's webhooks as the source of truth: in case the application crashes between the charge and the database update, the webhook catches this case anyway. And then a scheduled job can reconcile everything, checking for orders that are in 'pending' for too long and asking the payment gateway what actually happened.

5. Cache Invalidation in a Marketplace
   For a multi-vendor marketplace, I'd be inclined towards a tenant-based architecture, where each vendor gets their own database and cache store, which makes cache invalidation trivial because, for vendor 42, you're just clearing cache, and you're never touching anyone else's cache, so you're never accidentally invalidating cache for another vendor's items, and you're never concerned about cache saturation from a high-traffic vendor because you can scale that vendor's database and cache separately. 
   If that's too heavy a price to pay for full database per tenant, at least the cache should be tenant-scoped, so it's prefaced or tagged with vendor to allow for surgical flushing. And then there are the model observers that handle all that for you automatically: on create, update, delete, the vendor just saves their product, and the cache clears itself. And for things like stock counts that are known to change rapidly, I'd also recommend a short TTL of around 30-60 seconds as a fallback so that even if you forget to clear the cache, the data still self-corrects after a short period. 
   And then one step after saving changes for a vendor, I'd also recommend that their session bypasses the cache for a brief period so that they see their changes immediately. The bigger idea behind all this is that with tenant isolation, you solve most cache invalidation headaches simply by design: you stop wondering which caches you need to clear because the data is already isolated.

## Requirements

- PHP 8.3+
- Composer
- SQLite (default) or MySQL/PostgreSQL
- Node.js & npm

## Setup

```bash
# 1. Clone and install dependencies
git clone <repo-url> project-name && cd project-name
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Create SQLite database (if using SQLite)
touch database/database.sqlite

# 4. Run migrations
php artisan migrate

# 5. Seed the database (creates test user with $10 wallet)
php artisan db:seed --class="App\Domain\User\v1\Database\Seeders\UserSeeder"

# 6. Start the server
php artisan serve
```

Or use the shortcut:

```bash
composer setup
composer dev
```

## Test User

| Field    | Value              |
|----------|--------------------|
| Email    | test@example.com   |
| Password | password           |
| Wallet   | $10                |

## API Endpoints

Base URL: `http://localhost:8000`

### 1. Login

```
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "test@example.com",
    "password": "password"
}
```

Response:

```json
{
    "token": "1|abc123..."
}
```

### 2. Purchase Cookies

```
POST /api/v1/cart/purchase
Content-Type: application/json
Authorization: Bearer {token}

{
    "product": "cookies",
    "quantity": 3
}
```

Response:

```json
{
    "message": "Success, you have bought 3 cookies!"
}
```

## Step-by-Step Testing with cURL

```bash
# Step 1: Login and save token
TOKEN=$(curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}' | php -r 'echo json_decode(file_get_contents("php://stdin"))->token;')

echo $TOKEN

# Step 2: Purchase cookies (user has $10, each cookie costs $10)
curl -X POST http://localhost:8000/api/v1/cart/purchase \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"product":"cookies","quantity":1}'

# Step 3: Try to buy more than you can afford (should fail validation)
curl -X POST http://localhost:8000/api/v1/cart/purchase \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"product":"cookies","quantity":999}'

# Step 4: Try without auth (should return 401)
curl -X POST http://localhost:8000/api/v1/cart/purchase \
  -H "Content-Type: application/json" \
  -d '{"product":"cookies","quantity":1}'

# Step 5: Try invalid credentials (should return 401)
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"wrong"}'
```

## Project Structure

```
app/
├── Domain/
│   ├── Auth/v1/          # Authentication domain
│   │   ├── DTO/
│   │   ├── Exceptions/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Requests/
│   │   ├── Routes/
│   │   └── Services/
│   ├── Cart/v1/          # Cart/purchase domain
│   │   ├── DTO/
│   │   ├── Enums/
│   │   ├── Exceptions/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Requests/
│   │   ├── Routes/
│   │   └── Services/
│   └── User/v1/          # User domain
│       ├── Builders/
│       ├── Database/
│       │   ├── Factories/
│       │   ├── Migrations/
│       │   └── Seeders/
│       ├── Enums/
│       ├── Http/
│       │   └── Controllers/
│       ├── Models/
│       └── Routes/
└── Shared/               # Shared base classes
    ├── Base/
    ├── Enums/
    ├── Exceptions/
    ├── Providers/
    └── Traits/
```

## Key Architecture Decisions

- **Domain-Driven Design**: Each domain is self-contained with auto-wired routes, migrations, configs, and factories
- **Versioned API**: Domains support versioning via folder structure (`v1/`, `v2/`, etc.)
- **Atomic Wallet Updates**: Purchase uses database-level atomic update to prevent race conditions
- **Sanctum Auth**: Token-based API authentication
