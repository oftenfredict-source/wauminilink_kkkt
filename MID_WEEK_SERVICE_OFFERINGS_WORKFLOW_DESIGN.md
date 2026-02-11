# Mid-Week Service Offerings Workflow Design

## 🎯 Overview

This document outlines the complete workflow design for handling offerings from mid-week services in the community. The flow ensures proper tracking, accountability, and transparency from collection to final receipt by the General Secretary.

---

## 👥 Actors & Roles

### 1. **Church Elder** (Community Level)
- **Responsibility**: Records offerings collected during mid-week services in their community
- **Actions**: 
  - Create service record for mid-week service
  - Record offering amount from the service
  - Submit offering to Evangelism Leader

### 2. **Evangelism Leader** (Community/Regional Level)
- **Responsibility**: Collects and consolidates all offerings from multiple communities
- **Actions**:
  - Receive offerings from Church Elders
  - Verify and confirm receipt
  - Consolidate offerings from all communities
  - Forward consolidated offerings to General Secretary

### 3. **General Secretary** (Organization Level)
- **Responsibility**: Final receipt and official recording of all offerings
- **Actions**:
  - Receive consolidated offerings from Evangelism Leader
  - Verify total amounts
  - Finalize and mark as completed
  - Generate official records/reports

---

## 📋 Workflow Steps

### **Step 1: Mid-Week Service Conducted**
```
Church Elder conducts mid-week service (Prayer Meeting, Bible Study, etc.)
↓
Service is recorded in the system with:
- Service date
- Service type (prayer_meeting, bible_study, etc.)
- Attendance count
- Offering amount collected
```

### **Step 2: Church Elder Records Offering**
```
Church Elder logs into system
↓
Goes to: Community → Services → [Select Mid-Week Service]
↓
Records offering amount from the service
↓
System creates offering record with status: "pending_evangelism"
↓
Notification sent to Evangelism Leader
```

### **Step 3: Evangelism Leader Receives & Confirms**
```
Evangelism Leader logs into system
↓
Sees pending offerings from all communities
↓
Reviews offering details:
- Community name
- Service type & date
- Amount
- Recorded by (Church Elder)
↓
Confirms receipt (physically or digitally)
↓
System updates status to: "pending_secretary"
↓
Evangelism Leader can:
- View all offerings from all communities
- See consolidated totals
- Forward to General Secretary
```

### **Step 4: General Secretary Finalizes**
```
General Secretary logs into system
↓
Sees pending offerings from Evangelism Leader
↓
Reviews consolidated offering details
↓
Confirms final receipt
↓
System updates status to: "completed"
↓
Offering is officially recorded and finalized
```

---

## 🗄️ Data Model Design

### **Enhanced Community Offering Model**

```php
// Extend existing community_offerings table
Schema::table('community_offerings', function (Blueprint $table) {
    // Link to specific service
    $table->foreignId('service_id')->nullable()->constrained('sunday_services')->onDelete('set null');
    $table->string('service_type')->nullable(); // prayer_meeting, bible_study, etc.
    
    // Additional tracking
    $table->string('collection_method')->default('cash'); // cash, mobile_money, bank_transfer
    $table->string('reference_number')->nullable(); // For mobile money/bank transfers
    
    // Amount breakdown (optional)
    $table->decimal('cash_amount', 12, 2)->nullable();
    $table->decimal('mobile_money_amount', 12, 2)->nullable();
    $table->decimal('bank_transfer_amount', 12, 2)->nullable();
    
    // Verification fields
    $table->boolean('is_verified_by_elder')->default(false);
    $table->timestamp('verified_by_elder_at')->nullable();
    $table->boolean('is_verified_by_leader')->default(false);
    $table->timestamp('verified_by_leader_at')->nullable();
    $table->boolean('is_verified_by_secretary')->default(false);
    $table->timestamp('verified_by_secretary_at')->nullable();
    
    // Rejection/Correction tracking
    $table->string('rejection_reason')->nullable();
    $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('rejected_at')->nullable();
});
```

### **New Model: MidWeekServiceOffering** (Alternative Approach)

If you prefer a separate model for mid-week offerings:

```php
Schema::create('mid_week_service_offerings', function (Blueprint $table) {
    $table->id();
    
    // Service Information
    $table->foreignId('service_id')->constrained('sunday_services')->onDelete('cascade');
    $table->foreignId('community_id')->constrained('communities')->onDelete('cascade');
    
    // Offering Details
    $table->decimal('amount', 12, 2);
    $table->date('offering_date');
    $table->string('collection_method')->default('cash');
    $table->string('reference_number')->nullable();
    
    // Workflow Tracking
    $table->foreignId('church_elder_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('evangelism_leader_id')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('general_secretary_id')->nullable()->constrained('users')->onDelete('set null');
    
    // Status Flow
    $table->enum('status', [
        'pending_evangelism',    // Elder submitted, waiting for Leader
        'pending_secretary',      // Leader confirmed, waiting for Secretary
        'completed',              // Secretary finalized
        'rejected',               // Rejected at any stage
        'corrected'               // Corrected and resubmitted
    ])->default('pending_evangelism');
    
    // Timestamps for each stage
    $table->timestamp('submitted_at')->nullable();
    $table->timestamp('received_by_leader_at')->nullable();
    $table->timestamp('forwarded_to_secretary_at')->nullable();
    $table->timestamp('finalized_at')->nullable();
    
    // Notes and corrections
    $table->text('elder_notes')->nullable();
    $table->text('leader_notes')->nullable();
    $table->text('secretary_notes')->nullable();
    $table->text('rejection_reason')->nullable();
    
    $table->timestamps();
    $table->softDeletes();
    
    // Indexes
    $table->index(['status', 'offering_date']);
    $table->index('community_id');
    $table->index('service_id');
});
```

---

## 🔄 Detailed Workflow States

### **State 1: Pending Evangelism Leader** (`pending_evangelism`)
- **Who can see**: Church Elder (who created), Evangelism Leader
- **Who can act**: Evangelism Leader (confirm/reject)
- **Next states**: `pending_secretary` or `rejected`

### **State 2: Pending Secretary** (`pending_secretary`)
- **Who can see**: Evangelism Leader, General Secretary
- **Who can act**: General Secretary (finalize/reject)
- **Next states**: `completed` or `rejected`

### **State 3: Completed** (`completed`)
- **Who can see**: All authorized users (read-only)
- **Who can act**: None (finalized, cannot be modified)
- **Next states**: None (final state)

### **State 4: Rejected** (`rejected`)
- **Who can see**: Original creator, person who rejected
- **Who can act**: Original creator (can correct and resubmit)
- **Next states**: `pending_evangelism` (after correction)

---

## 🎨 UI/UX Flow Design

### **For Church Elder:**

#### **Dashboard Widget**
```
┌─────────────────────────────────────┐
│ Mid-Week Service Offerings         │
├─────────────────────────────────────┤
│ Pending Submission: 2              │
│ Submitted This Week: 5              │
│ Total Amount: TZS 125,000          │
│                                     │
│ [View All Offerings]                │
│ [Record New Offering]               │
└─────────────────────────────────────┘
```

#### **Record Offering Page**
```
┌─────────────────────────────────────────────┐
│ Record Mid-Week Service Offering            │
├─────────────────────────────────────────────┤
│ Service: [Select Service ▼]                 │
│   - Prayer Meeting - Jan 15, 2024           │
│   - Bible Study - Jan 17, 2024              │
│                                             │
│ Community: [Auto-filled]                    │
│                                             │
│ Offering Amount: [TZS _______]              │
│                                             │
│ Collection Method:                          │
│   ( ) Cash                                  │
│   ( ) Mobile Money                          │
│   ( ) Bank Transfer                         │
│                                             │
│ Reference Number: [________] (if applicable)│
│                                             │
│ Notes: [________________________]           │
│                                             │
│ [Cancel]  [Submit to Evangelism Leader]     │
└─────────────────────────────────────────────┘
```

### **For Evangelism Leader:**

#### **Dashboard Widget**
```
┌─────────────────────────────────────┐
│ Pending Offerings from Communities │
├─────────────────────────────────────┤
│ Total Pending: 8                    │
│ Total Amount: TZS 450,000           │
│                                     │
│ Communities:                        │
│ • Longuo A: TZS 75,000 (3 services)│
│ • Longuo B: TZS 125,000 (2 services)│
│ • Mwanga: TZS 250,000 (3 services) │
│                                     │
│ [Review & Confirm]                  │
└─────────────────────────────────────┘
```

#### **Pending Offerings List**
```
┌─────────────────────────────────────────────────────────────┐
│ Pending Offerings from Communities                         │
├─────────────────────────────────────────────────────────────┤
│ [Filter: All | This Week | This Month]                     │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Community: Longuo A                                     │ │
│ │ Service: Prayer Meeting - Jan 15, 2024                 │ │
│ │ Amount: TZS 25,000                                      │ │
│ │ Recorded by: Elder John Doe                             │ │
│ │ Submitted: Jan 15, 2024 8:30 PM                        │ │
│ │                                                         │ │
│ │ [View Details] [Confirm Receipt] [Request Correction]  │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Community: Longuo B                                     │ │
│ │ Service: Bible Study - Jan 17, 2024                   │ │
│ │ Amount: TZS 50,000                                      │ │
│ │ Recorded by: Elder Jane Smith                           │ │
│ │ Submitted: Jan 17, 2024 7:45 PM                        │ │
│ │                                                         │ │
│ │ [View Details] [Confirm Receipt] [Request Correction]  │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ [Bulk Actions: Select All] [Confirm Selected] [Forward All]│
└─────────────────────────────────────────────────────────────┘
```

#### **Consolidation View**
```
┌─────────────────────────────────────────────────────────────┐
│ Consolidated Offerings Summary                             │
├─────────────────────────────────────────────────────────────┤
│ Period: Jan 15 - Jan 21, 2024                              │
│                                                             │
│ Communities Summary:                                       │
│ • Longuo A:      TZS 125,000 (5 services)                 │
│ • Longuo B:      TZS 200,000 (4 services)                 │
│ • Mwanga:        TZS 300,000 (6 services)                 │
│ • Total:         TZS 625,000 (15 services)                │
│                                                             │
│ Service Type Breakdown:                                    │
│ • Prayer Meeting:    TZS 250,000 (8 services)            │
│ • Bible Study:        TZS 300,000 (5 services)            │
│ • Youth Service:      TZS 75,000 (2 services)             │
│                                                             │
│ [Generate Report] [Forward to General Secretary]           │
└─────────────────────────────────────────────────────────────┘
```

### **For General Secretary:**

#### **Dashboard Widget**
```
┌─────────────────────────────────────┐
│ Pending Offerings from Leader      │
├─────────────────────────────────────┤
│ Total Pending: 1                    │
│ Total Amount: TZS 625,000           │
│                                     │
│ From: Evangelism Leader             │
│ Submitted: Jan 21, 2024             │
│                                     │
│ [Review & Finalize]                 │
└─────────────────────────────────────┘
```

#### **Finalization Page**
```
┌─────────────────────────────────────────────────────────────┐
│ Finalize Consolidated Offerings                            │
├─────────────────────────────────────────────────────────────┤
│ Submitted by: Evangelism Leader                            │
│ Submission Date: Jan 21, 2024 10:30 AM                    │
│                                                             │
│ Consolidated Summary:                                      │
│ • Total Amount: TZS 625,000                                │
│ • Number of Services: 15                                   │
│ • Communities: 3                                          │
│                                                             │
│ Breakdown:                                                  │
│ [View Detailed Breakdown ▼]                                │
│                                                             │
│ Verification:                                               │
│ [ ] Physical receipt verified                              │
│ [ ] Amount matches records                                 │
│ [ ] All communities accounted for                          │
│                                                             │
│ Notes: [________________________________]                  │
│                                                             │
│ [Request Clarification] [Finalize & Complete]              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔔 Notification System

### **Notifications to Send:**

1. **When Elder Submits Offering:**
   - **To**: Evangelism Leader
   - **Message**: "New offering from [Community] - [Service Type] - TZS [Amount]. Please review and confirm."

2. **When Leader Confirms:**
   - **To**: Church Elder
   - **Message**: "Your offering from [Service Type] has been confirmed by Evangelism Leader."

3. **When Leader Forwards to Secretary:**
   - **To**: General Secretary
   - **Message**: "Consolidated offerings (TZS [Total]) from [X] communities ready for finalization."

4. **When Secretary Finalizes:**
   - **To**: Evangelism Leader, Church Elders (all involved)
   - **Message**: "Offerings have been finalized and officially recorded."

5. **When Rejected:**
   - **To**: Original Creator
   - **Message**: "Your offering has been rejected. Reason: [Reason]. Please correct and resubmit."

---

## 📊 Reporting & Analytics

### **Reports Available:**

1. **Church Elder Reports:**
   - My submitted offerings (by date range)
   - Pending vs. completed
   - Total amounts by service type

2. **Evangelism Leader Reports:**
   - All offerings from all communities
   - Consolidated totals by period
   - Community-wise breakdown
   - Service type breakdown
   - Pending vs. confirmed

3. **General Secretary Reports:**
   - All finalized offerings
   - Monthly/quarterly summaries
   - Community performance
   - Service type trends
   - Audit trail

---

## 🔒 Security & Permissions

### **Access Control:**

- **Church Elder**: Can only see/modify offerings from their own community
- **Evangelism Leader**: Can see all community offerings, can confirm/reject
- **General Secretary**: Can see all offerings, can finalize
- **Admin**: Full access (read-only for completed, can modify pending)

### **Audit Trail:**

- Track all status changes
- Record who made changes and when
- Store rejection reasons
- Maintain history of corrections

---

## ✅ Implementation Checklist

### **Phase 1: Database & Models**
- [ ] Extend `community_offerings` table or create `mid_week_service_offerings` table
- [ ] Add relationships to `SundayService` model
- [ ] Create `MidWeekServiceOffering` model (if separate)
- [ ] Add scopes for filtering by status, date, community

### **Phase 2: Controllers & Logic**
- [ ] Create/update `MidWeekServiceOfferingController`
- [ ] Implement workflow state transitions
- [ ] Add validation rules
- [ ] Implement permission checks

### **Phase 3: Views & UI**
- [ ] Create Church Elder offering form
- [ ] Create Evangelism Leader dashboard and list views
- [ ] Create General Secretary finalization view
- [ ] Add dashboard widgets for each role
- [ ] Create consolidation/reporting views

### **Phase 4: Notifications**
- [ ] Set up email/SMS notifications
- [ ] Create notification templates
- [ ] Test notification delivery

### **Phase 5: Reporting**
- [ ] Create report views
- [ ] Add export functionality (PDF/Excel)
- [ ] Create analytics dashboards

### **Phase 6: Testing**
- [ ] Test workflow end-to-end
- [ ] Test permissions and access control
- [ ] Test notifications
- [ ] Test reporting

---

## 💡 Recommendations

### **Option 1: Extend Existing System** (Recommended)
- Use existing `community_offerings` table
- Add `service_id` and `service_type` fields
- Leverage existing workflow infrastructure
- **Pros**: Faster implementation, consistent with existing system
- **Cons**: May mix general community offerings with service-specific ones

### **Option 2: Separate Model** (More Organized)
- Create dedicated `mid_week_service_offerings` table
- Clear separation of concerns
- **Pros**: Better organization, easier to query service-specific offerings
- **Cons**: More code to maintain, potential duplication

### **Recommendation: Option 1**
Extend the existing `community_offerings` system because:
1. The workflow is identical (Elder → Leader → Secretary)
2. Less code duplication
3. Easier to maintain
4. Can filter by `service_id` to distinguish mid-week service offerings

---

## 🎯 Key Features to Implement

1. **Service Linking**: Link offerings directly to service records
2. **Bulk Operations**: Allow Evangelism Leader to confirm multiple offerings at once
3. **Consolidation View**: Show aggregated totals before forwarding to Secretary
4. **Audit Trail**: Track all changes and who made them
5. **Notifications**: Keep all parties informed of status changes
6. **Reporting**: Generate comprehensive reports for all stakeholders
7. **Mobile-Friendly**: Ensure forms work well on mobile devices
8. **Offline Capability**: Consider offline recording for areas with poor connectivity

---

## 📝 Next Steps

1. **Review this design** with stakeholders
2. **Choose implementation approach** (extend existing vs. new model)
3. **Prioritize features** (MVP vs. full implementation)
4. **Create detailed technical specifications**
5. **Begin implementation** following the checklist above

---

## ❓ Questions to Consider

1. Can multiple offerings be submitted for the same service? (e.g., if collected over multiple days)
2. Should there be a time limit for each stage? (e.g., Leader must confirm within 48 hours)
3. Can offerings be edited after submission but before confirmation?
4. Should there be approval workflow for large amounts?
5. Do we need to track physical handover vs. digital confirmation?
6. Should there be a way to batch multiple services into one submission?

---

**Document Version**: 1.0  
**Last Updated**: January 2024  
**Status**: Design Phase - Awaiting Approval








