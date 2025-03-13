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
    title: 'Closed Registration',
    icon: 'ArrowRightCircleIcon',
    route: 'event-attendance',
    permission: 'event-attendance-view',
  },
  {
    title: 'Open Registration',
    icon: 'ArrowRightCircleIcon',
    route: 'event-attendance-open-registration',
    permission: 'event-attendance-view',
  },
  {
    title: 'Ticket Transaction',
    icon: 'TagIcon',
    route: 'event-ticket-transactions',
    permission: 'event-ticket-transaction-view',
  },
  {
    title: 'Booking Hotel Event',
    icon: 'BookIcon',
    route: 'booking-hotel-event',
    permission: 'booking-hotel-event-view',
  },
  {
    title: 'City',
    icon: 'MapPinIcon',
    route: 'city',
    permission: 'land-arrangement-view',
  },
  {
    title: 'Hotel',
    icon: 'SquareIcon',
    route: 'hotel',
    permission: 'land-arrangement-view',
  },
  {
    header: 'CRM',
    permissions: ['participant-crm-view', 'milad-view', 'greeting-management-view', 'qontak-view', 'crm-report-view'],
  },
  {
    title: 'Survey',
    icon: 'ListIcon',
    route: 'survey',
    permission: 'survey-view',
  },
  {
    title: 'Form Section',
    icon: 'LayersIcon',
    route: 'form-section',
    permission: 'survey-add-or-edit',
  },
  {
    header: 'Data & Access',
    permissions: ['participant-view', 'tour-crew-view', 'user-platform-view'],
  },
  {
    title: "Participant",
    icon: 'UsersIcon',
    route: 'participant',
    permission: 'participant-view',
  },
  {
    title: 'User Platform',
    icon: 'UserPlusIcon',
    route: 'user-platform',
    permission: 'user-platform-view',
  },
  {
    header: 'Media & Promotion',
    permissions: ['mitra-media-view'],
  },
  {
    title: "Media",
    icon: 'ImageIcon',
    route: 'mitra-media',
    permission: 'mitra-media-view',
  },
  {
    title: "Media Marketing",
    icon: 'ImageIcon',
    route: 'media-marketing',
    permission: 'media-marketing-view',
  },
  {
    header: 'CMS Website',
    permissions: ['web-settings-view'],
  },
  {
    title: 'Sales Name',
    icon: 'ShoppingBagIcon',
    route: 'web-sales',
    permission: 'web-sales-view',
  },
  {
    title: 'Social Media',
    icon: 'Share2Icon',
    route: 'social-media',
    permission: 'web-settings-view',
  },
  {
    title: 'Web Settings',
    icon: 'SettingsIcon',
    permission: 'web-settings-view',
    children: [
      {
        title: 'General',
        route: 'web-settings-general',
        permission: 'web-settings-view',
      },
      {
        title: 'Index Page',
        route: 'web-settings-index-page',
        permission: 'web-settings-view',
      },
      {
        title: 'Navbar',
        route: 'web-settings-navbar',
        permission: 'web-settings-view',
      },
      {
        title: 'Redirect URL',
        route: 'web-settings-redirect',
        permission: 'web-settings-view',
      },
      {
        title: 'Privacy Policy',
        route: 'privacy-policy-page',
        permission: 'web-settings-view',
      },
    ],
  },
  {
    title: 'Images and Slider',
    icon: 'ImageIcon',
    permission: 'images-slider-view',
    children: [
      {
        title: 'Main Visual',
        route: 'main-visual',
        permission: 'images-slider-view',
      },
      {
        title: 'Why Us',
        route: 'whyus',
        permission: 'images-slider-view',
      },
      {
        title: 'Programs',
        route: 'programs',
        permission: 'images-slider-view',
      },
      {
        title: 'Partner Logo',
        route: 'partners',
        permission: 'images-slider-view',
      },
      {
        title: 'Footer Logo',
        route: 'footer-logo',
        permission: 'images-slider-view',
      },
    ],
  },
  {
    title: 'Inquiries',
    icon: 'MailIcon',
    route: 'inquiries',
    permission: 'inquiry-view',
  },
  {
    title: 'Article',
    icon: 'FileTextIcon',
    permission: 'article-view',
    children: [
      {
        title: 'Articles',
        route: 'articles',
        permission: 'article-view',
      },
      {
        title: 'Category',
        route: 'article-categories',
        permission: 'article-view',
      },
    ]
  },
  {
    title: 'Link Text',
    icon: 'AtSignIcon',
    route: 'web-link-text',
    permission: 'web-link-text-view',
  },
  {
    title: 'Testimonial',
    icon: 'MessageCircleIcon',
    route: 'testimonials',
    permission: 'testimonial-view',
  },
  {
    title: 'FAQ',
    icon: 'FileTextIcon',
    permission: 'faq-view',
    children: [
      {
        title: 'Content',
        route: 'faq-content',
        permission: 'faq-view',
      },
      {
        title: 'Category',
        route: 'faq-category',
        permission: 'faq-view',
      },
    ]
  },
  {
    title: 'Gallery',
    icon: 'ImageIcon',
    permission: 'gallery-view',
    children: [
      {
        title: 'Category',
        route: 'gallery-category',
        permission: 'gallery-view',
      },
      {
        title: 'Content',
        route: 'gallery-content',
        permission: 'gallery-view',
      }
    ]
  },
  {
    title: 'Live Streaming',
    icon: 'TvIcon',
    route: 'live-stream',
    permission: 'live-stream-view',
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
