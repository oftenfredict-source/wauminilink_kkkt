# Campus System Implementation Guide

## Overview

This document describes the implementation of a hierarchical campus system for the WauminiLink church management system. The system supports a main campus and multiple sub campuses, with each campus able to register and manage their own members.

## Features Implemented

### 1. Campus Hierarchy
- **Main Campus**: The primary campus (only one allowed)
- **Sub Campuses**: Multiple sub campuses that belong to a main campus
- Hierarchical relationship maintained through `parent_id` foreign key

### 2. Member Registration
- Members are associated with a campus via `campus_id`
- Automatic campus assignment based on user's campus
- Manual campus selection during member registration

### 3. Access Control
- Users can be assigned to a campus
- Main campus users can see all sub campuses
- Sub campus users can only see their own campus
- Members inherit campus from their user account

## Database Structure

### Tables Created

#### `campuses` Table
- `id`: Primary key
- `name`: Campus name
- `code`: Unique campus code (MAIN for main campus, SUB-001, SUB-002, etc. for sub campuses)
- `description`: Optional description
- `address`: Physical address
- `region`, `district`, `ward`: Location fields
- `phone_number`, `email`: Contact information
- `parent_id`: Foreign key to parent campus (null for main campus)
- `is_main_campus`: Boolean flag (true for main campus)
- `is_active`: Boolean flag for active/inactive status
- `timestamps`: Created/updated timestamps
- `deleted_at`: Soft delete timestamp

#### Modified Tables

**`members` Table**
- Added `campus_id`: Foreign key to campuses table

**`users` Table**
- Added `campus_id`: Foreign key to campuses table (for access control)

## Files Created

### Migrations
1. `2025_12_25_100000_create_campuses_table.php` - Creates campuses table
2. `2025_12_25_100001_add_campus_id_to_members_table.php` - Adds campus_id to members
3. `2025_12_25_100002_add_campus_id_to_users_table.php` - Adds campus_id to users

### Models
1. `app/Models/Campus.php` - Campus model with relationships and helper methods

### Controllers
1. `app/Http/Controllers/CampusController.php` - CRUD operations for campuses

### Views
1. `resources/views/campuses/index.blade.php` - List all campuses
2. `resources/views/campuses/create.blade.php` - Create new campus form
3. `resources/views/campuses/edit.blade.php` - Edit campus form
4. `resources/views/campuses/show.blade.php` - View campus details

### Modified Files
1. `app/Models/Member.php` - Added campus relationship and campus_id to fillable
2. `app/Models/User.php` - Added campus relationship and getCampus() method
3. `app/Http/Controllers/MemberController.php` - Updated to handle campus assignment
4. `routes/web.php` - Added campus routes

## Usage Instructions

### Step 1: Run Migrations

```bash
php artisan migrate
```

This will create the campuses table and add campus_id columns to members and users tables.

### Step 2: Create Main Campus

1. Navigate to **Campuses** in the admin menu
2. Click **Add New Campus**
3. Select **Main Campus** as the type
4. Fill in the campus details:
   - Name (required)
   - Description (optional)
   - Address and location details
   - Contact information
5. Click **Create Campus**

**Note**: Only one main campus can exist in the system.

### Step 3: Create Sub Campuses

1. Navigate to **Campuses** in the admin menu
2. Click **Add New Campus**
3. Select **Sub Campus** as the type
4. Select the **Parent Campus** (main campus)
5. Fill in the sub campus details
6. Click **Create Campus**

You can create multiple sub campuses under the same main campus.

### Step 4: Assign Users to Campuses

When creating or editing users (especially admins, pastors, secretaries):
1. Assign them to a specific campus via the `campus_id` field
2. Main campus users can manage all campuses
3. Sub campus users can only manage their own campus

### Step 5: Register Members

When registering new members:
1. The system will automatically assign the member to:
   - The user's campus (if the registering user has a campus)
   - The main campus (if no user campus is set)
2. You can manually select a different campus during registration
3. The member's user account will inherit the same campus

## Access Control Logic

### For Main Campus Users
- Can view and manage all campuses (main + sub campuses)
- Can register members to any campus
- Can see all members across all campuses

### For Sub Campus Users
- Can only view and manage their own campus
- Can only register members to their own campus
- Can only see members from their own campus

### For Members
- Members belong to a specific campus
- Their user account inherits the campus from the member record

## API Endpoints

### Campus Management
- `GET /campuses` - List all campuses
- `GET /campuses/create` - Show create form
- `POST /campuses` - Store new campus
- `GET /campuses/{campus}` - Show campus details
- `GET /campuses/{campus}/edit` - Show edit form
- `PUT /campuses/{campus}` - Update campus
- `DELETE /campuses/{campus}` - Delete campus (soft delete)

### JSON API
- `GET /campuses-json` - Get campuses as JSON (for dropdowns, filtered by user's campus)

## Recommendations for Further Enhancement

### 1. Campus-Specific Settings
- Each campus could have its own settings (SMS templates, financial settings, etc.)
- Add `settings` JSON column to campuses table

### 2. Campus Reports
- Generate reports filtered by campus
- Compare statistics across campuses
- Aggregate reports for main campus (including sub campuses)

### 3. Campus Transfer
- Allow transferring members between campuses
- Maintain transfer history/audit log

### 4. Campus Admins
- Designate campus administrators
- Campus-specific permissions and roles

### 5. Financial Separation
- Track finances per campus
- Campus-specific budgets and expenses
- Financial reports per campus

### 6. Attendance Tracking
- Track attendance per campus
- Campus-specific services and events

### 7. Member Filtering in Views
- Update member list views to filter by campus
- Add campus filter dropdown in member management pages

### 8. Dashboard Statistics
- Show campus-specific statistics on dashboards
- Main campus dashboard shows aggregated data

## Migration Strategy for Existing Data

If you have existing members without campuses:

1. **Create Main Campus First**
   ```sql
   INSERT INTO campuses (name, code, is_main_campus, is_active, created_at, updated_at)
   VALUES ('Main Campus', 'MAIN', true, true, NOW(), NOW());
   ```

2. **Assign Existing Members to Main Campus**
   ```sql
   UPDATE members 
   SET campus_id = (SELECT id FROM campuses WHERE code = 'MAIN' LIMIT 1)
   WHERE campus_id IS NULL;
   ```

3. **Assign Existing Users to Main Campus**
   ```sql
   UPDATE users 
   SET campus_id = (SELECT id FROM campuses WHERE code = 'MAIN' LIMIT 1)
   WHERE campus_id IS NULL;
   ```

## Troubleshooting

### Issue: Cannot create main campus (already exists)
**Solution**: Only one main campus is allowed. If you need to change it, edit the existing main campus or delete it first.

### Issue: Cannot delete campus with members
**Solution**: Transfer members to another campus first, then delete the campus.

### Issue: Members not showing in campus
**Solution**: 
1. Check that members have `campus_id` set
2. Verify the user's campus assignment
3. Check access control permissions

### Issue: Sub campus not showing parent
**Solution**: Ensure `parent_id` is set correctly and the parent campus exists.

## Security Considerations

1. **Access Control**: Implement middleware to restrict campus access based on user's campus
2. **Data Isolation**: Ensure users can only access data from their assigned campus
3. **Audit Logging**: Log campus assignments and transfers for audit purposes
4. **Validation**: Validate campus assignments during member/user creation

## Testing Checklist

- [ ] Create main campus
- [ ] Create sub campus
- [ ] Register member to main campus
- [ ] Register member to sub campus
- [ ] Assign user to campus
- [ ] Test access control (main vs sub campus users)
- [ ] Edit campus details
- [ ] Deactivate/activate campus
- [ ] Delete campus (with and without members)
- [ ] View campus statistics
- [ ] Filter members by campus

## Support

For issues or questions about the campus system implementation, refer to:
- Campus model: `app/Models/Campus.php`
- Campus controller: `app/Http/Controllers/CampusController.php`
- Routes: `routes/web.php` (search for "campuses")

---

**Implementation Date**: December 25, 2025
**Version**: 1.0














