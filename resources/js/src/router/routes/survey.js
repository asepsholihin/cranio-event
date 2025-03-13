export default [
    {
      path: '/survey',
      name: 'survey',
      component: () => import('@/views/pages/survey/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Survey',
              active: true,
            },
          ],
      },
    },
    {
      path: '/add-survey',
      name: 'add-survey',
      component: () => import('@/views/pages/survey/add.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Survey',
              to: '/survey',
            },
            {
              text: 'Add',
              active: true,
            },
          ],
      },
    },
    {
      path: '/survey/:id',
      name: 'survey-detail',
      component: () => import('@/views/pages/survey/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Survey',
              to: '/survey',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },
    {
      path: '/survey/:id/summary',
      name: 'survey-summary',
      component: () => import('@/views/pages/survey/summary.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Survey',
              to: '/survey',
            },
            {
              text: 'Summary',
              active: true,
            },
          ],
      },
    },

  ]
