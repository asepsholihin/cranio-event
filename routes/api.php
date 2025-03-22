<?php

use App\Http\Controllers\ParticipantApp\AuthController;
use App\Http\Controllers\ParticipantApp\ParticipantController;
use App\Http\Controllers\ParticipantApp\MasterAddressController;
use App\Http\Controllers\ParticipantApp\JiosController as JiosParticipantController;
use App\Http\Controllers\CrewApp\AuthController as CrewAuthController;
use App\Http\Controllers\CrewApp\CrewController;
use App\Http\Controllers\CrewApp\UmrohTripController;
use App\Http\Controllers\CrewApp\EventAttendeeController;
use App\Http\Controllers\CrewApp\JiosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebSettings\GeneralSPAController;
use App\Http\Controllers\WebSettings\IndexPageSPAController;
use App\Http\Controllers\WebSettings\AboutPageSPAController;
use App\Http\Controllers\Catalog\WebCategorySPAController;
use App\Http\Controllers\Public\WebSubcategorySPAController;
use App\Http\Controllers\Public\WebProductSPAController;
use App\Http\Controllers\ImageSlider\WebSliderSPAController;
use App\Http\Controllers\ImageSlider\WebWhyusSPAController;
use App\Http\Controllers\ImageSlider\WebPartnerSPAController;
use App\Http\Controllers\ImageSlider\WebProgramSPAController;
use App\Http\Controllers\ImageSlider\WebTourPackageSPAController;
use App\Http\Controllers\ImageSlider\WebFooterLogoSPAController;
use App\Http\Controllers\ImageSlider\WebItinerarySPAController;
use App\Http\Controllers\InquirySPAController;
use App\Http\Controllers\Public\ArticleSPAController;
use App\Http\Controllers\ArticleCategorySPAController;
use App\Http\Controllers\WebSalesSPAController;
use App\Http\Controllers\TestimonialSPAController;
use App\Http\Controllers\Web\WebHomepageController;
use App\Http\Controllers\Web\WebAboutpageController;
use App\Http\Controllers\Web\WebContentController;
use App\Http\Controllers\WebSettings\CMSEditorController;
use App\Http\Controllers\WebSettings\WebNavbarSPAController;
use App\Http\Controllers\WebSettings\WebRedirectSPAController;
use App\Http\Controllers\CrewApp\LogEquipmentController;
use App\Http\Controllers\Public\JiosSPAController;
use App\Http\Controllers\EventAttendanceController;
use App\Http\Controllers\Public\EventOpenRegistration;
use App\Support\General;
use App\Http\Controllers\Public\DocumentController;
use App\Http\Controllers\FaqCategorySPAController;
use App\Http\Controllers\FaqContentSPAController;
use App\Http\Controllers\AsatidzSPAController;
use App\Http\Controllers\GalleryContentSPAController;
use App\Http\Controllers\GalleryCategorySPAController;
use App\Http\Controllers\NCSIntegrationController;
use App\Http\Controllers\Public\XenditController;
use App\Http\Controllers\Public\BookingOrderController;
use App\Http\Controllers\LiveStreamSPAController;
use App\Http\Controllers\Public\EquipmentDeliveryController;
use App\Http\Controllers\Public\SurveyController;
use App\Http\Controllers\Public\CampaignController;
use App\Http\Controllers\Public\CalendarRequestController;
use App\Http\Controllers\Public\MerchandiseConfirmationController;
use App\Http\Controllers\Public\RegistrationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Register prefix throttle on RouteServiceProvider

Route::prefix('participants')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/register/verification_code', 'verificationCode');
        Route::post('/register/create_password', 'createPassword');
    });

    Route::middleware('auth:participant-app')->group(function () {
        Route::controller(ParticipantController::class)->group(function () {
            Route::get('/profile', 'profile');
            Route::get('/trip', 'trip');
            Route::get('/trip/detail/{id}/{orderUmrohTripId}', 'tripDetail');
            Route::post('/profile/update', 'updateProfile');
            Route::get('/profile/barcode', 'barcode');
            Route::post('/delete-account', 'deleteAccount');

            Route::get('/invoice', 'invoice');
            Route::get('/invoice-list', 'invoiceList');
            Route::get('/invoice/detail/{invoiceId}/', 'invoiceDetail');
            Route::post('/upload-credit-image-receipt', 'uploadCreditImageReceipt');
            Route::get('/download-receipt/{invoice}', 'downloadInvoiceReceipt');

            Route::post('/member/add', 'addMemberParticipant');
            Route::post('/member/update', 'updateMemberParticipant');
            Route::get('/member/list', 'listMemberParticipant');
            Route::get('/member/detail/{id}', 'detailMemberParticipant');
            Route::post('/member/letter-address', 'addLetterAddressParticipant');
            Route::get('/member/letter-address/{participantId}', 'letterAddressParticipant');
            Route::get('/member/letter/{participantUmrohTrip}', 'generateLetter');
            Route::get('/member/barcode/{participantId}', 'barcodeParticipant');
            Route::get('/member/barcode/list/{orderUmrohTripId}', 'barcodeParticipantByBooking');
            Route::post('/member/post-attendee', 'postAttendee');
            Route::get('/member/equipment-delivery-detail/{umrohTripId}/{participantId}', 'equipmentDeliveryDetail');
            Route::get('/equipments/{packageUmrohTripId}/{gender}', 'equipments');
            Route::post('/received-equipment', 'receivedEquipmentParticipant');
            Route::post('/photo-baggage', 'photoBaggage');

            Route::post('/upload', 'uploadFileParticipant');

            Route::get('/participant-by-booking/list/{orderUmrohTripId}', 'listParticipantByBooking');
            Route::get('/baggage-participant-by-booking/list/{orderUmrohTripId}', 'listBaggageParticipantByBooking');
            Route::get('/group-room-by-booking/list/{orderUmrohTripId}', 'groupRoomByBooking');
        });

        Route::controller(JiosParticipantController::class)->group(function () {
            Route::get('jios-sales-list/{orderUmrohTripId}', 'jiosSalesList');
            Route::get('jios-sales-detail/{id}', 'jiosSalesDetail');
        });

        Route::get('/user', function (Request $requests) {
            return $requests->user();
        });
    });
});

Route::prefix('crew')->group(function () {
    Route::controller(CrewAuthController::class)->group(function () {
        Route::post('/login', 'login');
    });

    Route::middleware('auth:crew-app')->group(function () {
        Route::prefix('editor')->group(function () {
            Route::controller(CMSEditorController::class)->group(function () {
                Route::post('/main-visual', 'editorMainVisual');
                Route::post('/add-main-visual', 'editorAddMainVisual');
                Route::post('/why-us-slider', 'editorWhyUsSlider');
                Route::post('/add-why-us-slider', 'editorAddWhyUsSlider');
                Route::post('/tour-package-slider', 'editorTourPackageSlider');
                Route::post('/add-tour-package-slider', 'editorAddTourPackageSlider');
                Route::post('/program-slider', 'editorProgramSlider');
                Route::post('/add-program-slider', 'editorAddProgramSlider');
                Route::post('/partners-slider', 'editorPartnersSlider');
                Route::post('/partners-slider-upload-image', 'editorPartnerSliderUploadImage');
                Route::post('/add-partner-slider', 'editorAddPartnerSlider');
                Route::post('/image-slider/delete', 'imageSliderDelete');
                Route::post('/price-simulation', 'priceSimulation');
            });
            Route::post('/web-settings/index-page', [IndexPageSPAController::class, 'store']);
            Route::post('/web-settings/about-page', [AboutPageSPAController::class, 'store']);
            Route::post('/content', [WebContentController::class, 'store']);
            Route::post('/array-content', [WebContentController::class, 'saveArrayTour']);
            Route::post('/content/delete', [WebContentController::class, 'delete']);
            Route::post('/seo-page', [GeneralSPAController::class, 'addSeoPage']);
            Route::post('/seo-product', [GeneralSPAController::class, 'addSeoProduct']);
            Route::post('/seo-profile-ustadz', [GeneralSPAController::class, 'addSeoProfileUstadz']);
        });

        Route::controller(CrewController::class)->group(function () {
            Route::get('/profile', 'profile');
            Route::post('/profile/update', 'updateProfile');
            Route::post('/profile/update-photo', 'updateProfilePhoto');
            Route::get('/profile/barcode', 'barcode');

            Route::post('/member/add', 'addMemberParticipant');
            Route::post('/member/update', 'updateMemberParticipant');
            Route::get('/member/list', 'listMemberParticipant');
            Route::get('/member/detail/{id}', 'detailMemberParticipant');

            Route::post('/upload', 'uploadFileParticipant');

            Route::get('/nearest-departure', 'nearestDeparture');
            Route::get('/nearest-event', 'nearestEvent');
        });

        Route::controller(UmrohTripController::class)->group(function () {
            Route::get('/departures', 'departures');
            Route::get('/departure-list', 'departureList');
            Route::get('/departure/participant-list', 'departureParticipantList');
            Route::get('/departure/detail/{id}', 'departureDetail');
            Route::get('/departure/participant', 'departureParticipant');
            Route::get('/departure/participant/detail/{id}', 'departureParticipantDetail');
            Route::post('/departure/receive-bag', 'receiveBag');
            Route::post('/departure/delete-baggage-detail', 'deleteBaggageDetail');
            Route::get('/departure/receive-bag', 'receiveBagList');
            Route::get('/departure/receive-bag/summary', 'receiveBagSummary');
            Route::get('/departure/receive-bag/detail', 'receiveBagDetail');
            Route::get('/departure/receive-bag/detail-from-participant/{participantId}', 'receiveBagDetailFromParticipant');

            Route::get('/umroh-trip/packages', 'packages');

            Route::get('/umroh-trip/room-list', 'roomList');
            Route::post('/umroh-trip/participant/room', 'updateParticipantRoom');
            Route::post('/umroh-trip/participant/seat', 'updateParticipantSeat');

            Route::get('/event-list', 'eventList');
            Route::get('/event/participant', 'eventParticipant');
            Route::get('/event/manasik-tables', 'manasikTables');
        });

        Route::controller(JiosController::class)->group(function () {
            Route::get('item-list', 'itemList');
            Route::get('jios-umroh-trips', 'umrohTrips');
            Route::get('umroh-trip-list', 'umrohTripList');
            Route::get('jios-sales-list', 'jiosSalesList');
            Route::get('jios-sales-detail/{id}', 'jiosSalesDetail');
            Route::post('jios-sales', 'storeJiosSales');
            Route::post('update-jios-sales', 'updateJiosSales');
        });

        Route::controller(EventAttendeeController::class)->group(function () {
            Route::post('event/attendee', 'attendee');
            Route::post('event/summary-attendee', 'summaryAttendee');
            Route::post('event/draft-attendee', 'draftAttendee');
            Route::get('/event/attendance-draft', 'attendanceDraft');
            Route::get('/event/attendance-categories', 'attendanceCategories');
            Route::get('/event/attendee-descriptions', 'attendeeDescriptions');
            Route::get('/event/summary-attendee/detail', 'summaryAttendeeDetail');
            Route::get('/event/log_attendance/detail', 'logAttendances');
        });

        Route::get('/user', function (Request $requests) {
            return $requests->user();
        });

        // Equipment
        Route::controller(LogEquipmentController::class)->group(function () {
            Route::get('equipment/equipment-list', 'equipmentList');
            Route::post('equipment/post-data', 'postData');
        });
    });
});

Route::prefix('public')->group(function () {
    // Get Master Adress
    Route::get('/address/countries', [MasterAddressController::class, 'countries']);
    Route::get('/address/provinces', [MasterAddressController::class, 'provinces']);
    Route::get('/address/cities', [MasterAddressController::class, 'cities']);
    Route::get('/address/districts', [MasterAddressController::class, 'districts']);
    Route::get('/address/subdistricts', [MasterAddressController::class, 'subdistricts']);
    Route::get('/address/postalcodes', [MasterAddressController::class, 'postalcodes']);

    Route::get('/homepage', WebHomepageController::class)->name('public_web_homepage');
    Route::get('/aboutpage', WebAboutpageController::class)->name('public_web_aboutpage');
    Route::get('/about-page', [WebContentController::class, 'contentAboutPage'])->name('public_content_about');
    Route::get('/haji-page', [WebContentController::class, 'contentHajiPage'])->name('public_content_haji');
    Route::get('/haji-furoda-page', [WebContentController::class, 'contentHajiFurodaPage'])->name('public_content_haji_furoda');
    Route::get('/haji-khusus-page', [WebContentController::class, 'contentHajiKhususPage'])->name('public_content_haji_khusus');
    Route::get('/faq-page', [WebContentController::class, 'contentFAQPage'])->name('public_content_faq');
    Route::get('/contact-page', [WebContentController::class, 'contentContactPage'])->name('public_content_contact');
    Route::get('/tabungan-umroh-page', [WebContentController::class, 'contentTabunganUmrohPage'])->name('public_content_tabungan_umroh');
    Route::get('/umroh-page', [WebContentController::class, 'contentUmrohPage'])->name('public_content_umroh');
    Route::get('/profile-ustadz-page', [WebContentController::class, 'contentProfileUstadzPage'])->name('public_content_profile_ustadz');
    Route::get('/privacy-policy-page', [WebContentController::class, 'getPrivacyPolicy'])->name('public.web-setting.privacy-policy-page');
    Route::get('/badal-parent-page', [WebContentController::class, 'contentBadalParentPage'])->name('public_content_badal_parent');
    Route::get('/badal-haji-page', [WebContentController::class, 'contentBadalHajiPage'])->name('public_content_badal_haji');
    Route::get('/badal-umroh-page', [WebContentController::class, 'contentBadalUmrohPage'])->name('public_content_badal_umroh');
    Route::get('/halal-tour-page', [WebContentController::class, 'contentHalalTourPage'])->name('public_content_halal_tour');
    Route::get('/halal-tour-product-page', [WebContentController::class, 'contentHalalTourProductPage'])->name('public_content_halal_tour_product');
    Route::get('/halal-tour-detail-page', [WebContentController::class, 'contentHalalTourDetailPage'])->name('public_content_halal_tour_detail');
    Route::get('/halal-tour-category-page', [WebContentController::class, 'contentHalalTourCategoryPage'])->name('public_content_halal_tour_category');
    Route::get('/participant-room-page', [WebContentController::class, 'contentParticipantRoomPage'])->name('public_content_participant_room');
    Route::get('/participant-room-gallery-page', [WebContentController::class, 'contentParticipantRoomGalleryPage'])->name('public_content_participant_room_gallery');
    Route::get('/kajian-page', [WebContentController::class, 'contentKajianPage'])->name('public_content_kajian');

    // UPDATE LAYOUT
    Route::get('/umroh-haji-page', [WebContentController::class, 'contentUmrohHajiPage'])->name('public_content_umroh_haji');

    Route::get('/app-info', [GeneralSPAController::class, 'appInfo']);
    Route::get('/seo-page', [GeneralSPAController::class, 'seoPage']);
    Route::get('/content-calendar-request', [WebContentController::class, 'contentCalendarRequest']);
    Route::get('/content-merchandise-confirmation', [WebContentController::class, 'contentMerchandiseConfirmation']);

    // Web Settings Endpoints for Web Platform
    Route::prefix('web-settings')->group(function () {
        Route::get('/navbar', [WebNavbarSPAController::class, 'navbar'])->name('public.web-setting.navbar');
        Route::get('/navbar-parent', [WebNavbarSPAController::class, 'queryParentNavbars'])->name('public.web-setting.get_navbar');
        Route::post('/post-navbar', [WebNavbarSPAController::class, 'postNavbar'])->name('public.web-setting.post_navbar');
        Route::get('/redirects', [WebRedirectSPAController::class, 'redirects'])->name('public.web-setting.redirects');
        Route::get('/general', [GeneralSPAController::class, 'apiIndex'])->name('public.web-setting.general');
        Route::get('/footer-content', [GeneralSPAController::class, 'footerContent'])->name('public.web-setting.footer_content');
        Route::post('/footer-content', [GeneralSPAController::class, 'postFooterContent']);
        Route::get('/index-page', [IndexPageSPAController::class, 'apiIndex'])->name('public.web-setting.index-page');
        Route::get('/about-page', [AboutPageSPAController::class, 'apiIndex'])->name('public.web-setting.about-page');
        Route::get('/offices', [GeneralSPAController::class, 'getOffices']);


        Route::post('/post-offices', [GeneralSPAController::class, 'postOffice']);
        Route::post('/post-maps', [GeneralSPAController::class, 'postMaps']);
        Route::get('/delete-offices/{id}', [GeneralSPAController::class, 'deleteOffice']);

    });
    // Catalog Endpoints for Web Platform
    Route::prefix('catalog')->name('public.catalog.')->group(function () {
        Route::resource('categories', WebCategorySPAController::class)->only(['index', 'show']);
        Route::resource('products', WebProductSPAController::class)->only(['index']);
        Route::get('all-products', [WebProductSPAController::class, 'allProducts']);
        Route::get('batch-products', [WebProductSPAController::class, 'batchProducts']);
        Route::get('product/{slug}', [WebProductSPAController::class, 'productDetail']);
        Route::get('related-products', [WebProductSPAController::class, 'relatedProducts']);
        Route::get('related-product-dates', [WebProductSPAController::class, 'relatedProductDates']);
        Route::get('other-packages', [WebProductSPAController::class, 'otherPackages']);
        Route::get('other-packages-category', [WebProductSPAController::class, 'otherPackagesCategory']);
        Route::get('refresh-update-all-packages', [WebProductSPAController::class, 'updateAllPackages']);
        Route::post('product-edit', [WebProductSPAController::class, 'postData']);
        Route::get('sub-category/{slug}', [WebSubcategorySPAController::class, 'subcategoryDetail']);
        Route::get('halal-tour-products', [WebProductSPAController::class, 'halalTourProducts']);
        Route::get('haji-products', [WebProductSPAController::class, 'hajiProducts']);
        Route::get('umroh-products', [WebProductSPAController::class, 'umrohProducts']);
        Route::get('product-categories', [WebProductSPAController::class, 'productCategories']);
        Route::get('product-subcategories', [WebProductSPAController::class, 'productSubCategories']);
        Route::get('product-packages', [WebProductSPAController::class, 'productPackages']);
        Route::post('product-packages-post', [WebProductSPAController::class, 'productPackagesPost']);
        Route::post('post-content-product', [WebProductSPAController::class, 'postContentProduct']);
        Route::post('post-content-product/delete', [WebProductSPAController::class, 'deleteContentProduct']);
        Route::get('product-packages-post-delete/{id}', [WebProductSPAController::class, 'deleteProductPackagesPost']);

        Route::get('departure-dates', [WebProductSPAController::class, 'departureDates']);

    });

    // JIOS
    Route::prefix('jios')->name('public.jios.')->group(function () {
        Route::get('check', [JiosSPAController::class, 'checkJios']);
        Route::get('categories', [JiosSPAController::class, 'categories']);
        Route::get('products', [JiosSPAController::class, 'products']);
        Route::post('checkout', [JiosSPAController::class, 'checkout']);
        Route::get('invoice/detail/{transactionId}', [JiosSPAController::class, 'invoiceDetail']);
    });

    // Images and Slider
    Route::prefix('images-slider')->name('public.images-slider.')->group(function () {
        Route::resource('main-visual', WebSliderSPAController::class)->only(['index']);
        Route::resource('whyus', WebWhyusSPAController::class)->only(['index']);
        Route::resource('partner', WebPartnerSPAController::class)->only(['index']);
        Route::resource('program', WebProgramSPAController::class)->only(['index']);
        Route::resource('tour-package', WebTourPackageSPAController::class)->only(['index']);
        Route::resource('footer-logo', WebFooterLogoSPAController::class)->only(['index']);
        Route::get('itinerary/{categoryId}/{subcategoryId}', [WebItinerarySPAController::class, 'getList']);
    });

    Route::post('inquiry', [InquirySPAController::class, 'store']);
    Route::resource('articles', ArticleSPAController::class)->only(['index']);
    Route::get('all-articles', [ArticleSPAController::class, 'allArticles']);
    Route::get('article/{slug}', [ArticleSPAController::class, 'detail']);
    Route::get('related-articles', [ArticleSPAController::class, 'relatedArticles']);
    Route::post('article/edit', [ArticleSPAController::class, 'articleEdit']);
    Route::get('article-categories', [ArticleCategorySPAController::class, 'articleCategories']);
    Route::get('contact-sales', [WebSalesSPAController::class, 'contactSales']);
    Route::get('contact-office', [WebSalesSPAController::class, 'contactOffice']);
    Route::get('sales-in-footer', [WebSalesSPAController::class, 'salesInFooter']);
    Route::get('testimonials', [TestimonialSPAController::class, 'testimonials']);

    Route::post('testimonials-update-or-create', [TestimonialSPAController::class, 'testimonialsWeb']);
    Route::get('testimonials-delete/{id}', [TestimonialSPAController::class, 'deleteTestiomnial']);

    Route::get('/events', [EventOpenRegistration::class, 'events']);
    Route::get('related-events', [EventOpenRegistration::class, 'relatedEvents']);
    Route::get('/closed-events', [EventAttendanceController::class, 'closedEvents']);
    Route::get('event/{slug}', [EventAttendanceController::class, 'getEventDetail']);

    Route::get('/umroh-by-trip/{slug}', [UmrohTripController::class, 'getUmrohByLinkDetail']);
    Route::post('umroh-by-trip/check-participant', [UmrohTripController::class, 'checkParticipant']);

    Route::get('umroh-by-trip', [UmrohTripController::class, 'generateFilePDF']);

    Route::post('event/check-participant', [EventAttendanceController::class, 'eventCheckParticipant']);
    Route::post('event/confirm', [EventAttendanceController::class, 'eventConfirmation']);
    Route::post('event/departure-confirmation', [EventAttendanceController::class, 'departureConfirmation']);
    Route::post('closed-event/confirm', [EventAttendanceController::class, 'closedEventConfirmation']);

    Route::get('event-open/{uuid}', [EventOpenRegistration::class, 'getEventDetail']);
    Route::post('event-open/regisration', [EventOpenRegistration::class, 'eventRegistration']);
    Route::get('event-open/detail/{slug}', [EventOpenRegistration::class, 'eventDetail']);
    Route::post('event-open/booking', [EventOpenRegistration::class, 'eventBooking']);
    Route::get('event-open/booking/success/{transactionId}', [EventOpenRegistration::class, 'eventBookingSuccess']);
    Route::post('event-open/booking/callback', [EventOpenRegistration::class, 'eventBookingCallback']);

    Route::get('document/{slug}', [DocumentController::class, 'detailDocument']);
    Route::get('view-document/{slug}', [DocumentController::class, 'viewDocument']);
    Route::post('post-document-sign', [DocumentController::class, 'postDocumentSign']);

    // FAQ
    Route::get('faq-categories', [FaqCategorySPAController::class, 'faqCategories']);
    Route::get('faq-contents', [FaqContentSPAController::class, 'faqContents']);

    Route::get('asatidzs', [AsatidzSPAController::class, 'asatidzs']);
    Route::get('asatidz/{slug}', [AsatidzSPAController::class, 'detail']);
    Route::get('ustadzs', [AsatidzSPAController::class, 'ustadzs']);
    Route::get('ustadz/{slug}', [AsatidzSPAController::class, 'ustadzDetail']);
    Route::get('participant-articles', [ArticleSPAController::class, 'participantArticles']);
    Route::get('asatidz-articles', [ArticleSPAController::class, 'asatidzArticles']);
    Route::get('kajian', [ArticleSPAController::class, 'kajian']);
    Route::get('galleries', [GalleryContentSPAController::class, 'index']);
    Route::get('featured-gallery', [GalleryContentSPAController::class, 'featuredGallery']);
    Route::get('all-galleries', [GalleryContentSPAController::class, 'allGalleries']);
    Route::get('gallery/{slug}', [GalleryContentSPAController::class, 'detail']);
    Route::get('related-galleries', [GalleryContentSPAController::class, 'relatedGalleris']);
    Route::get('gallery-categories', [GalleryCategorySPAController::class, 'galleryCategories']);
    Route::get('live-streaming', [LiveStreamSPAController::class, 'liveStream']);

    Route::post('booking/callback', [XenditController::class, 'callback']);
    // Route::get('booking/detail', [BookingOrderController::class, 'bookingDetail']);
    Route::get('booking/invoice/detail/{transactionId}', [BookingOrderController::class, 'invoiceDetail']);
    Route::post('booking/generate-invoice', [BookingOrderController::class, 'generateInvoice']);
    Route::post('booking/assign-participant', [BookingOrderController::class, 'assignParticipant']);

    Route::get('equipment/delivery/{deliveryCode}', [EquipmentDeliveryController::class, 'equipmentDelivery']);
    Route::post('equipment/delivery-confirmation', [EquipmentDeliveryController::class, 'deliveryConfirmation']);

    Route::get('survey/{slug}', [SurveyController::class, 'show']);
    Route::post('survey/save-response', [SurveyController::class, 'saveResponse']);

    Route::get('campaign/{slug}', [CampaignController::class, 'show']);
    Route::post('campaign/donate', [CampaignController::class, 'donate']);

    Route::post('calendar-request', [CalendarRequestController::class, 'postCalendarRequest']);

    Route::post('merchandise-confirmation/check-participant', [MerchandiseConfirmationController::class, 'checkParticipant']);
    Route::post('merchandise-confirmation', [MerchandiseConfirmationController::class, 'postData']);


    Route::get('download-itinerary/{file}', [WebProductSPAController::class, 'downloadItinerary']);

    Route::post('registration', [RegistrationController::class, 'postData']);
    Route::post('registration/participant', [RegistrationController::class, 'postDataParticipant']);
    Route::get('registration/check-participant', [RegistrationController::class, 'checkParticipant']);
    Route::get('registration/{uuid}', [RegistrationController::class, 'registeredAccount']);
    Route::get('booking/detail', [RegistrationController::class, 'bookingDetail']);
    Route::post('booking/payment-confirmation', [RegistrationController::class, 'postDataPaymentConfirmation']);
});


// NCS Integration Test
Route::post('ncs/pickup-request', [NCSIntegrationController::class, 'pickupRequest']);
Route::get('ncs/publish-rate', [NCSIntegrationController::class, 'publishRate']);
