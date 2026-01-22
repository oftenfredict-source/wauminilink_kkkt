-- ============================================
-- Fix Profile Picture Paths in Database
-- ============================================
-- This script fixes common path issues that prevent images from displaying

-- Step 1: Check current paths (run this first to see what you have)
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 10;

-- Step 2: Fix paths that have 'storage/' prefix (WRONG - causes double storage/)
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'storage/', '')
WHERE profile_picture LIKE 'storage/%';

-- Step 3: Fix paths that have 'public/storage/' prefix (WRONG)
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'public/storage/', '')
WHERE profile_picture LIKE 'public/storage/%';

-- Step 4: Fix old 'assets/images/members/profile-pictures/' paths
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'assets/images/members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'assets/images/members/profile-pictures/%';

-- Step 5: Fix 'members/profile-pictures/' to 'member/profile-pictures/' (singular)
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'members/profile-pictures/%';

-- Step 6: Verify the fix (run this after the updates)
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 10;

-- Expected result: profile_picture should be like 'member/profile-pictures/filename.jpg'
-- NOT like 'storage/member/profile-pictures/filename.jpg'

