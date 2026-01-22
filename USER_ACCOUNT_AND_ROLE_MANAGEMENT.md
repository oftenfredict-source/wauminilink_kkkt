# User Account and Role Management Approach

## 🎯 Overview

The system uses a **single user account with role-based access control (RBAC)** instead of creating multiple accounts for the same person. This approach ensures one account per user, consistent user history, improved security, easier system management, and a better user experience.

---

## 🔑 Core Principles

### One Account Per User

When a person is registered as a church member, they are given **one account** with login credentials (username/email and password). By default, the user is assigned the role of **Member**, which allows them to access only their personal information and submitted service requests.

### Role Updates, Not New Accounts

If the same person is later appointed to a leadership position such as Secretary, Pastor, Elder, or Administrator, the system **does not create a new account**. Instead, the existing account is updated by:

- **Assigning additional roles** to the user, OR
- **Changing the user's role** to reflect their new position

### Dynamic Access Control

Using the same login credentials, the user will automatically gain access to leadership or pastoral features according to their assigned role(s). The system dynamically displays the appropriate dashboard and permissions based on the user's role.

---

## ✅ Benefits of This Approach

1. **One Account Per User**
   - Eliminates confusion from multiple accounts
   - Simplifies password management
   - Reduces account maintenance overhead

2. **Consistent User History and Records**
   - All user activities are tracked under one account
   - Complete audit trail of user actions
   - Easier to track member progression from member to leader

3. **Improved Security**
   - Fewer accounts to manage and secure
   - Reduced risk of orphaned or forgotten accounts
   - Centralized security controls

4. **Easier System Management**
   - Administrators can easily update user roles
   - No need to merge or delete duplicate accounts
   - Simplified user administration

5. **Better User Experience**
   - Users remember only one set of credentials
   - Seamless transition when roles change
   - No need to switch between multiple accounts

---

## 👥 Supported Roles

The system supports the following roles:

### 1. **Member**
- **Default role** for all registered church members
- Access to personal information
- Can submit service requests
- Can view own records (tithes, offerings, attendance)

### 2. **Secretary**
- Can manage member records
- Can register new members
- Can manage leadership positions
- Can access secretary-specific features

### 3. **Pastor**
- Full access to pastoral features
- Can approve financial transactions
- Can manage all church operations
- Can access pastor dashboard

### 4. **Elder**
- Can manage community assignments
- Can view community-specific data
- Can access elder-specific features

### 5. **Administrator**
- Full system access
- Can manage all users and roles
- Can configure system settings
- Can access admin dashboard

---

## 🔄 Role Assignment Workflow

### Scenario 1: New Member Registration

1. **Member is registered** in the system
2. **User account is automatically created** with:
   - Username/Email: Member ID or provided email
   - Password: Generated automatically (or lastname)
   - Role: **Member** (default)
   - Member ID: Linked to the member record

3. **Member receives credentials** via SMS or email
4. **Member can login** and access member features

### Scenario 2: Member Appointed to Leadership

1. **Administrator assigns leadership position**:
   - Selects the member
   - Assigns leadership role (Secretary, Pastor, Elder, etc.)
   - System automatically creates or updates user account
2. **System automatically handles user account**:
   - **If account exists**: Updates role to match leadership position
   - **If account doesn't exist**: Creates new account with default credentials
   - Default credentials:
     - **Username**: Member ID
     - **Password**: Lastname (in uppercase)
   - Grants appropriate permissions based on role
   - Maintains all existing user history (if account existed)
3. **Login credentials sent via SMS**:
   - Member receives SMS with appointment notification
   - SMS includes login credentials (username and password)
   - Member can immediately login with default credentials
4. **User logs in with default credentials**:
   - Automatically sees leadership dashboard
   - Has access to leadership features
   - Can still access member features
   - **Recommended**: Change password after first login

### Scenario 3: Multiple Roles

The system supports **multiple roles per user**, allowing one person to act as both a Member and a Church Leader when required. For example:

- A Pastor who is also a Member
- A Secretary who is also an Elder
- An Administrator who is also a Member

---

## 🔐 Permission System

Each role has defined permissions that control what the user can view or manage within the system. Permissions are organized by category:

- **Members**: View, create, edit, delete members
- **Leaders**: Manage leadership positions
- **Finance**: Financial records, budgets, approvals
- **Services**: Sunday services, attendance
- **Settings**: System configuration
- **Reports**: View and export reports
- **Analytics**: System analytics

### Permission Inheritance

- **Administrator**: Has all permissions by default
- **Pastor**: Has most permissions, can approve finances
- **Secretary**: Can manage members and leaders
- **Treasurer**: Can manage financial records
- **Member**: Limited to personal information

---

## 📋 Implementation Details

### User Account Creation

#### When a Member is Registered:

```php
// User account is automatically created
$user = User::create([
    'name' => $member->full_name,
    'email' => $member->member_id, // Member ID as username
    'password' => Hash::make($lastname), // Lastname in uppercase
    'role' => 'member', // Default role
    'member_id' => $member->id,
    'campus_id' => $member->campus_id,
]);
```

#### When a Leadership Position is Assigned:

The system **automatically** creates or updates the user account:

```php
// System automatically creates/updates user account
// If account exists: Updates role
// If account doesn't exist: Creates new account with default credentials

// Position to Role Mapping:
- pastor, assistant_pastor → 'pastor'
- secretary, assistant_secretary → 'secretary'
- treasurer, assistant_treasurer → 'treasurer'
- elder → 'elder'
- evangelism_leader → 'evangelism_leader'

// Default Credentials:
- Username: member_id
- Password: lastname (uppercase)
```

**No manual account creation required!** The system handles everything automatically when a leadership position is assigned.

### Multiple Roles Support

The system checks multiple roles when determining permissions:

```php
// User can have implicit roles based on leadership positions
$rolesToCheck = [$user->role];

// Add implicit roles based on leadership positions
if ($user->isEvangelismLeader()) {
    $rolesToCheck[] = 'evangelism_leader';
}

if ($user->isChurchElder()) {
    $rolesToCheck[] = 'elder';
}
```

---

## 🎨 Dashboard Access

The system automatically redirects users to the appropriate dashboard based on their role:

- **Member**: Member dashboard (personal information)
- **Secretary**: Secretary dashboard (member management)
- **Pastor**: Pastor dashboard (full church operations)
- **Elder**: Elder dashboard (community management)
- **Administrator**: Admin dashboard (system administration)

### Branch Users

Users assigned to a branch (sub-campus) are automatically redirected to the **Branch Dashboard**, which shows only their branch's data.

---

## 🔍 User Account Linking

### Member-User Relationship

Each user account can be linked to a member record:

- **One-to-One Relationship**: One user account per member
- **Member ID**: Links user to member record
- **Automatic Creation**: User account created when member is registered

### Benefits of Linking

- **Unified Records**: Member data and user account are connected
- **Role Updates**: Leadership positions automatically update user role
- **History Tracking**: All activities tracked under one account
- **Data Consistency**: Member and user data stay synchronized

---

## 📝 Best Practices

### For Administrators

1. **Assign leadership positions** - The system automatically creates/updates user accounts
2. **No manual account creation needed** - When you assign a leadership position, the account is created automatically
3. **Login credentials are sent via SMS** - Members receive their credentials automatically
4. **Review user roles** periodically to ensure accuracy
5. **Document role changes** for audit purposes

**Important**: You don't need to manually create user accounts for leaders. Simply assign the leadership position, and the system handles the rest!

### For Users

1. **Use one account** for all church-related activities
2. **Contact administrator** if you need role updates
3. **Keep credentials secure** and don't share with others
4. **Report any issues** with account access immediately

---

## 🚨 Common Scenarios

### Scenario: Member Becomes Pastor

**Process:**
1. Administrator assigns "Pastor" position to the member
2. System automatically:
   - Creates user account (if doesn't exist) OR updates existing account
   - Sets role to `pastor`
   - Sends SMS with login credentials (member_id as username, lastname as password)
3. Member receives SMS with credentials
4. Member logs in with default credentials
5. Member has access to pastor dashboard
6. All previous history maintained (if account existed)

### Scenario: Pastor Also Serves as Secretary

**Solution:**
- User account can have role: `pastor`
- System checks leadership positions for additional roles
- User has access to both pastor and secretary features

### Scenario: User Forgot Password

**Solution:**
- Password reset uses the same account
- Role and permissions remain unchanged
- User can reset password via email/SMS

---

## 🔧 Technical Implementation

### Database Structure

- **users table**: Stores user accounts
  - `id`: Primary key
  - `email`: Username/email for login
  - `password`: Hashed password
  - `role`: User's primary role
  - `member_id`: Link to member record (optional)
  - `campus_id`: Branch/campus assignment

- **role_permissions table**: Maps permissions to roles
  - `role`: Role name
  - `permission_id`: Permission reference

- **permissions table**: Defines all system permissions
  - `slug`: Permission identifier
  - `name`: Human-readable name
  - `category`: Permission category

### Role Checking Methods

The system provides helper methods to check user roles:

```php
$user->isAdmin()           // Check if admin
$user->isPastor()          // Check if pastor
$user->isSecretary()        // Check if secretary
$user->isMember()          // Check if member
$user->isChurchElder()     // Check if elder
$user->hasPermission($slug) // Check specific permission
```

---

## 📚 Related Documentation

- [Permissions Guide](./PERMISSIONS_GUIDE.md) - Detailed permissions configuration
- [Admin Setup](./ADMIN_SETUP.md) - Administrator account setup
- [Branch Dashboard Guide](./HOW_TO_ACCESS_BRANCH_DASHBOARD.md) - Branch user access

---

## ❓ Frequently Asked Questions

### Q: Can a user have multiple roles at the same time?

**A:** Yes, the system supports multiple roles per user. A user can be both a Member and a Pastor, for example.

### Q: What happens if I create a duplicate account for the same person?

**A:** The system will prevent duplicate accounts based on email/member_id. Always check for existing accounts before creating new ones.

### Q: Do I need to manually create user accounts for leaders?

**A:** No! When you assign a leadership position to a member, the system automatically creates or updates their user account. Login credentials are sent via SMS.

### Q: What are the default login credentials?

**A:** Default credentials are:
- **Username**: Member ID
- **Password**: Lastname (in uppercase)

These are sent via SMS when a leadership position is assigned.

### Q: Can I change a user's role after account creation?

**A:** Yes, administrators can update user roles at any time. The user will immediately have access to features for their new role. Alternatively, you can update the leadership position, and the system will automatically update the user role.

### Q: What if a member loses their leadership position?

**A:** The administrator can update the user's role back to "member". The account remains active, but access is limited to member features.

### Q: How do I know what permissions each role has?

**A:** Check the Roles & Permissions page in the admin panel, or refer to the [Permissions Guide](./PERMISSIONS_GUIDE.md).

---

## 📞 Support

For questions or issues regarding user accounts and role management:

1. Contact your system administrator
2. Refer to the [Permissions Guide](./PERMISSIONS_GUIDE.md)
3. Check the admin dashboard for role management options

---

**Last Updated:** December 2024

