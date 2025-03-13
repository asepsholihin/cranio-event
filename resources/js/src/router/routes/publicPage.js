export default [
    {
      path: '/register-event/:id',
      name: 'register-event',
      component: () => import('@/views/public/event-register/Index.vue'),
      meta: {
            public: true,
            layout: 'full',
      },
    },
  ]
