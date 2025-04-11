<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">
        <b-row class="d-flex align-items-center justify-content-end">
          <b-col cols="12" md="3" class="mb-1">
            <label>Filter by Package</label>
            <v-select v-model="packageFilter" :options="packageOptions" class="w-100" :reduce="label => label.name" label="name" />
          </b-col>
          <b-col cols="12" md="2" class="mb-1">
            <label>Filter by Status</label>
            <v-select v-model="statusFilter" :options="statusOptions" class="w-100" :reduce="val => val.value" />
          </b-col>
          <b-col cols="12" md="3" class="mb-1">
            <label>Filter by Date</label>
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
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refDataListTable" class="position-relative" :items="fetchData" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <template #cell(account_name)="data">
          <div>{{ data.item.account_name }}</div>
          <span class="text-nowrap">{{ data.item.account_wa }}</span>
        </template>

        <template #cell(total_price)="data">
          <span class="text-nowrap">Rp {{ parseInt(data.item.total_price).toLocaleString() }}</span><br>
          <span class="text-nowrap text-danger" v-if="data.item.total_unpaid > 0">Total Unpaid: Rp {{ parseInt(data.item.total_unpaid).toLocaleString() }}</span>
        </template>

        <template #cell(created_at)="data">
          <span class="text-nowrap">{{ formatDate(data.item.created_at) }}</span>
        </template>

        <template #cell(status)="data">
          <b-badge variant="warning" v-if="data.item.status == 0">Pending</b-badge>
          <b-badge variant="success" v-if="data.item.status == 1">Terdaftar</b-badge>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item variant="danger" @click="deleteData(data.item)" v-if="hasPermission('temp-booking-delete')">
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
import { deleteData, postData } from '@/network/booking-temporary'
import { hasPermission } from '@/auth/utils'
import { formatDate, formatDateTimeShort } from '@core/utils/filter'

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
      { label: 'Pending', value: 1 },
      { label: 'Created', value: 2 }
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
    const packageOptions = []

    return {
      required, numeric,
      formData: {},
      updateBookingInfoModal: false,
      isButtonLoading: false,
      packageOptions,
    }
  },
  methods : {
    clearDate() {
      this.checkinDateFilter = null
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
    deleteData(item){
        this.$swal({
        title: `Delete Data ${item.account_name}?`,
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
          deleteData(item.uuid).then(response => {
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
