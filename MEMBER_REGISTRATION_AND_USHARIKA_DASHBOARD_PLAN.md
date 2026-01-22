# Member Registration & Usharika Dashboard - Implementation Plan

## 📋 Requirements Summary

1. **Each member can register other members** (from their own account)
   - Members can only register to their own branch
   - Simplified registration form for members
   - Members cannot see/edit other members (only their own info)

2. **Usharika Dashboard** can see all members from all branches
   - Consolidated view of all branches
   - Branch statistics and comparisons
   - Member overview across all branches

## 🎯 Implementation Plan

### Phase 1: Member Registration Feature ⭐ HIGH PRIORITY

#### 1.1 Add "Register Member" to Member Portal
**Location**: `resources/views/members/dashboard.blade.php`

**Changes**:
- Add new card: "Register New Member"
- Link to member registration page
- Show count of members registered by current member (optional)

**Route**: 
```php
Route::get('/member/register-member', [MemberDashboardController::class, 'showRegisterMember'])
    ->name('member.register-member');
Route::post('/member/register-member', [MemberDashboardController::class, 'storeMember'])
    ->name('member.store-member');
```

#### 1.2 Create Member Registration Form for Members
**Location**: `resources/views/members/register-member.blade.php` (NEW)

**Features**:
- Simplified form (fewer fields than admin form)
- Branch automatically set to member's branch (hidden field)
- Required fields only:
  - Full Name
  - Gender
  - Phone Number
  - Date of Birth
  - Region, District, Ward
  - Tribe
- Optional fields:
  - Email
  - Profession
  - Education Level
- Auto-assign to member's branch
- Show success message with member ID

#### 1.3 Update MemberDashboardController
**Location**: `app/Http/Controllers/MemberDashboardController.php`

**New Methods**:
```php
public function showRegisterMember()
{
    // Get current member's branch
    // Show simplified registration form
    // Branch is auto-selected (member's branch)
}

public function storeMember(Request $request)
{
    // Validate simplified form
    // Auto-assign to current member's branch
    // Create member record
    // Create user account
    // Send welcome SMS
    // Log who registered this member (optional)
}
```

#### 1.4 Access Control
- Members can ONLY register to their own branch
- Members cannot change branch selection
- Members cannot see other members' details
- Members can only see members they registered (optional feature)

### Phase 2: Usharika Dashboard ⭐ HIGH PRIORITY

#### 2.1 Create Usharika Dashboard Controller
**Location**: `app/Http/Controllers/UsharikaDashboardController.php` (NEW)

**Features**:
- Show all members from all branches
- Branch statistics
- Member growth trends
- Branch comparison charts
- Recent registrations across all branches

#### 2.2 Create Usharika Dashboard View
**Location**: `resources/views/usharika/dashboard.blade.php` (NEW)

**Sections**:
1. **Overview Cards**:
   - Total Members (All Branches)
   - Total Branches
   - New Members This Month
   - Active Members

2. **Branch Statistics Table**:
   - Branch Name
   - Total Members
   - New Members (This Month)
   - Growth Rate
   - Last Registration Date

3. **Recent Registrations**:
   - List of recently registered members
   - Show which branch
   - Show who registered them (if tracked)

4. **Branch Comparison Chart**:
   - Bar chart comparing member counts
   - Line chart showing growth trends

5. **Member List** (Filterable):
   - All members from all branches
   - Filter by branch
   - Search functionality
   - Export option

#### 2.3 Add Route
```php
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/usharika/dashboard', [UsharikaDashboardController::class, 'index'])
        ->name('usharika.dashboard')
        ->middleware(function($request, $next) {
            // Only allow if user is from main campus (Usharika)
            $userCampus = auth()->user()->getCampus();
            if (!$userCampus || !$userCampus->is_main_campus) {
                abort(403, 'Only Usharika administrators can access this dashboard.');
            }
            return $next($request);
        });
});
```

#### 2.4 Add to Sidebar
**Location**: `resources/views/layouts/index.blade.php`

- Add "Usharika Dashboard" menu item
- Only visible to Usharika (main campus) users
- Place in Administration section

### Phase 3: Enhanced Features ⭐ MEDIUM PRIORITY

#### 3.1 Track Who Registered Each Member
**Database Migration**:
```php
Schema::table('members', function (Blueprint $table) {
    $table->foreignId('registered_by_member_id')->nullable()
        ->after('campus_id')
        ->constrained('members')
        ->onDelete('set null');
    $table->timestamp('registered_at')->nullable()->after('created_at');
});
```

**Benefits**:
- Track which member registered which member
- Show registration history
- Analytics on member recruitment

#### 3.2 Member Registration History
- Members can see list of members they registered
- Show registration dates
- Show member status

#### 3.3 Branch Leader Dashboard
- Branch-specific dashboard for branch administrators
- Show only their branch members
- Branch statistics
- Recent registrations in their branch

## 📊 Database Changes Required

### 1. Add `registered_by_member_id` to members table (Optional)
```sql
ALTER TABLE members 
ADD COLUMN registered_by_member_id BIGINT UNSIGNED NULL,
ADD COLUMN registered_at TIMESTAMP NULL,
ADD FOREIGN KEY (registered_by_member_id) REFERENCES members(id) ON DELETE SET NULL;
```

### 2. Update Member Model
```php
// In app/Models/Member.php
protected $fillable = [
    // ... existing fields
    'registered_by_member_id',
    'registered_at',
];

// Relationship
public function registeredBy()
{
    return $this->belongsTo(Member::class, 'registered_by_member_id');
}

public function registeredMembers()
{
    return $this->hasMany(Member::class, 'registered_by_member_id');
}
```

## 🎨 User Interface Design

### Member Registration Form (Simplified)
- **Step 1**: Personal Information
  - Full Name *
  - Gender *
  - Date of Birth *
  - Phone Number *
  - Email (optional)
  
- **Step 2**: Location
  - Region *
  - District *
  - Ward *
  - Street (optional)
  
- **Step 3**: Additional Info
  - Tribe *
  - Profession (optional)
  - Education Level (optional)
  
- **Step 4**: Confirmation
  - Review all information
  - Branch is shown (read-only): "Branch: [Member's Branch]"
  - Submit button

### Usharika Dashboard Layout
```
┌─────────────────────────────────────────────────┐
│  Usharika Dashboard - All Branches Overview     │
├─────────────────────────────────────────────────┤
│  [Total Members] [Total Branches] [New Members] │
├─────────────────────────────────────────────────┤
│  Branch Statistics Table                        │
│  ┌──────────┬──────────┬──────────┬──────────┐ │
│  │ Branch   │ Members │ New      │ Growth   │ │
│  ├──────────┼──────────┼──────────┼──────────┤ │
│  │ Branch 1 │   150    │   5     │  +3.3%   │ │
│  │ Branch 2 │   200    │   8     │  +4.0%   │ │
│  └──────────┴──────────┴──────────┴──────────┘ │
├─────────────────────────────────────────────────┤
│  Branch Comparison Chart                        │
│  [Bar Chart showing member counts per branch]   │
├─────────────────────────────────────────────────┤
│  Recent Registrations                            │
│  [List of recently registered members]         │
└─────────────────────────────────────────────────┘
```

## 🔒 Security & Access Control

### For Regular Members:
1. ✅ Can register new members
2. ✅ Can only register to their own branch
3. ✅ Cannot change branch selection
4. ✅ Cannot see other members' details
5. ✅ Can see members they registered (optional)
6. ❌ Cannot edit/delete members
7. ❌ Cannot access admin features

### For Usharika (Main Campus):
1. ✅ Full access to all branches
2. ✅ Can see all members from all branches
3. ✅ Can register members to any branch
4. ✅ Can view Usharika dashboard
5. ✅ Can see branch statistics
6. ✅ Can export data

### For Branch Administrators:
1. ✅ Can see all members in their branch
2. ✅ Can register members to their branch
3. ✅ Can manage branch members
4. ❌ Cannot see other branches' members

## 📝 Implementation Steps

### Step 1: Member Registration Feature
1. Create `showRegisterMember()` method in MemberDashboardController
2. Create `storeMember()` method in MemberDashboardController
3. Create `register-member.blade.php` view
4. Add route for member registration
5. Add "Register Member" card to member dashboard
6. Test member registration

### Step 2: Usharika Dashboard
1. Create `UsharikaDashboardController`
2. Create `dashboard.blade.php` view
3. Add route with access control
4. Add to sidebar menu
5. Implement statistics queries
6. Add charts/graphs
7. Test dashboard

### Step 3: Optional Enhancements
1. Add `registered_by_member_id` tracking
2. Create member registration history view
3. Add branch leader dashboard
4. Add export functionality

## 🧪 Testing Checklist

### Member Registration:
- [ ] Member can access registration form
- [ ] Branch is auto-selected and cannot be changed
- [ ] Form validation works
- [ ] Member is created successfully
- [ ] Member is assigned to correct branch
- [ ] User account is created
- [ ] Welcome SMS is sent
- [ ] Member cannot register to other branches

### Usharika Dashboard:
- [ ] Only Usharika users can access
- [ ] Shows all members from all branches
- [ ] Branch statistics are accurate
- [ ] Charts display correctly
- [ ] Recent registrations show correctly
- [ ] Filter by branch works
- [ ] Search functionality works
- [ ] Export works (if implemented)

## 🚀 Next Steps

1. **Review this plan** and confirm requirements
2. **Start with Phase 1** - Member Registration Feature
3. **Then Phase 2** - Usharika Dashboard
4. **Finally Phase 3** - Optional Enhancements

## 💡 Recommendations

1. **Start Simple**: Begin with basic member registration, then add features
2. **User Testing**: Test with actual members before full rollout
3. **Training**: Provide training materials for members on how to register others
4. **Monitoring**: Track how many members are registered by members vs admins
5. **Feedback**: Collect feedback and iterate

---

**Ready to implement?** Let me know which phase you'd like to start with! 🎯














