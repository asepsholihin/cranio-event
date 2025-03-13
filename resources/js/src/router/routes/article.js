export default [
    {
      path: '/articles',
      name: 'articles',
      component: () => import('@/views/pages/article/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Articles',
              active: true,
            },
          ],
      },
    },
    {
      path: '/add-article',
      name: 'add-article',
      component: () => import('@/views/pages/article/add.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Articles',
              to: '/articles',
            },
            {
              text: 'Add',
              active: true,
            },
          ],
      },
    },
    {
      path: '/article/:id',
      name: 'article-detail',
      component: () => import('@/views/pages/article/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Articles',
              to: '/articles',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
