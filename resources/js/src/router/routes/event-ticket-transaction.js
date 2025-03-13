export default [
    {
      path: '/event-ticket-transactions',
      name: 'event-ticket-transactions',
      component: () => import('@/views/pages/event-ticket-transaction/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Ticket Transaction',
              active: true,
            },
          ],
      },
    },

  ]
