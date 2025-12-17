# Fixes Applied - Image URLs & Dashboard

## Issue 1: Auto-Generated Images (FIXED)

### Problem
When approving submissions, the system was automatically replacing missing images with a default Unsplash image instead of using your custom image URL.

### Location of Issue
- File: `/api/submissions.php` (line 161)
- File: `/refugee-innovation-hub/api/submissions.php` (line 161)

### What Was Changed
**BEFORE:**
```php
$submission['image_url'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800',
```

**AFTER:**
```php
$submission['image_url'],
```

### Result
Now when you submit a story with your own image URL, it will be saved exactly as you provided it - no auto-generation or replacement.

### How to Use
1. Go to "Submit Story" page (must be logged in as admin)
2. Two options for images:
   - **Upload a file** from your device (JPEG, PNG, GIF, WebP - max 5MB)
   - **OR provide an image URL** (e.g., https://example.com/myimage.jpg)
3. If you provide both, the URL takes priority
4. When you approve the submission, your exact image URL will be used

---

## Issue 2: Dashboard Changes Not Visible (BROWSER CACHE)

### Problem
The dashboard changes ARE applied to the code, but your browser is showing an old cached version of the JavaScript file.

### Verification (Changes ARE in the code)
I verified the following changes exist in `/js/app.js`:
- Line 808: Added "Location" column header
- Lines 820-826: Added location display with mapping status (📍 Mapped or ⚠️ No coordinates)
- Line 830: Added "Edit Location" button

### Solution: Clear Your Browser Cache

#### Chrome / Edge
1. Press `Ctrl + Shift + Delete` (Windows) or `Cmd + Shift + Delete` (Mac)
2. Select "Cached images and files"
3. Choose "All time" from the time range
4. Click "Clear data"
5. **OR** Hard refresh: `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)

#### Firefox
1. Press `Ctrl + Shift + Delete` (Windows) or `Cmd + Shift + Delete` (Mac)
2. Select "Cache"
3. Click "Clear Now"
4. **OR** Hard refresh: `Ctrl + F5` (Windows) or `Cmd + Shift + R` (Mac)

#### Safari
1. Press `Cmd + Option + E` to empty caches
2. Then `Cmd + R` to reload
3. **OR** Hard refresh: `Cmd + Option + R`

### After Clearing Cache
Reload the dashboard page and you should see:

**Published Stories Table Now Shows:**
```
| Title | Innovator | Region | Theme | Location | Views | Actions |
```

**Location Column Shows:**
- Story location (e.g., "Nairobi, Kenya")
- 📍 Mapped (green) - if coordinates exist
- ⚠️ No coordinates (amber) - if coordinates missing

**Actions Column Now Has 3 Buttons:**
1. **View** - View the story
2. **Edit Location** (blue) - Add/update coordinates
3. **Delete** - Delete the story

---

## Testing the Fixes

### Test 1: Submit Story with Custom Image URL
1. Login to admin dashboard
2. Go to "Submit Story"
3. Fill in all required fields
4. In "Or provide Image URL" field, enter: `https://picsum.photos/800/600`
5. Submit
6. Approve the submission
7. Check the published story - it should show YOUR image URL, not an Unsplash image

### Test 2: Dashboard Edit Location Feature
1. Login to admin dashboard
2. Scroll to "Published Stories"
3. Look for stories with "⚠️ No coordinates"
4. Click "Edit Location" button
5. Modal should appear with:
   - Current location text
   - "Find Coordinates Automatically" button
   - Latitude/Longitude input fields
6. Test auto-geocoding or enter coordinates manually
7. Save and verify story appears on map

---

## Quick Troubleshooting

### "I still don't see the Edit Location button"
- **Hard refresh** the page: `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)
- Clear all browser cache (see instructions above)
- Try a different browser or incognito/private mode
- Check browser console for JavaScript errors (F12 → Console tab)

### "My custom image isn't showing"
- Check the image URL is valid and accessible
- Make sure you're testing with NEW submissions (created after this fix)
- Old stories may still have the default Unsplash images (you can update them manually in the database)

### "The modal appears but coordinates don't save"
- Check browser console for errors (F12 → Console)
- Verify you're logged in as admin
- Check that latitude is -90 to 90 and longitude is -180 to 180
- Try manual entry if auto-geocoding fails

---

## Summary of All Changes

### Files Modified
1. `/api/submissions.php` - Removed default image auto-generation
2. `/refugee-innovation-hub/api/submissions.php` - Removed default image auto-generation
3. `/js/app.js` - Added Edit Location feature to dashboard (already applied in previous update)

### Features Now Available
1. ✅ Custom image URLs are respected (no auto-generation)
2. ✅ File upload works (saves to /uploads/stories/)
3. ✅ Dashboard shows mapping status for each story
4. ✅ Edit Location button opens modal
5. ✅ Auto-geocoding feature (converts location name to coordinates)
6. ✅ Manual coordinate entry
7. ✅ Stories with coordinates appear on map with individual markers

---

## Need More Help?

If after clearing cache you still don't see the changes:
1. Open browser console (F12 → Console tab)
2. Look for any red error messages
3. Take a screenshot and share it
4. Try accessing the site from a different browser
