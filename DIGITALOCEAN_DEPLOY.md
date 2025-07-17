# DigitalOcean App Platform Deployment Guide

## Prerequisites
- DigitalOcean account
- GitHub repository (already set up)
- Database SQL file ready for import

## Step 1: Create a New App

1. **Login to DigitalOcean**
   - Go to: https://cloud.digitalocean.com
   - Navigate to "Apps" in the left sidebar
   - Click "Create App"

2. **Connect GitHub Repository**
   - Choose "GitHub" as source
   - Select your repository: `balirwaalvin/Pharmacy-X`
   - Choose branch: `main`
   - Auto-deploy: Enable (recommended)

## Step 2: Configure App Settings

### Service Configuration
- **Service Type**: Web Service
- **Environment**: PHP
- **Build Command**: (leave empty or use `composer install --no-dev`)
- **Run Command**: `apache2-foreground`
- **HTTP Port**: 8080
- **Instance Size**: Basic ($5/month)

### Environment Variables
Set these in the App Platform dashboard:

```
DB_HOST=your-database-host
DB_USER=your-database-user
DB_PASS=your-database-password
DB_NAME=your-database-name
APP_ENV=production
```

## Step 3: Add Database

1. **Create Managed Database**
   - In your app settings, go to "Database"
   - Click "Attach Database"
   - Choose "Create and attach new database"
   - **Engine**: MySQL 8
   - **Size**: Basic ($15/month for smallest)
   - **Name**: pharmacy-db

2. **Database will auto-populate environment variables**
   - DATABASE_URL will be automatically set
   - Our config.php handles this automatically

## Step 4: Import Database

After deployment:

1. **Get Database Connection Details**
   - Go to your database in DigitalOcean dashboard
   - Note the connection details

2. **Connect and Import**
   - Use MySQL Workbench, phpMyAdmin, or command line
   - Import your `Database/PharmacyX_DB.sql` file

   ```bash
   mysql -h your-host -P your-port -u your-user -p your-database < Database/PharmacyX_DB.sql
   ```

## Step 5: Configure Domain (Optional)

1. **Custom Domain**
   - Go to your app settings
   - Click "Domains"
   - Add your custom domain
   - Update DNS records as instructed

## Step 6: SSL Certificate

- DigitalOcean automatically provides SSL certificates
- Your site will be available at `https://your-app-name.ondigitalocean.app`

## Troubleshooting

### Build Failures
- Ensure `composer.json` and `composer.lock` are in your repository
- Check build logs in the DigitalOcean dashboard

### Database Connection Issues
- Verify environment variables are set correctly
- Check database credentials in the DigitalOcean dashboard
- Ensure database is running and accessible

### File Upload Issues
- Upload directories are created automatically
- Check file permissions if uploads fail

## Estimated Costs

- **Basic App**: $5/month
- **Basic Database**: $15/month
- **Total**: ~$20/month

## Environment Variables Reference

```
# Database (automatically set when using managed database)
DATABASE_URL=mysql://user:pass@host:port/database

# Or individual variables
DB_HOST=your-database-host
DB_USER=your-database-user
DB_PASS=your-database-password
DB_NAME=your-database-name
DB_PORT=25060

# Application
APP_ENV=production
```

## Files Added for DigitalOcean

- ✅ `composer.lock` - Dependency lock file
- ✅ `.do/app.yaml` - App Platform specification
- ✅ `build.sh` - Build script
- ✅ Updated `Dockerfile` - Container configuration
- ✅ `.htaccess` - Apache configuration
- ✅ Updated `db_Config/config.php` - Environment variable support

Your app should now deploy successfully on DigitalOcean App Platform!
