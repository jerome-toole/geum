// Get a Date a number of days in the future.
const futureDate = (days) => new Date(Date.now() + days * 24 * 60 * 60 * 1000);

// Get a future date as a UTC string.
const utcFutureDate = (days) => futureDate(days).toUTCString();

// Create a key-value string for a cookie attribute.
const cAttr = (key, value) => `${key}=${value};`;

// Set a cookie with a given lifetime (in days).
const setCookie = (name, value, lifetime) => {
    document.cookie =
        cAttr(name, value) +
        cAttr('expires', utcFutureDate(lifetime)) +
        cAttr('path', '/') +
        cAttr('SameSite', 'strict');
};

// Retrieve a cookie value by name. Empty string if not found.
const getCookie = (cname) => {
    const name = `${cname}=`;
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i += 1) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
};

export { setCookie, getCookie };
