export default [
  {
    title: 'Dashboard',
    icon: 'HomeIcon',
    route: 'dashboard',
  },
  {
    header: 'Events & Attendance',
    permissions: ['event-attendance-view','master-hotel-event-view', 'booking-hotel-event-view', 'event-timeline-view'],
  },
  {
    title: 'Events',
    icon: 'ArrowRightCircleIcon',
    route: 'event-attendance',
    permission: 'event-attendance-view',
  },
  {
    title: 'Booking',
    icon: 'BookIcon',
    route: 'booking',
    permission: 'booking-view',
  },
  {
    title: 'Pembayaran',
    icon: 'FileIcon',
    route: 'booking-receipt',
    permission: 'booking-view',
  },
  {
    title: 'Temp. Booking',
    icon: 'BookIcon',
    route: 'booking-temporary',
    permission: 'booking-view',
  },
  {
    header: 'Data & Access',
    permissions: ['participant-view', 'tour-crew-view', 'user-platform-view'],
  },
  {
    title: "Participant",
    icon: 'UsersIcon',
    route: 'participants',
    permission: 'participant-view',
  },
  {
    title: 'User Platform',
    icon: 'UserPlusIcon',
    route: 'user-platform',
    permission: 'user-platform-view',
  },
  {
    header: 'SEO',
    permissions: ['seo-settings-view'],
  },
  {
    title: 'SEO Settings',
    icon: 'SettingsIcon',
    route: 'seo-setting',
    permission: 'seo-settings-view',
  },
  {
    title: 'Log Article',
    icon: 'SettingsIcon',
    route: 'log-article-activity',
    permission: 'log-activity-view',
  },
]
