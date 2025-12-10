// Main Application
let currentPage = 'home';
let currentStory = null;
let stories = [];
let submissions = [];

// Navigation
function navigateTo(page, data = null) {
    currentPage = page;
    if (data) {
        if (page === 'story') {
            currentStory = data;
        }
    }
    render();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Expose navigation globally
window.navigateTo = navigateTo;

// Render header
function renderHeader() {
    const isAuth = window.auth?.isAuthenticated();

    return `
        <header>
            <nav>
                <div class="nav-container">
                    <div class="logo" onclick="navigateTo('home')">
                        <img src="/images/jrs-logo.png" alt="JRS Logo" class="logo-image" onerror="this.onerror=null; this.src='https://jrsusa.org/wp-content/uploads/2021/01/cropped-jrsusa_logo_blue-1.png'">
                        <span class="logo-text">Refugee Innovation Hub</span>
                    </div>

                    <ul class="nav-links">
                        <li><button class="${currentPage === 'home' ? 'active' : ''}" onclick="navigateTo('home')">Home</button></li>
                        <li><button class="${currentPage === 'gallery' ? 'active' : ''}" onclick="navigateTo('gallery')">Stories</button></li>
                        <li><button class="${currentPage === 'map' ? 'active' : ''}" onclick="navigateTo('map')">Map</button></li>
                        <li><button class="${currentPage === 'submit' ? 'active' : ''}" onclick="navigateTo('submit')">Submit Story</button></li>
                        ${isAuth ?
                            `<li><button class="${currentPage === 'dashboard' ? 'active' : ''}" onclick="navigateTo('dashboard')">Dashboard</button></li>
                             <li><button onclick="handleSignOut()">Sign Out</button></li>` :
                            `<li><button class="${currentPage === 'login' ? 'active' : ''}" onclick="navigateTo('login')">Sign In</button></li>`
                        }
                    </ul>

                    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
                </div>

                <div class="mobile-menu" id="mobileMenu">
                    <button class="${currentPage === 'home' ? 'active' : ''}" onclick="navigateTo('home'); toggleMobileMenu()">Home</button>
                    <button class="${currentPage === 'gallery' ? 'active' : ''}" onclick="navigateTo('gallery'); toggleMobileMenu()">Stories</button>
                    <button class="${currentPage === 'map' ? 'active' : ''}" onclick="navigateTo('map'); toggleMobileMenu()">Map</button>
                    <button class="${currentPage === 'submit' ? 'active' : ''}" onclick="navigateTo('submit'); toggleMobileMenu()">Submit Story</button>
                    ${isAuth ?
                        `<button class="${currentPage === 'dashboard' ? 'active' : ''}" onclick="navigateTo('dashboard'); toggleMobileMenu()">Dashboard</button>
                         <button onclick="handleSignOut(); toggleMobileMenu()">Sign Out</button>` :
                        `<button class="${currentPage === 'login' ? 'active' : ''}" onclick="navigateTo('login'); toggleMobileMenu()">Sign In</button>`
                    }
                </div>
            </nav>
        </header>
    `;
}

// Render footer
function renderFooter() {
    return `
        <footer>
            <div class="footer-content">
                <div class="footer-grid">
                    <div class="footer-section">
                        <h3>About Us</h3>
                        <p>The Refugee Innovation Hub is a JRS/USA initiative showcasing innovative solutions from refugee communities worldwide.</p>
                        <div class="social-links">
                            <a href="https://twitter.com/jrsusa" target="_blank" aria-label="Twitter">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                                </svg>
                            </a>
                            <a href="https://facebook.com/jrsusa" target="_blank" aria-label="Facebook">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                                </svg>
                            </a>
                            <a href="https://linkedin.com/company/jrsusa" target="_blank" aria-label="LinkedIn">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
                                    <circle cx="4" cy="4" r="2"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-section">
                        <h3>Quick Links</h3>
                        <ul class="footer-links">
                            <li><a href="#" onclick="event.preventDefault(); navigateTo('home')">Home</a></li>
                            <li><a href="#" onclick="event.preventDefault(); navigateTo('gallery')">Story Gallery</a></li>
                            <li><a href="#" onclick="event.preventDefault(); navigateTo('map')">Innovation Map</a></li>
                            <li><a href="#" onclick="event.preventDefault(); navigateTo('submit')">Submit Story</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h3>Resources</h3>
                        <ul class="footer-links">
                            <li><a href="https://jrsusa.org" target="_blank">JRS/USA Website</a></li>
                            <li><a href="https://jrsusa.org/what-we-do/" target="_blank">Our Programs</a></li>
                            <li><a href="https://jrsusa.org/get-involved/" target="_blank">Get Involved</a></li>
                            <li><a href="https://jrsusa.org/donate/" target="_blank">Support Our Work</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h3>Contact</h3>
                        <p>Jesuit Refugee Service/USA<br>
                        1016 16th Street NW, Suite 500<br>
                        Washington, DC 20036</p>
                        <p>Email: <a href="mailto:info@jrsusa.org">info@jrsusa.org</a></p>
                        <p>Phone: (202) 629-5073</p>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; ${new Date().getFullYear()} Jesuit Refugee Service/USA. All rights reserved.</p>
                    <p>Empowering refugee-led innovation worldwide.</p>
                </div>
            </div>
        </footer>
    `;
}

// Toggle mobile menu
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Home page
function renderHomePage() {
    const featuredStories = stories.filter(s => s.is_featured).slice(0, 6);

    return `
        <div>
            <section class="hero">
                <div class="hero-content">
                    <h1>Empowering Refugee-Led Innovation</h1>
                    <p>Discover inspiring stories of resilience, creativity, and impact from refugee communities around the world. Together, we're building a future where every voice matters.</p>
                    <div class="hero-buttons">
                        <button class="btn btn-primary" onclick="navigateTo('gallery')">
                            Explore Innovations →
                        </button>
                        <button class="btn btn-secondary" onclick="navigateTo('submit')">
                            Submit a Story
                        </button>
                    </div>
                </div>
            </section>

            <section class="features">
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #d1fae5;">
                            <svg width="32" height="32" fill="#059669" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M12 2v20M2 12h20" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <h3>Global Reach</h3>
                        <p>Stories from refugee communities across continents</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon" style="background: #ccfbf1;">
                            <svg width="32" height="32" fill="#0d9488" viewBox="0 0 24 24">
                                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" fill="none"/>
                                <circle cx="12" cy="12" r="3" fill="currentColor"/>
                            </svg>
                        </div>
                        <h3>Creative Solutions</h3>
                        <p>Innovative approaches to community challenges</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon" style="background: #cffafe;">
                            <svg width="32" height="32" fill="#0891b2" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 108 0 4 4 0 00-8 0zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                        </div>
                        <h3>Community Impact</h3>
                        <p>Projects transforming lives and building futures</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon" style="background: #d1fae5;">
                            <svg width="32" height="32" fill="#059669" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" fill="currentColor"/>
                            </svg>
                        </div>
                        <h3>Inspiring Hope</h3>
                        <p>Stories of resilience and human dignity</p>
                    </div>
                </div>
            </section>

            <section class="stories-section">
                <div class="section-header">
                    <h2>Featured Stories</h2>
                    <p>Discover how refugee-led innovations are creating lasting change in communities worldwide</p>
                </div>

                <div class="stories-grid">
                    ${featuredStories.length > 0 ? featuredStories.map(story => `
                        <div class="story-card" onclick="navigateTo('story', ${story.id})">
                            <img src="${story.image_url || 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800'}" alt="${story.title}" class="story-image" onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800'">
                            <div class="story-content">
                                <div class="story-meta">
                                    <span class="badge badge-region">${story.region}</span>
                                    <span class="badge badge-theme">${story.theme}</span>
                                </div>
                                <h3>${story.title}</h3>
                                <p class="innovator">by ${story.innovator_name || 'Anonymous'}</p>
                                <p class="description">${(story.description || story.summary || 'No description available.').substring(0, 120)}...</p>
                            </div>
                        </div>
                    `).join('') : '<p style="text-align: center; color: #6b7280;">No featured stories available yet.</p>'}
                </div>

                <div style="text-align: center; margin-top: 3rem;">
                    <button class="btn btn-primary" onclick="navigateTo('gallery')">
                        View All Stories →
                    </button>
                </div>
            </section>

            <section class="about-section">
                <div class="about-content">
                    <h2>About the Refugee Innovation Hub</h2>
                    <p>The Refugee Innovation Hub is a JRS/USA initiative dedicated to showcasing and supporting innovative solutions created by refugee communities. We believe that refugees are not just recipients of aid, but active agents of change who bring creativity, resilience, and valuable perspectives to addressing global challenges.</p>
                    <p>Through this platform, we amplify refugee voices, connect innovators with resources, and inspire a global community to recognize and support refugee-led initiatives across education, livelihoods, health, and beyond.</p>
                </div>
            </section>
        </div>
    `;
}

// Gallery page
function renderGalleryPage() {
    return `
        <div class="gallery-page">
            <div class="gallery-header">
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Story Gallery</h1>
                <p style="font-size: 1.125rem; color: var(--gray-600); margin-bottom: 2rem;">Explore innovations from refugee communities worldwide</p>

                <input type="text" id="searchInput" class="search-bar" placeholder="Search stories by title, innovator, or description..." oninput="filterStories()">

                <div class="filters">
                    <select id="regionFilter" onchange="filterStories()">
                        <option value="">All Regions</option>
                        <option value="East Africa">East Africa</option>
                        <option value="West Africa">West Africa</option>
                        <option value="Middle East">Middle East</option>
                        <option value="Southeast Asia">Southeast Asia</option>
                        <option value="Latin America">Latin America</option>
                    </select>

                    <select id="themeFilter" onchange="filterStories()">
                        <option value="">All Themes</option>
                        <option value="Education">Education</option>
                        <option value="Livelihoods">Livelihoods</option>
                        <option value="Health">Health</option>
                        <option value="Technology">Technology</option>
                        <option value="Arts & Culture">Arts & Culture</option>
                    </select>
                </div>
            </div>

            <div class="stories-section">
                <div class="stories-grid" id="storiesGrid">
                    ${renderStoryCards()}
                </div>
            </div>
        </div>
    `;
}

// Render story cards
function renderStoryCards() {
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const regionFilter = document.getElementById('regionFilter')?.value || '';
    const themeFilter = document.getElementById('themeFilter')?.value || '';

    let filtered = stories.filter(story => {
        const matchesSearch = !searchTerm ||
            story.title.toLowerCase().includes(searchTerm) ||
            story.innovator_name.toLowerCase().includes(searchTerm) ||
            story.description.toLowerCase().includes(searchTerm);

        const matchesRegion = !regionFilter || story.region === regionFilter;
        const matchesTheme = !themeFilter || story.theme === themeFilter;

        return matchesSearch && matchesRegion && matchesTheme;
    });

    if (filtered.length === 0) {
        return '<p style="text-align: center; color: #6b7280; padding: 4rem;">No stories match your filters.</p>';
    }

    return filtered.map(story => `
        <div class="story-card" onclick="navigateTo('story', ${story.id})">
            <img src="${story.image_url || 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800'}" alt="${story.title}" class="story-image" onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800'">
            <div class="story-content">
                <div class="story-meta">
                    <span class="badge badge-region">${story.region}</span>
                    <span class="badge badge-theme">${story.theme}</span>
                </div>
                <h3>${story.title}</h3>
                <p class="innovator">by ${story.innovator_name || 'Anonymous'}</p>
                <p class="description">${(story.description || story.summary || 'No description available.').substring(0, 120)}...</p>
            </div>
        </div>
    `).join('');
}

// Filter stories
function filterStories() {
    const grid = document.getElementById('storiesGrid');
    if (grid) {
        grid.innerHTML = renderStoryCards();
    }
}

// Story detail page
function renderStoryDetailPage() {
    if (!currentStory) {
        return '<div class="loading"><p>Story not found</p></div>';
    }

    const story = stories.find(s => s.id === currentStory);
    if (!story) {
        return '<div class="loading"><p>Story not found</p></div>';
    }

    // Track story view
    trackAnalytics('story_view', { story_id: story.id, story_title: story.title });

    return `
        <div class="gallery-page">
            <div style="max-width: 900px; margin: 0 auto; padding: 2rem 1rem;">
                <button onclick="navigateTo('gallery')" class="btn" style="margin-bottom: 2rem;">
                    ← Back to Gallery
                </button>

                <div class="form-card">
                    <img src="${story.image_url || 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800'}" alt="${story.title}" style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 2rem;" onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800'">

                    <div class="story-meta" style="margin-bottom: 1.5rem;">
                        <span class="badge badge-region">${story.region}</span>
                        <span class="badge badge-theme">${story.theme}</span>
                    </div>

                    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">${story.title}</h1>
                    <p style="font-size: 1.125rem; color: var(--gray-600); margin-bottom: 2rem;">by ${story.innovator_name}</p>

                    ${story.location ? `
                        <div style="margin-bottom: 1.5rem; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                            <strong>📍 Location:</strong> ${story.location}
                        </div>
                    ` : ''}

                    <div style="line-height: 1.8; color: var(--gray-700); margin-bottom: 2rem;">
                        <p>${story.description || story.summary || 'No description available.'}</p>
                    </div>

                    <div style="background: var(--emerald-50); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                        <h3 style="color: var(--emerald-700); margin-bottom: 0.5rem;">Impact</h3>
                        <p style="color: var(--gray-700);">${story.impact || 'Making a positive difference in the community.'}</p>
                        ${story.beneficiaries_count ? `
                            <p style="color: var(--emerald-700); margin-top: 0.5rem; font-weight: 600;">
                                Beneficiaries: ${story.beneficiaries_count} people
                            </p>
                        ` : ''}
                    </div>

                    ${(story.contact_info || story.contact_email) ? `
                        <div style="background: var(--gray-50); padding: 1.5rem; border-radius: 8px;">
                            <h3 style="margin-bottom: 0.5rem;">Contact</h3>
                            ${story.contact_email ? `<p style="color: var(--gray-700);"><strong>Email:</strong> <a href="mailto:${story.contact_email}">${story.contact_email}</a></p>` : ''}
                            ${story.contact_info ? `<p style="color: var(--gray-700); margin-top: 0.5rem;">${story.contact_info}</p>` : ''}
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Submit story page
function renderSubmitStoryPage() {
    const isAuth = window.auth?.isAuthenticated();

    if (!isAuth) {
        return `
            <div class="submit-page">
                <div class="submit-container">
                    <div class="form-card">
                        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Admin Access Required</h1>
                        <p style="color: var(--gray-600); margin-bottom: 2rem;">Only administrators can submit stories.</p>
                        <button class="btn btn-primary" onclick="navigateTo('login')">Sign In</button>
                    </div>
                </div>
            </div>
        `;
    }

    return `
        <div class="submit-page">
            <div class="submit-container">
                <div class="form-card">
                    <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Submit Your Story</h1>
                    <p style="color: var(--gray-600); margin-bottom: 2rem;">Share your innovation with the world</p>

                    <div id="submitMessage"></div>

                    <form id="submitStoryForm" onsubmit="handleSubmitStory(event)" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Story Title *</label>
                            <input type="text" name="title" required placeholder="Give your innovation a compelling name">
                        </div>

                        <div class="form-group">
                            <label>Innovator Name *</label>
                            <input type="text" name="innovator_name" required placeholder="Your name or organization">
                        </div>

                        <div class="form-group">
                            <label>Region *</label>
                            <select name="region" required>
                                <option value="">Select a region</option>
                                <option value="East Africa">East Africa</option>
                                <option value="West Africa">West Africa</option>
                                <option value="Middle East">Middle East</option>
                                <option value="Southeast Asia">Southeast Asia</option>
                                <option value="Latin America">Latin America</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Theme *</label>
                            <select name="theme" required>
                                <option value="">Select a theme</option>
                                <option value="Education">Education</option>
                                <option value="Livelihoods">Livelihoods</option>
                                <option value="Health">Health</option>
                                <option value="Technology">Technology</option>
                                <option value="Arts & Culture">Arts & Culture</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" required placeholder="Describe your innovation, the problem it solves, and how it works"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Impact *</label>
                            <textarea name="impact" required placeholder="What impact has this innovation had on your community?"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Image *</label>
                            <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">
                                Upload an image from your device (JPEG, PNG, GIF, or WebP, max 5MB)
                            </p>
                            <input type="file" name="image" id="imageInput" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(event)">
                            <div id="imagePreview" style="margin-top: 1rem; display: none;">
                                <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 2px solid var(--gray-200);">
                                <button type="button" onclick="clearImagePreview()" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: var(--gray-200); color: var(--gray-700); border: none; border-radius: 4px; cursor: pointer;">
                                    Remove Image
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Or provide Image URL (optional)</label>
                            <input type="url" name="image_url" id="imageUrlInput" placeholder="https://example.com/image.jpg" onchange="clearFileInput()">
                            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.5rem;">
                                If you provide a URL, the uploaded file will be ignored
                            </p>
                        </div>

                        <div class="form-group">
                            <label>Location *</label>
                            <input type="text" name="location" required placeholder="City, Country (e.g., Nairobi, Kenya)">
                        </div>

                        <div class="form-group">
                            <label>Contact Email *</label>
                            <input type="email" name="contact_email" required placeholder="your-email@example.com">
                        </div>

                        <div class="form-group">
                            <label>Additional Contact Information</label>
                            <input type="text" name="contact_info" placeholder="Website or phone (optional)">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            Submit Story
                        </button>
                    </form>
                </div>
            </div>
        </div>
    `;
}

// Preview uploaded image
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Clear image preview
function clearImagePreview() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
}

// Clear file input when URL is provided
function clearFileInput() {
    const urlInput = document.getElementById('imageUrlInput');
    if (urlInput.value) {
        document.getElementById('imageInput').value = '';
        document.getElementById('imagePreview').style.display = 'none';
    }
}

// Map page
function renderMapPage() {
    const regionCounts = {};
    stories.forEach(story => {
        regionCounts[story.region] = (regionCounts[story.region] || 0) + 1;
    });

    // Track map page view
    trackAnalytics('page_view', { page: 'map' });

    return `
        <div class="gallery-page">
            <div style="max-width: 1280px; margin: 0 auto; padding: 2rem 1rem;">
                <div class="section-header">
                    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Innovation Map</h1>
                    <p>Geographic distribution of refugee-led innovations</p>
                </div>

                <div id="mapContainer" style="height: 600px; margin: 2rem 0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);"></div>

                <div class="form-card" style="max-width: 800px; margin: 2rem auto;">
                    <h3 style="margin-bottom: 1.5rem;">Innovations by Region</h3>
                    ${Object.entries(regionCounts).map(([region, count]) => `
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="font-weight: 600;">${region}</span>
                                <span style="color: var(--emerald-600); font-weight: bold;">${count} ${count === 1 ? 'story' : 'stories'}</span>
                            </div>
                            <div style="background: var(--gray-200); height: 12px; border-radius: 6px; overflow: hidden;">
                                <div style="background: linear-gradient(90deg, var(--emerald-600), var(--teal-600)); height: 100%; width: ${stories.length > 0 ? (count / stories.length) * 100 : 0}%; border-radius: 6px;"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>

                <div style="text-align: center; margin-top: 3rem;">
                    <button class="btn btn-primary" onclick="navigateTo('gallery')">
                        Explore All Stories
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Initialize map after render
let mapInstance = null;

function initializeMap() {
    if (currentPage !== 'map' || typeof L === 'undefined') {
        return;
    }

    const mapContainer = document.getElementById('mapContainer');
    if (!mapContainer) return;

    // Destroy existing map if it exists
    if (mapInstance) {
        mapInstance.remove();
        mapInstance = null;
    }

    // Default center (world view)
    mapInstance = L.map('mapContainer').setView([20, 0], 2);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(mapInstance);

    // Add markers for stories with coordinates
    const storiesWithCoords = stories.filter(s => s.latitude && s.longitude);
    
    if (storiesWithCoords.length === 0) {
        // If no coordinates, use region-based approximate locations
        const regionCoords = {
            'East Africa': [1.0, 38.0],
            'West Africa': [8.0, -5.0],
            'Middle East': [31.0, 36.0],
            'Southeast Asia': [5.0, 120.0],
            'Latin America': [-15.0, -60.0]
        };

        const regionGroups = {};
        stories.forEach(story => {
            if (!regionGroups[story.region]) {
                regionGroups[story.region] = [];
            }
            regionGroups[story.region].push(story);
        });

        Object.entries(regionGroups).forEach(([region, regionStories]) => {
            const coords = regionCoords[region] || [0, 0];
            const marker = L.marker(coords).addTo(mapInstance);
            const popupContent = `
                <div style="min-width: 200px;">
                    <h4 style="margin: 0 0 0.5rem 0; font-weight: bold;">${region}</h4>
                    <p style="margin: 0 0 0.5rem 0; color: #666;">${regionStories.length} ${regionStories.length === 1 ? 'innovation' : 'innovations'}</p>
                    <div style="max-height: 200px; overflow-y: auto;">
                        ${regionStories.map(s => `
                            <div style="margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid #eee;">
                                <strong>${s.title}</strong><br>
                                <small>${s.theme}</small>
                            </div>
                        `).join('')}
                    </div>
                    <button onclick="navigateTo('gallery')" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: var(--emerald-600); color: white; border: none; border-radius: 4px; cursor: pointer;">
                        View Stories
                    </button>
                </div>
            `;
            marker.bindPopup(popupContent);
        });
    } else {
        // Add individual markers for stories with coordinates
        storiesWithCoords.forEach(story => {
            const marker = L.marker([parseFloat(story.latitude), parseFloat(story.longitude)]).addTo(mapInstance);
            const description = story.description || story.summary || 'No description available.';
            const popupContent = `
                <div style="min-width: 250px;">
                    ${story.image_url ? `<img src="${story.image_url}" alt="${story.title}" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px; margin-bottom: 0.5rem;">` : ''}
                    <h4 style="margin: 0 0 0.5rem 0; font-weight: bold;">${story.title}</h4>
                    <p style="margin: 0 0 0.5rem 0; color: #666; font-size: 0.875rem;">by ${story.innovator_name || 'Anonymous'}</p>
                    <p style="margin: 0 0 0.5rem 0; font-size: 0.875rem;">${description.substring(0, 100)}${description.length > 100 ? '...' : ''}</p>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="background: var(--emerald-100); color: var(--emerald-700); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">${story.region || 'Unknown'}</span>
                        <span style="background: var(--gray-100); color: var(--gray-700); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">${story.theme || 'General'}</span>
                    </div>
                    <button onclick="navigateTo('story', ${story.id})" style="width: 100%; padding: 0.5rem 1rem; background: var(--emerald-600); color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Read More
                    </button>
                </div>
            `;
            marker.bindPopup(popupContent);
        });

        // Fit map to show all markers
        if (storiesWithCoords.length > 0) {
            const group = new L.featureGroup(storiesWithCoords.map(s => 
                L.marker([parseFloat(s.latitude), parseFloat(s.longitude)])
            ));
            mapInstance.fitBounds(group.getBounds().pad(0.1));
        }
    }
}

// Login page
function renderLoginPage() {
    return `
        <div class="login-page">
            <div class="login-card">
                <h2>Field Agent Sign In</h2>
                <p>Access the admin dashboard to manage story submissions</p>

                <div id="loginMessage"></div>

                <form onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="your-email@example.com">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Enter your password">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Sign In
                    </button>
                </form>

                <p style="text-align: center; margin-top: 1.5rem; color: var(--gray-600); font-size: 0.875rem;">
                    Need access? Contact your administrator.
                </p>
            </div>
        </div>
    `;
}

// Dashboard page
async function renderDashboardPage() {
    if (!window.auth?.isAuthenticated()) {
        navigateTo('login');
        return '';
    }

    // Load analytics
    const stats = await loadDashboardStats();

    return `
        <div class="dashboard">
            <div class="dashboard-container">
                <h1>Admin Dashboard</h1>
                <p style="color: var(--gray-600); margin-bottom: 2rem;">Welcome back! Manage story submissions and view analytics.</p>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Published Stories</h3>
                        <div class="value">${stats.publishedStories}</div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Views</h3>
                        <div class="value">${stats.totalViews}</div>
                    </div>
                    <div class="stat-card">
                        <h3>Pending Submissions</h3>
                        <div class="value">${stats.pendingSubmissions}</div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Submissions</h3>
                        <div class="value">${stats.totalSubmissions}</div>
                    </div>
                </div>

                <div class="submissions-table">
                    <h2>Pending Submissions</h2>
                    ${submissions.length > 0 ? `
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Innovator</th>
                                    <th>Region</th>
                                    <th>Theme</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${submissions.map(sub => `
                                    <tr>
                                        <td>${sub.title}</td>
                                        <td>${sub.innovator_name}</td>
                                        <td>${sub.region}</td>
                                        <td>${sub.theme}</td>
                                        <td class="action-buttons">
                                            <button class="btn-approve" onclick="handleApprove(${sub.id})">Approve</button>
                                            <button class="btn-reject" onclick="handleReject(${sub.id})">Reject</button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    ` : '<p style="text-align: center; color: var(--gray-600); padding: 2rem;">No pending submissions</p>'}
                </div>

                <div class="submissions-table" style="margin-top: 3rem;">
                    <h2>Published Stories</h2>
                    ${stories.length > 0 ? `
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Innovator</th>
                                    <th>Region</th>
                                    <th>Theme</th>
                                    <th>Views</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${stories.map(story => `
                                    <tr>
                                        <td>${story.title}</td>
                                        <td>${story.innovator_name}</td>
                                        <td>${story.region}</td>
                                        <td>${story.theme}</td>
                                        <td>${story.view_count || story.views || 0}</td>
                                        <td class="action-buttons">
                                            <button class="btn-view" onclick="navigateTo('story', ${story.id})">View</button>
                                            <button class="btn-delete" onclick="handleDeleteStory(${story.id}, '${story.title.replace(/'/g, "\\'")}')">Delete</button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    ` : '<p style="text-align: center; color: var(--gray-600); padding: 2rem;">No published stories</p>'}
                </div>
            </div>
        </div>
    `;
}

// Handle login
async function handleLogin(event) {
    event.preventDefault();
    const form = event.target;
    const email = form.email.value;
    const password = form.password.value;
    const messageDiv = document.getElementById('loginMessage');

    messageDiv.innerHTML = '<div class="alert alert-info">Signing in...</div>';

    try {
        const { user, error } = await window.auth.signIn(email, password);

        if (error) {
            messageDiv.innerHTML = `<div class="alert alert-error">${error}</div>`;
        } else {
            messageDiv.innerHTML = '<div class="alert alert-success">Sign in successful! Redirecting...</div>';
            setTimeout(() => navigateTo('dashboard'), 1000);
        }
    } catch (error) {
        messageDiv.innerHTML = `<div class="alert alert-error">Login failed: ${error.message}</div>`;
    }
}

// Handle sign out
async function handleSignOut() {
    await window.auth.signOut();
    navigateTo('home');
}

// Handle story submission
async function handleSubmitStory(event) {
    event.preventDefault();
    const form = event.target;
    const messageDiv = document.getElementById('submitMessage');

    messageDiv.innerHTML = '<div class="alert alert-info">Submitting your story...</div>';

    // Check if file is uploaded or URL is provided
    const fileInput = form.image;
    const imageUrl = form.image_url.value;
    const hasFile = fileInput.files && fileInput.files.length > 0;
    const hasUrl = imageUrl && imageUrl.trim() !== '';

    if (!hasFile && !hasUrl) {
        messageDiv.innerHTML = '<div class="alert alert-error">Please upload an image or provide an image URL.</div>';
        return;
    }

    // Create FormData for file upload
    const formData = new FormData();
    formData.append('title', form.title.value);
    formData.append('innovator_name', form.innovator_name.value);
    formData.append('region', form.region.value);
    formData.append('theme', form.theme.value);
    formData.append('description', form.description.value);
    formData.append('impact', form.impact.value);
    formData.append('location', form.location.value);
    formData.append('contact_email', form.contact_email.value);
    if (form.contact_info.value) {
        formData.append('contact_info', form.contact_info.value);
    }
    
    // Add file if uploaded
    if (hasFile) {
        formData.append('image', fileInput.files[0]);
    }
    
    // Add URL if provided (will override file if both are present)
    if (hasUrl) {
        formData.append('image_url', imageUrl);
    }

    try {
        // Use fetch directly for FormData
        const response = await fetch('api/submissions.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        const result = await response.json();

        if (!response.ok || result.error) {
            messageDiv.innerHTML = `<div class="alert alert-error">Error: ${result.error || 'Submission failed'}</div>`;
        } else {
            messageDiv.innerHTML = '<div class="alert alert-success">Thank you! Your story has been submitted and will be reviewed shortly.</div>';
            form.reset();
            document.getElementById('imagePreview').style.display = 'none';
            await loadSubmissions();
            trackAnalytics('submission', { type: 'story_submission' });
        }
    } catch (error) {
        messageDiv.innerHTML = `<div class="alert alert-error">Error: ${error.message}</div>`;
    }
}

// Handle approve submission
async function handleApprove(id) {
    if (!confirm('Approve this submission?')) return;

    const submission = submissions.find(s => s.id === id);
    if (!submission) return;

    try {
        // Update submission status to approved (this will automatically create the story)
        const { data, error } = await window.supabase.updateSubmissionStatus(id, 'approved');

        if (error) {
            alert('Error approving submission: ' + error);
            return;
        }

        // Track approval
        trackAnalytics('submission_approved', { submission_id: id });

        // Reload data
        await loadStories();
        await loadSubmissions();
        render();
    } catch (error) {
        alert('Error approving submission: ' + error.message);
    }
}

// Handle reject submission
async function handleReject(id) {
    if (!confirm('Reject this submission?')) return;

    try {
        const { data, error } = await window.supabase.updateSubmissionStatus(id, 'rejected');

        if (error) {
            alert('Error rejecting submission: ' + error);
            return;
        }

        await loadSubmissions();
        render();
    } catch (error) {
        alert('Error rejecting submission: ' + error.message);
    }
}

// Handle delete story
async function handleDeleteStory(id, title) {
    if (!confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) return;

    try {
        const { data, error } = await window.supabase.deleteStory(id);

        if (error) {
            alert('Error deleting story: ' + error);
            return;
        }

        alert('Story deleted successfully');
        await loadStories();
        render();
    } catch (error) {
        alert('Error deleting story: ' + error.message);
    }
}

// Expose function globally
window.handleDeleteStory = handleDeleteStory;

// Load stories from database
async function loadStories() {
    try {
        const { data, error } = await window.supabase.getStories();

        if (!error && data) {
            stories = data.map(story => ({
                ...story,
                // Map view_count to views for backward compatibility
                views: story.view_count || story.views || 0
            }));
        } else if (!data || data.length === 0) {
            // Load sample data if database is empty
            stories = getSampleStories();
        }
    } catch (error) {
        console.error('Error loading stories:', error);
        // Fallback to sample data
        stories = getSampleStories();
    }
}

// Sample stories for demonstration
function getSampleStories() {
    return [
        {
            id: 1,
            title: "Digital Learning Platform for Refugee Children",
            innovator_name: "Amina Hassan",
            region: "East Africa",
            theme: "Education",
            description: "Amina created an innovative digital learning platform that provides quality education to refugee children in Kakuma camp. The platform works offline and includes curriculum in multiple languages, making education accessible even without consistent internet connectivity.",
            impact: "Over 500 children have accessed quality education through this platform, with 80% showing improved literacy rates.",
            location: "Kakuma, Kenya",
            latitude: 3.7167,
            longitude: 34.8667,
            image_url: "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800",
            contact_email: "amina@example.com",
            is_featured: true,
            views: 0,
            beneficiaries_count: 500
        },
        {
            id: 2,
            title: "Solar-Powered Water Purification System",
            innovator_name: "Mohammed Al-Rashid",
            region: "Middle East",
            theme: "Health",
            description: "Mohammed developed a low-cost, solar-powered water purification system that provides clean drinking water to refugee communities. The system uses locally available materials and can be maintained by community members.",
            impact: "The system provides clean water to over 1,000 people daily, reducing waterborne diseases by 60%.",
            location: "Zaatari, Jordan",
            latitude: 32.3078,
            longitude: 36.3275,
            image_url: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800",
            contact_email: "mohammed@example.com",
            is_featured: true,
            views: 0,
            beneficiaries_count: 1000
        },
        {
            id: 3,
            title: "Mobile App for Job Matching",
            innovator_name: "Fatima Al-Zahra",
            region: "Middle East",
            theme: "Livelihoods",
            description: "Fatima created a mobile application that connects refugee job seekers with local employers. The app includes skills assessment, resume building, and job matching features tailored to refugee needs.",
            impact: "Over 300 refugees have found employment through this platform, improving their economic independence.",
            location: "Beirut, Lebanon",
            latitude: 33.8938,
            longitude: 35.5018,
            image_url: "https://images.unsplash.com/photo-1551434678-e076c223a692?w=800",
            contact_email: "fatima@example.com",
            is_featured: true,
            views: 0,
            beneficiaries_count: 300
        },
        {
            id: 4,
            title: "Community Garden Initiative",
            innovator_name: "Jean-Baptiste Nkurunziza",
            region: "East Africa",
            theme: "Livelihoods",
            description: "Jean-Baptiste established a community garden that provides fresh vegetables to refugee families while creating income opportunities. The garden uses sustainable farming techniques and trains community members.",
            impact: "The garden feeds 200 families and generates income for 50 community members through vegetable sales.",
            location: "Kigali, Rwanda",
            latitude: -1.9441,
            longitude: 30.0619,
            image_url: "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800",
            contact_email: "jean@example.com",
            is_featured: false,
            views: 0,
            beneficiaries_count: 200
        },
        {
            id: 5,
            title: "Refugee Artisan Marketplace",
            innovator_name: "Sara Ahmed",
            region: "Southeast Asia",
            theme: "Arts & Culture",
            description: "Sara created an online marketplace that connects refugee artisans with global customers. The platform showcases traditional crafts and provides fair-trade opportunities for refugee communities.",
            impact: "Over 100 artisans have sold their products globally, generating sustainable income and preserving cultural heritage.",
            location: "Kuala Lumpur, Malaysia",
            latitude: 3.1390,
            longitude: 101.6869,
            image_url: "https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=800",
            contact_email: "sara@example.com",
            is_featured: true,
            views: 0,
            beneficiaries_count: 100
        }
    ];
}

// Load submissions from database
async function loadSubmissions() {
    try {
        const { data, error } = await window.supabase.getSubmissions('pending');

        if (!error && data) {
            submissions = data;
        }
    } catch (error) {
        console.error('Error loading submissions:', error);
    }
}

// Load dashboard stats
async function loadDashboardStats() {
    try {
        const { data, error } = await window.supabase.getStats();
        
        if (!error && data) {
            return data;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }

    // Fallback to local stats
    return {
        publishedStories: stories.length,
        totalViews: stories.reduce((sum, story) => sum + (story.views || 0), 0),
        pendingSubmissions: submissions.length,
        totalSubmissions: submissions.length
    };
}

// Track analytics
async function trackAnalytics(eventType, metadata = {}) {
    // Track in Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', eventType, metadata);
    }

    // Track in PHP API
    try {
        await window.supabase.trackAnalytics(eventType, {
            ...metadata,
            story_id: metadata.story_id || null
        });
    } catch (error) {
        console.error('Error tracking analytics:', error);
    }
}

// Main render function
async function render() {
    const app = document.getElementById('app');
    if (!app) return;

    let content = '';

    switch (currentPage) {
        case 'home':
            content = renderHomePage();
            trackAnalytics('page_view', { page: 'home' });
            break;
        case 'gallery':
            content = renderGalleryPage();
            trackAnalytics('page_view', { page: 'gallery' });
            break;
        case 'story':
            content = renderStoryDetailPage();
            if (currentStory) {
                trackAnalytics('story_view', { story_id: currentStory });
            }
            break;
        case 'submit':
            content = renderSubmitStoryPage();
            trackAnalytics('page_view', { page: 'submit' });
            break;
        case 'map':
            content = renderMapPage();
            break;
        case 'login':
            content = renderLoginPage();
            break;
        case 'dashboard':
            content = await renderDashboardPage();
            trackAnalytics('page_view', { page: 'dashboard' });
            break;
        default:
            content = renderHomePage();
    }

    app.innerHTML = renderHeader() + content + renderFooter();

    // Initialize map after DOM is updated
    setTimeout(() => {
        if (currentPage === 'map') {
            initializeMap();
        }
    }, 100);
}

// Initialize app
async function init() {
    await loadStories();
    await loadSubmissions();
    render();

    // Listen for auth state changes
    window.addEventListener('authStateChanged', () => {
        render();
    });
}

// Start the app when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
