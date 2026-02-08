# Deploying BnB Housekeeping to Render

This guide walks you through deploying the BnB Housekeeping application to Render.

## Prerequisites

- A [Render account](https://render.com/) (free tier available)
- Your GitHub repository with the code

## Method 1: Blueprint Deployment (Recommended)

### Step 1: Connect Repository

1. Go to [Render Dashboard](https://dashboard.render.com/)
2. Click **New** → **Blueprint**
3. Connect your GitHub account if not already connected
4. Select the `9501893704rahul/bnb` repository
5. Render will detect the `render.yaml` file

### Step 2: Configure Services

Render will automatically create:
- **Web Service**: `bnb-housekeeping`
- **Database**: `bnb-database` (PostgreSQL)

### Step 3: Set Environment Variables

In the Render dashboard, add these environment variables:

| Key | Value |
|-----|-------|
| `APP_KEY` | Click "Generate" or run `php artisan key:generate --show` |
| `APP_URL` | `https://bnb-housekeeping.onrender.com` (your actual URL) |

### Step 4: Deploy

Click **Apply** to start the deployment.

---

## Method 2: Manual Deployment

### Step 1: Create PostgreSQL Database

1. Go to Render Dashboard → **New** → **PostgreSQL**
2. Configure:
   - **Name**: `bnb-database`
   - **Database**: `bnb_housekeeping`
   - **User**: `bnb_user`
   - **Region**: Choose closest to your users
   - **Plan**: Free (or paid for production)
3. Click **Create Database**
4. Copy the **Internal Database URL**

### Step 2: Create Web Service

1. Go to Render Dashboard → **New** → **Web Service**
2. Connect your GitHub repository
3. Configure:
   - **Name**: `bnb-housekeeping`
   - **Region**: Same as database
   - **Branch**: `master` or `feature/add-ons-v2`
   - **Runtime**: Docker
   - **Plan**: Free (or paid for production)

### Step 3: Environment Variables

Add these in the **Environment** section:

```
APP_NAME=BnB Housekeeping
APP_ENV=production
APP_DEBUG=false
APP_URL=https://bnb-housekeeping.onrender.com
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

DB_CONNECTION=pgsql
DATABASE_URL=<paste your Internal Database URL here>

LOG_CHANNEL=stderr
LOG_LEVEL=error

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

MAIL_MAILER=log
```

### Step 4: Build & Start Commands

If not using Docker:

**Build Command:**
```bash
./render-build.sh
```

**Start Command:**
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

### Step 5: Deploy

Click **Create Web Service** and wait for deployment.

---

## Post-Deployment Setup

### 1. Create Admin User

After deployment, you need to create the first admin user:

1. Go to your Render web service
2. Click **Shell** tab
3. Run:
```bash
php artisan tinker
```
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-secure-password'),
]);
$user->assignRole('admin');
exit;
```

### 2. Verify Storage Link

```bash
php artisan storage:link
```

### 3. Test the Application

Visit your app URL: `https://bnb-housekeeping.onrender.com`

---

## Troubleshooting

### Database Connection Issues

If you see database errors:
1. Verify `DATABASE_URL` is set correctly
2. Check if database is running in Render dashboard
3. Ensure `DB_CONNECTION=pgsql`

### Storage/Upload Issues

Render's free tier has ephemeral storage. For production:
1. Use **Cloudinary** or **AWS S3** for file uploads
2. Update `FILESYSTEM_DISK=s3` in environment variables
3. Add S3 credentials

### Build Failures

Check the build logs for errors:
1. Missing PHP extensions → Update Dockerfile
2. npm errors → Clear cache and retry
3. Memory issues → Upgrade to paid plan

### Migrations Not Running

Manually run migrations:
1. Go to Shell tab in Render
2. Run: `php artisan migrate --force`

---

## Production Recommendations

### 1. Use Paid Plan
- Free tier sleeps after 15 minutes of inactivity
- First request after sleep takes 30+ seconds

### 2. Configure Email
Update environment variables for real email:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### 3. Add Custom Domain
1. Go to Settings → Custom Domains
2. Add your domain
3. Configure DNS as instructed

### 4. Enable SSL
Render provides free SSL. It's enabled by default.

### 5. Set Up File Storage (S3)
For persistent file storage:
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

---

## Environment Variables Reference

| Variable | Description | Required |
|----------|-------------|----------|
| `APP_KEY` | Laravel encryption key | ✅ |
| `APP_URL` | Your app URL | ✅ |
| `DATABASE_URL` | PostgreSQL connection string | ✅ |
| `APP_ENV` | `production` | ✅ |
| `APP_DEBUG` | `false` for production | ✅ |
| `MAIL_*` | Email configuration | For reports |
| `GOOGLE_PLACES_API_KEY` | For address autocomplete | Optional |
| `TWILIO_*` | For SMS notifications | Optional |

---

## Support

If you encounter issues:
1. Check Render's [documentation](https://render.com/docs)
2. Review Laravel's [deployment guide](https://laravel.com/docs/deployment)
3. Check the application logs in Render dashboard
