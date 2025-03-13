export default [
  {
    path: '/live-stream',
    name: 'live-stream',
    component: () => import('@/views/pages/live-stream/index.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Live Stream',
        },
      ],
    },
  },

  {
    path: '/live-stream/:id',
    name: 'live-stream-detail',
    component: () => import('@/views/pages/live-stream/edit.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Live Stream',
          to: '/live-stream',
        },
        {
          text: 'Detail',
          active: true,
        },
      ],
    },
  },

]
