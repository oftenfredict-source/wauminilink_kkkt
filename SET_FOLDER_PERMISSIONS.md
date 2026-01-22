# How to Set Folder Permissions for Passport Images

## 🎯 Goal
Set permissions to **755** for the folder `public/assets/images/members/profile-pictures/` so that:
- ✅ Laravel can **write** (upload) images to the folder
- ✅ Web server can **read** (display) images from the folder
- ✅ Files are **accessible** via browser

---

## Method 1: Via cPanel File Manager (Easiest - Recommended)

### Step 1: Open cPanel File Manager
1. Log into your **cPanel**
2. Find and click **"File Manager"**
3. Navigate to your Laravel project folder

### Step 2: Navigate to the Images Folder
1. Go to: `public/assets/images/`
2. If the folder doesn't exist, create it:
   - Click **"New Folder"**
   - Name it: `assets` (if it doesn't exist)
   - Inside `assets`, create: `images`
   - Inside `images`, create: `members`
   - Inside `members`, create: `profile-pictures`

### Step 3: Set Permissions
1. **Right-click** on the `images` folder
2. Click **"Change Permissions"** (or "File Permissions")
3. Set permissions to **755**:
   - ✅ **Owner (User)**: Read, Write, Execute (7)
   - ✅ **Group**: Read, Execute (5)
   - ✅ **Public (Others)**: Read, Execute (5)
4. Check **"Recurse into subdirectories"** (important!)
5. Click **"Change Permissions"**

### Step 4: Verify
- The folder should now show permissions: **755**
- All subfolders should also have **755**

---

## Method 2: Via SSH (Command Line)

### Step 1: Connect to Your Server
```bash
ssh username@your-server-ip
# or
ssh username@wauminilink.co.tz
```

### Step 2: Navigate to Your Project
```bash
cd /path/to/your/laravel/project
# Example: cd /home/username/public_html/demo
# or: cd /var/www/html/demo
```

### Step 3: Create Folder (If It Doesn't Exist)
```bash
mkdir -p public/assets/images/members/profile-pictures
```

### Step 4: Set Permissions
```bash
# Set permissions for the entire images directory tree
chmod -R 755 public/assets/images

# Verify permissions
ls -la public/assets/images/
ls -la public/assets/images/members/
ls -la public/assets/images/members/profile-pictures/
```

### Step 5: Set Ownership (If Needed)
If you get permission errors, you may also need to set ownership:
```bash
# Replace 'www-data' with your web server user (common: www-data, apache, nginx)
chown -R www-data:www-data public/assets/images

# Or if you're not sure of the web server user, use:
chown -R $(whoami):$(whoami) public/assets/images
```

---

## Method 3: Via FTP Client (FileZilla, etc.)

### Step 1: Connect to Server
1. Open your FTP client (FileZilla, WinSCP, etc.)
2. Connect to your server

### Step 2: Navigate to Folder
Navigate to: `public/assets/images/`

### Step 3: Set Permissions
1. **Right-click** on the `images` folder
2. Select **"File Permissions"** or **"Change Permissions"**
3. Enter: **755**
4. Check **"Recurse into subdirectories"**
5. Click **OK**

---

## 📋 Permission Number Breakdown

### What does 755 mean?

```
7 5 5
│ │ │
│ │ └─ Others (Public): Read + Execute = 4 + 1 = 5
│ └─── Group: Read + Execute = 4 + 1 = 5
└───── Owner (User): Read + Write + Execute = 4 + 2 + 1 = 7
```

**Read (4)**: Can view/list files  
**Write (2)**: Can create/modify/delete files  
**Execute (1)**: Can enter/access the folder

### Why 755?
- **Owner (7)**: Full access (read, write, execute) - Laravel needs this to upload files
- **Group (5)**: Read and execute - Web server can read files
- **Public (5)**: Read and execute - Browser can access images

---

## ✅ Verify Permissions Are Correct

### Check via SSH:
```bash
ls -ld public/assets/images/members/profile-pictures/
```

**Should show:**
```
drwxr-xr-x 2 www-data www-data 4096 Jan 15 10:30 profile-pictures
```
- `drwxr-xr-x` = 755 (directory with correct permissions)

### Check via cPanel:
- Right-click folder → "Change Permissions"
- Should show: **755**

---

## 🔧 Troubleshooting

### Problem: "Permission Denied" when uploading

**Solution:**
```bash
# Make sure folder exists
mkdir -p public/assets/images/members/profile-pictures

# Set correct permissions
chmod -R 755 public/assets/images

# Set correct ownership (replace www-data with your web server user)
chown -R www-data:www-data public/assets/images
```

### Problem: "403 Forbidden" when viewing images

**Solution:**
```bash
# Ensure parent folders also have execute permission
chmod 755 public
chmod 755 public/assets
chmod 755 public/assets/images
chmod 755 public/assets/images/members
chmod 755 public/assets/images/members/profile-pictures
```

### Problem: Can't create folder via cPanel

**Solution:**
1. Check if you have enough disk space
2. Check if parent folder has write permissions
3. Try creating folder via SSH instead

### Problem: Files uploaded but can't be accessed

**Solution:**
```bash
# Set permissions for files (644 = readable by web server)
find public/assets/images/members/profile-pictures -type f -exec chmod 644 {} \;

# Set permissions for folders (755 = accessible)
find public/assets/images/members/profile-pictures -type d -exec chmod 755 {} \;
```

---

## 🎯 Quick Command Reference

### Create folder and set permissions (one command):
```bash
mkdir -p public/assets/images/members/profile-pictures && \
chmod -R 755 public/assets/images && \
chown -R www-data:www-data public/assets/images
```

### Set permissions for existing folder:
```bash
chmod -R 755 public/assets/images
```

### Set ownership (if needed):
```bash
chown -R www-data:www-data public/assets/images
```

### Verify everything:
```bash
ls -la public/assets/images/members/profile-pictures/
```

---

## 📝 Step-by-Step Checklist

- [ ] Navigate to `public/assets/images/` folder
- [ ] Create `members` folder if it doesn't exist
- [ ] Create `profile-pictures` folder inside `members`
- [ ] Set permissions to **755** for `images` folder
- [ ] Apply recursively to all subfolders
- [ ] Verify permissions are correct
- [ ] Test uploading an image
- [ ] Test viewing an uploaded image

---

## ✅ Expected Result

After setting permissions correctly:
- ✅ Laravel can upload images to the folder
- ✅ Images are saved successfully
- ✅ Images display correctly in browser
- ✅ No 403 or 404 errors

---

## 💡 Pro Tip

If you're not sure which method to use:
1. **cPanel File Manager** - Easiest if you have cPanel access
2. **SSH** - Fastest if you have SSH access
3. **FTP Client** - Good if you're already using FTP

All three methods achieve the same result: **755 permissions** on the folder.

