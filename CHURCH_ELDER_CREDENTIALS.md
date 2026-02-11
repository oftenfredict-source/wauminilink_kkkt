# Church Elder Login Credentials

## Sample Church Elder Accounts

The following church elder accounts have been created for testing purposes:

### Elder 1: Elder John Mwangi
- **Email:** elder.john@waumini.com
- **Password:** elder123
- **Member ID:** 202603Z65-WL
- **Assigned Community:** Sifa
- **Role:** Church Elder

### Elder 2: Elder Mary Kamau
- **Email:** elder.mary@waumini.com
- **Password:** elder123
- **Member ID:** 202604B00-WL
- **Assigned Community:** Ufeso
- **Role:** Church Elder

### Elder 3: Elder Peter Ochieng
- **Email:** elder.peter@waumini.com
- **Password:** elder123
- **Member ID:** 202605B37-WL
- **Assigned Community:** Yeriko
- **Role:** Church Elder

---

## How to Use

1. **Login:** Go to the login page and use any of the email addresses above with password `elder123`
2. **Dashboard:** After login, you will be automatically redirected to the Church Elder Dashboard
3. **Features Available:**
   - View community information
   - View service attendance records
   - Record offerings for the community
   - Generate reports with date filtering

## Security Note

⚠️ **Important:** These are sample credentials for testing. Please change passwords after first login for security purposes.

## Creating More Elders

To create additional church elder accounts, run:

```bash
php artisan db:seed --class=ChurchElderSeeder
```

Or modify the seeder file at `database/seeders/ChurchElderSeeder.php` to add more elders.













