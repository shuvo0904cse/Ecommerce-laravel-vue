/**
 * Save Token
 */
export function saveToken(token) {
    return localStorage.setItem("token", "Bearer " + token);
}

/**
 * Remove Token
 */
export function removeToken() {
    return localStorage.removeItem("token");
}

/**
 * Get Token
 */
export function getToken() {
    return localStorage.getItem("token")
}