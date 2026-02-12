# Quick Deployment Checklist

## ✅ Database Credentials (From Your Screenshot)

Your InfinityFree database details:
- **Host:** `sql104.infinityfree.com`
- **Database:** `id_41082096_aspord`
- **Username:** `id_41082096`
- **Password:** Your vPanel password (the one you use to login to InfinityFree)

## 📝 Steps to Deploy

### 1. Update Configuration File

**Option A - Use the production config:**
1. Open `api/config.production.php`
2. Replace `YOUR_VPANEL_PASSWORD_HERE` with your actual vPanel password
3. When uploading to InfinityFree, rename it to `config.php` in the `api/` folder

**Option B - Edit existing config:**
1. Open `api/config.php`
2. Update lines 8-11:
   ```php
   define('DB_HOST', 'sql104.infinityfree.com');
   define('DB_NAME', 'id_41082096_aspord');
   define('DB_USER', 'id_41082096');
   define('DB_PASS', 'your_vpanel_password');
   ```

### 2. Import Database

1. Go to your InfinityFree control panel
2. Click **phpMyAdmin**
3. Select database `id_41082096_aspord` from left sidebar
4. Click **Import** tab
5. Choose file: `database_setup.sql`
6. Click **Go**
7. Wait for "Import has been successfully finished" message

### 3. Upload Files via FTP

**FTP Details (from InfinityFree control panel):**
- Host: `ftpupload.net`
- Username: (shown in your FTP accounts)
- Password: (your FTP password)
- Port: 21

**Files to upload to `/htdocs/` folder:**
```
/htdocs/
├── home.php
├── payment.php
├── extrafood.php
├── index.php
├── test_menu_api.php          ← Important for testing!
├── app.js
├── auth.js
├── otp-api.js
├── database_setup.sql
├── api/
│   ├── config.php             ← Updated with InfinityFree credentials
│   ├── db.php
│   ├── get_menu.php
│   ├── send-otp-email.php
│   ├── verify-otp-email.php
│   ├── place_order.php
│   └── PHPMailer/ (folder)
└── images/ (entire folder)
```

### 4. Test Your Setup

**Visit the diagnostic page:**
```
https://your-domain.infinityfreeapp.com/test_menu_api.php
```

**All 5 tests should show ✓:**
- ✓ Configuration file found
- ✓ Database connection successful
- ✓ menu_items table exists
- ✓ Menu data loaded (should show ~50+ items)
- ✓ API endpoint working

### 5. Test Menu Display

**Visit your home page:**
```
https://your-domain.infinityfreeapp.com/home.php
```

**Verify:**
- Categories appear (Veg, Non Veg, Chinese, South, Snacks)
- Menu items display with images
- Search works
- Add to cart works

---

## 🔧 If Something Doesn't Work

### Database Connection Failed?
- Double-check password in `api/config.php`
- Make sure you're using your **vPanel password**, not FTP password
- Verify database name is exactly: `id_41082096_aspord`

### Table Doesn't Exist?
- Re-import `database_setup.sql` via phpMyAdmin
- Make sure you selected the correct database before importing

### Images Not Showing?
- Verify `images/` folder uploaded to `/htdocs/images/`
- Check that image files are web-compatible (jpg, png, webp)

### Still Having Issues?
1. Check `test_menu_api.php` - it will show exactly what's wrong
2. Check browser console (F12) for JavaScript errors
3. Look at `error_log.txt` in your root directory

---

## 🎯 Quick Reference

**Your Database Info:**
```
Host: sql104.infinityfree.com
Database: id_41082096_aspord
Username: id_41082096
Password: [Your vPanel Password]
```

**Test URLs:**
- Diagnostic: `https://your-domain.infinityfreeapp.com/test_menu_api.php`
- Home Page: `https://your-domain.infinityfreeapp.com/home.php`
- API Test: `https://your-domain.infinityfreeapp.com/api/get_menu.php?lang=en`

---

## ✨ After Everything Works

Once the menu displays correctly:
1. Set `DEBUG_MODE` to `false` in `api/config.php`
2. Delete or password-protect `test_menu_api.php`
3. Test the complete user flow (browse → cart → email OTP → payment)
