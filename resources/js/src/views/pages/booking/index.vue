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
              <b-button variant="success" class="mb-lg-0 mb-1" @click="exportData" :disabled="isButtonLoading">
                <b-spinner small v-show="isButtonLoading" /> <span class="text-nowrap">Export</span>
              </b-button>
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

        <template #cell(total_price_with_tax)="data">
          <span class="text-nowrap" v-if="data.item.total_price_with_tax">Rp {{ parseInt(data.item.total_price_with_tax).toLocaleString() }}</span>
          <span class="text-nowrap" v-else>Rp {{ data.item.total_price.toLocaleString() }}</span>
          <br>
          <span class="text-nowrap text-danger" v-if="data.item.total_unpaid > 0">Unpaid: Rp {{ parseInt(data.item.total_unpaid).toLocaleString() }}</span>
        </template>

        <template #cell(created_at)="data">
          <span class="text-nowrap">{{ formatDate(data.item.created_at) }}</span>
        </template>

        <template #cell(order_status)="data">
          <b-badge variant="warning" v-if="data.item.order_status == 'unpaid'">Unpaid</b-badge>
          <b-badge variant="success" v-if="data.item.order_status == 'paid'">Paid</b-badge>
          <b-badge variant="danger" v-if="data.item.order_status == 'cancel'">Cancel</b-badge>
        </template>

        <template #cell(room_info)="data">
          <div v-if="data.item.room_number != null">
            <p class="m-0">{{ data.item.room_number }}</p>
            <b-button variant="primary" size="sm" @click="viewRoomInfo(data.item)">View Detail</b-button>
          </div>
          <b-button variant="primary" size="sm" v-if="data.item.room_number == null && hasPermission('booking-add-or-edit')" @click="setRoom(data.item)">Set Room Number</b-button>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item @click="updateInfo(data.item)" v-if="hasPermission('booking-add-or-edit')">
              Update Booking Info
            </b-dropdown-item>
            <b-dropdown-item @click="setRoom(data.item)" v-if="data.item.room_number == null && hasPermission('booking-add-or-edit')">
              Set Room Number
            </b-dropdown-item>
            <b-dropdown-item @click="downloadInvoice(data.item)">
              Donwload Invoice
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteData(data.item)" v-if="hasPermission('booking-delete')">
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

    <b-modal size="lg" v-model="updateInfoModal" :busy="isSubmitModal" @hidden="resetModal" no-close-on-backdrop @ok="handleOkUpdateInfo" ok-title="Submit">
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
              <td>Nama Pemesan</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.account_name }}</td>
            </tr>
            <tr>
              <td>Whatsapp</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.account_wa }}</td>
            </tr>
            <tr>
              <td>Paket</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.package }}</td>
            </tr>
            <tr>
              <td>Total Peserta</td>
              <td width="10%" class="text-center">:</td>
              <td>{{ formData.total_pax }} Peserta</td>
            </tr>
          </table>

          <validation-provider #default="{ errors }" name="Price per Pax" vid="price_per_pax">
            <b-form-group label="Price per Pax">
              <cleave v-model="formData.price_per_pax" class="form-control" :options="optionClave" :state="errors.length > 0 ? false : null" v-on:input="calculatePrice" />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Number of Pax" vid="total_pax" rules="numeric:min:1">
            <b-form-group label="Number of Pax">
              <b-form-input v-model="formData.total_pax" name="total_pax" :state="errors.length > 0 ? false : null" trim v-on:input="calculatePrice" />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Total Amount" vid="total_price">
            <b-form-group label="Total Amount">
              <cleave v-model="formData.total_price" class="form-control" :options="optionClave" :state="errors.length > 0 ? false : null" v-on:input="calculatePrice" />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Tax Amount" vid="tax_amount">
            <b-form-group label="Tax Amount">
              <cleave v-model="formData.tax_amount" class="form-control" :options="optionClave" :state="errors.length > 0 ? false : null" v-on:input="calculatePrice" />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Total Amount with Tax" vid="total_price_with_tax">
            <b-form-group label="Total Amount with Tax">
              <cleave v-model="formData.total_price_with_tax" class="form-control" :options="optionClave" :state="errors.length > 0 ? false : null" />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

        </b-form>
      </validation-observer>
    </b-modal>

    <b-modal v-model="setRoomModal" :busy="isSubmitModal" @hidden="resetModal" no-close-on-backdrop @ok="handleOkSetRoom" ok-title="Submit">
      <template #modal-title>
          <h4>Set Room Number</h4>
      </template>
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form @submit.prevent="onSubmitSetRoom" @reset.prevent="resetModal">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Room Number" vid="room_number">
            <b-form-group label="Room Number">
              <b-form-input v-model="formData.room_number" name="room_number" :state="errors.length > 0 ? false : null" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Received By" vid="received_by">
            <b-form-group label="Received By">
              <b-form-input v-model="formData.received_by" name="received_by" :state="errors.length > 0 ? false : null" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
          
          <b-media class="mb-2">
              <template #aside>
              <b-avatar :src="formData.evidence" :text="avatarText('NA')" size="90px" rounded />
              </template>
              <div class="d-flex flex-wrap">
              <b-button v-if="hasPermission('booking-add-or-edit')" variant="primary" size="sm" @click="$refs.refInputEl.click()">
                  <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer">
                  <span class="d-none d-sm-inline">Upload Evidence</span>
                  <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
              </b-button>
              </div>
              <div class="mt-1 text-muted">Max Size: 5MB</div>
          </b-media>
        </b-form>
      </validation-observer>
    </b-modal>

    <b-modal size="lg" v-model="viewRoomInfoModal" @hidden="resetModal" no-close-on-backdrop ok-only ok-title="Close">
      <template #modal-title>
          <h4>View Room Info</h4>
      </template>
      <table class="mb-1">
        <tr>
          <td>Room Number</td>
          <td width="10%" class="text-center">:</td>
          <td>{{ formData.room_number }}</td>
        </tr>
        <tr>
          <td>Received By</td>
          <td width="10%" class="text-center">:</td>
          <td>{{ formData.received_by }}</td>
        </tr>
        <tr>
          <td>Given By</td>
          <td width="10%" class="text-center">:</td>
          <td>{{ formData.given_by_name }}</td>
        </tr>
        <tr>
          <td>Received Date</td>
          <td width="10%" class="text-center">:</td>
          <td>{{ formatDateTime(formData.received_at) }}</td>
        </tr>
      </table>
      <p>Evidence</p>
      <img class="w-50" :src="formData.room_key_evidence" alt="Evidence">
    </b-modal>
  </div>
</template>

<script>
import {
  BAlert,
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
  BSpinner
} from 'bootstrap-vue'
import _ from 'lodash'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import Cleave from 'vue-cleave-component'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric } from '@validations'
import useDataList from './useDataList'
import { deleteData, postAction, exportData, getInvoicePDF } from '@/network/booking'
import { hasPermission } from '@/auth/utils'
import { avatarText, formatDate, formatDateTime } from '@core/utils/filter'

export default {
  components: {
    BAlert,
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
    BSpinner,

    vSelect,
    flatPickr,
    Cleave,
    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  setup() {

    const statusOptions = [
      { label: 'Pending', value: 'pending' },
      { label: 'Unpaid', value: 'unpaid' },
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
      avatarText,
      formatDate,
      formatDateTime,
      statusOptions
    }
  },
  data() {
    const umrohTripFilterOptions = []
    const packageOptions = []
    const roomTypeOptions = []
    const hotelOptions = []
    
    return {
      required, numeric,
      formData: {},
      updateInfoModal: false,
      setRoomModal: false,
      viewRoomInfoModal: false,
      isSubmitModal: false,
      isButtonLoading: false,
      umrohTripFilterOptions,
      packageOptions,
      roomTypeOptions,
      hotelOptions,
      optionClave: {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
      },
    }
  },
  methods : {
    clearDate() {
      this.checkinDateFilter = null
    },
    resetModal() {
      this.formData = {}
    },
    updateInfo(item) {
      this.updateInfoModal = true
      this.formData = item
    },
    setRoom(item) {
      this.setRoomModal = true
      this.formData = {
        id: item.id,
        room_number: item.room_number,
        received_by: item.received_by,
      }
    },
    viewRoomInfo(item) {
      this.viewRoomInfoModal = true
      this.formData = item
    },
    calculatePrice() {
      this.formData = {
        ...this.formData,
        total_price: parseInt(this.formData.total_pax) * parseInt(this.formData.price_per_pax),
        total_price_with_tax: parseInt(this.formData.tax_amount) + parseInt(this.formData.total_price),
      }
    },
    handleOkUpdateInfo(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.onSubmitUpdateInfo()
    },
    onSubmitUpdateInfo() {
      this.$refs.refObsForm.validate().then((success) => {
        if (!success) return;
        this.isSubmitModal = true
        
        this.formData['update_booking'] = true
        postAction(this.formData).then(response => {
          this.$bvToast.toast(`Booking has been updated successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.updateInfoModal = false
          this.isSubmitModal = false
          this.refetchData()
        })
        .catch(error => {
          if (error.response.data.errors) {
            this.$refs.refObsForm.setErrors(error.response.data.errors)
          } else {
            this.$refs.refObsForm.setErrors(error.response.data)
          }
          this.isSubmitModal = false
        })
      })
    },
    inputImageRenderer() {
      this.formData.file_evidence = this.$refs.refInputEl.files[0]
      const file = this.$refs.refInputEl.files[0]
      const reader = new FileReader()

      reader.addEventListener(
        'load',
        () => {
          this.formData = {
            ...this.formData,
            evidence: reader.result
          }
        },
        false,
      )

      if (file) {
        reader.readAsDataURL(file)
      }
    },
    handleOkSetRoom(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.onSubmitSetRoom()
    },
    onSubmitSetRoom() {
      this.$refs.refObsForm.validate().then((success) => {
        if (!success) return;
        this.isSubmitModal = true
        const vForm = new FormData()
        for (var key in this.formData) {
          if (key == 'evidence')
            continue
          if (this.formData[key] != null)
            vForm.append(key, this.formData[key])
        }
        vForm.append('set_room', true)
        postAction(vForm).then(response => {
          this.$bvToast.toast(`Booking has been updated successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.setRoomModal = false
          this.isSubmitModal = false
          this.refetchData()
        })
        .catch(error => {
          if (error.response.data.errors) {
            this.$refs.refObsForm.setErrors(error.response.data.errors)
          } else {
            this.$refs.refObsForm.setErrors(error.response.data)
          }
          this.isSubmitModal = false
        })
      })
    },
    downloadInvoice(item) {
      getInvoicePDF(item.id).then(response => {
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
        this.isSubmitModal = false
      }).catch(error => {
        this.$bvToast.toast(`Error: ${error}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        this.isSubmitModal = false
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
