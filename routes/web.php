<?php

use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\EventAttendanceController;
use App\Http\Controllers\SPAController;
use App\Http\Controllers\ParticipantSPAController;
use App\Http\Controllers\UserPlatformSPAController;
use App\Http\Controllers\MasterAddressController;
use App\Http\Controllers\EventOpenRegistrationController;
use App\Http\Controllers\AttendeeOpenRegistrationController;
use App\Http\Controllers\WebSettings\GeneralSPAController;
use App\Http\Controllers\WebSettings\IndexPageSPAController;
use App\Http\Controllers\Catalog\WebCategorySPAController;
use App\Http\Controllers\Catalog\WebSubcategorySPAController;
use App\Http\Controllers\Catalog\WebProductSPAController;
use App\Http\Controllers\WebSalesSPAController;
use App\Http\Controllers\ImageSlider\WebSliderSPAController;
use App\Http\Controllers\ImageSlider\WebWhyusSPAController;
use App\Http\Controllers\ImageSlider\WebPartnerSPAController;
use App\Http\Controllers\ImageSlider\WebProgramSPAController;
use App\Http\Controllers\ImageSlider\WebFooterLogoSPAController;
use App\Http\Controllers\InquirySPAController;
use App\Http\Controllers\ArticleSPAController;
use App\Http\Controllers\ArticleCategorySPAController;
use App\Http\Controllers\WebLinkTextSPAController;
use App\Http\Controllers\TestimonialSPAController;
use App\Http\Controllers\NotificationSPAController;
use App\Http\Controllers\CitySPAController;
use App\Http\Controllers\HotelSPAController;
use App\Http\Controllers\HotelRoomTypeSPAController;
use App\Http\Controllers\SocialMediaSPAController;
use App\Http\Controllers\SeoSettingSPAController;
use App\Http\Controllers\WebSettings\WebNavbarSPAController;
use App\Http\Controllers\WebSettings\WebRedirectSPAController;
use App\Http\Controllers\LogArticleActivitySPAController;
use App\Http\Controllers\MediaMarketingSPAController;
use App\Http\Controllers\EventTicketTransactionController;
use App\Http\Controllers\FaqCategorySPAController;
use App\Http\Controllers\FaqContentSPAController;
use App\Http\Controllers\LiveStreamSPAController;
use App\Http\Controllers\GalleryCategorySPAController;
use App\Http\Controllers\GalleryContentSPAController;
use App\Http\Controllers\SurveySPAController;
use App\Http\Controllers\FormSectionSPAController;
use App\Http\Controllers\DepartmentSPAController;
use App\Http\Controllers\BookingHotelEventSPAController;
use App\Http\Controllers\BookingSPAController;
use App\Http\Controllers\MasterHotelEventSPAController;
use App\Http\Controllers\EventTimelineSPAController;
use App\Http\Controllers\EventManasikOnlineSPAController;
use App\Http\Controllers\BookingTemporarySPAController;
use App\Http\Controllers\BookingReceiptSPAController;
use App\Models\Participant;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [SPAController::class, 'index'])->name('login');
Route::get('participant/generate-ji-code', function () {
    Participant::createJiCodeForParticipant();
});
Route::prefix('spa')->middleware(['auth'])->group(function () {

    Route::get('/auth-user', [SPAController::class, 'userAuth']);
    Route::get('/dashboard', [SPAController::class, 'dashboard']);

    Route::get('/director-dashboard', [SPAController::class, 'directorDashboard']);

    Route::get('/count-badge-nav', [SPAController::class, 'countBadgeNav']);
    Route::get('participant/medical-record-search', [ParticipantSPAController::class, 'medicalRecordParticipant']);
    Route::post('participant/action', [ParticipantSPAController::class, 'action']);
    Route::get('participant/job-search', [ParticipantSPAController::class, 'jobSearch']);
    Route::get('participant/search', [ParticipantSPAController::class, 'queryParticipant']);
    Route::get('participant/search-for-booking', [ParticipantSPAController::class, 'participantForBooking']);
    Route::get('participant/barcode/{participant}', [ParticipantSPAController::class, 'barcode']);
    Route::get('participant/files/{id}', [ParticipantSPAController::class, 'files']);
    Route::post('participant/import', [ParticipantSPAController::class, 'import']);
    Route::post('participant/import-participant-address', [ParticipantSPAController::class, 'importParticipantAddress']);
    Route::post('participant/upload-verification-file', [ParticipantSPAController::class, 'uploadVerificationFile']);
    Route::post('participant/upload-pas-photo', [ParticipantSPAController::class, 'uploadPasPhoto']);
    Route::get('participant/download-example-import', [ParticipantSPAController::class, 'downloadExampleImport']);
    Route::post('participant/delete-file', [ParticipantSPAController::class, 'deleteFile']);
    Route::post('participant/delete-specific-file', [ParticipantSPAController::class, 'deleteSpecificFile']);
    Route::post('participant/create-access-login', [ParticipantSPAController::class, 'createAccessLogin']);
    Route::post('participant/create-name-in-certificate', [ParticipantSPAController::class, 'createNameInCertificate']);
    Route::post('participant/upload-receive-document', [ParticipantUmrohTripSPAController::class, 'uploadReceiveDocument']);
    // CONVERT TO BLOB
    Route::post('participant/convert-to-blob', [ParticipantUmrohTripSPAController::class, 'changeBaseCode']);

    // Get Master Adress
    Route::get('/address/provinces', [MasterAddressController::class, 'provinces']);
    Route::get('/address/cities', [MasterAddressController::class, 'cities']);
    Route::get('/address/districts', [MasterAddressController::class, 'districts']);
    Route::get('/address/subdistricts', [MasterAddressController::class, 'subdistricts']);
    Route::get('/address/postalcodes', [MasterAddressController::class, 'postalcodes']);
    Route::get('/address/city-search', [MasterAddressController::class, 'queryCities']);

    Route::get('booking-order/badal', [BookingOrderSPAController::class, 'badal']);
    Route::get('booking-order/badal/{orderUmrohTripId}', [BookingOrderSPAController::class, 'badalDetail']);
    Route::get('booking-order/badal-price-list', [BookingOrderSPAController::class, 'badalPriceList']);
    Route::get('booking-order/credit-receipt', [BookingOrderSPAController::class, 'creditReceipt']);
    Route::delete('booking-order/delete-credit-receipt/{id}', [BookingOrderSPAController::class, 'deleteCreditReceipt']);
    Route::get('booking-order/trip-search', [BookingOrderSPAController::class, 'tripSearch']);
    Route::get('participant/export-participant-address', [ParticipantSPAController::class, 'exportParticipantAddress']);
    Route::get('participant/chart-gender', [ParticipantSPAController::class, 'chartGender']);
    Route::get('participant/chart-polo-size', [ParticipantSPAController::class, 'chartPoloSize']);
    Route::get('participant/export', [ParticipantSPAController::class, 'exportParticipant']);
    Route::resource('participant', ParticipantSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('participant-raw-data', [ParticipantSPAController::class, 'participantRawData']);
    Route::post('participant/update-domisili', [ParticipantSPAController::class, 'updateDomisili']);
    Route::post('participant/update-data', [ParticipantSPAController::class, 'updateData']);

    // CRM
    Route::resource('participant-crm', ParticipantCRMSPAController::class)->only(['index', 'store', 'destroy']);
    Route::get('participant-crm-detail/{id}', [ParticipantCRMSPAController::class, 'participantCRMdetail']);
    Route::post('participant-crm/upload-milad-photo', [ParticipantCRMSPAController::class, 'uploadMiladPhoto']);
    Route::get('participant-crm/files/{id}', [ParticipantCRMSPAController::class, 'files']);
    Route::post('participant-crm/delete-photo-milad', [ParticipantCRMSPAController::class, 'deleteMiladPhoto']);
    Route::get('participant-crm/export', [ParticipantCRMSPAController::class, 'exportParticipantCRM']);
    Route::post('participant-crm/import', [ParticipantCRMSPAController::class, 'importParticipantCRM']);
    Route::get('participant-crm/download-example-import', [ParticipantCRMSPAController::class, 'downloadExampleImport']);
    Route::get('participant-crm/trip-search', [ParticipantCRMSPAController::class, 'tripSearch']);
    Route::get('participant-crm/certificate/{id}', [ParticipantCRMSPAController::class, 'certificate']);
    Route::get('participant-crm/preview-certificate/{id}', [ParticipantCRMSPAController::class, 'previewCertificate']);
    Route::get('participant-crm/download-certificate', [ParticipantCRMSPAController::class, 'downloadCertificate']);
    Route::get('participant-crm/list-participant-merge', [ParticipantCRMSPAController::class, 'listParticipantMerge']);
    Route::post('participant-crm/merge-participant', [ParticipantCRMSPAController::class, 'mergeJmaah']);
    Route::post('participant-crm/remove-merge-participant', [ParticipantCRMSPAController::class, 'removeMergeJmaah']);
    Route::post('participant-crm/set-parent-account', [ParticipantCRMSPAController::class, 'setParentAccount']);
    Route::get('participant-crm/filter-cities', [ParticipantCRMSPAController::class, 'filterCities']);
    Route::get('participant-crm/filter-provinces', [ParticipantCRMSPAController::class, 'filterProvinces']);
    Route::get('participant-crm/filter-packages', [ParticipantCRMSPAController::class, 'filterPackages']);
    Route::get('participant-crm/filter-jobs', [ParticipantCRMSPAController::class, 'filterJobs']);
    Route::get('participant-crm/filter-total-account', [ParticipantCRMSPAController::class, 'filterTotalAccount']);
    Route::get('participant-crm/transactions', [ParticipantCRMSPAController::class, 'transactions']);
    Route::get('participant-crm/view-parent-account/{id}', [ParticipantCRMSPAController::class, 'viewParentAccount']);
    Route::resource('milad-crm', MiladCRMSPAController::class)->only(['index', 'store']);
    // CHNAGE PHOTO
    Route::post('milad-crm-change-our-photo', [MiladCRMSPAController::class, 'changeOurPhoto']);
    Route::get('participant-crm/getCertificateImage/{id}', [ParticipantCRMSPAController::class, 'getCertificateImage']);
    Route::get('milad-crm/getMiladCardWithPhoto/{id}', [MiladCRMSPAController::class, 'getMiladCardWithPhoto']);
    Route::get('milad-crm/images-milad-card/{id}', [MiladCRMSPAController::class, 'getImages']);
    Route::get('milad-crm/getMiladCardWithoutPhoto/{id}', [MiladCRMSPAController::class, 'getMiladCardWithoutPhoto']);
    Route::get('milad-crm/download-milad-card/{id}', [MiladCRMSPAController::class, 'downloadMiladCard']);
    Route::get('milad-crm/send-milad-card/{id}', [MiladCRMSPAController::class, 'sendMiladCard']);
    Route::post('milad-crm/activation-reminder-milad', [MiladCRMSPAController::class, 'activationRemiderMilad']);
    Route::post('milad-crm-post-send-milad-card', [MiladCRMSPAController::class, 'sendPostMiladCard']);
    Route::post('milad-crm-post-download-milad-card', [MiladCRMSPAController::class, 'sendPostDownloadMiladCard']);
    Route::get('packages-get-list', [PackageSPAController::class, 'getList']);

    Route::get('crm-report/chart-total-trip', [CRMReportSPAController::class, 'chartTotalTrip']);
    Route::get('crm-report/chart-total-transaction', [CRMReportSPAController::class, 'chartTotalTransaction']);
    Route::get('crm-report/chart-package', [CRMReportSPAController::class, 'chartPackage']);
    Route::get('crm-report/chart-top-trip', [CRMReportSPAController::class, 'chartTopTrip']);
    Route::get('crm-report/chart-gender', [CRMReportSPAController::class, 'chartGender']);
    Route::get('crm-report/chart-age', [CRMReportSPAController::class, 'chartAge']);
    Route::get('crm-report/chart-job', [CRMReportSPAController::class, 'chartJob']);
    Route::get('crm-report/chart-education', [CRMReportSPAController::class, 'chartEducation']);
    Route::get('crm-report/chart-city', [CRMReportSPAController::class, 'chartTopCity']);
    Route::get('crm-report/chart-province', [CRMReportSPAController::class, 'chartTopProvince']);
    Route::get('crm-report/chart-other', [CRMReportSPAController::class, 'chartOther']);
    Route::get('crm-report/chart-has-account', [CRMReportSPAController::class, 'chartHasAccount']);
    Route::get('crm-report/chart-has-phone', [CRMReportSPAController::class, 'chartHasPhone']);
    Route::get('crm-report/chart-has-instagram', [CRMReportSPAController::class, 'chartHasInstagram']);
    Route::get('crm-report/chart-has-linkedin', [CRMReportSPAController::class, 'chartHasLinkedin']);
    Route::get('crm-report/chart-participant-growth', [CRMReportSPAController::class, 'chartParticipantGrowth']);
    Route::get('crm-report/chart-repetisi', [CRMReportSPAController::class, 'chartRepetisi']);

    // Trend
    Route::get('crm-report/chart-trend-participant', [CRMReportSPAController::class, 'chartTrendParticipant']);
    Route::get('crm-report/chart-trend-package', [CRMReportSPAController::class, 'chartTrendPackage']);
    Route::get('crm-report/chart-trend-growth', [CRMReportSPAController::class, 'chartTrendGrowth']);
    Route::get('crm-report/chart-trend-product', [CRMReportSPAController::class, 'chartTrendProduct']);

    // Participant Reference
    Route::resource('participant-reference', ParticipantReferenceSPAController::class)->only(['show', 'store', 'index', 'destroy']);

    // Umroh Trip
    Route::get('umroh-trip/available-seats', [UmrohTripSPAController::class, 'availableSeats']);
    Route::get('umroh-trip/resume', [UmrohTripSPAController::class, 'resumeDeparture']);
    Route::get('umroh-trip/absensi-kepulangan', [UmrohTripSPAController::class, 'absensiKepulangan']);
    Route::get('umroh-trip/room-composition/{umrohTripId}', [UmrohTripSPAController::class, 'roomComposition']);
    Route::resource('umroh-trip', UmrohTripSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('umroh-trip/add-package', [UmrohTripSPAController::class, 'addPackage']);

    // GENERATE LINK DOCUMENT
    Route::post('umroh-trip/generate-link-document', [UmrohTripSPAController::class, 'generateLinkDocument']);
    Route::post('umroh-trip/upload-trip-document', [UmrohTripSPAController::class, 'uploadTripDocument']);
    Route::get('umroh-trip/download-trip-document/{id}', [UmrohTripSPAController::class, 'downloadTripDocument']);
    Route::get('umroh-trip/trip-documents/{id}', [UmrohTripSPAController::class, 'tripDocuments']);
    Route::delete('umroh-trip/trip-document/{id}', [UmrohTripSPAController::class, 'deleteTripDocument']);
    // Ready Manifest
    Route::post('umroh-trip/ready-manifest', [UmrohTripSPAController::class, 'readyManifest']);

    Route::post('umroh-trip/delete-package', [UmrohTripSPAController::class, 'deletePackage']);
    Route::post('umroh-trip/add-hotel', [UmrohTripSPAController::class, 'addHotel']);
    Route::post('umroh-trip/delete-hotel', [UmrohTripSPAController::class, 'deleteHotel']);
    Route::get('umroh-trip-hotels', [UmrohTripSPAController::class, 'hotelUmrohTrip']);
    Route::post('umroh-trip/copy-umroh-trip', [UmrohTripSPAController::class, 'copyUmrohTrip']);
    Route::get('umroh-trip-tour-crew', [UmrohTripSPAController::class, 'tourCrews']);
    Route::post('umroh-trip/add-tour-crew', [UmrohTripSPAController::class, 'addTourCrew']);
    Route::post('umroh-trip/add-badal-price', [UmrohTripSPAController::class, 'addBadalPrice']);
    Route::post('umroh-trip-participant/action', [ParticipantUmrohTripSPAController::class, 'action']);
    Route::get('umroh-trip-participant-pdf', [ParticipantUmrohTripSPAController::class, 'downloadPDF']);
    Route::get('umroh-trip-participant/search', [ParticipantUmrohTripSPAController::class, 'queryParticipantUmrohTrip']);
    Route::get('umroh-trip-participant-room-list/search', [ParticipantUmrohTripSPAController::class, 'queryParticipantRoomList']);
    Route::get('umroh-trip-participant/bus-search', [ParticipantUmrohTripSPAController::class, 'busGroupSearch']);
    Route::get('umroh-trip-crew/search', [TourCrewSPAController::class, 'queryCrew']);
    Route::get('umroh-trip-search', [UmrohTripSPAController::class, 'queryUmrohTripBelongsProduct']);
    Route::post('umroh-trip-participant/verification', [ParticipantUmrohTripSPAController::class, 'verification']);
    Route::get('umroh-trip-participant/tour-crew-letter/{participantUmrohTrip}', [ParticipantUmrohTripSPAController::class, 'tourCrewLetter']);
    Route::get('umroh-trip-participant/letter/{participantUmrohTrip}', [ParticipantUmrohTripSPAController::class, 'letter']);
    Route::get('umroh-trip-participant/send-letter/{participantUmrohTrip}', [ParticipantUmrohTripSPAController::class, 'sendLetter']);
    Route::get('umroh-trip-participant/view-letter/{participantUmrohTrip}', [ParticipantUmrohTripSPAController::class, 'viewLetter']);
    Route::post('umroh-trip-participant/post-letter-sign', [ParticipantUmrohTripSPAController::class, 'postLetterSign']);
    Route::get('umroh-trip-participant/list-document-letter/{participantUmrohTrip}', [ParticipantUmrohTripSPAController::class, 'listDocumentLetter']);
    Route::get('umroh-trip-participant/certificate/{participantUmrohTrip}', [ParticipantUmrohTripSPAController::class, 'certificate']);
    Route::post('umroh-trip-participant/update-without-bed', [ParticipantUmrohTripSPAController::class, 'updateWithoutBed']);
    Route::post('umroh-trip-participant/update-infant', [ParticipantUmrohTripSPAController::class, 'updateInfant']);
    Route::post('umroh-trip-participant/generate-no-urut', [ParticipantUmrohTripSPAController::class, 'generateNoUrut']);
    Route::post('umroh-trip-participant/download-documents', [ParticipantUmrohTripSPAController::class, 'downloadAllDocuments']);
    Route::post('umroh-trip-participant/download-passport-documents', [ParticipantUmrohTripSPAController::class, 'downloadPassportDocuments']);
    Route::post('umroh-trip-participant/download-participant-documents', [ParticipantUmrohTripSPAController::class, 'downloadParticipantDocuments']);
    Route::post('umroh-trip-participant/download-letters', [ParticipantUmrohTripSPAController::class, 'downloadLetters']);
    Route::get('umroh-trip-participant/download-pas-photos', [ParticipantUmrohTripSPAController::class, 'downloadPasPhotos']);
    Route::post('umroh-trip-participant/download-attendance-tags', [ParticipantUmrohTripSPAController::class, 'downloadAttendanceTagImages']);
    Route::get('umroh-trip-participant/files', [ParticipantUmrohTripSPAController::class, 'files']);
    Route::resource('umroh-trip-participant', ParticipantUmrohTripSPAController::class)->only(['show', 'store', 'index']);
    Route::post('umroh-trip-participant/import-siskopatuh', [ParticipantUmrohTripSPAController::class, 'importSiskopatuh']);
    Route::post('umroh-trip-participant/import-seat', [ParticipantUmrohTripSPAController::class, 'importSeat']);
    Route::post('umroh-trip-participant/add-room-group', [ParticipantUmrohTripSPAController::class, 'addRoomGroup']);
    Route::post('umroh-trip-participant/reset-room-group', [ParticipantUmrohTripSPAController::class, 'resetRoomGroup']);
    Route::post('umroh-trip-participant/remove-room-group', [ParticipantUmrohTripSPAController::class, 'removeRoomGroup']);
    Route::post('umroh-trip-participant/copy-room-group', [ParticipantUmrohTripSPAController::class, 'copyRoomGroup']);
    Route::post('umroh-trip-participant/add-seat', [ParticipantUmrohTripSPAController::class, 'addSeat']);
    Route::get('download-siskopatuh-example-import', [ParticipantUmrohTripSPAController::class, 'downloadExampleSiskopatuhImport']);
    Route::get('download-seat-example-import', [ParticipantUmrohTripSPAController::class, 'downloadExampleSeatImport']);
    Route::get('umroh-trip-airlines', [UmrohTripSPAController::class, 'airlines']);
    Route::resource('equipment-delivery-participant', EquipmentDeliveryParticipantSPAController::class)->only(['show', 'store', 'index']);
    Route::post('equipment-delivery-participant/action', [EquipmentDeliveryParticipantSPAController::class, 'action']);
    Route::post('equipment-delivery-participant/process', [EquipmentDeliveryParticipantSPAController::class, 'processEquipment']);
    Route::post('equipment-delivery-participant/delivery', [EquipmentDeliveryParticipantSPAController::class, 'deliveryEquipment']);
    Route::post('equipment-delivery-participant/bulk-delivery', [EquipmentDeliveryParticipantSPAController::class, 'bulkDeliveryEquipment']);
    Route::post('equipment-delivery-participant/delete-delivery-evidence', [EquipmentDeliveryParticipantSPAController::class, 'deleteDeliveryEvidence']);
    Route::get('equipment-received/{equipmentDeliveryId}', [EquipmentDeliveryParticipantSPAController::class, 'receivedEquipments']);
    Route::get('equipment-delivery-log/{equipmentDeliveryId}', [EquipmentDeliveryParticipantSPAController::class, 'deliveryLogEquipments']);
    Route::get('equipment-delivery/shipment-label/{participantUmrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'shipmentLabel']);
    Route::get('equipment-delivery/shipment-labels/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'shipmentLabels']);
    // MULTIPLE
    Route::get('equipment-delivery/shipment-labels-multiple', [EquipmentDeliveryParticipantSPAController::class, 'shipmentLabelsMultiple']);

    Route::get('equipment-delivery/name-label/{participantUmrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'deliveryNameLabel']);
    Route::get('equipment-delivery/name-labels/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'deliveryNameLabels']);
    // MULTIPLE
    Route::get('equipment-delivery/name-labels-multiple', [EquipmentDeliveryParticipantSPAController::class, 'deliveryNameLabelsMultiple']);
    Route::get('equipment-delivery-export', [EquipmentDeliveryParticipantSPAController::class, 'exportData']);
    // EXPORT JNE
    Route::get('equipment-export-jne', [EquipmentDeliveryParticipantSPAController::class, 'exportDataJNE']);
    Route::get('checklist-equipment-delivery-export', [EquipmentDeliveryParticipantSPAController::class, 'exportChecklistEquipmentDelivery']);
    Route::get('download-luggage-tag/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadLuggageTag']);
    Route::get('download-koper-tag/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadKoperTag']);
    Route::get('download-id-card/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadIDCard']);
    Route::get('download-certificate/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadCertificate']);
    Route::get('download-direct-certificate/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadDirectCertificate']);
    Route::get('download-table-number/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadTableNumber']);
    Route::get('download-label-passport/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadLabelPassport']);
    // MULTIPLE
    Route::get('download-label-passport-multiple', [EquipmentDeliveryParticipantSPAController::class, 'downloadLabelPassportMultiple']);
    Route::get('download-label-name/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadLabelName']);
    Route::get('download-attendance-tag/{umrohTripId}', [EquipmentDeliveryParticipantSPAController::class, 'downloadAttendanceTag']);
    Route::get('download-album-kepulangan', [EquipmentDeliveryParticipantSPAController::class, 'downloadAlbumKepulangan']);
    Route::get('event-attendance/umroh-trip-search', [EventAttendanceController::class, 'umrohTripSearch']);
    Route::resource('user-platform', UserPlatformSPAController::class)->only(['show', 'store', 'index']);
    Route::get('user-sales-search', [UserPlatformSPAController::class, 'queryUserSales']);
    Route::get('refine-user-department-office', [UserPlatformSPAController::class, 'refineUserDepartmentOffice']);
    // CHART OF ACCOUNTS
    Route::resource('chart-of-account', ChartOfAccountSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('chart-of-account-parent-list', [ChartOfAccountSPAController::class, 'getParentList']);
    // Department
    Route::resource('department', DepartmentSPAController::class)->only(['show', 'store', 'index']);
    Route::get('department-search', [DepartmentSPAController::class, 'queryDepartments']);
    Route::post('department-status', [DepartmentSPAController::class, 'changeStatus']);
    //
    Route::get('event-attendance/send-barcode', [EventAttendanceController::class, 'sendBarcode']);
    Route::get('event-attendance/multiple-send-barcode', [EventAttendanceController::class, 'multipleSendBarcode']);
    Route::get('event-attendance/participant-detail/{id}', [EventAttendanceController::class, 'participantDetail']);
    Route::get('event-attendance/list-participant-unattendee/{eventId}', [EventAttendanceController::class, 'participantUnattendeeList']);
    Route::get('event-attendance/chart-attendance', [EventAttendanceController::class, 'chartAttendance']);
    Route::get('event-attendance/chart-manasik-online', [EventAttendanceController::class, 'chartManasikOnline']);
    Route::get('event-attendance/chart-attendance-confirmation', [EventAttendanceController::class, 'chartAttendanceConfirmation']);
    Route::get('event-attendance/chart-attendance-confirmation-by-package', [EventAttendanceController::class, 'chartAttendanceConfirmationByPackage']);
    Route::post('event-attendance/generate-event-link', [EventAttendanceController::class, 'generateEventLink']);
    Route::get('event-attendance/generate-departure-confirmation-link/{eventId}', [EventAttendanceController::class, 'generateDepartureConfirmationLink']);
    Route::resource('event-attendance', EventAttendanceController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('event-attendance/update-manasik-table', [EventAttendanceController::class, 'updateManasikTable']);
    Route::post('event-attendance/departure-confirmation', [EventAttendanceController::class, 'departureConfirmation']);
    Route::post('event-attendance/departure-confirmation-admin', [EventAttendanceController::class, 'departureConfirmationByAdmin']);
    Route::post('event-attendance/export-departure', [EventAttendanceController::class, 'exportDeparture']);
    Route::post('event-attendance/export-departure-update', [EventAttendanceController::class, 'exportDepartureUpdate']);
    Route::post('event-attendance/import-departure', [EventAttendanceController::class, 'importDeparture']);
    Route::get('event-attendance-export-report', [EventAttendanceController::class, 'exportAttendanceReport']);
    Route::resource('event-attendee', AttendeeController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('event-attendee/delete', [AttendeeController::class, 'delete']);
    Route::post('event-attendee/reset-departure-confirmation', [AttendeeController::class, 'resetDepartureConfirmation']);
    Route::get('event-open-registration/chart-attendance', [EventOpenRegistrationController::class, 'chartAttendance']);
    Route::get('event-open-registration/chart-participant', [EventOpenRegistrationController::class, 'chartParticipant']);
    Route::get('event-open-registration/generate-event-registration-link/{eventId}', [EventOpenRegistrationController::class, 'generateOpenRegistrationLink']);
    Route::post('attendee-open-registration/send-barcode', [AttendeeOpenRegistrationController::class, 'sendBarcode']);
    Route::post('attendee-open-registration/download-barcode', [AttendeeOpenRegistrationController::class, 'downloadBarcode']);
    Route::resource('event-open-registration', EventOpenRegistrationController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('attendee-open-registration-seats', [AttendeeOpenRegistrationController::class, 'mappingSeatAttendeeList']);
    Route::get('event-open-settled-seats', [AttendeeOpenRegistrationController::class, 'eventOpenSeatSettledList']);
    Route::get('checkin-event-open-seat-report', [AttendeeOpenRegistrationController::class, 'reportCheckinEventOpenSeat']);
    Route::post('checkin-event-open-seat', [AttendeeOpenRegistrationController::class, 'postCheckinEventOpenSeat']);
    Route::post('attendee-open-registration-seat', [AttendeeOpenRegistrationController::class, 'postMappingSeatAttendee']);
    Route::resource('attendee-open-registration', AttendeeOpenRegistrationController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('attendee-open-registration/add', [AttendeeOpenRegistrationController::class, 'postAddAttendee']);
    Route::resource('event-ticket-transaction', EventTicketTransactionController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('event-ticket-transaction/resend-ticket', [EventTicketTransactionController::class, 'resendTicket']);
    Route::resource('facility', FacilitySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('equipment', EquipmentSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    // CATEGORY 1
    Route::get('equipment-category-list', [EquipmentSPAController::class, 'getListCategory']);
    //
    Route::get('equipment-export', [EquipmentSPAController::class, 'export']);
    Route::get('equipment-search', [EquipmentSPAController::class, 'queryEquipments']);
    Route::get('equipment-participant', [EquipmentSPAController::class, 'equipmentParticipant']);
    Route::get('equipment-log-report', [EquipmentSPAController::class, 'equipmentLogReports']);
    Route::post('equipment/import', [EquipmentSPAController::class, 'import']);
    Route::resource('itinerary', ItinerarySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('itinerary-category', [ItinerarySPAController::class, 'storeCategory']);
    Route::get('itinerary-category/search', [ItinerarySPAController::class, 'queryItineraryCategories']);
    Route::delete('itinerary-category/{id}', [ItinerarySPAController::class, 'deleteCategory']);
    Route::get('tour-crew-download-example-import', [TourCrewSPAController::class, 'downloadExampleImport']);
    Route::post('tour-crew/import', [TourCrewSPAController::class, 'import']);
    Route::post('tour-crew/export', [TourCrewSPAController::class, 'export']);
    Route::resource('tour-crew', TourCrewSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('package', PackageSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('package-list', [PackageSPAController::class, 'getList']);
    Route::get('packages/search', [PackageSPAController::class, 'queryPackages']);
    Route::get('airline/search', [AirlineSPAController::class, 'queryAirlines']);
    Route::get('airline-list', [AirlineSPAController::class, 'getAllList']);
    Route::resource('airline', AirlineSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('umroh-booking-seat', UmrohBookingSeatSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('umroh-booking-seat/badal', [UmrohBookingSeatSPAController::class, 'postBadal']);
    Route::post('umroh-booking-seat/update-badal', [UmrohBookingSeatSPAController::class, 'postUpdateBadal']);
    Route::get('booking-order/get-participant-data', [BookingOrderSPAController::class, 'getParticipantData']);
    // All
    Route::get('booking-order/packages-array', [BookingOrderSPAController::class, 'packagesArray']);
    // Single
    Route::get('booking-order/packages/{umrohTrip}', [BookingOrderSPAController::class, 'packages']);
    Route::get('booking-order/mutawwif', [BookingOrderSPAController::class, 'mutawwifForBadal']);
    Route::resource('booking-order', BookingOrderSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('booking-order-export', [BookingOrderSPAController::class, 'exportData']);
    Route::get('booking-order/invoice/{invoice}', [BookingOrderSPAController::class, 'detailInvoice']);
    Route::get('booking-order/refund/{refund}', [BookingOrderSPAController::class, 'detailRefund']);
    Route::get('booking-order/discount/{discount}', [BookingOrderSPAController::class, 'detailDiscount']);
    Route::get('booking-order/order-item/{orderItem}', [BookingOrderSPAController::class, 'detailOrderItem']);
    Route::post('booking-order/invoice/{invoice}', [BookingOrderSPAController::class, 'postInvoice']);
    Route::get('booking-order/download-invoice/{invoice}', [BookingOrderSPAController::class, 'downloadPDF']);
    Route::get('booking-order/download-invoice-participant/{invoice}', [BookingOrderSPAController::class, 'downloadPDFParticipant']);
    Route::get('booking-order/download-empty-invoice/{order}', [BookingOrderSPAController::class, 'downloadEmptyInvoice']);
    Route::get('booking-order/download-receipt/{invoice}', [BookingOrderSPAController::class, 'downloadReceiptPDF']);
    Route::get('booking-order/download-refund/{refund}', [BookingOrderSPAController::class, 'downloadRefundPDF']);
    Route::post('booking-order/download-recap-trip/{umrohTrip}', [BookingOrderSPAController::class, 'downloadRecapTrip']);
    Route::get('booking-order/recap-trip/{umrohTrip}', [BookingOrderSPAController::class, 'getRecapTrip']);
    Route::get('booking-order-tabulation-trip/', [BookingOrderSPAController::class, 'getTabulationTrip']);
    Route::post('booking-order/download-tabulation-trip', [BookingOrderSPAController::class, 'downloadTabulationTrip']);
    Route::get('booking-order/item-room-pax/{orderItem}', [BookingOrderSPAController::class, 'detailItemRoomPax']);
    Route::delete('booking-order/delete-payment/{invoice}', [BookingOrderSPAController::class, 'deletePayment']);
    Route::delete('booking-order/delete-order-item/{orderItem}', [BookingOrderSPAController::class, 'deleteOrderItem']);
    Route::delete('booking-order/delete-item-pax/{orderItem}', [BookingOrderSPAController::class, 'deleteItemPax']);
    Route::delete('booking-order/delete-refund/{refund}', [BookingOrderSPAController::class, 'deleteRefund']);
    Route::delete('booking-order/delete-invoice/{invoice}', [BookingOrderSPAController::class, 'deleteInvoice']);
    Route::post('booking-order/create-invoice/{order}', [BookingOrderSPAController::class, 'createInvoice']);
    Route::post('booking-order/edit-invoice/{order}', [BookingOrderSPAController::class, 'editInvoice']);
    Route::post('booking-order/create-order-item/{order}', [BookingOrderSPAController::class, 'createOrderItem']);
    Route::post('booking-order/send-invoice-email/{invoice}', [BookingOrderSPAController::class, 'sendInvoiceMail']);
    Route::post('booking-order/send-invoice-whatsapp/{invoice}', [BookingOrderSPAController::class, 'sendInvoiceWhatsapp']);
    Route::post('booking-order/send-receipt-email/{invoice}', [BookingOrderSPAController::class, 'sendReceiptMail']);
    Route::post('booking-order/upload-credit-receipt/{invoice}', [BookingOrderSPAController::class, 'uploadCreditReceipt']);
    Route::post('booking-order/ack-credit-receipt/{invoice}', [BookingOrderSPAController::class, 'ackCreditReceipt']);
    Route::post('booking-order/send-refund-email/{refund}', [BookingOrderSPAController::class, 'sendRefundMail']);
    Route::post('booking-order/assign-participant', [BookingOrderSPAController::class, 'assignParticipant']);
    Route::post('booking-order/assign-new-participant/{orderItem}', [BookingOrderSPAController::class, 'assignNewParticipant']);
    Route::post('booking-order/create-refund/{order}', [BookingOrderSPAController::class, 'createRefund']);
    Route::post('booking-order/edit-item-pax', [BookingOrderSPAController::class, 'postItemPax']);
    Route::post('booking-order/change-item-pax', [BookingOrderSPAController::class, 'postChangeItemPax']);
    Route::post('booking-order/edit-assigned-participant', [BookingOrderSPAController::class, 'editAssignedParticipant']);
    Route::get('booking-order-invoice-search', [BookingOrderSPAController::class, 'invoiceSearch']);
    Route::get('umroh-booking-seat/download-invoice/{order}', [UmrohBookingSeatSPAController::class, 'downloadPDF']);
    Route::post('umroh-booking-seat/send-invoice-email/{order}', [UmrohBookingSeatSPAController::class, 'sendInvoiceMail']);
    Route::get('umroh-booking-seat-room-types', [UmrohBookingSeatSPAController::class, 'roomTypes']);
    Route::get('umroh-booking-seat-search', [UmrohBookingSeatSPAController::class, 'queryBooking']);
    Route::get('umroh-booking-order-search', [UmrohBookingSeatSPAController::class, 'queryBookingOrderSearch']);
    Route::get('umroh-booking-seat/{order}/assigned-participant', [UmrohBookingSeatSPAController::class, 'assignedParticipant']);
    Route::get('umroh-booking-seat-hotels', [UmrohBookingSeatSPAController::class, 'hotels']);
    Route::post('umroh-booking-seat-upgrade-hotel', [UmrohBookingSeatSPAController::class, 'upgradeHotel']);
    Route::get('participant-letter-information/{participantId}', [ParticipantUmrohTripSPAController::class, 'participantLetterInformation']);
    Route::get('chart-document-participant/{umrohTripId}', [ParticipantUmrohTripSPAController::class, 'chartDocumentParticipant']);
    Route::get('document-participant/{umrohTripId}', [ParticipantUmrohTripSPAController::class, 'documentParticipant']);
    Route::post('booking-order/add-discount', [BookingOrderSPAController::class, 'addDiscount']);
    Route::post('booking-order/edit-old-discount', [BookingOrderSPAController::class, 'editOldDiscount']);
    Route::delete('booking-order/delete-old-discount/{order}', [BookingOrderSPAController::class, 'deleteOldDiscount']);
    Route::post('booking-order/edit-discount', [BookingOrderSPAController::class, 'editDiscount']);
    Route::delete('booking-order/delete-discount/{discount}', [BookingOrderSPAController::class, 'deleteDiscount']);
    Route::post('booking-order/add-reference', [BookingOrderSPAController::class, 'addReference']);
    Route::post('booking-order/set-mahrom', [BookingOrderSPAController::class, 'setMahrom']);
    Route::post('booking-order/set-umroh-trip', [BookingOrderSPAController::class, 'setUmrohTrip']);
    Route::get('booking-order/special-request/{specialRequest}', [BookingOrderSPAController::class, 'detailSpecialRequest']);
    Route::post('booking-order/add-special-request/{order}', [BookingOrderSPAController::class, 'addSpecialRequest']);
    Route::post('booking-order/edit-special-request/{order}', [BookingOrderSPAController::class, 'editSpecialRequest']);
    Route::delete('booking-order/delete-special-request/{specialRequest}', [BookingOrderSPAController::class, 'deleteSpecialRequest']);
    Route::post('booking-order/order-equipment', [BookingOrderSPAController::class, 'orderEquipment']);
    Route::get('booking-order/order-equipment/{orderItem}', [BookingOrderSPAController::class, 'detailOrderEquipment']);
    Route::delete('booking-order/delete-order-equipment/{orderItem}', [BookingOrderSPAController::class, 'deleteOrderEquipment']);
    Route::resource('invoice', InvoiceSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('invoice/add-payment', [InvoiceSPAController::class, 'addPayment']);
    Route::post('invoice/upload-credit-receipt/{invoice}', [InvoiceSPAController::class, 'uploadCreditReceipt']);
    Route::get('invoice/download-invoice/{invoice}', [InvoiceSPAController::class, 'downloadInvoicePDF']);
    Route::get('invoice/download-receipt/{invoice}', [InvoiceSPAController::class, 'downloadReceiptPDF']);
    Route::post('booking-order/edit-order-information/{order}', [BookingOrderSPAController::class, 'editOrderInformation']);
    Route::resource('attendance-category', AttendanceCategorySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('badal-prices', [UmrohTripSPAController::class, 'badalPriceList']);
    Route::delete('badal-prices/{id}', [UmrohTripSPAController::class, 'deleteBadalPrice']);
    Route::get('additional-delivery', [AdditionalDeliveryParticipantSPAController::class, 'index']);
    Route::get('additional-delivery/participants', [AdditionalDeliveryParticipantSPAController::class, 'participants']);
    Route::get('additional-delivery/participant', [AdditionalDeliveryParticipantSPAController::class, 'participants']);
    Route::post('additional-delivery/process', [AdditionalDeliveryParticipantSPAController::class, 'additionalProcessEquipment']);
    Route::post('additional-delivery/delivery', [AdditionalDeliveryParticipantSPAController::class, 'additionalDeliveryEquipment']);
    Route::get('additional-delivery/shipment-labels/{umrohTripId}', [AdditionalDeliveryParticipantSPAController::class, 'shipmentLabels']);
    Route::post('check-shipping-rates', [EquipmentDeliveryParticipantSPAController::class, 'checkShippingRates']);

    Route::get('calendar-request/shipment-label/{id}', [CalendarRequestSPAController::class, 'shipmentLabel']);
    Route::get('calendar-request/shipment-labels', [CalendarRequestSPAController::class, 'shipmentLabels']);
    Route::resource('calendar-request', CalendarRequestSPAController::class)->only('index','show','store','destroy');
    Route::post('calendar-request/process', [CalendarRequestSPAController::class, 'process']);
    Route::post('calendar-request/delivery', [CalendarRequestSPAController::class, 'delivery']);
    Route::post('calendar-request/deletes', [CalendarRequestSPAController::class, 'deletes']);
    Route::get('calendar-request-export', [CalendarRequestSPAController::class, 'exportData']);
    Route::get('calendar-request-shipment-export', [CalendarRequestSPAController::class, 'exportShipmentData']);

    Route::get('participant-merchandise/shipment-label/{id}', [ParticipantMerchandiseSPAController::class, 'shipmentLabel']);
    Route::get('participant-merchandise/shipment-labels', [ParticipantMerchandiseSPAController::class, 'shipmentLabels']);
    Route::resource('participant-merchandise', ParticipantMerchandiseSPAController::class)->only('index','show','store','destroy');
    Route::post('participant-merchandise/add-participant', [ParticipantMerchandiseSPAController::class, 'addParticipant']);
    Route::post('participant-merchandise/process', [ParticipantMerchandiseSPAController::class, 'process']);
    Route::post('participant-merchandise/delivery', [ParticipantMerchandiseSPAController::class, 'delivery']);
    Route::post('participant-merchandise/deletes', [ParticipantMerchandiseSPAController::class, 'deletes']);
    Route::get('participant-merchandise-export', [ParticipantMerchandiseSPAController::class, 'exportData']);
    Route::get('participant-merchandise-shipment-export', [ParticipantMerchandiseSPAController::class, 'exportShipmentData']);


    Route::get('document-delivery/shipment-labels', [DocumentDeliverySPAController::class, 'shipmentLabels']);
    Route::resource('document-delivery', DocumentDeliverySPAController::class)->only('index','show','store','destroy');
    Route::post('document-delivery/address', [DocumentDeliverySPAController::class, 'updateAddress']);
    Route::post('document-delivery/pickup', [DocumentDeliverySPAController::class, 'pickup']);
    Route::post('document-delivery/delivery', [DocumentDeliverySPAController::class, 'delivery']);
    Route::post('document-delivery/deletes', [DocumentDeliverySPAController::class, 'deletes']);

    // Web Settings
    Route::get('/web-settings/general', [GeneralSPAController::class, 'index']);
    Route::post('/web-settings/general', [GeneralSPAController::class, 'store']);
    Route::get('/web-settings/index-page', [IndexPageSPAController::class, 'index']);
    Route::post('/web-settings/index-page', [IndexPageSPAController::class, 'store']);
    Route::get('/web-settings/country-codes', [GeneralSPAController::class, 'getCountryCode']);
    Route::resource('/web-navbar', WebNavbarSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('web-navbar-search', [WebNavbarSPAController::class, 'queryNavbars']);
    Route::get('web-navbar-parent-search', [WebNavbarSPAController::class, 'queryParentNavbars']);
    Route::resource('/web-redirect', WebRedirectSPAController::class)->only(['show', 'store', 'index', 'destroy']);

    // Web Catalog
    Route::prefix('catalog')->name('spa.catalog.')->group(function () {
        Route::resource('categories', WebCategorySPAController::class)->only(['index', 'store', 'destroy', 'show']);
        Route::resource('sub-categories', WebSubcategorySPAController::class)->only(['index', 'store', 'destroy', 'show']);
        Route::get('categories-list', [WebCategorySPAController::class, 'getAllCategories']);
        Route::get('sub-categories-list/{id}', [WebSubcategorySPAController::class, 'getListSubCategories']);
        Route::get('categories-search', [WebCategorySPAController::class, 'categoriesSearch']);
        Route::get('sub-categories-search', [WebSubcategorySPAController::class, 'subCategoriesSearch']);
        Route::post('products/action', [WebProductSPAController::class, 'action']);
        Route::post('products/delete-image', [WebProductSPAController::class, 'deleteImage']);
        Route::resource('products', WebProductSPAController::class)->only(['index', 'store', 'destroy', 'show']);
    });

    Route::resource('social-media', SocialMediaSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('web-sales', WebSalesSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('web-sales-search', [WebSalesSPAController::class, 'queryWebSales']);
    Route::get('inquiry/get-inquiry-group', [InquirySPAController::class, 'getInquiriesGroupBy']);
    Route::resource('inquiry', InquirySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('inquiry/export', [InquirySPAController::class, 'exportInquiry']);
    Route::resource('article', ArticleSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('article-seo-check/{id}', [ArticleSPAController::class, 'articleSeoCheck']);
    Route::get('articles-seo-check', [ArticleSPAController::class, 'articlesSeoCheck']);
    Route::resource('article-category', ArticleCategorySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('article/action', [ArticleSPAController::class, 'action']);
    Route::get('article-category-search', [ArticleCategorySPAController::class, 'queryArticleCategories']);
    Route::resource('web-link-text', WebLinkTextSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('testimonial', TestimonialSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('other-package', OtherPackageSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('seo-setting', SeoSettingSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('lead-source', LeadSourceSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('lead-source-search', [LeadSourceSPAController::class, 'queryLeadSources']);
    Route::resource('lead-response', LeadResponseSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('lead-response-search', [LeadResponseSPAController::class, 'queryLeadResponses']);
    Route::resource('package-type', PackageTypeSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('package-type-search', [PackageTypeSPAController::class, 'queryPackageTypes']);
    Route::resource('sales-lead', SalesLeadSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('sales-lead/action', [SalesLeadSPAController::class, 'action']);
    Route::post('sales-lead/performance-invoice', [SalesLeadSPAController::class, 'performanceInvoice']);
    Route::get('log-performance-invoices/{salesLeadId}', [SalesLeadSPAController::class, 'logPerformanceInvoice']);
    Route::get('log-performance-invoices-detail/{salesLeadId}', [SalesLeadSPAController::class, 'logPerformanceInvoiceDetail']);
    Route::get('log-performance-invoices-download/{salesLeadId}', [SalesLeadSPAController::class, 'downloadInvoice']);
    Route::post('sales-lead/booking-order', [SalesLeadSPAController::class, 'bookingOrder']);
    Route::post('sales-lead/import', [SalesLeadSPAController::class, 'import']);
    Route::get('sales-lead-export', [SalesLeadSPAController::class, 'exportData']);
    Route::get('download-leads-example-import', [SalesLeadSPAController::class, 'downloadExampleLeadsImport']);
    Route::get('sales-lead-cities', [SalesLeadSPAController::class, 'querySalesLeadCities']);
    Route::resource('purchase-request', PurchaseRequestSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('purchase-request-document-pdf/{id}', [PurchaseRequestSPAController::class, 'documentPdf']);
    Route::get('purchase-request-delete-item/{id}', [PurchaseRequestSPAController::class, 'deleteItem']);

    // PURCHASE ORDERS
    Route::resource('purchase-orders', PurchaseOrdersSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('purchase-orders-document-pdf/{id}', [PurchaseOrdersSPAController::class, 'documentPdf']);

    // PAYMENT REQUEST
    Route::resource('payment-approval', PaymentApprovalSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('payment-approval-document-pdf/{id}', [PaymentApprovalSPAController::class, 'documentPdf']);
    Route::get('payment-approval-delete-item/{id}', [PaymentApprovalSPAController::class, 'deleteItem']);
    Route::post('payment-approval/approval-item', [PaymentApprovalSPAController::class, 'approvalItem']);
    Route::post('payment-approval/approval-parent', [PaymentApprovalSPAController::class, 'approvalParent']);
    Route::get('payment-approval-list-po', [PaymentApprovalSPAController::class, 'listPo']);
    Route::post('payment-approval-pa-number', [PaymentApprovalSPAController::class, 'generatePaNumber']);
    Route::post('payment-approval-post-submit', [PaymentApprovalSPAController::class, 'postSubmit']);
    Route::post('payment-approval-remove-item', [PaymentApprovalSPAController::class, 'removeItem']);
    Route::get('payment-approval-payment-header', [PaymentApprovalSPAController::class, 'paymentHeader']);
    Route::post('payment-approval-done-payement', [PaymentApprovalSPAController::class, 'donePayment']);

    // Images and Slider
    Route::prefix('images-slider')->name('spa.images-slider.')->group(function () {
        Route::resource('main-visual', WebSliderSPAController::class)->only(['show', 'store', 'index', 'destroy']);
        Route::resource('whyus', WebWhyusSPAController::class)->only(['show', 'store', 'index', 'destroy']);
        Route::resource('partner', WebPartnerSPAController::class)->only(['show', 'store', 'index', 'destroy']);
        Route::resource('program', WebProgramSPAController::class)->only(['show', 'store', 'index', 'destroy']);
        Route::resource('tour-package', WebTourPackageSPAController::class)->only(['show', 'store', 'index', 'destroy']);
        Route::resource('footer-logo', WebFooterLogoSPAController::class)->only(['show', 'store', 'index', 'destroy']);
        Route::resource('itinerary', WebItinerarySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    });

    // Sales Report
    Route::prefix('sales-report')->name('spa.sales-report.')->group(function () {
        Route::get('all-sales', [SalesReportSPAController::class, 'allSales']);
        Route::get('group-sales', [SalesReportSPAController::class, 'groupSales']);
        Route::get('chart-omzet', [SalesReportSPAController::class, 'chartOmzet']);
        Route::get('chart-leadsource', [SalesReportSPAController::class, 'chartLeadSource']);
        Route::get('chart-leadstatus', [SalesReportSPAController::class, 'chartLeadStatus']);
        Route::get('chart-leadcity', [SalesReportSPAController::class, 'chartLeadCity']);
        Route::get('chart-leadpackage', [SalesReportSPAController::class, 'chartLeadPackage']);
        Route::get('chart-sales-category', [SalesReportSPAController::class, 'chartSalesCategory']);
        Route::get('chart-sales-subcategory', [SalesReportSPAController::class, 'chartSalesSubCategory']);
        Route::get('performance-monthly', [SalesReportSPAController::class, 'performanceMonthly']);
        Route::get('chart-performance-monthly', [SalesReportSPAController::class, 'chartPerformanceMonthly']);
        Route::get('performance-daily', [SalesReportSPAController::class, 'performanceDaily']);
        Route::get('performance-daily-export', [SalesReportSPAController::class, 'performanceDailyExport']);
        Route::get('performance-monthly-export', [SalesReportSPAController::class, 'performanceMonthlyExport']);
        Route::get('top-trip-export', [SalesReportSPAController::class, 'topTripExport']);
        Route::get('top-trip-monthly-export', [SalesReportSPAController::class, 'topTripMonthlyExport']);
        Route::get('top-package-monthly-export', [SalesReportSPAController::class, 'topPackageMonthlyExport']);
        Route::get('top-umroh-trip', [SalesReportSPAController::class, 'topUmrohTrip']);
        Route::get('top-package-umroh-trip', [SalesReportSPAController::class, 'topPackageUmrohTrip']);
        Route::get('sales-haji-furoda', [SalesReportSPAController::class, 'salesHajiFuroda']);
        Route::get('chart-sales-haji', [SalesReportSPAController::class, 'cartSalesHaji']);
        Route::get('detail-recommendation', [SalesReportSPAController::class, 'detailRecommendation']);
        Route::get('chart-order-cso', [SalesReportSPAController::class, 'chartOrderByCso']);
        Route::get('log-cso-closings', [SalesReportSPAController::class, 'logCsoClosings']);
        Route::post('log-cso-closings/create-closing', [SalesReportSPAController::class, 'createLogClosing']);
    });

    // Old Sales Report
    Route::prefix('sales-report-old')->name('spa.sales-report.')->group(function () {
        Route::get('all-sales', [SalesReportSPAControllerOld::class, 'allSales']);
        Route::get('group-sales', [SalesReportSPAControllerOld::class, 'groupSales']);
        Route::get('chart-omzet', [SalesReportSPAControllerOld::class, 'chartOmzet']);
        Route::get('chart-leadsource', [SalesReportSPAControllerOld::class, 'chartLeadSource']);
        Route::get('chart-leadstatus', [SalesReportSPAControllerOld::class, 'chartLeadStatus']);
        Route::get('chart-leadcity', [SalesReportSPAControllerOld::class, 'chartLeadCity']);
        Route::get('chart-leadpackage', [SalesReportSPAControllerOld::class, 'chartLeadPackage']);
        Route::get('chart-sales-category', [SalesReportSPAControllerOld::class, 'chartSalesCategory']);
        Route::get('chart-sales-subcategory', [SalesReportSPAControllerOld::class, 'chartSalesSubCategory']);
        Route::get('performance-monthly', [SalesReportSPAControllerOld::class, 'performanceMonthly']);
        Route::get('chart-performance-monthly', [SalesReportSPAControllerOld::class, 'chartPerformanceMonthly']);
        Route::get('performance-daily', [SalesReportSPAControllerOld::class, 'performanceDaily']);
        Route::get('performance-daily-export', [SalesReportSPAControllerOld::class, 'performanceDailyExport']);
        Route::get('performance-monthly-export', [SalesReportSPAControllerOld::class, 'performanceMonthlyExport']);
        Route::get('top-trip-export', [SalesReportSPAControllerOld::class, 'topTripExport']);
        Route::get('top-trip-monthly-export', [SalesReportSPAControllerOld::class, 'topTripMonthlyExport']);
        Route::get('top-package-monthly-export', [SalesReportSPAControllerOld::class, 'topPackageMonthlyExport']);
        Route::get('top-umroh-trip', [SalesReportSPAControllerOld::class, 'topUmrohTrip']);
        Route::get('top-package-umroh-trip', [SalesReportSPAControllerOld::class, 'topPackageUmrohTrip']);
        Route::get('sales-haji-furoda', [SalesReportSPAControllerOld::class, 'salesHajiFuroda']);
        Route::get('chart-sales-haji', [SalesReportSPAControllerOld::class, 'cartSalesHaji']);
        Route::get('detail-recommendation', [SalesReportSPAControllerOld::class, 'detailRecommendation']);
        Route::get('chart-order-cso', [SalesReportSPAControllerOld::class, 'chartOrderByCso']);
        Route::get('log-cso-closings', [SalesReportSPAControllerOld::class, 'logCsoClosings']);
        Route::post('log-cso-closings/create-closing', [SalesReportSPAControllerOld::class, 'createLogClosing']);
    });

    // Report
    Route::prefix('report')->name('spa.report.')->group(function () {
        Route::get('tabulation-data', [ReportSPAController::class, 'tabulationData']);
        Route::get('tabulation-detail/{id}', [ReportSPAController::class, 'tabulationDetail']);
        Route::post('download-tabulation-data', [ReportSPAController::class, 'downloadTabulationData']);
        Route::get('download-tabulation-data-detail', [ReportSPAController::class, 'downloadTabulationDataDetail']);
        Route::get('resume-departure', [ReportSPAController::class, 'resumeDeparture']);
        Route::get('resume-package', [ReportSPAController::class, 'resumePackage']);
        Route::get('control-budget', [ReportSPAController::class, 'controlBudget']);
        Route::get('control-budget-detail', [ReportSPAController::class, 'controlBudgetDetail']);
        Route::get('control-budget-summary/{id}', [ReportSPAController::class, 'controlBudgetSummary']);
    });

    Route::get('notifications', [NotificationSPAController::class, 'index']);
    Route::post('notifications/read', [NotificationSPAController::class, 'readNotification']);
    Route::post('notifications/read-all', [NotificationSPAController::class, 'readAllNotification']);

    // Budgeting
    Route::resource('budgeting', BudgetingSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('budgeting/copy-budgeting', [BudgetingSPAController::class, 'copyBudgeting']);
    Route::get('budgeting-search', [BudgetingSPAController::class, 'queryBudgetings']);
    Route::resource('budgeting-category', BudgetingCategorySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('budgeting-category-search', [BudgetingCategorySPAController::class, 'queryCategories']);
    Route::post('budgeting/budget-ticket', [BudgetingSPAController::class, 'postBudgetTicket']);
    Route::post('budgeting/budget-hotel', [BudgetingSPAController::class, 'postBudgetHotel']);
    Route::post('budgeting/budget-mutawwif', [BudgetingSPAController::class, 'postBudgetMutawwif']);
    Route::post('budgeting/budget-tour-leader', [BudgetingSPAController::class, 'postBudgetTourLeader']);
    Route::post('budgeting/budget-equipment', [BudgetingSPAController::class, 'postBudgetEquipment']);
    Route::post('budgeting/budget-general', [BudgetingSPAController::class, 'postBudgetGeneral']);
    Route::get('budgeting-list', [BudgetingSPAController::class, 'budgetingList']);

    // Budgeting Realization
    Route::resource('budget-realization', BudgetRealizationSPAController::class)->only(['show', 'store', 'index', 'destroy']);

    Route::resource('currency', CurrencySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('currency-all', [CurrencySPAController::class, 'getAll']);
    Route::get('currency-search', [CurrencySPAController::class, 'queryCurrencies']);
    Route::resource('city', CitySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('city-search', [CitySPAController::class, 'queryCities']);
    Route::get('city-all', [CitySPAController::class, 'getAllCity']);
    Route::resource('hotel', HotelSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('hotel-graphic/{hotelId}', [HotelSPAController::class, 'viewGraphic']);
    Route::get('hotel-search', [HotelSPAController::class, 'queryHotels']);
    Route::get('hotel-by-city/{id}', [HotelSPAController::class, 'getHotelByCity']);
    Route::post('hotel/rate', [HotelSPAController::class, 'postHotelRate']);
    Route::get('hotel-rate-histories/{hotelId}', [HotelSPAController::class, 'hotelRateHistories']);
    Route::resource('hotel-room-type', HotelRoomTypeSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('hotel-room-type-search', [HotelRoomTypeSPAController::class, 'queryHotelRoomTypes']);

    // JIOS
    Route::resource('item-category', CategorySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('item-category-search', [CategorySPAController::class, 'queryCategories']);
    Route::get('parent-category-search', [CategorySPAController::class, 'queryParentCategories']);
    Route::resource('master-unit', MasterUnitSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('master-unit-search', [MasterUnitSPAController::class, 'queryUnits']);
    Route::resource('item', ItemSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('item-search', [ItemSPAController::class, 'queryItems']);
    Route::post('jios/generate-link', [JiosSPAController::class, 'generateLink']);
    Route::get('jios/resume/{umrohTripId}', [JiosSPAController::class, 'resume']);
    Route::resource('jios', JiosSPAController::class)->only(['show', 'index', 'destroy']);

    Route::resource('log-trip-activity', LogTripActivitySPAController::class)->only(['index']);
    Route::resource('log-product-activity', LogProductActivitySPAController::class)->only(['index']);
    Route::resource('log-article-activity', LogArticleActivitySPAController::class)->only(['index']);
    Route::resource('log-letter-activity', LogLetterActivitySPAController::class)->only(['index']);
    Route::get('log-letter-export', [LogLetterActivitySPAController::class, 'export']);
    Route::resource('log-qontak-activity', LogQontakActivitySPAController::class)->only(['index']);
    Route::resource('log-accurate-activity', LogAccurateActivitySPAController::class)->only(['index']);

    // Mitra
    Route::resource('mitra', MitraSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('mitra-transaction', [MitraSPAController::class, 'mitraTransactions']);
    Route::get('mitra-media', [MediaMarketingSPAController::class, 'mitraMedia']);
    Route::get('mitra-media/download/{id}', [MediaMarketingSPAController::class, 'mitraMediaDownload']);
    Route::get('mitra-withdraw', [MitraSPAController::class, 'mitraWithdrawals']);
    Route::post('mitra-withdraw/submit', [MitraSPAController::class, 'mitraWithdrawalSubmit']);

    // Media Marketings
    Route::resource('media-marketing', MediaMarketingSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('media-marketing-preview-flyer/{id}', [MediaMarketingSPAController::class, 'previewFlyerPDF']);
    Route::get('media-marketing-download-flyer/{id}', [MediaMarketingSPAController::class, 'downloadFlyerPDF']);
    Route::post('media-marketing/delete-image', [MediaMarketingSPAController::class, 'deleteImage']);

    // FAQ
    Route::resource('faq-category', FaqCategorySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('faq-category-search', [FaqCategorySPAController::class, 'queryCategories']);
    Route::get('faq-parent-category-search', [FaqCategorySPAController::class, 'queryParentCategories']);
    Route::resource('faq-content', FaqContentSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('faq-content-search', [FaqContentSPAController::class, 'queryContents']);

    // FAQ
    Route::resource('gallery-category', GalleryCategorySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('gallery-category-search', [GalleryCategorySPAController::class, 'queryCategories']);
    Route::get('gallery-parent-category-search', [GalleryCategorySPAController::class, 'queryParentCategories']);
    Route::resource('gallery-content', GalleryContentSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('gallery-content-search', [GalleryContentSPAController::class, 'queryContents']);

    // Asatidz
    Route::resource('asatidz', AsatidzSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('asatidz-search', [AsatidzSPAController::class, 'queryAsatidz']);

    // Live Stream
    Route::resource('live-stream', LiveStreamSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('live-stream-search', [LiveStreamSPAController::class, 'queryLiveStream']);

    // Equipment Master Data
    Route::resource('equipment-category', EquipmentCategorySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('equipment-category-search', [EquipmentCategorySPAController::class, 'queryCategories']);
    Route::resource('equipment-size', EquipmentSizeSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('equipment-size-search', [EquipmentSizeSPAController::class, 'querySizes']);
    Route::resource('equipment-unit', EquipmentUnitSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('equipment-unit-search', [EquipmentUnitSPAController::class, 'queryUnits']);
    Route::resource('equipment-broken', EquipmentBrokenSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('vendor-search', [VendorSPAController::class, 'queryVendors']);
    Route::resource('vendor', VendorSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('vendor-list-vendor', [VendorSPAController::class, 'getList']);

    Route::resource('equipment-borrow-purpose', EquipmentBorrowPurposeSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('equipment-borrow-purpose-search', [EquipmentBorrowPurposeSPAController::class, 'queryPurposes']);
    Route::resource('equipment-borrow', EquipmentBorrowSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('equipment-in-out', EquipmentInOutSPAController::class)->only(['store']);

    Route::get('booking-transaction', [BookingTransactionController::class, 'index']);
    Route::get('jios-transaction', [JiosTransactionController::class, 'index']);
    Route::resource('marketplace-transaction', MarketplaceTransactionSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('marketplace-transaction-document-pdf/{id}', [MarketplaceTransactionSPAController::class, 'documentPdf']);
    Route::get('marketplace-transaction-delete-item/{id}', [MarketplaceTransactionSPAController::class, 'deleteItem']);

    Route::resource('survey', SurveySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('survey/{id}/summary', [SurveySPAController::class, 'summary']);
    Route::get('survey-trip-search', [SurveySPAController::class, 'tripSearch']);
    Route::get('survey-export-summary-question', [SurveySPAController::class, 'exportSummaryQuestion']);
    Route::get('survey-export-summary', [SurveySPAController::class, 'exportSummary']);
    Route::post('survey/update-answer-section', [SurveySPAController::class, 'updateAnswerSection']);
    Route::post('survey/update-answer', [SurveySPAController::class, 'updateAnswer']);
    Route::get('survey/{id}/report', [SurveySPAController::class, 'surveyReport']);
    Route::get('survey-report-comprehensive', [SurveySPAController::class, 'surveyMultipleReport']);
    Route::post('survey-report-comprehensive', [SurveySPAController::class, 'downloadSurveyReport']);

    Route::resource('form-section', FormSectionSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('form-section-search', [FormSectionSPAController::class, 'queryFormSectionSearch']);

    Route::get('ticketing/download-manifest', [TicketingSPAController::class, 'downloadManifest']);
    Route::resource('ticketing', TicketingSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('ticketing/update-data', [TicketingSPAController::class, 'updateDate']);
    // Ticketing Auctions
    Route::resource('ticketing-auction', TicketingAuctionSPAController::class)->only(['show', 'store', 'index', 'destroy']);

    Route::get('refine-ticketing-seeder', [TicketingSPAController::class, 'refineTicketingSeeder']);
    Route::post('ticketing-copy', [TicketingSPAController::class, 'ticketCopy']);
    Route::get('ticketing-trip-search', [TicketingSPAController::class, 'getSearch']);
    Route::get('ticketing-search', [TicketingSPAController::class, 'getTicketingSearch']);
    Route::get('ticketing-list', [TicketingSPAController::class, 'getListTicketing']);
    // TRANSACTIONS
    Route::resource('transaction', TransactionSPAController::class)->only(['store', 'destroy']);
    Route::get('transaction-list', [TransactionSPAController::class, 'getListTransaction']);

    Route::resource('operational-umroh-trip', OperationalUmrohTripSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('refine-operational-umroh-trip-seeder', [OperationalUmrohTripSPAController::class, 'refineOperationalUmrohTripSeeder']);
    Route::post('operational-umroh-trip/add-reservation', [OperationalUmrohTripSPAController::class, 'postAddReservation']);
    Route::post('operational-umroh-trip/add-payment', [OperationalUmrohTripSPAController::class, 'postAddPayment']);
    Route::post('operational-umroh-trip/add-itinerary', [OperationalUmrohTripSPAController::class, 'postAddItinerary']);
    Route::get('operational-hotel-reservation', [OperationalUmrohTripSPAController::class, 'hotelReservations']);
    Route::get('operational-hotel-reservation-list/{id}', [OperationalUmrohTripSPAController::class, 'getListAllContract']);

    Route::get('operational-hotel-reservation/{id}', [OperationalUmrohTripSPAController::class, 'hotelReservationDetail']);
    Route::delete('operational-hotel-reservation/{id}', [OperationalUmrohTripSPAController::class, 'deleteHotelReservation']);
    Route::post('operational-umroh-trip/upload-roomlist-by-handling', [OperationalUmrohTripSPAController::class, 'postUploadRoomlistByHandling']);
    Route::get('operational-hotel-payment', [OperationalUmrohTripSPAController::class, 'hotelReservationPayments']);
    Route::delete('operational-hotel-payment/{id}', [OperationalUmrohTripSPAController::class, 'deleteReservationPayment']);
    Route::get('operational-hotel-itinerary', [OperationalUmrohTripSPAController::class, 'itineraries']);
    Route::get('operational-hotel-statis-itinerary', [OperationalUmrohTripSPAController::class, 'statisItineraries']);
    Route::delete('operational-hotel-itinerary/{id}', [OperationalUmrohTripSPAController::class, 'deleteItinerary']);
    Route::post('operational-hotel-itinerary/checklist-itinerary', [OperationalUmrohTripSPAController::class, 'postChecklistItinerary']);

    // BADAL UMROH HAJI
    Route::post('badal-umroh-haji/update-badal', [BadalUmrohTripSPAController::class, 'postUpdateBadal']);
    Route::post('badal-umroh-haji/post-badal-certificate', [BadalUmrohTripSPAController::class, 'postBadalCertificate']);
    Route::get('badal-umroh-haji/download-badal-certificate', [BadalUmrohTripSPAController::class, 'downloadCertificate']);
    Route::get('badal-umroh-haji/shipment-labels', [BadalUmrohTripSPAController::class, 'shipmentLabels']);
    Route::get('badal-umroh-haji/assatidz', [BadalUmrohTripSPAController::class, 'getAssatidz']);
    Route::get('badal-umroh-haji/{id}', [BadalUmrohTripSPAController::class, 'geDetail']);

    Route::resource('campaign', CampaignSPAController::class)->only(['show', 'store', 'index', 'destroy']);

    Route::resource('order-change-request', OrderChangeRequestSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('order-change-request/approval', [OrderChangeRequestSPAController::class, 'approval']);

    Route::get('booking-hotel-event/hotel-search', [BookingHotelEventSPAController::class, 'hotelSearch']);
    Route::get('booking-hotel-event/room-type-search', [BookingHotelEventSPAController::class, 'roomTypeSearch']);
    Route::post('booking-hotel-event/recreate-invoice', [BookingHotelEventSPAController::class, 'reCreateInvoice']);
    Route::resource('booking-hotel-event', BookingHotelEventSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('booking-hotel-event-export', [BookingHotelEventSPAController::class, 'exportData']);
    Route::get('booking-hotel-event/download-receipt/{id}', [BookingHotelEventSPAController::class, 'downloadReceiptPDF']);

    // REFINE ORDER DATA WITH DISCOUNT
    Route::get('refine-discount-order', [BookingOrderSPAController::class, 'refineDiscount']);
    Route::get('refine-assigned-discount-order', [BookingOrderSPAController::class, 'refineAssignedDiscount']);
    // REFINE ORDER ITEM
    Route::get('refine-order-item', [BookingOrderSPAController::class, 'refineOrderItem']);
    Route::get('refine-booking-order-item-trip', [BookingOrderSPAController::class, 'refineBookingOrderItemTrip']);
    // REFINE JAMAAH CRM
    Route::get('refine-participant-crm', [ParticipantCRMSPAController::class, 'refineParticipantCRM']);
    Route::get('refine-participant-crm-manual', [ParticipantCRMSPAController::class, 'refineManualParticipantCRM']);
    Route::get('refine-participant-crm-history', [ParticipantCRMSPAController::class, 'refineHistoryParticipantCRM']);
    Route::get('refine-participant-by-office', [ParticipantSPAController::class, 'refineParticipantByOffice']);
    Route::get('refine-booking-order-from', [UmrohBookingSeatSPAController::class, 'refineBookingOrderFrom']);
    Route::get('refine-package-umroh-trip', [PackageSPAController::class, 'refinePackageUmrohTrip']);
    Route::get('refine-full-book-umroh-trip', [UmrohBookingSeatSPAController::class, 'refineFullBookUmrohTrip']);
    Route::get('refine-booking-no-hp', [UmrohBookingSeatSPAController::class, 'refineBookingNoHp']);
    Route::get('refine-participant-no-hp', [UmrohBookingSeatSPAController::class, 'refineParticipantNoHp']);
    Route::get('refine-log-qontak-broadcast/{umrohTripId}', [EventAttendanceController::class, 'refineLogQontakBroadcast']);
    Route::get('refine-attendee-paid-event', [AttendeeOpenRegistrationController::class, 'refineAttendeePaidEvent']);
    Route::get('refine-participant-city', [ParticipantSPAController::class, 'refineParticipantCity']);
    Route::get('refine-participant-kecamatan', [ParticipantSPAController::class, 'refineParticipantKecamatan']);
    Route::get('refine-participant-kelurahan', [ParticipantSPAController::class, 'refineParticipantKelurahan']);
    Route::get('refine-participant-domisili-city', [ParticipantSPAController::class, 'refineParticipantDomisiliCity']);
    Route::get('refine-participant-domisili-kecamatan', [ParticipantSPAController::class, 'refineParticipantDomisiliKecamatan']);
    Route::get('refine-participant-domisili-kelurahan', [ParticipantSPAController::class, 'refineParticipantDomisiliKelurahan']);
    Route::get('refine-participant-duplicate', [ParticipantSPAController::class, 'refineParticipantDuplicate']);
    Route::get('refine-log-letter', [LogLetterActivitySPAController::class, 'refineLogLetter']);
    Route::get('refine-participant-transaction', [ParticipantCRMSPAController::class, 'refineParticipantCRMTotalTransaction']);
    Route::get('check-barcode-event', [EventAttendanceController::class, 'checkBarcodeEvent']);
    Route::get('check-down-payment-unpaid', [BookingOrderSPAController::class, 'checkDownPaymentUnpaid']);
    Route::get('refine-participant-address', [ParticipantSPAController::class, 'refineParticipantAddress']);
    Route::get('refine-master-address', [ParticipantSPAController::class, 'refineMasterAddress']);
    Route::get('refine-room-umroh-trip', [ParticipantUmrohTripSPAController::class, 'refineRoomUmrohTrips']);
    Route::get('refine-seat-umroh-trip', [ParticipantUmrohTripSPAController::class, 'refineSeatUmrohTrips']);
    Route::get('refine-order-sales-id', [BookingOrderSPAController::class, 'refineOrderSalesId']);
    Route::get('refine-badal-umroh-trip-price-id', [BookingOrderSPAController::class, 'refineBadalUmrohTripPriceId']);
    Route::get('refine-participant-jicode', [ParticipantSPAController::class, 'refineParticipantJICode']);

    // OPERATIONAL COST
    Route::resource('operational-cost', OperationalCostSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('operational-cost-search', [OperationalCostSPAController::class, 'queryOperationalCosts']);
    // COMMISION FEE
    Route::resource('commission-fee', CommissionFeeSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    // TARGET ACHIVEMENT
    Route::resource('target-achivement', TargetAchivementCostSPAController::class)->only(['store']);
    Route::get('target-achivement-detail', [TargetAchivementCostSPAController::class, 'getDetail']);

    // MASTER HOTEL EVENT
    Route::resource('master-hotel-event', MasterHotelEventSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('master-hotel-event-price-category', [MasterHotelEventSPAController::class, 'priceCategoryList']);
    Route::get('master-hotel-event-get-counter/{id}', [MasterHotelEventSPAController::class, 'getCounter']);
    Route::get('master-hotel-event-transit-hotel', [MasterHotelEventSPAController::class, 'getTransitHotel']);
    Route::get('master-hotel-event-manasik-hotel', [MasterHotelEventSPAController::class, 'getManasikHotel']);
    // ADD CATEGORY
    Route::post('master-hotel-event-add-category', [MasterHotelEventSPAController::class, 'addCategory']);
    Route::post('master-hotel-event-delete-item', [MasterHotelEventSPAController::class, 'deleteItem']);
    // PREFER
    Route::post('master-hotel-event-prefer/{id}', [MasterHotelEventSPAController::class, 'prefer']);

    // EVENT TIMELINE
    Route::resource('event-timeline', EventTimelineSPAController::class)->only(['show', 'index', 'destroy']);
    Route::post('event-timeline/update', [EventTimelineSPAController::class, 'updateData']);
    Route::get('event-timeline-seeder', [EventTimelineSPAController::class, 'refineEventTimelineSeeder']);
    Route::resource('event-manasik-online', EventManasikOnlineSPAController::class)->only(['show', 'store', 'index', 'destroy']);

    Route::get('operational-cost-parent-list', [OperationalCostSPAController::class, 'getList']);
    Route::get('operational-cost-user-list', [OperationalCostSPAController::class, 'getUserList']);

    Route::get('get-upgrade-hotel', [ParticipantUmrohTripSPAController::class, 'getUpgradeHotel']);

    // Refund Notes
    Route::resource('refund-notes', RefundNotesSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::get('refund-notes-list', [RefundNotesSPAController::class, 'getList']);

    Route::resource('booking', BookingSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('booking/action', [BookingSPAController::class, 'action']);
    Route::get('booking-export', [BookingSPAController::class, 'exportData']);
    Route::get('booking/download-invoice/{id}', [BookingSPAController::class, 'downloadInvoicePDF']);
    Route::get('booking/download-receipt/{id}', [BookingSPAController::class, 'downloadReceiptPDF']);


    Route::resource('booking-temporary', BookingTemporarySPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::resource('booking-receipt', BookingReceiptSPAController::class)->only(['show', 'store', 'index', 'destroy']);
    Route::post('booking-receipt/action', [BookingReceiptSPAController::class, 'action']);
});

Route::prefix('public')->group(function () {
    Route::resource('event-open-registration-form', EventOpenRegistration::class)->only(['show', 'store']);
    Route::get('cron/qontakDocumentInformation', [CrontController::class, 'sendQontakDocumentInformation']);
    Route::get('cron/qontakClosedEventConfirmation', [CrontController::class, 'sendClosedEventConfirmation']);
});

Route::get('/{any}', [SPAController::class, 'index'])->where('any', '.*');
