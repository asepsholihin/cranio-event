export default [
    {
      path: '/event-attendance',
      name: 'event-attendance',
      component: () => import('@/views/pages/event-attendance/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Event Attendance (Closed Registration)',
              active: true,
            },
          ],
      },
    },

    {
      path: '/event-attendance/:id',
      name: 'event-attendance-detail',
      component: () => import('@/views/pages/event-attendance/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Event Attendance (Closed Registration)',
              to: '/event-attendance',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

    {
      path: '/attendance-report/:online/:id',
      name: 'attendance-report',
      component: () => import('@/views/pages/event-attendance/report.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Event Attendance (Closed Registration)',
              to: '/event-attendance',
            },
            {
              text: 'Report',
              active: true,
            },
          ],
      },
    },

    {
      path: '/event-attendance-confirmation/:id',
      name: 'event-attendance-confirmation',
      component: () => import('@/views/pages/event-attendance/confirmation.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Event Attendance (Closed Registration)',
              to: '/event-attendance',
            },
            {
              text: 'Confirmation',
              active: true,
            },
          ],
      },
    },

    {
        path: '/event-attendee/:id',
        name: 'event-attendee',
        component: () => import('@/views/pages/event-attendance/attendee.vue'),
        meta: {
            pageTitle: '',
            breadcrumb: [
              {
                text: 'Event Attendance (Closed Registration)',
                to: '/event-attendance',
              },
              {
                text: 'Attendee',
                active: true,
              },
            ],
        },
      },

      {
        path: '/event-attendance-open-registration',
        name: 'event-attendance-open-registration',
        component: () => import('@/views/pages/event-attendance/open-registration/index.vue'),
        meta: {
            pageTitle: '',
            breadcrumb: [
              {
                text: 'Event Attendance (Open Registration)',
                active: true,
              },
            ],
        },
      },

      {
        path: '/event-attendance-open-registration/:id',
        name: 'event-attendance-open-registration-detail',
        component: () => import('@/views/pages/event-attendance/open-registration/edit.vue'),
        meta: {
            pageTitle: '',
            breadcrumb: [
              {
                text: 'Event Attendance (Open Registration)',
                to: '/event-attendance-open-registration',
              },
              {
                text: 'Detail',
                active: true,
              },
            ],
        },
      },

      {
        path: '/event-attendee-open-registration/:id',
        name: 'event-attendee-open-registration',
        component: () => import('@/views/pages/event-attendance/open-registration/attendee.vue'),
        meta: {
            pageTitle: '',
            breadcrumb: [
              {
                text: 'Event Attendance (Open Registration)',
                to: '/event-attendance-open-registration',
              },
              {
                text: 'Attendee',
                active: true,
              },
            ],
        },
      },

      {
        path: '/event-attendance-open-registration-report/:id',
        name: 'event-attendance-open-registration-report',
        component: () => import('@/views/pages/event-attendance/open-registration/report.vue'),
        meta: {
            pageTitle: '',
            breadcrumb: [
              {
                text: 'Event Attendance (Open Registration) Report',
                active: true,
              },
            ],
        },
      },

      {
        path: '/mapping-attendee-open-registration/:id',
        name: 'mapping-attendee-open-registration',
        component: () => import('@/views/pages/event-attendance/open-registration/seat.vue'),
        meta: {
            pageTitle: '',
            breadcrumb: [
              {
                text: 'Event Attendance (Open Registration)',
                to: '/event-attendance-open-registration',
              },
              {
                text: 'Mapping Seat Number',
                active: true,
              },
            ],
        },
      },

      {
        path: '/checkin-attendee-open-registration/:id',
        name: 'checkin-attendee-open-registration',
        component: () => import('@/views/pages/event-attendance/open-registration/checkin.vue'),
        meta: {
            pageTitle: '',
            breadcrumb: [
              {
                text: 'Event Attendance (Open Registration)',
                to: '/event-attendance-open-registration',
              },
              {
                text: 'Checkin',
                active: true,
              },
            ],
        },
      },


  ]
