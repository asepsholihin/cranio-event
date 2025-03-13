export default [
  {
    path: '/log-article-activity',
    name: 'log-article-activity',
    component: () => import('@/views/pages/log-article-activity/index.vue'),
    meta: {
      pageTitle: '',
      breadcrumb: [
        {
          text: 'Log Article Activity',
        },
      ],
    },
  },

]
