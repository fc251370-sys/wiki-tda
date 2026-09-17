function hasRole(required) {
    const roles = JSON.parse(localStorage.getItem('roles')) || [];

    if (Array.isArray(required)) {
        return required.some(role => roles.includes(role));
    }

    return roles.includes(required);
}

export default {
    mounted(el, binding) {
        if (!hasRole(binding.value)) {
            el.remove();
        }
    }
};