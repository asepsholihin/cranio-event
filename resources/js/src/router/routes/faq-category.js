export default [
    {
      path: '/faq-category',
      name: 'faq-category',
      component: () => import('@/views/pages/faq-category/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'FAQ Category',
              active: true,
            },
          ],
      },
    },

    {
      path: '/faq-category/:id',
      name: 'faq-category-detail',
      component: () => import('@/views/pages/faq-category/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'FAQ Category',
              to: '/faq-category',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
