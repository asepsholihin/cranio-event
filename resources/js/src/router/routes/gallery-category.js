export default [
    {
      path: '/gallery-category',
      name: 'gallery-category',
      component: () => import('@/views/pages/gallery-category/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Gallery Category',
              active: true,
            },
          ],
      },
    },

    {
      path: '/gallery-category/:id',
      name: 'gallery-category-detail',
      component: () => import('@/views/pages/gallery-category/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Gallery Category',
              to: '/gallery-category',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
