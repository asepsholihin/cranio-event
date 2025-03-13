export default [
  {
    path: '/faq-content',
    name: 'faq-content',
    component: () => import('@/views/pages/faq-content/index.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'FAQ Content',
          active: true,
        },
      ],
    },
  },
  {
    path: '/faq-content/create',
    name: 'faq-content-create',
    component: () => import('@/views/pages/faq-content/add.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'FAQ Content',
          to: '/faq-content',
        },
        {
          text: 'Add',
          active: true,
        },
      ],
    },
  },
  {
    path: '/faq-content/:id',
    name: 'faq-content-detail',
    component: () => import('@/views/pages/faq-content/edit.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'FAQ Content',
          to: '/faq-content',
        },
        {
          text: 'Detail',
          active: true,
        },
      ],
    },
  },

]
