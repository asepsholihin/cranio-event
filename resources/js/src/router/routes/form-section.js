export default [
    {
      path: '/form-section',
      name: 'form-section',
      component: () => import('@/views/pages/form-section/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Form Section',
              active: true,
            },
          ],
      },
    },
    {
      path: '/add-form-section',
      name: 'add-form-section',
      component: () => import('@/views/pages/form-section/add.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Form Section',
              to: '/form-section',
            },
            {
              text: 'Add',
              active: true,
            },
          ],
      },
    },
    {
      path: '/form-section/:id',
      name: 'form-section-detail',
      component: () => import('@/views/pages/form-section/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Survey',
              to: '/form-section',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    }

  ]
