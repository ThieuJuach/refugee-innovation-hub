# Refugee Innovation Hub Microsite - MVP

A comprehensive microsite showcasing refugee-led innovation stories, built for JRS/USA. This MVP includes all essential features for storytelling, content management, and engagement tracking.

## 🎯 MVP Features

### ✅ Core Features Implemented

1. **Home Page**
   - Mission overview and hero section
   - Featured refugee innovation stories
   - Call-to-action buttons (Explore Innovations, Submit Story)
   - About section

2. **Story/Project Gallery**
   - Searchable and filterable story list
   - Filters by region and theme
   - Story cards with images, summaries, and metadata
   - Responsive grid layout

3. **Individual Project Pages**
   - Full story descriptions with images
   - Impact metrics and beneficiary counts
   - Location information
   - Contact details

4. **Story Submission Form**
   - Accessible form for field staff
   - Required fields: Title, Innovator, Region, Theme, Description, Impact, Location, Email
   - Optional image upload (via URL)
   - Submission tracking

5. **Interactive Map**
   - Leaflet.js-powered world map
   - Project location markers
   - Clickable popups with story previews
   - Region-based grouping for stories without coordinates
   - Responsive design

6. **Admin Dashboard**
   - Authentication via PHP/MySQL
   - View pending submissions
   - Approve/reject submissions
   - Analytics dashboard with key metrics
   - Story management

7. **Analytics Integration**
   - Google Analytics tracking
   - Database analytics events
   - Page view tracking
   - Story engagement metrics
   - Submission activity tracking

## 🚀 Quick Start

### Prerequisites
- **XAMPP** installed (includes Apache, MySQL, PHP)
  - Download from: https://www.apachefriends.org/
- Modern web browser
- Google Analytics account (optional, for analytics)

### Setup Instructions

**For detailed XAMPP setup, see [XAMPP_SETUP.md](XAMPP_SETUP.md)**

1. **Install XAMPP**
   - Download and install XAMPP
   - Start Apache and MySQL services

2. **Copy Project to XAMPP**
   - Copy `ProjectJRS1-main` folder to `C:\xampp\htdocs\refugee-innovation-hub`

3. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create database: `refugee_innovation_hub`
   - Import `database/schema.sql`

4. **Configure Database**
   - Open `api/config.php`
   - Verify database settings (default XAMPP settings should work)

5. **Configure Google Analytics** (Optional)
   - Open `index.html`
   - Replace `G-XXXXXXXXXX` with your Google Analytics ID

6. **Access the Application**
   - Open browser: `http://localhost/refugee-innovation-hub/`
   - Default admin: `admin@jrsusa.org` / `admin123` (change this!)

## 📁 Project Structure

```
ProjectJRS1-main/
├── index.html              # Main HTML file
├── css/
│   └── styles.css          # All styles
├── js/
│   ├── app.js              # Main application logic
│   └── auth.js             # PHP authentication
├── api/                    # PHP API endpoints
│   ├── config.php          # Database configuration
│   ├── stories.php         # Stories API
│   ├── submissions.php     # Submissions API
│   ├── auth.php            # Authentication API
│   ├── analytics.php       # Analytics API
│   ├── stats.php           # Statistics API
│   ├── php-api-client.js   # JavaScript API client
│   └── generate-password.php # Password hash generator
├── database/
│   └── schema.sql          # MySQL database schema
├── ADMIN_GUIDE.md          # Admin documentation
├── XAMPP_SETUP.md          # XAMPP setup guide
└── README.md               # This file
```

## 🗄️ Database Schema

The application uses MySQL with the following tables:

- **innovation_stories**: Published refugee innovation stories
- **story_submissions**: Pending/approved/rejected submissions
- **site_analytics**: Analytics events and tracking
- **users**: Admin user accounts

See `database/schema.sql` for full schema.

## 🎨 Features in Detail

### Interactive Map
- Uses Leaflet.js for map rendering
- OpenStreetMap tiles (free, no API key required)
- Supports both coordinate-based markers and region-based grouping
- Clickable popups with story previews and navigation

### Story Submission
- Form validation
- Automatic submission to database
- Admin review workflow
- Email notifications (can be added)

### Admin Dashboard
- Secure PHP session-based authentication
- Submission review interface
- Analytics overview
- Story management

### Analytics
- Google Analytics integration
- MySQL-based custom event tracking
- Page view tracking
- Story engagement metrics

## 🔧 Configuration

### XAMPP Setup
1. Install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services
3. Copy project to `C:\xampp\htdocs\refugee-innovation-hub`
4. See [XAMPP_SETUP.md](XAMPP_SETUP.md) for detailed instructions

### Database Setup
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create database: `refugee_innovation_hub`
3. Import `database/schema.sql`
4. Update `api/config.php` if needed (default settings work for XAMPP)

### Admin User Setup
1. Default admin: `admin@jrsusa.org` / `admin123`
2. **IMPORTANT:** Change the default password!
3. Use `api/generate-password.php?password=yourpassword` to generate hash
4. Update password in `users` table via phpMyAdmin

### Google Analytics Setup
1. Create a Google Analytics property
2. Get your Measurement ID (G-XXXXXXXXXX)
3. Update `index.html` with your ID

## 📱 Responsive Design

The site is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## 📝 Sample Data

The application includes sample stories for demonstration. These are loaded automatically if the database is empty. Sample stories include:
- Digital Learning Platform (Education)
- Solar-Powered Water Purification (Health)
- Mobile Job Matching App (Livelihoods)
- Community Garden (Livelihoods)
- Artisan Marketplace (Arts & Culture)

## 🔒 Security

- PHP session-based authentication for admin access
- MySQL database access control
- Input validation on forms
- XSS protection through proper escaping
- SQL injection prevention via prepared statements

## 📚 Documentation

- **ADMIN_GUIDE.md**: Complete guide for content management
- **Code Comments**: Inline documentation in JavaScript files
- **Database Schema**: Documented in migration file

## 🚀 Deployment

### Local Development (XAMPP)
- Follow [XAMPP_SETUP.md](XAMPP_SETUP.md) for local setup
- Access at: `http://localhost/refugee-innovation-hub/`

### Production Deployment
1. **Upload to Web Server**
   - Upload all files to your web server
   - Ensure PHP 7.4+ and MySQL are available
   - Set proper file permissions

2. **Database Setup**
   - Create MySQL database on server
   - Import `database/schema.sql`
   - Update `api/config.php` with production database credentials

3. **Security**
   - Change default admin password
   - Update `JWT_SECRET` in `api/config.php`
   - Disable error display in production
   - Use HTTPS
   - Restrict database user permissions

4. **Configuration**
   - Update `api/config.php` with production settings
   - Configure CORS if needed
   - Set up Google Analytics

## 🐛 Troubleshooting

### Map Not Loading
- Check browser console for errors
- Verify Leaflet.js is loaded
- Ensure internet connection

### Stories Not Appearing
- Check MySQL database connection
- Verify database tables exist
- Check browser console for errors
- Ensure Apache and MySQL are running in XAMPP

### Authentication Issues
- Check PHP sessions are working
- Verify database connection in `api/config.php`
- Check `users` table exists and has admin user
- Verify password hash is correct

## 📞 Support

For issues or questions:
1. Check the ADMIN_GUIDE.md
2. Review browser console for errors
3. Verify all configurations are correct
4. Contact your system administrator

## 🎯 Next Steps

1. **Complete XAMPP Setup**
   - Follow [XAMPP_SETUP.md](XAMPP_SETUP.md)
   - Import database schema
   - Test admin login

2. **Security**
   - Change default admin password
   - Update JWT_SECRET in `api/config.php`
   - Review security settings

3. **Customization**
   - Add your stories
   - Customize branding
   - Configure Google Analytics

Potential enhancements for future versions:
- Image upload functionality
- Email notifications for submissions
- Advanced search and filtering
- Multi-language support
- Social media sharing
- Newsletter subscription
- Story categories and tags
- User comments and feedback

## 📄 License

This project is developed for JRS/USA. All rights reserved.

## 🙏 Acknowledgments

- Built with inspiration from UNHCR Innovation Service
- Uses Leaflet.js for mapping
- PHP for backend API
- MySQL for database
- XAMPP for local development
- OpenStreetMap for map tiles

---

**Version:** 1.0 MVP  
**Last Updated:** November 2024  
**Status:** Production Ready
