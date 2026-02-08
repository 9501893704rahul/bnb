# BnB Housekeeping Checklist - Agent Guide

## Project Overview
A Laravel 11 application for managing vacation rental property cleaning. Enables property owners/companies to manage housekeepers, track cleaning sessions, and generate accountability reports.

## Tech Stack
- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Blade templates, Tailwind CSS, Alpine.js
- **Database**: MySQL/MariaDB
- **Auth**: Laravel Breeze with Spatie Permissions

## Key Commands
```bash
# Install dependencies
composer install
npm install

# Build assets
npm run dev     # Development with hot reload
npm run build   # Production build

# Database
php artisan migrate
php artisan db:seed

# Run development server
php artisan serve
```

## User Roles
1. **Admin**: Full system access
2. **Company**: Manages multiple owners and housekeepers (NEW)
3. **Owner**: Manages their properties and assigned housekeepers
4. **Housekeeper**: Completes cleaning sessions

## Key Models & Relationships
- `User` - Has roles (admin/company/owner/housekeeper), belongs to company
- `Property` - Belongs to owner, optionally to company
- `Room` - Many-to-many with Properties via pivot
- `Task` - Many-to-many with Rooms, has TaskMedia for example photos/videos
- `CleaningSession` - Links property, owner, housekeeper
- `ChecklistItem` - Task completion tracking per session
- `RoomPhoto` - Photos with timestamps, high-res storage
- `CalendarIntegration` - iCal sync with Airbnb/VRBO/Booking.com
- `CalendarEvent` - Parsed events from iCal feeds
- `CleaningReport` - Shareable report with token-based access

## Important Features

### Photo Handling
- Photos must use `capture="environment"` to force camera-only (no gallery)
- `ImageTimestampService::overlayAndSave()` adds timestamp at bottom-right
- High-res originals stored separately from web-optimized thumbnails
- `photo_type`: 'completion' or 'problem'

### Calendar Integration
- `CalendarSyncService` parses iCal feeds
- Supports Airbnb, VRBO, Booking.com
- Creates checkout alerts for scheduling cleanings

### Reports
- Generated from completed sessions
- Shareable via token-based URLs
- Can be sent via email (implemented) or SMS (placeholder)
- Auto-expires after 30 days

## File Locations
- Controllers: `app/Http/Controllers/`
- Models: `app/Models/`
- Services: `app/Services/`
- Views: `resources/views/`
- Routes: `routes/web.php`
- Migrations: `database/migrations/`

## Recent Additions (2024)
1. Company role for property management companies
2. Calendar integrations with iCal sync
3. Cleaning reports with sharing
4. Improved photo handling with timestamps
5. Redesigned homepage as sales pitch

## Testing Notes
- Run migrations before testing: `php artisan migrate`
- Seed roles: `php artisan db:seed --class=SetupRolesAndPermissionsSeeder`
- Photo processing requires GD extension with image support
