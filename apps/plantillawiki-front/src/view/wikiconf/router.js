export const routerWikiconf = [
  { path: 'wikiconf',
    name: 'wiki-conf', component: () => import('./index.vue'), 
    meta: { roles: ['admin', 'coordinador'] } },

  { path: 'wikiconf/categoria', 
    name: 'wiki-categoria', component: () => import('./categoria/index.vue'), 
    meta: { roles: ['admin', 'coordinador'] } },

  { path: 'wikiconf/tag', 
    name: 'wiki-tag', component: () => import('./tag/index.vue'), 
    meta: { roles: ['admin', 'coordinador'] } },
]