# HouseScout

UK property intelligence platform for homebuyers. Enter any UK address or postcode and get comprehensive property data from official government sources — EPC ratings, planning applications, flood risk, crime statistics, and land registry records.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4
- **Admin Panel:** Filament v5
- **Frontend:** Livewire 4, Tailwind CSS v4
- **Database:** PostgreSQL 18 (via Docker/Sail locally, Laravel Cloud in production)
- **Testing:** Pest 4

## Architecture

```
/                    Marketing landing page (Blade + Tailwind)
/app                 Filament dashboard (auth required)
/app/login           Login
/app/register        Registration
/app/properties/{id} Property detail with tabbed data sections
/app/saved-properties Bookmarked properties
```

### Data Sources

| Feature | API | Auth Required |
|---------|-----|---------------|
| EPC Ratings | EPC Register API | Yes (API key) |
| Planning Applications | Planning Data Platform (gov.uk) | No |
| Flood Risk | Environment Agency API | No |
| Crime Statistics | Police Data API (data.police.uk) | No |
| Land Registry | HM Land Registry APIs | No |

### How It Works

1. User searches a postcode/address
2. `PropertySearchService` finds or creates a Property record and logs the search
3. `FetchPropertyDataJob` dispatches 5 async jobs to fetch data from each API
4. Data is stored in dedicated models with `fetched_at` timestamps for cache management
5. The property detail page displays results with loading states for pending data

## Local Development Setup

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 20+ & npm
- Docker (for PostgreSQL)

### Installation

```bash
# Clone the repo
git clone https://github.com/JacobWLMS/HouseScout.git
cd HouseScout

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env
php artisan key:generate

# Start PostgreSQL via Docker
docker compose up -d pgsql

# Run migrations
php artisan migrate

# Build frontend assets
npm run build

# Start the dev server
php artisan serve
```

The app runs at `http://localhost:8000` with the Filament panel at `http://localhost:8000/app`.

### Local Database

PostgreSQL runs in Docker via Laravel Sail. The `.env` defaults connect to it:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=housescout
DB_USERNAME=sail
DB_PASSWORD=password
```

Note: `DB_HOST` is `127.0.0.1` (not `pgsql`) because PHP runs on the host, not inside the Sail container.

### Running Tests

```bash
php artisan test --compact
```

### Code Formatting

```bash
vendor/bin/pint
```

## API Keys

### Overview

Only **one** API key is required — the EPC Register API. The other four APIs (Planning, Flood, Crime, Land Registry) are open and need no authentication.

Without the EPC key, the app works fine — EPC data just won't be fetched (the service logs a warning and skips gracefully).

### Getting the EPC API Key

1. Go to https://epc.opendatacommunities.org
2. Click **"Get an API key"** (or go directly to https://epc.opendatacommunities.org/login)
3. Register with your email address
4. Confirm your email — your API key will be shown on your account page
5. The key is a long alphanumeric string (e.g., `abc123def456...`)

This is completely free with no rate limits for reasonable usage.

### Adding API Keys Locally

Add to your `.env` file:

```
EPC_API_KEY=your_api_key_here
```

That's the only required key. The other API base URLs and cache TTLs have sensible defaults, but you can override them if needed:

```env
# Optional overrides (defaults shown)
EPC_API_BASE_URL=https://epc.opendatacommunities.org/api/v1
EPC_CACHE_TTL=86400
PLANNING_API_BASE_URL=https://www.planning.data.gov.uk/api/v1
PLANNING_CACHE_TTL=86400
FLOOD_API_BASE_URL=https://environment.data.gov.uk/flood-monitoring
FLOOD_CACHE_TTL=3600
POLICE_API_BASE_URL=https://data.police.uk/api
POLICE_CACHE_TTL=86400
LAND_REGISTRY_API_BASE_URL=https://landregistry.data.gov.uk
LAND_REGISTRY_CACHE_TTL=604800
SEARCH_CLEANUP_DAYS=90
```

### Adding API Keys in Laravel Cloud

1. Go to your Laravel Cloud dashboard
2. Select the **HouseScout** project
3. Select the environment (production or development)
4. Go to **Environment Variables**
5. Add a custom variable:
   - **Key:** `EPC_API_KEY`
   - **Value:** your API key
6. Click **Save** and redeploy

Do this for both your production and development environments if you want EPC data in both.

### API Reference

| API | Docs | Auth | Notes |
|-----|------|------|-------|
| **EPC Register** | https://epc.opendatacommunities.org/docs/api | Basic Auth (key as username, empty password) | Free, registration required |
| **Planning Data** | https://www.planning.data.gov.uk | None | Free, open data |
| **Environment Agency Flood** | https://environment.data.gov.uk/flood-monitoring/doc/reference | None | Free, real-time data |
| **Police Data** | https://data.police.uk/docs/ | None | Free, street-level crime data |
| **HM Land Registry** | https://landregistry.data.gov.uk | None | Free, Price Paid Data |

## Laravel Cloud Deployment

The app is deployed on [Laravel Cloud](https://cloud.laravel.com) with two environments:

| Environment | Branch | URL |
|-------------|--------|-----|
| Production | `main` | https://housescout-main-zaousg.laravel.cloud |
| Development | `develop` | Configured separately |

### Required Cloud Environment Variables

These should be set as custom variables (Cloud auto-injects DB, APP_URL, etc.):

```
QUEUE_CONNECTION=database
EPC_API_KEY=your_key_here
```

### Deployment Workflow

```
develop  ->  push  ->  auto-deploys to development environment
main     ->  push  ->  auto-deploys to production environment
```

Feature branches merge into `develop` for testing, then `develop` merges into `main` for production.

## Scheduled Tasks

| Command | Schedule | Description |
|---------|----------|-------------|
| `app:cleanup-old-searches` | Daily at 2am | Purges search records older than 90 days |
| `app:refresh-stale-data` | Every 6 hours | Re-fetches stale cached property data |

## Project Structure

```
app/
  Console/Commands/        Artisan commands (cleanup, refresh)
  Exceptions/              Custom exceptions (API, postcode, property)
  Filament/
    Pages/                 PropertyDetailPage
    Resources/             SavedPropertyResource
    Widgets/               Search, RecentSearches, StatsOverview
  Jobs/                    Queue jobs for async API fetching
  Models/                  8 domain models + User
  Providers/Filament/      AppPanelProvider (panel config)
  Services/
    Api/
      Contracts/           PropertyDataProvider interface
      EpcApiService        5 API service implementations
      PlanningApiService
      FloodMonitoringApiService
      PoliceApiService
      LandRegistryApiService
    PostcodeService        UK postcode validation & normalization
    PropertySearchService  Search orchestration & demand tracking
    PropertyIntelligenceService  Data freshness & refresh orchestration
config/
  housescout.php           API endpoints, keys, cache TTLs
```
