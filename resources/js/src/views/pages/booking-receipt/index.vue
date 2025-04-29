<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">
        <b-row class="d-flex align-items-center justify-content-end">
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

        <template #cell(sender_name)="data">
          <div>{{ data.item.sender_name }}</div>
          <span class="text-nowrap">{{ data.item.account_wa }}</span>
        </template>

        <template #cell(payment_amount)="data">
          <span class="text-nowrap">Rp {{ parseInt(data.item.payment_amount).toLocaleString() }}</span>
        </template>

        <template #cell(created_at)="data">
          <span class="text-nowrap">{{ formatDate(data.item.created_at) }}</span>
        </template>

        <template #cell(status)="data">
          <b-badge variant="warning" v-if="data.item.status == 1">Pending</b-badge>
          <b-badge variant="success" v-if="data.item.status == 2">Verified</b-badge>
          <b-badge variant="success" v-if="data.item.status == 3">Rejected</b-badge>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item @click="updateReceiptStatus(data.item)" v-if="hasPermission('booking-receipt-add-or-edit') && data.item.status == 1">
              Update Status
            </b-dropdown-item>
            <b-dropdown-item @click="viewReceipt(data.item)" v-if="hasPermission('booking-receipt-add-or-edit') && data.item.status != 1">
              View
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteData(data.item)" v-if="hasPermission('booking-receipt-delete')">
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

    <b-modal size="lg" v-model="updateReceiptStatusModal" :busy="isButtonLoading" @hidden="resetModal" no-close-on-backdrop @ok="handleOkUpdateInfo" ok-title="Submit">
      <template #modal-title>
          <h4>Update Status</h4>
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
              <td valign="top">Booking No</td>
              <td valign="top" width="10%" class="text-center">:</td>
              <td valign="top">{{ formData.booking_no }}<br>{{ formData.account_wa }}</td>
            </tr>
            <tr>
              <td>Sender Name</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.sender_name }}</td>
            </tr>
            <tr>
              <td>Rekening Tujuan</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.bank_account }}</td>
            </tr>
            <tr>
              <td>Payment Amount</td>
              <td width="10%" class="text-center">:</td>
              <td>Rp {{ parseInt(formData.payment_amount).toLocaleString() }}</td>
            </tr>
          </table>

          <div>
            <img :src="formData.evidence" class="img-fluid" alt="Receipt">
          </div>
          
          <!-- Status -->
          <validation-provider #default="{ errors }" name="Status" vid="status" rules="required">
            <b-form-group label="Status" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.status" :options="statusOptions" class="w-100" :reduce="val => val.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                  {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
        </b-form>
      </validation-observer>
    </b-modal>

    <b-modal size="lg" v-model="viewReceiptModal" @hidden="resetModal" no-close-on-backdrop ok-only>
      <template #modal-title>
          <h4>View Receipt</h4>
      </template>
      
      <table class="mb-2">
        <tr>
          <td valign="top">Booking No</td>
          <td valign="top" width="10%" class="text-center">:</td>
          <td valign="top">{{ formData.booking_no }}<br>{{ formData.account_wa }}</td>
        </tr>
        <tr>
          <td>Sender Name</td>
          <td width="10%" class="text-center">:</td>
          <td>{{ formData.sender_name }}</td>
        </tr>
        <tr>
          <td>Rekening Tujuan</td>
          <td width="10%" class="text-center">:</td>
          <td>{{ formData.bank_account }}</td>
        </tr>
        <tr>
          <td>Payment Amount</td>
          <td width="10%" class="text-center">:</td>
          <td>Rp {{ parseInt(formData.payment_amount).toLocaleString() }}</td>
        </tr>
        <tr>
          <td>Status</td>
          <td width="10%" class="text-center">:</td>
          <td>
            <b-badge variant="warning" v-if="formData.status == 1">Pending</b-badge>
            <b-badge variant="success" v-if="formData.status == 2">Verified</b-badge>
            <b-badge variant="success" v-if="formData.status == 3">Rejected</b-badge>
          </td>
        </tr>
      </table>

      <div>
        <img :src="formData.evidence" class="img-fluid" alt="Receipt">
      </div>
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
import { deleteData, postAction } from '@/network/booking-receipt'
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
      { label: 'Verified', value: 2 },
      { label: 'Rejected', value: 3 }
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

    return {
      required, numeric,
      formData: {},
      updateReceiptStatusModal: false,
      viewReceiptModal: false,
      isButtonLoading: false,
    }
  },
  methods : {
    clearDate() {
      this.checkinDateFilter = null
    },
    resetModal() {
      this.formData = {}
    },
    updateReceiptStatus(item) {
      this.updateReceiptStatusModal = true
      this.formData = item
    },
    viewReceipt(item) {
      this.viewReceiptModal = true
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
        this.formData['update_status'] = true
        postAction(this.formData).then(response => {
          this.$bvToast.toast(`${this.formData.sender_name} has been added successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.isButtonLoading = false
          this.updateReceiptStatusModal = false
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
        title: `Delete Data ${item.sender_name}?`,
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
