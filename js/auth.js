/**
 * PHP Authentication Module
 * Works with auth.php (login, logout, check session)
 */

window.authState = {
    user: null,
    initialized: false
};

// API Base Path - use relative path for XAMPP compatibility
function getApiUrl() {
    const pathname = window.location.pathname;
    const baseDir = pathname.substring(0, pathname.lastIndexOf('/') + 1);
    return `${baseDir}api/auth.php`;
}
const API_URL = getApiUrl();

// Initialize authentication (check if logged in)
async function initializePHPAuth() {
    try {
        const res = await fetch(`${API_URL}?action=check`, {
            method: "POST",
            credentials: "include" // important for PHP sessions
        });

        const data = await res.json();

        if (data.authenticated) {
            window.authState.user = data.user;
        }

        window.authState.initialized = true;

        window.dispatchEvent(new CustomEvent("authStateChanged", {
            detail: { user: window.authState.user }
        }));
    } catch (error) {
        console.error("Auth init error:", error);
    }
}

// Login user
async function signIn(email, password) {
    try {
        const res = await fetch(`${API_URL}?action=login`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (data.error) {
            return { user: null, error: data.error };
        }

        if (data.user) {
            window.authState.user = data.user;

            window.dispatchEvent(new CustomEvent("authStateChanged", {
                detail: { user: data.user }
            }));
        }

        return { user: data.user, error: null };
    } catch (error) {
        return { user: null, error: error.message };
    }
}

// Logout
async function signOut() {
    await fetch(`${API_URL}?action=logout`, {
        method: "POST",
        credentials: "include"
    });

    window.authState.user = null;

    window.dispatchEvent(new CustomEvent("authStateChanged", {
        detail: { user: null }
    }));
}

function getCurrentUser() {
    return window.authState.user;
}

function isAuthenticated() {
    return window.authState.user !== null;
}

window.auth = {
    initialize: initializePHPAuth,
    signIn,
    signOut,
    getCurrentUser,
    isAuthenticated
};

// Auto-run on page load
document.addEventListener("DOMContentLoaded", initializePHPAuth);
