import Vue from 'vue'
import VueRouter from 'vue-router'
import refetchCountBadgeNav from '@/navigation/count-badge'

// Routes
import { isUserLoggedIn, getHomeRouteForLoggedInUser } from '@/auth/utils'
import dashboard from './routes/dashboard'
import authPage from './routes/authPage'
import profile from './routes/profile'
import participant from './routes/participant.js'
import userPlatform from './routes/user-platform.js'
import eventAttendance from './routes/event-attendance.js'
import webSettings from './routes/web-settings'
import webSales from './routes/web-sales'
import mainVisual from './routes/main-visual'
import whyus from './routes/whyus'
import webPartner from './routes/web-partner'
import webProgram from './routes/web-program'
import webFooterLogo from './routes/web-footer-logo'
import webInquiriy from './routes/web-inquiry'
import article from './routes/article'
import webLinkText from './routes/web-link-text'
import testimonial from './routes/testimonial'
import city from './routes/city'
import hotel from './routes/hotel'
import socialMedia from './routes/social-media'
import seoSettings from './routes/seo-settings'
import articleCategory from './routes/article-category'
import webNavbar from './routes/web-navbar'
import webRedirect from './routes/web-redirect'
import logArticleActivity from './routes/log-article-activity'
import mitra from './routes/mitra'
import mediaMarketing from './routes/media-marketing'
import eventTicketTransaction from './routes/event-ticket-transaction'
import faqCategory from './routes/faq-category'
import faqContent from './routes/faq-content'
import liveStream from './routes/live-stream'
import galleryCategory from './routes/gallery-category'
import galleryContent from './routes/gallery-content'
import survey from './routes/survey'
import formSection from './routes/form-section'
import bookingHotelEvent from './routes/booking-hotel-event'
import masterHotelEvent from './routes/master-hotel-event'
import booking from './routes/booking'
import bookingTemporary from './routes/booking-temporary'
import bookingReceipt from './routes/booking-receipt'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    { path: '/', redirect: { name: 'dashboard' } },
    ...dashboard,
    ...authPage,
    ...profile,
    ...participant,
    ...userPlatform,
    ...eventAttendance,
    ...webSettings,
    ...webSales,
    ...mainVisual,
    ...whyus,
    ...webPartner,
    ...webProgram,
    ...webFooterLogo,
    ...webInquiriy,
    ...article,
    ...webLinkText,
    ...testimonial,
    ...city,
    ...hotel,
    ...socialMedia,
    ...seoSettings,
    ...articleCategory,
    ...webNavbar,
    ...webRedirect,
    ...logArticleActivity,
    ...mitra,
    ...mediaMarketing,
    ...eventTicketTransaction,
    ...faqCategory,
    ...faqContent,
    ...liveStream,
    ...galleryCategory,
    ...galleryContent,
    ...survey,
    ...formSection,
    ...bookingHotelEvent,
    ...masterHotelEvent,
    ...booking,
    ...bookingTemporary,
    ...bookingReceipt,
    {
      path: '*',
      redirect: 'not-found',
    },
  ],
})

router.beforeEach((to, _, next) => {
  const isLoggedIn = isUserLoggedIn()

  if (to.meta.public) {
    return next()
  }

  if (!isLoggedIn && to.name != 'auth-login') {
    return next({ name: 'auth-login' })
  }

  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    next(getHomeRouteForLoggedInUser())
  }

  // refetchCountBadgeNav()
  return next()
})

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

export default router
