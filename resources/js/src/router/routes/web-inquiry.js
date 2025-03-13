export default [
    {
      path: '/inquiries',
      name: 'inquiries',
      component: () => import('@/views/pages/inquiry/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Inquiries',
              active: true,
            },
          ],
      },
    }

  ]
