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
- **Build Command**: `./build.sh` (or leave empty)
- **Run Command**: `apache2-foreground`
- **HTTP Port**: 8080 (DigitalOcean App Platform standard for PHP)
- **Instance Size**: Basic ($5/month)

### Important: Port Configuration
- DigitalOcean App Platform expects PHP applications to run on port 8080
- Do NOT specify `http_port` in app.yaml (let it use default 8080)
- Our configuration automatically sets up Apache for port 8080

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

**IMPORTANT**: You must add a database and set environment variables for the app to work.

1. **Create Managed Database**
   - In your DigitalOcean dashboard, go to "Databases"
   - Click "Create Database Cluster"
   - **Engine**: MySQL 8
   - **Size**: Basic ($15/month for smallest)
   - **Name**: pharmacy-db
   - **Wait for creation** (takes 5-10 minutes)

2. **Connect Database to App**
   - Go to your app settings in DigitalOcean
   - Click "Database"
   - Click "Attach Database"
   - Select your "pharmacy-db" database
   - This will automatically set DATABASE_URL environment variable

3. **Verify Environment Variables**
   - In your app settings, go to "Settings" → "App-Level Environment Variables"
   - You should see DATABASE_URL automatically added
   - Format: `mysql://username:password@host:port/database`

## Step 4: Import Database

**CRITICAL**: Your app won't work until you import the database schema.

1. **Get Database Connection Details**
   - Go to your database in DigitalOcean dashboard
   - Click "Connection Details"
   - Note: Host, Port, Username, Password, Database name

2. **Connect and Import using MySQL Client**
   ```bash
   mysql -h your-host -P your-port -u your-user -p your-database < Database/PharmacyX_DB.sql
   ```

3. **Or use phpMyAdmin/MySQL Workbench**
   - Connect using the connection details
   - Import your `Database/PharmacyX_DB.sql` file
   - Verify tables are created

4. **Alternative: Import via DigitalOcean Console**
   - Go to your database cluster
   - Click "Console" tab
   - Upload and run your SQL file

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

### Port Mismatch Issues
- DigitalOcean App Platform expects PHP apps on port 8080 by default
- Do NOT specify `http_port` in app.yaml - let it use the default
- Our Dockerfile and Apache config are set for port 8080
- If you see port mismatch errors, ensure no `http_port` is specified in app.yaml

### Apache Configuration Warnings
- The ServerName warning is handled automatically by our configuration
- Check that `apache.conf` and `start.sh` are in your repository

### Database Connection Issues
- **Error "No such file or directory"**: Database not attached to app
  - Solution: Attach managed database in app settings
  - Verify DATABASE_URL environment variable is set
- **Error "Connection refused"**: Wrong host/port or database not running
  - Check database cluster status in DigitalOcean dashboard
  - Verify connection details match DATABASE_URL
- **Error "Access denied"**: Wrong username/password
  - Check DATABASE_URL format: `mysql://user:pass@host:port/db`
  - Verify database user has proper permissions
- **Error "Unknown database"**: Database schema not imported
  - Import PharmacyX_DB.sql file to your database
  - Check database name in connection string

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
