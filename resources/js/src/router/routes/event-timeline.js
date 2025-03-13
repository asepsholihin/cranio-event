export default [
    {
      path: '/event-timeline',
      name: 'event-timeline',
      component: () => import('@/views/pages/event-timeline/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Event Timeline',
              active: true,
            },
          ],
      },
    },

  ]
