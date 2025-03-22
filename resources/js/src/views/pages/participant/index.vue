<template>
  <div>
    <add-sidebar :is-add-sidebar-active.sync="isAddSidebarActive" :gender-options="genderOptions"
      :title-options="titleOptions" :married-options="marriedOptions" :education-options="educationOptions"
      :yes-no-options="yesNoOptions" :job-options="jobOptions" :blood-options="bloodOptions" :body-size-options="bodySizeOptions"
      :data-status-options="dataStatusOptions" @refetch-data="refetchData" v-if="hasPermission('participant-add-or-edit')" />

    <import-sidebar :umroh-trip-id="umrohTripId" :is-import-sidebar-active.sync="isImportSidebarActive"
      @refetch-data="refetchData" v-if="hasPermission('participant-add-or-edit')" />

    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">

        <!-- Table Top -->
        <b-row class="justify-content-end">
            <b-col cols="12" md="3" class="mb-1">
                <label>Gender</label>
                <v-select v-model="genderFilter" :options="genderOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
        </b-row>
        <b-row>
          <!-- Per Page -->
          <b-col cols="12" md="4" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
            <label>Show</label>
            <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
              class="per-page-selector d-inline-block mx-50" />
            <label>entries</label>
          </b-col>

          <!-- Search -->
          <b-col cols="12" md="8">
            <div class="d-lg-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1 mb-lg-0 mb-2" placeholder="Search..." />
              <b-overlay :show="isLoading" rounded opacity="0.6" spinner-small spinner-variant="primary" class="d-inline-block">
                <b-dropdown right class="mr-1" variant="gradient-primary" :disabled="isLoading" v-if="hasPermission('participant-add-or-edit')">
                  <template #button-content>
                      Action
                  </template>
                  <b-dropdown-item @click="isAddSidebarActive = true">
                    Add
                  </b-dropdown-item>
                  <b-dropdown-item @click="exportParticipant()">
                    Export
                  </b-dropdown-item>
                  <b-dropdown-item @click="isImportSidebarActive = true">
                    Import
                  </b-dropdown-item>
                </b-dropdown>
              </b-overlay>
              <b-button variant="primary" :to="{ name: 'participant-raw' }" class="mb-lg-0 mb-2" v-if="hasPermission('participant-add-or-edit')">
                <span class="text-nowrap">Raw Data</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchUsers" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

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
            <b-dropdown-item :to="{ name: 'participant-detail', params: { id: data.item.id, name: data.item.name } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Details</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="hasPermission('participant-add-or-edit')" @click="showAccessLogin(data.item)">
              <feather-icon icon="KeyIcon" />
              <span class="align-middle ml-50">Access Login</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="hasPermission('participant-add-or-edit')" @click="showNameInCerificate(data.item)">
              <feather-icon icon="UserCheckIcon" />
              <span class="align-middle ml-50">Name in Certificate</span>
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

    <b-modal v-model="accessLoginModal" ok-title="Update Access Login" @hidden="resetModal"
        @ok="handleSubmitAccessLogin" :busy="isSubmitModal" centered no-close-on-backdrop>
        <template #modal-title>
            <h3>Access Login Information</h3>
        </template>
        <validation-observer ref="refAccessLoginForm">
            <b-form class="p-2" @submit.prevent="onSubmitAccessLogin">
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
                        <validation-provider #default="{ errors }" name="Email" vid="email"
                            rules="required|email">
                            <b-form-group label="Email">
                                <b-form-input type="text" v-model="formAccessLogin.email"
                                    :state="errors.length > 0 ? false : null" trim />
                                <b-form-invalid-feedback>
                                    {{ errors[0] }}
                                </b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <b-col cols="12">
                        <validation-provider #default="{ errors }" name="Password" vid="password"
                            rules="required">
                            <b-form-group label="Password">
                                <b-form-input type="password" v-model="formAccessLogin.password"
                                    :state="errors.length > 0 ? false : null" trim autocomplete="new-password" />
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

    <b-modal v-model="nameInCerificateModal" ok-title="Name in Certificate" @hidden="resetModal"
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
                        <validation-provider #default="{ errors }" name="Name in Certificate" vid="name_in_certificate"
                            rules="required">
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
import { ref } from '@vue/composition-api'
import { avatarText, formatDateShort } from '@core/utils/filter'
import useDataList from './useDataList'
import { createAccessLogin, createNameInCertificate, deleteData, exportParticipant, getJobSearch } from '@/network/participant'
import addSidebar from './addSidebar.vue'
import importSidebar from './importSidebar.vue'
import { hasPermission } from '@/auth/utils'
import _ from 'lodash'
import { getTripSearch, getPackages } from '@/network/booking-order'
import { getBookingSearch } from '@/network/umroh-booking-seat'

export default {
  components: {
    addSidebar,
    importSidebar,
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
    ValidationProvider,
    ValidationObserver
  },
  setup() {
    const isAddSidebarActive = ref(false)
    const isImportSidebarActive = ref(false)

    const genderOptions = [{ label: 'Man', value: 1 }, { label: 'Woman', value: 2 }]
    const titleOptions = [{ label: 'Mr', value: 'Mr' }, { label: 'Ms', value: 'Ms' }, { label: 'Mrs', value: 'Mrs' }, { label: 'Mstr', value: 'Mstr' }, { label: 'Miss', value: 'Miss' }]
    const marriedOptions = [{ label: 'Menikah', value: 1 }, { label: 'Belum Menikah', value: 2 }, { label: 'Janda/Duda', value: 3 }]
    const educationOptions = [{ label: 'SD/MI', value: 'SD/MI' }, { label: 'SMP/MTS', value: 'SMP/MTS' }, { label: 'SMA/MA', value: 'SMA/MA' }, { label: 'D1', value: 'D1' }, { label: 'D2', value: 'D2' }, { label: 'D3', value: 'D3' }, { label: 'D4/S1', value: 'D4/S1' }, { label: 'S2', value: 'S2' }, { label: 'S3', value: 'S3' }, { label: 'BELUM SEKOLAH', value: 'BELUM SEKOLAH' }]
    const yesNoOptions = [{ label: 'Yes', value: 1 }, { label: 'No', value: 2 }]
    const bloodOptions = [{ label: 'A+', value: 'A+' }, { label: 'A-', value: 'A-' }, { label: 'B+', value: 'B+' }, { label: 'B-', value: 'B-' }, { label: 'AB+', value: 'AB+' }, { label: 'AB-', value: 'AB-' }, { label: 'O+', value: 'O+' }, { label: 'O-', value: 'O-' }, { label: 'Tidak Tahu', value: 'Tidak Tahu' }]
    const bodySizeOptions = [{ label: 'XS (Balita 0-5 tahun)', value: 'XS' }, { label: 'S (Anak 6-12 tahun)', value: 'S' }, { label: 'L (All Size)', value: 'L' }, { label: 'XL (Jumbo)', value: 'XL' }]
    const dataStatusOptions = [{ label: 'Lengkap', value: 1 }, { label: 'Belum Ada Passport', value: 2 }, { label: 'Passport Expired', value: 3 }, { label: 'Data Butuh Perbaikan / Penambahan Nama', value: 4 }, { label: 'Data Dikunci', value: 5 }]

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
      umrohTripId,

      // UI
      resolveGender,

      // Extra Filters
      genderFilter,
    } = useDataList()

    return {
      // Sidebar
      isAddSidebarActive,
      isImportSidebarActive,

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
      titleOptions,
      marriedOptions,
      educationOptions,
      yesNoOptions,
      bloodOptions,
      dataStatusOptions,
      umrohTripId,
      bodySizeOptions,

      // Filter
      avatarText,
      formatDateShort,

      // UI
      resolveGender,

      // Extra Filters
      genderFilter,
      hasPermission
    }
  },
  created() {

  },
  data() {
    const jobOptions = []

    getJobSearch().then(response => {
        this.jobOptions = response.data
    }).catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    return {
      jobOptions,
      filter: {},
      isLoading: false,
      isSubmitModal: false,
      accessLoginModal: false,
      nameInCerificateModal: false,
      selectedParticipant: {},
      formAccessLogin: {email: '', password: '' },
      formNameInCerificate: {front_title: '', name_in_certificate: '', back_title: '' }
    }
  },
  methods : {
    showAccessLogin(item) {
      this.accessLoginModal = true
      this.selectedParticipant = item
      this.formAccessLogin.email = item.email
    },
    handleSubmitAccessLogin(bvModalEvent) {
        bvModalEvent.preventDefault()
        this.onSubmitAccessLogin()
    },
    onSubmitAccessLogin() {
        this.$refs.refAccessLoginForm.validate().then(success => {
            if (!success) return
            this.isSubmitModal = true
            this.formAccessLogin.id = this.selectedParticipant.id
            createAccessLogin(this.formAccessLogin).then(response => {
                this.accessLoginModal = false
                this.$swal({ icon: 'success', title: 'Success', text: `Access login has been saved successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                this.refetchData()
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                this.isSubmitModal = false
            })
        })
    },
    showNameInCerificate(item) {
      this.nameInCerificateModal = true
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
                this.nameInCerificateModal = false
                this.$swal({ icon: 'success', title: 'Success', text: `Name in Certificate has been saved successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                this.refetchData()
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                this.isSubmitModal = false
            })
        })
    },
    resetModal() {
        this.formAccessLogin = {email: '', password: '' }
        this.formNameInCerificate = {front_title: '', name_in_certificate: '', back_title: '' }
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
      if(!this.umrohTripId) {
        this.$swal({ icon: 'error', title: 'Error', text: 'Harap Pilih Umroh Trip', customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        return
      }
      this.isLoading = true
      exportParticipant({ umrohTripId: this.umrohTripId }).then(response => {
          this.isLoading = false
          window.location = response.data.downloadLink
      }).catch(error => {
          this.isLoading = false
          this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
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
</style>
