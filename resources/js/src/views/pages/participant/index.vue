<template>
  <div>

    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">

        <!-- Table Top -->
        <b-row class="mb-1">
          <b-col cols="12" md="3" class="mb-1">
            <h5>Persentase By Gender</h5>
            <apexchart type="pie" height="200" :options="chartGenderOptions" :series="chartGenderSeries"/>
          </b-col>
          <b-col cols="12" md="3" class="mb-1">
            <h5>Persentase By Polo Size</h5>
            <apexchart type="pie" height="200" :options="chartPoloSizeOptions" :series="chartPoloSizeSeries"/>
          </b-col>
        </b-row>
        <b-row class="justify-content-end">
            <b-col cols="12" md="3" class="mb-1">
                <label>Filter by Gender</label>
                <v-select v-model="genderFilter" :options="genderOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
            <b-col cols="12" md="3" class="mb-1">
                <label>Filter by Polo Size</label>
                <v-select v-model="poloSizeFilter" :options="poloSizeOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
            <b-col cols="12" md="3" class="mb-1">
              <label>Filter by Date</label>
              <div class="input-group">
                <flat-pickr v-model="dateFilter" :config="{ mode: 'range' }" class="form-control" name="date"/>
                <div class="input-group-append">
                  <button class="btn btn-danger btn-sm" type="button" @click="clearDate">
                    <feather-icon icon="XIcon" />
                  </button>
                </div>
              </div>
            </b-col>
        </b-row>
        <b-row>
          <!-- Per Page -->
          <b-col cols="12" md="6" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
            <label>Show</label>
            <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
              class="per-page-selector d-inline-block mx-50" />
            <label>entries</label>
          </b-col>

          <!-- Search -->
          <b-col cols="12" md="6">
            <div class="d-lg-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1 mb-lg-0 mb-2" placeholder="Search..." />
              <b-button variant="primary" @click="exportParticipant()" class="mb-lg-0 mb-2" v-if="hasPermission('participant-add-or-edit')" :disabled="isLoading">
                <b-spinner small v-show="isLoading" /> <span class="text-nowrap">Export Data</span>
              </b-button>
            </div>
          </b-col>
        </b-row>
      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchUsers" responsive hover
        :fields="tableColumns" primary-key="id" :tbody-tr-class="rowClass" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- Column: Booking -->
        <template #cell(booking)="data">
          <span>{{ data.item.booking_no }}</span><br>
          <span>{{ data.item.booking_account_name }}</span>
        </template>

        <!-- Column: Room Info -->
        <template #cell(room_info)="data">
          <div v-if="data.item.room_number != null">
            <p class="m-0">{{ data.item.room_number }}</p>
            <b-button variant="primary" size="sm" @click="viewRoomInfo(data.item)">View Detail</b-button>
          </div>
          <b-button variant="primary" size="sm" v-if="data.item.room_number == null" @click="setRoom(data.item)">Set Room Number</b-button>
        </template>

        <!-- Column: Request -->
        <template #cell(request)="data">
          <span>{{ (data.item.request) ? data.item.request : ' - ' }}</span><br>
        </template>

        <!-- Column: Name -->
        <template #cell(name)="data">
          <b-media vertical-align="center">
            <template #aside>
              <b-avatar size="40" :src="data.item.profile_thumbnail" :text="avatarText(data.item.name)"
                :variant="`light-primary`"
                :to="{ name: 'participant-detail', params: { id: data.item.id} }" />
            </template>
            <b-link :to="{ name: 'participant-detail', params: { id: data.item.id } }"
              class="font-weight-bold d-block text-nowrap">
              {{ data.item.name.toUpperCase() }}
            </b-link>
            <small class="text-nowrap">Gender: {{ resolveGender(data.item.gender) }}</small>
          </b-media>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item v-if="hasPermission('participant-add-or-edit')" @click="updateInfo(data.item)">
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Update Data</span>
            </b-dropdown-item>
            <b-dropdown-item @click="viewNotes(data.item)">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">View Notes</span>
            </b-dropdown-item>
            <b-dropdown-item @click="setRoom(data.item)" v-if="data.item.room_number == null && hasPermission('booking-add-or-edit')">
              Set Room Number
            </b-dropdown-item>
            <b-dropdown-item v-if="hasPermission('participant-add-or-edit')">
              <feather-icon icon="SendIcon" />
              <span class="align-middle ml-50">Kirim QR Code</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteParticipant(data.item)">
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

            <b-pagination v-model="currentPage" :total-rows="totalUsers" :per-page="perPage" first-number last-number
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

    <b-modal v-model="setNameInCerificateModal" ok-title="Name in Certificate" @hidden="resetModal"
      @ok="handleSubmitNameInCerificate" :busy="isSubmitModal" centered no-close-on-backdrop>
      <template #modal-title>
        <h3>Name In Certificate</h3>
      </template>
      <validation-observer ref="refNameInCerificateForm">
        <b-form class="p-2" @submit.prevent="onSubmitNameInCerificate">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <b-row>
            <b-col cols="12">
              <label for="">Participant Name</label>
              <p class="font-weight-bold">{{ selectedParticipant.name }}</p>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Name in Certificate" vid="name_in_certificate" rules="required">
                <b-form-group label="Name in Certificate">
                  <b-form-input type="text" v-model="formNameInCerificate.name_in_certificate" placeholder="Please Set Name in Certificate"
                    :state="errors.length > 0 ? false : null" trim />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
          </b-row>
        </b-form>
      </validation-observer>
    </b-modal>

    <b-modal v-model="updateInfoModal" ok-title="Save Changes" @hidden="resetModal" @ok="handleSubmitUpdateInfo" :busy="isSubmitModal" centered no-close-on-backdrop>
      <template #modal-title>
        <h3>Update Participant</h3>
      </template>
      <validation-observer ref="refObsForm">
        <b-form class="p-2" @submit.prevent="onSubmitUpdateInfo">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <b-row>
            <b-col sm="6">
              <label for="">Booking Order</label>
              <p class="font-weight-bold">{{ formData.booking_no }}</p>
            </b-col>
            <b-col sm="6">
              <label for="">Account Name</label>
              <p class="font-weight-bold">{{ formData.booking_account_name }}</p>
            </b-col>
            <b-col sm="6">
              <label for="">Hospital</label>
              <p class="font-weight-bold">{{ formData.booking_account_hospital }}</p>
            </b-col>
            <b-col sm="6">
              <label for="">Booking Status</label>
              <div>
                <b-badge variant="warning" v-if="formData.booking_order_status == 'unpaid'">Unpaid</b-badge>
                <b-badge variant="success" v-if="formData.booking_order_status == 'paid'">Paid</b-badge>
                <b-badge variant="danger" v-if="formData.booking_order_status == 'cancel'">Cancel</b-badge>
              </div>
            </b-col>
          </b-row>

          <validation-provider #default="{ errors }" name="Full Name" vid="name" rules="required">
            <b-form-group label="Full Name">
              <b-form-input type="text" v-model="formData.name" placeholder="Full Name"
                :state="errors.length > 0 ? false : null" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Email" vid="email" rules="required|email">
            <b-form-group label="Email">
              <b-form-input type="text" v-model="formData.email" placeholder="Email"
                :state="errors.length > 0 ? false : null" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Whatsapp" vid="whatsapp" rules="required|numeric">
            <b-form-group label="Whatsapp">
              <b-form-input type="text" v-model="formData.whatsapp" placeholder="Whatsapp"
                :state="errors.length > 0 ? false : null" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider #default="{ errors }" name="Jumlah Pembayaran" vid="booking_total_price_with_tax" rules="required|numeric">
            <b-form-group label="Jumlah Pembayaran">
              <cleave v-model="formData.booking_total_price_with_tax" class="form-control" :options="optionClave" :state="errors.length > 0 ? false : null" v-on:input="calculatePrice" />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <b-row>
            <b-col sm="6">
              <validation-provider #default="{ errors }" name="Apakah Menginap?" vid="is_stay_in" rules="required">
                <b-form-group label="Apakah Menginap">
                  <v-select id="type" v-model="formData.is_stay_in" :options="stayOptions" :clearable="true" :reduce="(label) => label.value" />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col sm="6">
              <validation-provider #default="{ errors }" name="Ukuran Kaos Polo" vid="polo_size" rules="required">
                <b-form-group label="Ukuran Kaos Polo">
                  <v-select id="type" v-model="formData.polo_size" :options="poloSizeOptions" :clearable="true" :reduce="(label) => label.value" />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
          </b-row>

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

    <b-modal v-model="viewNotesModal" ok-only @hidden="resetModal" centered no-close-on-backdrop>
      <template #modal-title>
        <h3>Catatan Teman Sekamar</h3>
      </template>
      <b-row>
        <b-col sm="6">
          <label for="">Booking Order</label>
          <p class="font-weight-bold">{{ formData.booking_no }}</p>
        </b-col>
        <b-col sm="6">
          <label for="">Participant Name</label>
          <p class="font-weight-bold">{{ formData.name }}</p>
        </b-col>
        <b-col sm="6">
          <label for="">Hospital</label>
          <p class="font-weight-bold">{{ formData.booking_account_hospital }}</p>
        </b-col>
        <b-col sm="6">
          <label for="">Whatsapp</label>
          <p class="font-weight-bold">{{ formData.whatsapp }}</p>
        </b-col>
        <b-col sm="12">
          <label for="">Catatan Teman Sekamar</label>
          <p class="font-weight-bold">{{ (formData.request) ? formData.request : '-' }}</p>
        </b-col>
      </b-row>
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
  </div>
</template>

<script>
import {
  BOverlay,
  BCard,
  BRow,
  BCol,
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
  BForm,
  BFormGroup,
  BFormInvalidFeedback,
  BSpinner
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { avatarText, formatDateShort, formatDate, formatDateTime } from '@core/utils/filter'
import useDataList from './useDataList'
import { createNameInCertificate, postUpdateData, deleteData, exportParticipant, getChartGender, getChartPoloSize, postAction } from '@/network/participant'
import { hasPermission } from '@/auth/utils'
import _ from 'lodash'
import { required, numeric, email } from '@validations'
import Cleave from 'vue-cleave-component'

export default {
  components: {
    BOverlay,
    BCard,
    BRow,
    BCol,
    BForm,
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
    BFormGroup,
    BFormInvalidFeedback,
    BSpinner,

    vSelect,
    flatPickr,
    Cleave,
    ValidationProvider,
    ValidationObserver
  },
  setup() {
    const stayOptions = [{ label: 'Ya', value: 1 }, { label: 'Tidak', value: 2 }]
    const genderOptions = [{ label: 'Laki-laki', value: 1 }, { label: 'Perempuan', value: 2 }]
    const poloSizeOptions = [{ label: 'S', value: 'S' }, { label: 'M', value: 'M' }, { label: 'L', value: 'L' }, { label: 'XL', value: 'XL' }, { label: 'XXL', value: 'XXL' }, { label: 'XXXL', value: 'XXXL' }]

    const {
      fetchUsers,
      tableColumns,
      perPage,
      currentPage,
      totalUsers,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,

      // UI
      resolveGender,

      // Extra Filters
      genderFilter,
      poloSizeFilter,
      dateFilter
    } = useDataList()

    return {
      fetchUsers,
      tableColumns,
      perPage,
      currentPage,
      totalUsers,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,
      genderOptions,
      stayOptions,
      poloSizeOptions,

      // Filter
      avatarText,
      formatDateShort,

      // UI
      resolveGender,

      // Extra Filters
      genderFilter,
      poloSizeFilter,
      dateFilter,
      hasPermission,
      formatDateTime,
      formatDate
    }
  },
  watch: {
    genderFilter: function (value) {
      this.getChartData()
    },
    poloSizeFilter: function (value) {
      this.getChartData()
    },
    dateFilter: function (value) {
      this.getChartData()
    },
  },
  data() {
    const chartGenderOptions = {}
    const chartGenderSeries = []
    const chartPoloSizeOptions = {}
    const chartPoloSizeSeries = []

    this.getChartData()

    return {
      required, numeric, email,
      filter: {},
      isLoading: false,
      isSubmitModal: false,
      setNameInCerificateModal: false,
      updateInfoModal: false,
      viewNotesModal: false,
      selectedParticipant: {},
      formData: {},
      formNameInCerificate: {front_title: '', name_in_certificate: '', back_title: '' },
      chartGenderOptions,
      chartGenderSeries,
      chartPoloSizeOptions,
      chartPoloSizeSeries,
      setRoomModal: false,
      viewRoomInfoModal: false,
      optionClave: {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
      },
    }
  },
  methods : {
    clearDate() {
      this.dateFilter = null
    },
    getChartData() {
      getChartGender({params: {date: this.dateFilter, gender: this.genderFilter, poloSize: this.poloSizeFilter}}).then(response => {
        this.chartGenderOptions = {
          chart: {
            id: 'chart-gender',
            toolbar: {
              show: true
            }
          },
          labels: response.data.categories,
        }
        this.chartGenderSeries = response.data.data
      }).catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
      })

      getChartPoloSize({params: {date: this.dateFilter, gender: this.genderFilter, poloSize: this.poloSizeFilter}}).then(response => {
        this.chartPoloSizeOptions = {
          chart: {
            id: 'chart-gender',
            toolbar: {
              show: true
            }
          },
          labels: response.data.categories,
        }
        this.chartPoloSizeSeries = response.data.data
      }).catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
      })
    },
    setNameInCerificate(item) {
      this.setNameInCerificateModal = true
      this.selectedParticipant = item
      this.formNameInCerificate.front_title = item.front_title
      this.formNameInCerificate.name_in_certificate = (item.name_in_certificate) ? item.name_in_certificate : (item.name_in_passport) ? item.name_in_passport : ""
      this.formNameInCerificate.back_title = item.back_title
    },
    handleSubmitNameInCerificate(bvModalEvent) {
        bvModalEvent.preventDefault()
        this.onSubmitNameInCerificate()
    },
    onSubmitNameInCerificate() {
        this.$refs.refNameInCerificateForm.validate().then(success => {
            if (!success) return
            this.isSubmitModal = true
            this.formNameInCerificate.id = this.selectedParticipant.id
            createNameInCertificate(this.formNameInCerificate).then(response => {
                this.setNameInCerificateModal = false
                this.$swal({ icon: 'success', title: 'Success', text: `Name in Certificate has been saved successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                this.refetchData()
            }).catch(error => {
                this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                this.isSubmitModal = false
            })
        })
    },
    handleSubmitUpdateInfo(bvModalEvent) {
        bvModalEvent.preventDefault()
        this.onSubmitUpdateInfo()
    },
    onSubmitUpdateInfo() {
        this.$refs.refObsForm.validate().then(success => {
            if (!success) return
            this.isSubmitModal = true
            this.formData.id = this.formData.id
            postUpdateData(this.formData).then(response => {
                this.$swal({ icon: 'success', title: 'Success', text: `Name in Certificate has been saved successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                this.updateInfoModal = false
                this.isSubmitModal = false
                this.refetchData()
            }).catch(error => {
                this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                this.isSubmitModal = false
            })
        })
    },
    updateInfo(item) {
      this.updateInfoModal = true
      this.formData = item
    },
    viewNotes(item) {
      this.viewNotesModal = true
      this.formData = item
    },
    viewRoomInfo(item) {
      this.viewRoomInfoModal = true
      this.formData = item
    },
    resetModal() {
        this.formAccessLogin = {email: '', password: '' }
        this.formNameInCerificate = {front_title: '', name_in_certificate: '', back_title: '' }
        this.formData = {}
    },
    setRoom(item) {
      this.setRoomModal = true
      this.formData = {
        id: item.id,
        room_number: item.room_number,
        received_by: item.received_by,
      }
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
    deleteParticipant(item){
        this.$swal({
        title: `Delete Participant ${item.name}?`,
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
            }
      })
    },
    exportParticipant() {
      this.isLoading = true
      exportParticipant({}).then(response => {
          this.isLoading = false
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
      }).catch(error => {
          this.isLoading = false
          this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
      })
    },
    rowClass(item) {
        if(item){
            if(item.request != '' && item.request != '-' && item.request != null && item.request != 'null'){
                return 'table-request';
            }
        }
    },
  },
}
</script>

<style lang="scss" scoped>
::v-deep .table-request > tr, ::v-deep .table-request > td {
    background-color: #fdcb6e !important;
}
.per-page-selector {
  width: 90px;
}
</style>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
