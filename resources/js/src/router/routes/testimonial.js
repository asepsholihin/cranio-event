export default [
    {
      path: '/testimonials',
      name: 'testimonials',
      component: () => import('@/views/pages/testimonial/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Testimonials',
              active: true,
            },
          ],
      },
    },
    {
      path: '/testimonial/:id',
      name: 'testimonial-detail',
      component: () => import('@/views/pages/testimonial/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Testimonials',
              to: '/testimonials',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
