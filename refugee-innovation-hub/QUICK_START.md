# Quick Start Guide - Refugee Innovation Hub

## 🚀 Get Started in 5 Minutes

### Step 1: Configure Firebase (2 minutes)
1. Go to https://console.firebase.google.com
2. Create a new project or select existing
3. Enable **Authentication** → **Email/Password**
4. Copy your config from Project Settings → General
5. Paste into `js/firebase-config.js`

### Step 2: Configure Supabase (2 minutes)
1. Go to https://supabase.com
2. Create a new project
3. Go to SQL Editor
4. Run the migration: `supabase/migrations/20251105102848_create_refugee_innovation_schema.sql`
5. Copy your project URL and anon key from Settings → API
6. Update `js/supabase-client.js` (already configured, but verify)

### Step 3: Set Up Google Analytics (1 minute - Optional)
1. Go to https://analytics.google.com
2. Create a property
3. Copy your Measurement ID (G-XXXXXXXXXX)
4. Replace `G-XXXXXXXXXX` in `index.html` line 21 and 26

### Step 4: Run the Application
1. Open `index.html` in a browser, OR
2. Use a local server:
   ```bash
   # Python
   python -m http.server 8000
   
   # Node.js
   npx serve
   ```
3. Visit `http://localhost:8000`

## ✅ Test the Application

1. **Home Page**: Should show featured stories
2. **Story Gallery**: Browse and filter stories
3. **Submit Story**: Fill out the form and submit
4. **Map**: View stories on interactive map
5. **Admin Login**: Sign in to review submissions

## 🔑 Create Admin User

1. Go to Firebase Console → Authentication
2. Click "Add User"
3. Enter email and password
4. Use these credentials to sign in to the dashboard

## 📝 Sample Data

The app includes 5 sample stories that load automatically if the database is empty. These demonstrate:
- Different regions (East Africa, Middle East, Southeast Asia)
- Different themes (Education, Health, Livelihoods, Arts & Culture)
- Location coordinates for map display

## 🎯 Next Steps

- Review `ADMIN_GUIDE.md` for content management
- Customize colors and branding in `css/styles.css`
- Add your own stories through the submission form
- Configure email notifications (optional)

## ❓ Need Help?

- Check `README.md` for detailed documentation
- Review `ADMIN_GUIDE.md` for admin tasks
- Check browser console for errors
- Verify all configurations are correct

---

**Ready to go!** 🎉
