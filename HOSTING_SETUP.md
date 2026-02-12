# InfinityFree Hosting Setup Guide

## 📋 Prerequisites

Before deploying to InfinityFree, make sure you have:
- InfinityFree account created
- FTP credentials (from InfinityFree control panel)
- MySQL database created in cPanel

---

## Step 1: Create MySQL Database

1. **Log into InfinityFree Control Panel**
   - Go to your InfinityFree account dashboard
   - Click on "Control Panel" or "cPanel"

2. **Create Database**
   - Find "MySQL Databases" in cPanel
   - Click "Create Database"
   - Database name will be auto-prefixed (e.g., `epiz_XXXXX_qrify_db`)
   - Note down the full database name

3. **Create Database User**
   - In the same MySQL Databases section
   - Create a new user with a strong password
   - Username will be auto-prefixed (e.g., `epiz_XXXXX_user`)
   - Note down the username and password

4. **Add User to Database**
   - In "Add User to Database" section
   - Select your user and database
   - Grant ALL PRIVILEGES

5. **Note Your Database Host**
   - Usually shown in the MySQL Databases page
   - Format: `sqlXXX.infinityfreeapp.com` or `localhost`
   - **Important:** InfinityFree often uses a specific SQL server hostname

---

## Step 2: Update Configuration File

1. **Open `api/config.php`** in your local project

2. **Update Database Credentials:**

```php
// Database Configuration
define('DB_HOST', 'sqlXXX.infinityfreeapp.com'); // ← Your InfinityFree DB host
define('DB_NAME', 'epiz_XXXXX_qrify_db');        // ← Your full database name
define('DB_USER', 'epiz_XXXXX_user');            // ← Your database username
define('DB_PASS', 'your_password_here');         // ← Your database password
```

3. **Update Environment Settings:**

```php
// Environment
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false); // Set to false in production for security
```

---

## Step 3: Upload Files via FTP

1. **Get FTP Credentials**
   - From InfinityFree control panel
   - Note: Hostname, Username, Password, Port (usually 21)

2. **Connect via FTP Client** (FileZilla recommended)
   - Host: `ftpupload.net` or your specific FTP host
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21

3. **Upload Files**
   - Navigate to `/htdocs/` folder on the server
   - Upload ALL your project files:
     - `home.php`
     - `payment.php`
     - `extrafood.php`
     - `index.php`
     - `api/` folder (all files)
     - `images/` folder
     - `*.js` files
     - `test_menu_api.php` ← Important for diagnostics!

---

## Step 4: Import Database

1. **Access phpMyAdmin**
   - From InfinityFree control panel
   - Click "phpMyAdmin"

2. **Select Your Database**
   - Click on your database name in the left sidebar

3. **Import SQL File**
   - Click "Import" tab
   - Click "Choose File"
   - Select `database_setup.sql` from your local project
   - Click "Go" at the bottom
   - Wait for success message

---

## Step 5: Test Your Setup

### 5.1 Run Diagnostic Tool

1. **Visit the diagnostic page:**
   ```
   https://your-domain.infinityfreeapp.com/test_menu_api.php
   ```

2. **Check All Tests:**
   - ✓ Configuration File exists
   - ✓ Database connection successful
   - ✓ menu_items table exists
   - ✓ Menu data loaded (should show item count)
   - ✓ API endpoint working

3. **If Any Test Fails:**
   - Read the error message carefully
   - Common issues:
     - **Wrong DB credentials** → Double-check `api/config.php`
     - **Table doesn't exist** → Re-import `database_setup.sql`
     - **Empty database** → Make sure SQL import completed successfully

### 5.2 Test Menu Display

1. **Visit your home page:**
   ```
   https://your-domain.infinityfreeapp.com/home.php
   ```

2. **Verify:**
   - Categories appear below search bar
   - Menu items display with images
   - Add to cart works
   - Search functionality works

### 5.3 Check Browser Console (If Issues Persist)

1. **Open Developer Tools** (F12)
2. **Check Console Tab** for JavaScript errors
3. **Check Network Tab:**
   - Look for `get_menu.php` request
   - Check if it returns 200 status
   - View response to see if data is returned

---

## Common InfinityFree Issues & Solutions

### Issue 1: "Database connection failed"

**Cause:** Wrong database credentials

**Solution:**
1. Verify DB_HOST is correct (check cPanel MySQL section)
2. Ensure database name includes the prefix (e.g., `epiz_XXXXX_`)
3. Ensure username includes the prefix
4. Check password is correct (no extra spaces)

### Issue 2: "menu_items table does not exist"

**Cause:** Database not imported

**Solution:**
1. Go to phpMyAdmin
2. Select your database
3. Import `database_setup.sql`
4. Refresh diagnostic page

### Issue 3: "No menu items found"

**Cause:** Database is empty or import failed

**Solution:**
1. Check phpMyAdmin → your database → menu_items table
2. Click "Browse" to see if data exists
3. If empty, re-import `database_setup.sql`
4. Make sure you selected the correct database before importing

### Issue 4: Images not showing

**Cause:** Images folder not uploaded or wrong path

**Solution:**
1. Verify `images/` folder is uploaded to `/htdocs/images/`
2. Check image filenames match database entries
3. Ensure images are web-compatible formats (jpg, png, webp)

### Issue 5: 500 Internal Server Error

**Cause:** PHP errors or file permissions

**Solution:**
1. Check `error_log.txt` in your root directory
2. Set file permissions to 644 for PHP files
3. Set folder permissions to 755
4. Enable error display temporarily in `api/config.php`:
   ```php
   define('DEBUG_MODE', true);
   ```

---

## Security Checklist (Production)

Before going live:

- [ ] Set `DEBUG_MODE` to `false` in `api/config.php`
- [ ] Use strong database password
- [ ] Update `ALLOWED_ORIGINS` in `api/config.php` to your domain
- [ ] Delete `test_menu_api.php` after setup (or password-protect it)
- [ ] Verify SMTP credentials are correct for email OTP
- [ ] Test the entire user flow (browse → add to cart → verify email → payment)

---

## Need Help?

If you're still experiencing issues after following this guide:

1. **Check the diagnostic tool** at `/test_menu_api.php`
2. **Review error messages** in browser console (F12)
3. **Check `error_log.txt`** in your root directory
4. **Verify all files uploaded** correctly via FTP

---

## Quick Reference: File Locations

```
/htdocs/
├── home.php
├── payment.php
├── extrafood.php
├── index.php
├── test_menu_api.php
├── app.js
├── auth.js
├── otp-api.js
├── api/
│   ├── config.php          ← Update this with InfinityFree credentials
│   ├── db.php
│   ├── get_menu.php
│   ├── send-otp-email.php
│   ├── verify-otp-email.php
│   └── place_order.php
└── images/
    └── (all menu images)
```
