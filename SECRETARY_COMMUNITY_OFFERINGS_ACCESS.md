# General Secretary - Community Offerings Access Guide

## 📍 Where to Access Community Offerings from Evangelism Leader

The General Secretary can access and finalize community offerings from the Evangelism Leader in the following location:

---

## 🎯 Access Point

### **Finance Approval Dashboard - Community Offerings Tab**

**URL:** `http://127.0.0.1:8000/finance/approval/dashboard#community-offerings`

**Location:** Financial Approval Dashboard → Community Offerings Tab

**How to Access:**
1. Login as General Secretary (or Admin/Pastor)
2. Go to **Finance** menu in sidebar
3. Click **"Approval Dashboard"** (or go directly to `/finance/approval/dashboard`)
4. Click on the **"Community Offerings"** tab
5. You'll see all pending offerings from Evangelism Leader

---

## 📋 What the Secretary Can See

### **Community Offerings Tab**
Shows all offerings with status: `pending_secretary` (already confirmed by Evangelism Leader)

**Table Columns:**
- **Community** - Name of the community
- **Service Type** - Type of mid-week service (Prayer Meeting, Bible Study, etc.)
- **Amount (TZS)** - Offering amount
- **Collection Method** - Cash, Mobile Money, or Bank Transfer
- **Date** - Offering date
- **From Leader** - Evangelism Leader who confirmed it
- **Received** - When Evangelism Leader forwarded it
- **Actions** - View Details and Finalize buttons

**Total Row:** Shows sum of all pending community offerings

---

## ✅ Actions Available

### **1. View Details** 👁️
- Click the **"View Details"** button (blue eye icon)
- Opens detailed view showing:
  - Complete offering information
  - Service details (if linked)
  - Collection method and reference number
  - Workflow information (who recorded, confirmed, etc.)
  - Notes from each stage (Elder, Leader, Secretary)

### **2. Finalize Offering** ✅
- Click the **"Finalize"** button (green checkmark icon)
- Modal opens to add optional notes
- Click "Finalize" in modal
- Offering status changes to `completed`
- Offering is officially recorded and finalized
- Evangelism Leader and Church Elder receive notifications

---

## 🔄 Workflow

### **Step 1: Access Dashboard**
1. Navigate to `/finance/approval/dashboard`
2. Click on **"Community Offerings"** tab
3. See all pending offerings from Evangelism Leader

### **Step 2: Review Offerings**
- See list of all confirmed offerings
- Each row shows:
  - Community name
  - Service type and date
  - Amount
  - Collection method
  - Who forwarded it (Evangelism Leader)
  - When it was received

### **Step 3: Finalize**
1. Click **"Finalize"** button on an offering
2. Modal opens
3. Add optional notes (if needed)
4. Click "Finalize"
5. Offering is completed and officially recorded

---

## 📊 Summary Information

**At the top of the dashboard, you'll see:**
- **Total Pending Records** - Includes community offerings count
- **Total Pending Amount** - Includes community offerings amount

**In the Community Offerings tab:**
- **Table footer** shows total amount of all pending community offerings
- **Badge** on tab shows count of pending community offerings

---

## 🔗 Direct Access

**Direct URL to Community Offerings Tab:**
```
http://127.0.0.1:8000/finance/approval/dashboard#community-offerings
```

**Note:** The `#community-offerings` hash automatically opens the Community Offerings tab when the page loads.

---

## 📱 Route Names (for developers)

- `finance.approval.dashboard` - Main approval dashboard
- `secretary.offerings.index` - Secretary offerings list (alternative access)
- `secretary.offerings.show` - View offering details
- `secretary.offerings.confirm` - Finalize offering (POST)

---

## ✅ Summary

**General Secretary can access Community Offerings from:**

1. ✅ **Finance Approval Dashboard** - `/finance/approval/dashboard#community-offerings`
   - Click on "Community Offerings" tab
   - See all pending offerings from Evangelism Leader
   - Finalize them directly from the dashboard

**What they can do:**
- ✅ View all pending community offerings (confirmed by Evangelism Leader)
- ✅ View detailed information for each offering
- ✅ Finalize offerings (change status to `completed`)
- ✅ Add notes when finalizing
- ✅ See total amount of all pending community offerings

---

## 🎯 Quick Access Steps

1. **Login** as General Secretary
2. **Navigate** to Finance → Approval Dashboard
3. **Click** "Community Offerings" tab
4. **Review** pending offerings
5. **Click** "Finalize" to complete each offering

---

**Last Updated:** January 20, 2024








