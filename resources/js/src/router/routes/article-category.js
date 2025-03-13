export default [
    {
      path: '/article-categories',
      name: 'article-categories',
      component: () => import('@/views/pages/article-category/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Article Categories',
              active: true,
            },
          ],
      },
    },
    {
      path: '/article-category/:id',
      name: 'article-category-detail',
      component: () => import('@/views/pages/article-category/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Article Categories',
              to: '/article-categories',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
