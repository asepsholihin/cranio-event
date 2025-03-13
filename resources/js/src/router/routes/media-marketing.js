export default [
  {
    path: '/media-marketing',
    name: 'media-marketing',
    component: () => import('@/views/pages/media-marketing/index.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Media Marketing',
        },
      ],
    },
  },

  {
    path: '/media-marketing/:id',
    name: 'media-marketing-detail',
    component: () => import('@/views/pages/media-marketing/edit.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Media Marketing',
          to: '/media-marketing',
        },
        {
          text: 'Detail',
          active: true,
        },
      ],
    },
  },

]
