export default [
  {
    path: '/master-hotel-event',
    name: 'master-hotel-event',
    component: () => import('@/views/pages/master-hotel-event/index.vue'),
    meta: {
        pageTitle: '',
        breadcrumb: [
          {
            text: 'Master Hotel Event',
            active: true,
          },
        ],
    },
  },
  {
    path: '/master-hotel-event/create',
    name: 'master-hotel-event-create',
    component: () => import('@/views/pages/master-hotel-event/create.vue'),
    meta: {
        pageTitle: '',
        breadcrumb: [
          {
            text: 'Master Event Hotel',
            to: '/master-hotel-event',
          },
          {
            text: 'Add New Hotel',
            active: true,
          },
        ],
    },
  },
  {
    path: '/master-hotel-event/:id',
    name: 'master-hotel-event-detail',
    component: () => import('@/views/pages/master-hotel-event/edit.vue'),
    meta: {
        pageTitle: '',
        breadcrumb: [
          {
            text: 'Master Hotel Event',
            to: '/master-hotel-event',
          },
          {
            text: 'Detail',
            active: true,
          },
        ],
    },
  },

]
