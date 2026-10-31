export function clearAuthSession() {
  for (const key of ['token', 'user', 'roles', 'permissions']) {
    localStorage.removeItem(key)
  }
}
