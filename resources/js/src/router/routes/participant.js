export default [
    {
      path: '/participant',
      name: 'participants',
      component: () => import('@/views/pages/participant/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Participant',
              active: true,
            },
          ],
      },
    },
    {
      path: '/participant/:id',
      name: 'participant-detail',
      component: () => import('@/views/pages/participant/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Participant',
              to: '/participant',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },
    {
      path: '/participant-raw',
      name: 'participant-raw',
      component: () => import('@/views/pages/participant/index-raw.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Participant',
              to: '/participant',
            },
            {
              text: 'Participant Raw Data',
              active: true,
            },
          ],
      },
    },
  ]
