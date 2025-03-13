export default [
  {
    path: '/gallery-content',
    name: 'gallery-content',
    component: () => import('@/views/pages/gallery-content/index.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Gallery Content',
          active: true,
        },
      ],
    },
  },
  {
    path: '/gallery-content/create',
    name: 'gallery-content-create',
    component: () => import('@/views/pages/gallery-content/add.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Gallery Content',
          to: '/gallery-content',
        },
        {
          text: 'Add',
          active: true,
        },
      ],
    },
  },
  {
    path: '/gallery-content/:id',
    name: 'gallery-content-detail',
    component: () => import('@/views/pages/gallery-content/edit.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Gallery Content',
          to: '/gallery-content',
        },
        {
          text: 'Detail',
          active: true,
        },
      ],
    },
  },

]
