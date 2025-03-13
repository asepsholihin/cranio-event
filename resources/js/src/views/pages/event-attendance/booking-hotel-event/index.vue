<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">
        <b-row class="d-flex align-items-center justify-content-end">
          <b-col cols="12" md="4" class="mb-1">
            <label>Filter by Trip</label>
            <v-select v-model="umrohTripFilter" placeholder="Select trip" :options="umrohTripFilterOptions"
              :filterable="false" @search="onSearch" :reduce="trip => trip.id" label="title" />
          </b-col>
          <b-col cols="12" md="3" class="mb-1">
            <label>Filter by Package</label>
            <v-select v-model="packageFilter" :options="packageOptions" class="w-100" :reduce="label => label.name" label="name" />
          </b-col>
          <b-col cols="12" md="3" class="mb-1">
            <label>Filter by Hotel</label>
            <v-select v-model="hotelFilter" :options="hotelOptions" class="w-100" :reduce="val => val.hotel_name" label="hotel_name" />
          </b-col>
        </b-row>
        <b-row class="d-flex align-items-center justify-content-end">
          <b-col cols="12" md="4" class="mb-1">
            <label>Filter by Room Type</label>
            <v-select v-model="roomTypeFilter" :options="roomTypeOptions" class="w-100" :reduce="val => val.room_type" label="room_type" />
          </b-col>
          <b-col cols="12" md="2" class="mb-1">
            <label>Filter by Status</label>
            <v-select v-model="statusFilter" :options="statusOptions" class="w-100" :reduce="val => val.value" />
          </b-col>
          <b-col cols="12" md="3" class="mb-1">
            <label>Filter by Checkin Date</label>
            <div class="input-group">
              <flat-pickr v-model="checkinDateFilter" :config="{ mode: 'range' }" class="form-control" name="date"/>
              <div class="input-group-append">
                <button class="btn btn-danger btn-sm" type="button" @click="clearDate">
                  <feather-icon icon="XIcon" />
                </button>
              </div>
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12" md="6" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
            <label>Show</label>
            <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
              class="per-page-selector d-inline-block mx-50" />
            <label>entries</label>
          </b-col>
          <b-col cols="12" md="6">
            <div class="d-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1" placeholder="Search..." />
              <b-button variant="success" class="mb-lg-0 mb-1" @click="exportData">
                <span class="text-nowrap">Export</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refDataListTable" class="position-relative" :items="fetchData" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <template #cell(name)="data">
          <div style="width:200px">{{ data.item.name }}</div>
          <span class="text-nowrap" v-if="data.item.package_name">{{ data.item.package_name }}<br></span>
          <span class="text-nowrap">{{ data.item.no_hp }}</span>
        </template>

        <template #cell(umroh_trip_title)="data">
          <div style="width:250px">{{ data.item.umroh_trip_title }}</div>
        </template>

        <template #cell(checkin)="data">
          <span class="text-nowrap">IN: {{ formatDate(data.item.checkin_date) }}</span><br>
          <span class="text-nowrap">OUT: {{ formatDate(data.item.checkout_date) }}</span>
        </template>

        <template #cell(room)="data">
          <span class="text-nowrap">{{ data.item.room_type }}</span><br>
          <span class="text-nowrap">Rp. {{ parseInt(data.item.room_price_pax).toLocaleString() }}</span>
        </template>

        <template #cell(total_room)="data">
          <span class="text-nowrap">{{ data.item.total_room }} Room</span><br>
          <span class="text-nowrap">{{ data.item.total_pax }} Pax</span>
        </template>

        <template #cell(additional_item)="data">
          <span class="text-nowrap">{{ data.item.additional_item }}</span><br>
          <div v-if="data.item.additional_pax">{{ data.item.additional_pax }} Pax</div>
          <span class="text-nowrap">Rp. {{ parseInt(data.item.additional_cost).toLocaleString() }}</span>
        </template>

        <template #cell(payment_method)="data">
          <span class="text-nowrap">{{ formatDateTimeShort(data.item.created_at) }}</span><br>
          <span class="text-nowrap font-weight-bolder">Rp. {{ parseInt(data.item.total_amount).toLocaleString() }}</span>
          <br />
          <b-badge pill v-if="data.item.status == 'pending'" variant="warning">PENDING</b-badge>
          <b-badge pill v-else-if="data.item.status == 'paid'" variant="success">PAID</b-badge>
          <b-badge pill v-else-if="data.item.status == 'booked'" variant="primary">BOOKED</b-badge>
          <b-badge pill v-else-if="data.item.status == 'access_given'" variant="primary">ACCESS GIVEN</b-badge>
          <b-badge pill v-else-if="data.item.status == 'cancelled'" variant="danger">CANCELLED</b-badge>
          <b-badge pill v-else-if="data.item.status == 'payment_expired'" variant="danger">PAYMENT EXPIRED</b-badge>
          <b-badge pill v-else variant="warning">PENDING</b-badge>
          <div v-if="data.item.status == 'pending'">
              <b-link @click="copyLinkXendit(data.item.invoice_xendit_url)">Copy Link Xendit</b-link>
          </div>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item v-if="data.item.status == 'payment_expired'" @click="reCreateInvoice(data.item)">
              <feather-icon icon="LinkIcon" />
              <span class="align-middle ml-50">Buat Ulang Pesanan</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="data.item.status == 'paid'" @click="downloadReceipt(data.item)">
              Download Receipt
            </b-dropdown-item>
            <b-dropdown-item @click="updateBookingInfo(data.item)">
              Update Booking Info
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteData(data.item)">
              <feather-icon icon="Trash2Icon" />
              <span class="align-middle ml-50">Delete</span>
            </b-dropdown-item>
          </b-dropdown>
        </template>

      </b-table>
      <div class="mx-2 mb-2">
        <b-row>

          <b-col cols="12" sm="6" class="d-flex align-items-center justify-content-center justify-content-sm-start">
            <span class="text-muted">Showing {{ dataMeta.from }} to {{ dataMeta.to }} of {{ dataMeta.of }}
              entries</span>
          </b-col>
          <!-- Pagination -->
          <b-col cols="12" sm="6" class="d-flex align-items-center justify-content-center justify-content-sm-end">

            <b-pagination v-model="currentPage" :total-rows="totalCurrencies" :per-page="perPage" first-number last-number
              class="mb-0 mt-1 mt-sm-0" prev-class="prev-item" next-class="next-item">
              <template #prev-text>
                <feather-icon icon="ChevronLeftIcon" size="18" />
              </template>
              <template #next-text>
                <feather-icon icon="ChevronRightIcon" size="18" />
              </template>
            </b-pagination>

          </b-col>

        </b-row>
      </div>
    </b-card>

    <b-modal size="lg" v-model="updateBookingInfoModal" :busy="isButtonLoading" @hidden="resetModal" no-close-on-backdrop @ok="handleOkUpdateInfo" ok-title="Submit">
      <template #modal-title>
          <h4>Update Booking Info</h4>
      </template>
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form @submit.prevent="onSubmitUpdateInfo" @reset.prevent="resetModal">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <table class="mb-2">
            <tr>
              <td>Full Name</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.name }}</td>
            </tr>
            <tr>
              <td>Phone Number</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.no_hp }}</td>
            </tr>
            <tr>
              <td>Room Type</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.room_type }}</td>
            </tr>
            <tr>
              <td>Total Room</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.total_room }} Room - {{ formData.total_pax }} Pax</td>
            </tr>
          </table>
          
          <!-- Room Number -->
          <validation-provider #default="{ errors }" name="Room Number" vid="room_number" rules="required">
            <b-form-group label="Room Number" :state="errors.length > 0 ? false : null">
              <b-form-input v-model="formData.room_number"/>
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                  {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
        </b-form>
      </validation-observer>
    </b-modal>
  </div>
</template>

<script>
import {
  BCard,
  BRow,
  BCol,
  BForm,
  BFormGroup,
  BFormInput,
  BButton,
  BTable,
  BMedia,
  BAvatar,
  BLink,
  BBadge,
  BDropdown,
  BDropdownItem,
  BPagination,
  BFormInvalidFeedback
} from 'bootstrap-vue'
import _ from 'lodash'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import useDataList from './useDataList'
import { deleteData, postData, exportData, getReceiptPDF, getHotelSearch, getRoomTypeSearch, reCreateInvoice } from '@/network/booking-hotel-event'
import { hasPermission } from '@/auth/utils'
import { formatDate, formatDateTimeShort } from '@core/utils/filter'
import { getTripSearch } from '@/network/booking-order'
import { getPackageSearch } from '@/network/package'

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BForm,
    BFormGroup,
    BFormInput,
    BButton,
    BTable,
    BMedia,
    BAvatar,
    BLink,
    BBadge,
    BDropdown,
    BDropdownItem,
    BPagination,
    BFormInvalidFeedback,

    vSelect,
    flatPickr,
    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  setup() {

    const statusOptions = [
      { label: 'Pending', value: 'pending' },
      { label: 'Paid', value: 'paid' },
      { label: 'Booked', value: 'booked' },
      { label: 'Access Given', value: 'access_given' },
      { label: 'Cancelled', value: 'cancelled' },
      { label: 'Payment Expired', value: 'payment_expired' }
    ]
    
    const {
      fetchData,
      tableColumns,
      perPage,
      currentPage,
      totalCurrencies,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refDataListTable,
      refetchData,

      // Extra Filters
      statusFilter,
      hotelFilter,
      roomTypeFilter,
      umrohTripFilter,
      packageFilter,
      checkinDateFilter
    } = useDataList()

    return {
      fetchData,
      tableColumns,
      perPage,
      currentPage,
      totalCurrencies,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refDataListTable,
      refetchData,

      // Extra Filters
      statusFilter,
      hotelFilter,
      roomTypeFilter,
      umrohTripFilter,
      packageFilter,
      checkinDateFilter,

      hasPermission,
      formatDate,
      formatDateTimeShort,
      statusOptions
    }
  },
  data() {
    const umrohTripFilterOptions = []
    const packageOptions = []
    const roomTypeOptions = []
    const hotelOptions = []

    getTripSearch({ q: "", available: true }).then((res) => {
        this.umrohTripFilterOptions = res.data;
    }).catch((error) => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: "danger", toaster: "b-toaster-top-center", solid: true });
    })
    getPackageSearch().then(response => {
        this.packageOptions = response.data;
    }).catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: "danger", toaster: "b-toaster-top-center", solid: true });
    })
    getHotelSearch({ q: "" }).then((res) => {
        this.hotelOptions = res.data;
    }).catch((error) => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: "danger", toaster: "b-toaster-top-center", solid: true });
    })
    getRoomTypeSearch({ q: "" }).then((res) => {
        this.roomTypeOptions = res.data;
    }).catch((error) => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: "danger", toaster: "b-toaster-top-center", solid: true });
    })
    
    return {
      required, numeric,
      formData: {},
      updateBookingInfoModal: false,
      isButtonLoading: false,
      umrohTripFilterOptions,
      packageOptions,
      roomTypeOptions,
      hotelOptions
    }
  },
  methods : {
    clearDate() {
      this.checkinDateFilter = null
    },
    onSearch(search, loading) {
      loading(true)
      this.search(loading, search, this);
    },
    search: _.debounce((loading, search, vm) => {
      getTripSearch({ q: search })
      .then(res => {
        vm.umrohTripFilterOptions = res.data
        loading(false)
      })
      .catch(error => {
        vm.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        loading(false)
      })
    }, 300),
    downloadReceipt(item) {
      window.open(getReceiptPDF(item.id), "_blank");
    },
    resetModal() {
      this.formData = {}
    },
    updateBookingInfo(item) {
      this.updateBookingInfoModal = true
      this.formData = item
    },
    handleOkUpdateInfo(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.onSubmitUpdateInfo()
    },
    onSubmitUpdateInfo() {
      this.$refs.refObsForm.validate().then((success) => {
        if (!success) return;
        this.isButtonLoading = true
        postData(this.formData).then(response => {
          this.$bvToast.toast(`${this.formData.name} has been added successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.isButtonLoading = false
        })
        .catch(error => {
          if (error.response.data.errors) {
            this.$refs.refObsForm.setErrors(error.response.data.errors)
          } else {
            this.$refs.refObsForm.setErrors(error.response.data)
          }
          this.isButtonLoading = false
        })
      })
    },
    exportData() {
      this.isButtonLoading = true
      var vForm = {}
      vForm.status = this.statusFilter
      vForm.checkinDate = this.checkinDateFilter
      vForm.hotel = this.hotelFilter
      vForm.roomType = this.roomTypeFilter
      vForm.umrohTripId = this.umrohTripFilter
      exportData(vForm).then(response => {
        const fileURL = window.URL.createObjectURL(new Blob([response.data]))
        const fileLink = document.createElement('a')
        const contentDisposition = response.headers['content-disposition']
        fileLink.href = fileURL;
        let fileName = 'unknown';
        if (contentDisposition) {
            const fileNameMatch = contentDisposition.match(/filename=(.+)/);
            if (fileNameMatch.length === 2)
                fileName = fileNameMatch[1];
        }
        fileLink.setAttribute('download', fileName);
        document.body.appendChild(fileLink);
        fileLink.click();
        this.isButtonLoading = false
      }).catch(error => {
        this.$bvToast.toast(`Error: ${error}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        this.isButtonLoading = false
      })
    },
    copyLinkXendit(link) {
      navigator.clipboard.writeText(link)
      this.$bvToast.toast(`Link Xendit has been copied`, { title: `Info`, variant: "info", toaster: "b-toaster-top-center", solid: true })
    },
    reCreateInvoice(item){
      this.$swal({
        title: `Buat Ulang Pesanan`,
        text: "Buat ulang Invoice Xendit baru dibuat hanya untuk invoice yang sudah expired, Lanjutkan?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes!',
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-outline-primary ml-1',
        },
        buttonsStyling: false,
      }).then(result => {
            if (result.value) {
                var vForm = {}
                vForm.id = item.id
                reCreateInvoice(vForm).then(response => {
                    this.refetchData()
                })
                .catch(error => {
                    if (error.response.data.errors) {
                        this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                    } else {
                        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                    }
                })
            }
      })
    },
    deleteData(item){
        this.$swal({
        title: `Delete Data ${item.name}?`,
        text: "It cannot be reverted",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-outline-primary ml-1',
        },
        buttonsStyling: false,
      }).then(result => {
            if (result.value) {
                deleteData(item.id).then(response => {
                    this.refetchData()
                })
                .catch(error => {
                    if (error.response.data.errors) {
                        this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                    } else {
                        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                    }
                })
            }
      })
    },
  },
}
</script>

<style lang="scss" scoped>
.per-page-selector {
  width: 90px;
}
</style>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
