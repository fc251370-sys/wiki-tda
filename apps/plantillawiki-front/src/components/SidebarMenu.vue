<script setup>
import { computed } from 'vue';
import { menuItems } from '../utils/menuDb';
import { getStoredList } from '../utils/access';

const emit = defineEmits(['navigate']);
const isAuthenticated = Boolean(localStorage.getItem('token'));
const roles = getStoredList('roles');
const permissions = getStoredList('permissions');

const hasAccess = (item) => {
  if (item.guestOnly && isAuthenticated) {
    return false;
  }

  if (item.requiresAuth && !isAuthenticated) {
    return false;
  }

  const hasRoleRequirement = item.roles?.length > 0;
  const hasPermissionRequirement = item.permisos?.length > 0;

  if (hasRoleRequirement && !item.roles.some((role) => roles.includes(role))) {
    return false;
  }

  if (
    hasPermissionRequirement &&
    !item.permisos.some((permission) => permissions.includes(permission))
  ) {
    return false;
  }

  return true;
};

const filterMenu = (items) =>
  items
    .filter(hasAccess)
    .map((item) => {
      if (!item.children) {
        return item;
      }

      return {
        ...item,
        children: filterMenu(item.children),
      };
    })
    .filter((item) => !item.children || item.children.length > 0);

const menu = computed(() => filterMenu(menuItems));
</script>

<template>
  <v-list class="sidebar-menu" color="primary" density="comfortable" nav>
    <template v-for="item in menu" :key="item.title">
      <v-list-item
        v-if="!item.children"
        :to="item.to"
        :prepend-icon="item.icon"
        :title="item.title"
        active-class="sidebar-menu__item--active"
        exact
        @click="emit('navigate')"
      />

      <v-list-group v-else :value="item.title">
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
          :prepend-icon="child.icon"
          :title="child.title"
          active-class="sidebar-menu__item--active"
          exact
          @click="emit('navigate')"
        />
      </v-list-group>
    </template>
  </v-list>
</template>

<style scoped>
.sidebar-menu {
  padding: 16px 12px;
}

.sidebar-menu :deep(.v-list-item) {
  min-height: 46px;
  margin-bottom: 4px;
  border-radius: 10px;
  
}

.sidebar-menu :deep(.v-list-item--active) {
  background: rgb(var(--v-theme-primary));
  color: rgb(var(--v-theme-on-primary));
}

.sidebar-menu :deep(.v-list-item--active .v-icon) {
  color: rgb(var(--v-theme-on-primary));
}
</style>
