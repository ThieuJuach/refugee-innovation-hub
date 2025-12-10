/**
 * PHP API Client (improved)
 * - Default baseUrl is 'api' (relative) so site works when served from subfolder
 * - Detects FormData and avoids forcing Content-Type
 * - Gracefully handles HTML (404/500 pages) responses
 */

class PHPAPIClient {
    constructor(baseUrl = 'api') {
        // default to relative 'api' folder so paths like "ProjectJRS1-main/index.html" work
        this.baseUrl = baseUrl;
    }

    async request(endpoint, options = {}) {
        // Ensure endpoint begins with a slash if baseUrl ends without slash
        const url = `${this.baseUrl}${endpoint.startsWith('/') ? endpoint : '/' + endpoint}`;

        const method = (options.method || 'GET').toUpperCase();
        const config = {
            method,
            credentials: 'same-origin',
            ...options
        };

        // Build headers only if user explicitly passed them
        config.headers = options.headers ? { ...options.headers } : {};

        // If body is FormData, do NOT set Content-Type; let the browser set boundary
        if (config.body instanceof FormData) {
            // leave headers alone
        } else if (config.body && typeof config.body === 'object') {
            // For plain objects, send JSON
            config.headers['Content-Type'] = 'application/json';
            config.body = JSON.stringify(config.body);
        } else if (typeof config.body === 'string') {
            // If it's a string, assume user formatted it correctly (or it's empty)
            if (!config.headers['Content-Type']) config.headers['Content-Type'] = 'application/json';
        }

        try {
            const response = await fetch(url, config);

            // read raw text first
            const text = await response.text();

            // quick detection: if the text looks like HTML (starts with '<'), treat it as an error
            const trimmed = (text || '').trim();
            if (trimmed.startsWith('<')) {
                // Return helpful info so caller sees the HTML and error code
                return {
                    data: null,
                    error: `Server returned HTML (status ${response.status}). Response preview: ${trimmed.slice(0, 500)}`
                };
            }

            // Attempt to parse JSON if there is text
            let data = {};
            try {
                data = text ? JSON.parse(text) : {};
            } catch (e) {
                return { data: null, error: 'Invalid JSON response from server' };
            }

            if (!response.ok) {
                return { data: null, error: data.error || `Request failed (status ${response.status})` };
            }

            return { data, error: null };
        } catch (err) {
            return { data: null, error: err.message || String(err) };
        }
    }

    // ---- Stories API
    async getStories(filters = {}) {
        const params = new URLSearchParams();
        if (filters.featured !== undefined) params.append('featured', filters.featured ? 1 : 0);
        if (filters.region) params.append('region', filters.region);
        if (filters.theme) params.append('theme', filters.theme);
        const query = params.toString();
        return this.request(`/stories.php${query ? '?' + query : ''}`);
    }

    async getStory(id) {
        return this.request(`/stories.php?id=${id}`);
    }

    async createStory(storyData) {
        return this.request('/stories.php', {
            method: 'POST',
            body: storyData
        });
    }

    async updateStory(id, storyData) {
        return this.request(`/stories.php?id=${id}`, {
            method: 'PUT',
            body: storyData
        });
    }

    async deleteStory(id) {
        return this.request(`/stories.php?id=${id}`, {
            method: 'DELETE'
        });
    }

    // ---- Submissions API
    async getSubmissions(status = 'pending') {
        return this.request(`/submissions.php?status=${status}`);
    }

    async createSubmission(submissionData) {
        // If submissionData is FormData, pass it through (caller must provide FormData)
        return this.request('/submissions.php', {
            method: 'POST',
            body: submissionData
        });
    }

    async updateSubmissionStatus(id, status) {
        return this.request(`/submissions.php?id=${id}`, {
            method: 'PUT',
            body: { status }
        });
    }

    // ---- Analytics API
    async trackAnalytics(eventType, metadata = {}) {
        return this.request('/analytics.php', {
            method: 'POST',
            body: {
                event_type: eventType,
                metadata: metadata
            }
        });
    }

    async getAnalytics(filters = {}) {
        const params = new URLSearchParams();
        if (filters.event_type) params.append('event_type', filters.event_type);
        if (filters.story_id) params.append('story_id', filters.story_id);
        if (filters.limit) params.append('limit', filters.limit);
        const query = params.toString();
        return this.request(`/analytics.php${query ? '?' + query : ''}`);
    }

    // ---- Stats & Auth
    async getStats() {
        return this.request('/stats.php');
    }

    async login(email, password) {
        return this.request('/auth.php?action=login', {
            method: 'POST',
            body: { email, password }
        });
    }

    async logout() {
        return this.request('/auth.php?action=logout', {
            method: 'POST'
        });
    }

    async checkAuth() {
        return this.request('/auth.php?action=check', {
            method: 'POST'
        });
    }

    // Minimal Supabase-like adapter (keeps your current app code)
    from(table) {
        return {
            select: (cols='*') => {
                return {
                    eq: (column, value) => {
                        return {
                            then: async (resolve, reject) => {
                                try {
                                    let res;
                                    if (table === 'story_submissions') {
                                        res = await this.getSubmissions(value || 'pending');
                                    } else {
                                        const filters = {};
                                        filters[column] = value;
                                        res = await this.getStories(filters);
                                    }
                                    if (res.error) reject(res.error);
                                    else resolve({ data: res.data, error: null });
                                } catch (e) {
                                    reject(e);
                                }
                            }
                        };
                    },
                    then: async (resolve, reject) => {
                        try {
                            let res;
                            if (table === 'story_submissions') res = await this.getSubmissions();
                            else res = await this.getStories();
                            if (res.error) reject(res.error); else resolve({ data: res.data, error: null });
                        } catch (e) { reject(e); }
                    }
                };
            },
            insert: (data) => {
                return {
                    then: async (resolve, reject) => {
                        try {
                            let res;
                            if (table === 'innovation_stories') res = await this.createStory(data);
                            else if (table === 'story_submissions') res = await this.createSubmission(data);
                            else if (table === 'site_analytics') res = await this.trackAnalytics(data.event_type, data.metadata || {});
                            if (res.error) reject(res.error); else resolve({ data: res.data, error: null });
                        } catch (e) { reject(e); }
                    }
                };
            },
            update: (data) => {
                return {
                    eq: (column, value) => {
                        return {
                            then: async (resolve, reject) => {
                                try {
                                    let res;
                                    if (table === 'story_submissions') res = await this.updateSubmissionStatus(value, data.status);
                                    else if (table === 'innovation_stories') res = await this.updateStory(value, data);
                                    if (res.error) reject(res.error); else resolve({ data: res.data, error: null });
                                } catch (e) { reject(e); }
                            }
                        };
                    }
                };
            }
        };
    }
}

// Export instance
const phpApi = new PHPAPIClient();
window.supabase = phpApi;
