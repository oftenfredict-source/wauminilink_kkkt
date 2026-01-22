# Evangelism Leader - Community Offerings Access Guide

## 📍 Where to Access Community Offerings

The Evangelism Leader can access and manage community offerings from Church Elders in the following locations:

---

## 🎯 Access Points

### 1. **Sidebar Menu** (Main Navigation)
**Location:** Left sidebar menu under "Evangelism Leader" section

**Menu Item:**
```
📊 Community Offerings
   Icon: 💰 (money-bill-wave)
   Route: /evangelism-leader/offerings
```

**How to Access:**
1. Login as Evangelism Leader
2. Look at the left sidebar menu
3. Find "Evangelism Leader" section
4. Click on **"Community Offerings"** menu item

---

### 2. **Dashboard Widget** (Statistics Card)
**Location:** Evangelism Leader Dashboard → Statistics Cards

**Widget Shows:**
- **Pending Offerings Count** - Number of offerings waiting for confirmation
- **Total Amount** - Sum of all pending offerings in TZS

**How to Access:**
1. Go to Evangelism Leader Dashboard (`/evangelism-leader/dashboard`)
2. Look at the statistics cards at the top
3. Find the **"Pending Offerings"** card (4th card)
4. Click on the card or use the Quick Action button below

---

### 3. **Quick Actions** (Dashboard)
**Location:** Evangelism Leader Dashboard → Quick Actions Section

**Button:**
```
💰 Community Offerings
   Shows badge with pending count if there are pending offerings
```

**How to Access:**
1. Go to Evangelism Leader Dashboard
2. Scroll to "Quick Actions" section
3. Click the **"Community Offerings"** button (green button with money icon)

---

## 📋 What the Evangelism Leader Can See

### **Pending Confirmation Section**
- List of all offerings with status: `pending_evangelism`
- Shows:
  - Date
  - Community name
  - Service type (Prayer Meeting, Bible Study, etc.)
  - Amount (TZS)
  - Recorded by (Church Elder name)
  - Submission date/time

### **Actions Available:**
1. **View Details** 👁️ - See full offering information
2. **Confirm** ✅ - Accept and forward to General Secretary
3. **Reject** ❌ - Reject with reason (sends back to Elder)
4. **Bulk Confirm** 📦 - Select multiple and confirm at once

### **Confirmed & Forwarded Section**
- List of offerings already confirmed (status: `pending_secretary`)
- Shows offerings that have been forwarded to General Secretary
- Can view details but cannot modify (already forwarded)

---

## 🔄 Workflow Steps

### **Step 1: View Pending Offerings**
1. Navigate to Community Offerings page
2. See list of all pending offerings from Church Elders
3. Each offering shows:
   - Community name
   - Service type
   - Amount
   - Who recorded it
   - When it was submitted

### **Step 2: Review Offering**
1. Click **"View Details"** (👁️ icon) to see full information:
   - Complete offering details
   - Service information (if linked to a service)
   - Collection method
   - Notes from Church Elder
   - Community information

### **Step 3: Confirm or Reject**

#### **Option A: Confirm Individual Offering**
1. Click **"Confirm"** button (✅ green button)
2. Modal opens to add optional notes
3. Click "Confirm" in modal
4. Offering status changes to `pending_secretary`
5. Automatically forwarded to General Secretary
6. Church Elder receives notification

#### **Option B: Bulk Confirm Multiple Offerings**
1. Check the boxes next to offerings you want to confirm
2. Or click "Select All" to select all pending offerings
3. Click **"Confirm Selected"** button
4. All selected offerings are confirmed at once
5. Consolidated notification sent to General Secretary

#### **Option C: Reject Offering**
1. Click **"Reject"** button (❌ red button)
2. Modal opens requiring rejection reason
3. Enter reason (required, minimum 10 characters)
4. Click "Reject"
5. Offering status changes to `rejected`
6. Church Elder receives notification with rejection reason
7. Elder can correct and resubmit

---

## 📊 Consolidated View

**Access:** Click "View Consolidated" button at top of offerings page

**Shows:**
- Total amount of all confirmed offerings
- Total number of services
- Breakdown by Community
- Breakdown by Service Type
- Detailed list of all offerings

**Purpose:** See summary before forwarding to General Secretary

---

## 🔗 Direct URLs

- **Main Offerings Page:** `/evangelism-leader/offerings`
- **Consolidated View:** `/evangelism-leader/offerings/consolidated`
- **View Specific Offering:** `/evangelism-leader/offerings/{offering_id}`

---

## 📱 Route Names (for developers)

- `evangelism-leader.offerings.index` - List all offerings
- `evangelism-leader.offerings.consolidated` - Consolidated view
- `evangelism-leader.offerings.show` - View details
- `evangelism-leader.offerings.confirm` - Confirm offering (POST)
- `evangelism-leader.offerings.reject` - Reject offering (POST)
- `evangelism-leader.offerings.bulk-confirm` - Bulk confirm (POST)

---

## ✅ Summary

**Evangelism Leader can access Community Offerings from:**

1. ✅ **Sidebar Menu** - "Community Offerings" link in Evangelism Leader section
2. ✅ **Dashboard Widget** - "Pending Offerings" statistics card
3. ✅ **Quick Actions** - "Community Offerings" button on dashboard

**What they can do:**
- ✅ View all pending offerings from Church Elders
- ✅ View offering details
- ✅ Confirm individual offerings
- ✅ Bulk confirm multiple offerings
- ✅ Reject offerings with reason
- ✅ View consolidated summary
- ✅ See confirmed offerings (forwarded to Secretary)

---

**Last Updated:** January 20, 2024



