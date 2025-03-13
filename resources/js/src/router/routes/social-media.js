export default [
  {
    path: '/social-media',
    name: 'social-media',
    component: () => import('@/views/pages/social-media/index.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Social Media',
        },
      ],
    },
  },

  {
    path: '/social-media/:id',
    name: 'social-media-detail',
    component: () => import('@/views/pages/social-media/edit.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Social Media',
          to: '/social-media',
        },
        {
          text: 'Detail',
          active: true,
        },
      ],
    },
  },

]
