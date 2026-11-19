export function clearAuthSession() {
  for (const key of ['token', 'user', 'roles', 'permissions']) {
    localStorage.removeItem(key)
  }
}

export function getRoles() {
  try {
    return JSON.parse(localStorage.getItem('roles')) ?? []
  } catch (error) {
    return []
  }
}

export function hasRole(...roles) {
  const userRoles = getRoles()
  return roles.some((r)=>userRoles.includes(r))
}

export function canManageCarreras() {
  return hasRole('admin', 'coordenador')
}

export function canManageWiki() {
  return hasRole('admin', 'coordinador', 'teacher')
} 
