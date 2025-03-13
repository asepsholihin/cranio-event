export default [
  {
    path: '/seo-setting',
    name: 'seo-setting',
    component: () => import('@/views/pages/seo-settings/index.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'SEO Settings',
        },
      ],
    },
  },

  {
    path: '/seo-setting/:id',
    name: 'seo-setting-detail',
    component: () => import('@/views/pages/seo-settings/edit.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'SEO Settings',
          to: '/seo-setting',
        },
        {
          text: 'Detail',
          active: true,
        },
      ],
    },
  },

]
