<script setup>
import { menuItems } from '../utils/menuDb';
import { ref } from 'vue';

const roles = JSON.parse(localStorage.getItem('roles')) || [];
const permissions = JSON.parse(localStorage.getItem('permissions')) || [];

const hasAccess = (item) => {
    const requiresRoles = item.roles && item.roles.length > 0;
    const requiresPermissions = item.permisos && item.permisos.length > 0;

    // Si no requiere roles ni permisos, se permite el acceso
    if (!requiresRoles && !requiresPermissions) {
        return true;
    }

    // Validar roles
    if (requiresRoles && item.roles.some(role => roles.includes(role))) {
        return true;
    }

    // Validar permisos
    if (requiresPermissions && item.permisos.some(permission => permissions.includes(permission))) {
        return true;
    }

    return false;
};

const filterMenu = (items) => {
    return items
        .filter(hasAccess)
        .map(item => {
            if (item.children) {
                const filteredChildren = filterMenu(item.children);
                return { ...item, children: filteredChildren };
            }
            return item;
        })
        // Ocultar grupos que se quedaron sin hijos después de filtrar
        .filter(item => !item.children || item.children.length > 0);
};

const menu = ref(filterMenu(menuItems));

</script>

<template>
    <v-list density="comfortable" nav>
        <template v-for="item in menu" :key="item.title">

            <!-- Item simple -->
            <v-list-item
                v-if="!item.children"
                :to="item.to"
                :prepend-icon="item.icon"
                :title="item.title"
                exact
            />

            <!-- Grupo -->
            <v-list-group
                v-else
                :value="item.title"
            >
                <template #activator="{ props }">
                    <v-list-item
                        v-bind="props"
                        :prepend-icon="item.icon"
                        :title="item.title"
                    />
                </template>

                <v-list-item
                    v-for="child in item.children"
                    :key="child.title"
                    :to="child.to"
                    :title="child.title"
                    exact
                />
            </v-list-group>

        </template>
    </v-list>
</template>
