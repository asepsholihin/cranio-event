<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <template>
        <div class="m-2">
          <b-row>
            <b-col cols="12" md="6" class="mb-md-1 mb-2">
              <label>Trip</label>
              <v-select v-model="umrohTripId" placeholder="Choose Trip" :options="umrohTripFilterOptions"
                :filterable="false" @search="onSearch" :reduce="name => name.latest_trip_name" label="latest_trip_name">
              </v-select>
            </b-col>
            <b-col cols="12" md="6" class="mb-md-1 mb-2">
              <label>Gender</label>
              <v-select v-model="genderFilter" :options="genderOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="12" md="6" class="mb-md-1 mb-2">
              <label>Start Date</label>
              <flat-pickr v-model="startDateFilter" :config="startDateConfig" class="form-control" />
            </b-col>
            <b-col cols="12" md="6" class="mb-md-1 mb-2">
              <label>End Date</label>
              <flat-pickr v-model="endDateFilter" :config="endDateConfig" class="form-control" />
            </b-col>

          </b-row>
        </div>
      </template>

      <div class="m-2">

        <!-- Table Top -->
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
            <div class="d-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1"
                placeholder="Search..." />
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchUsers" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc" :tbody-tr-class="rowClass">

        <!-- Column: Name -->
        <template #cell(name)="data">
          <b-media vertical-align="center">
            <template #aside>
              <b-avatar size="40" :src="data.item.profile_thumbnail" :text="avatarText(data.item.name)"
                :variant="`light-primary`" :to="{ name: 'participant-crm-detail', params: { id: data.item.participant_id } }" />
            </template>
            <b-link :to="{ name: 'participant-crm-detail', params: { id: data.item.participant_id } }"
              class="font-weight-bold d-block text-nowrap">
              {{ (data.item.front_title != null && data.item.front_title != "" && data.item.front_title != "-") ? data.item.front_title : "" }} {{ data.item.name.toUpperCase() }} {{ (data.item.back_title != null && data.item.back_title != "" && data.item.back_title != "-") ? data.item.back_title : "" }}
            </b-link>
            <small>Gender: {{ resolveGender(data.item.gender) }}</small>
          </b-media>
        </template>

        <template #cell(birth_date)="data">
          <b-media vertical-align="center">
            {{ data.item.birth_date ? formatDate(data.item.birth_date) : '--' }}
          </b-media>
        </template>


        <!-- Column: Status -->
        <template #cell(married_status)="data">
          {{ resolveMarriedStatus(data.item.married_status) }}
        </template>

        <!-- Column: Status -->
        <template #cell(status)="data">
          <b-badge pill :variant="`light-${resolveUserStatusVariant(data.item.status)}`" class="text-capitalize">
            {{ data.item.status }}
          </b-badge>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item @click="sendMiladCard(data.item)">
              <feather-icon icon="SendIcon" />
              <span class="align-middle ml-50">Kirim Ucapan Whatsapp</span>
            </b-dropdown-item>
            <b-dropdown-item @click="downloadMiladCard(data.item)">
              <feather-icon icon="DownloadIcon" />
              <span class="align-middle ml-50">Download Ucapan</span>
            </b-dropdown-item>
            <b-dropdown-item @click="showPreviewMilad(data.item)">
              <feather-icon icon="EyeIcon" />
              <span class="align-middle ml-50">Preview Ucapan Milad</span>
            </b-dropdown-item>
            <b-dropdown-item @click="reminderMilad(data.item)">
              <feather-icon icon="AlertCircleIcon" />
              <span class="align-middle ml-50" v-if="data.item.remind_milad === 1">Nonaktifkan Reminder Milad</span>
              <span class="align-middle ml-50" v-if="data.item.remind_milad === 2">Aktifkan Reminder Milad</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="hasPermission('participant-crm-edit')" @click="showUpdateDataCRM(data.item)">
              <feather-icon icon="KeyIcon" />
              <span class="align-middle ml-50">Update Data</span>
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

    <b-modal id="modal-lg" size="lg" v-model="accessPreviewMilad" ok-title="Download" :busy="isSubmitModal" centered
      no-close-on-backdrop>
      <template #modal-title>
        <h3>Preview Ucapan Milad</h3>
      </template>
      <b-row>
        <b-col cols="6" cols-md="6">
          <div class="pages" v-if="summon && !loadingImage">
            <b-button variant="primary" type="button" size="sm" class="bn-save-changes" :disabled="savingImgeChanges" v-if="isEdit" @click="saveChangesNew">
              <b-spinner v-show="savingImgeChanges"></b-spinner> Save Changes
            </b-button>
            <b-img v-if="!loadingImage" class="background-milad-card" fluid :src="backgroundMiladCard.background" alt="" />
            <div class="left-image" v-if="!loadingImage">
                <b-img v-if="!loadingImage" class="partOfLeft" fluid :src="backgroundMiladCard.partOfLeft" alt="" />
            </div>
            <div class="right-image" v-if="!loadingImage">
                <b-img v-if="!loadingImage" class="partOfLeft" fluid :src="backgroundMiladCard.partOfRight" alt="" />
            </div>
            <div class="bottom-image" v-if="!loadingImage">
                <b-img v-if="!loadingImage" class="partOfLeft" fluid :src="backgroundMiladCard.partOfBottom" alt="" />
            </div>
            <div class="rules-trigger" :style="{ overflow: overflowImage, zIndex: zIndexFrame}" v-if="!loadingImage" ref="captureDiv">
                <vue-drag-resize :w="242" :h="242" :x="6" :y="20" id="images-edit">
                    <b-img v-if="!loadingImage" class="milad-image" fluid :src="miladCard" alt="" @mousedown="opendend"/>
                </vue-drag-resize>
            </div>
          </div>
          <div class="pages" v-else-if="!summon && !loadingImage">
            <b-img v-if="!loadingImage" fluid :src="miladCard" alt="" />
          </div>
          <div v-if="loadingImage" class="pages" style="z-index:99">
            <div class="h-100 w-100 d-flex justify-content-center align-items-center">
              <b-spinner v-if="loadingImage" @hidden="resetModal" class="" variant="primary" key="primary"></b-spinner>
            </div>
          </div>
        </b-col>
        <b-col cols="6" cols-md="6">
          <b-form-group label="Redaksi Pesan di Whatsapp (view only)">
            <b-form-textarea id="" :value="miladMessage" trim rows="15" readonly />
          </b-form-group>
          <b-form-checkbox id="checkbox-1" v-model="previewMode" value="without_photo" unchecked-value="with_photo"
            @change="changePreviewMode()">
            Hilangkan foto participant pada ucapan milad
          </b-form-checkbox>
        </b-col>
      </b-row>
      <template #modal-footer>
        <b-button variant="success" size="md" class="mt-25" @click="sendMiladCard(selectedParticipant)"
          :disabled="isSubmitModal">
          <b-spinner v-show="isSubmitModal"></b-spinner> Kirim via Whatsapp
        </b-button>
        <b-button variant="primary" size="md" class="mt-25" @click="downloadMiladCard(selectedParticipant, true)"
          :disabled="isSubmitModal">
          <b-spinner v-show="isSubmitModal"></b-spinner> Download
        </b-button>
      </template>
    </b-modal>

    <b-modal v-model="accessUpdateParticipantCrm" :busy="isSubmitModal" centered
      no-close-on-backdrop @hidden="resetModal">
      <template #modal-title>
        <h3>Participant CRM Information</h3>
      </template>
      <validation-observer ref="refUpdateCrmForm">
        <b-form class="p-2" @submit.prevent="onSubmitAccessLogin">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert v-if="errors[0]" variant="danger" show>
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <b-row>
            <b-col cols="12">
              <label for="">JI Code</label>
              <p class="font-weight-bold">
                {{ selectedParticipant.ji_code }}
              </p>
            </b-col>
            <b-col cols="12">
              <label for="">Participant Name</label>
              <p class="font-weight-bold">
                {{ selectedParticipant.name }}
              </p>
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="12" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
              <h5>Milad Photo</h5>
            </b-col>
          </b-row>
          <template v-if="loadingImage">
            <div class="h-100 d-flex justify-content-center align-items-center">
              <b-spinner v-if="loadingImage" @hidden="resetModal" class="" variant="primary" key="primary"></b-spinner>
            </div>
          </template>
          <template v-else>
            <b-img v-if="fileMiladPhoto != null && !loadingImage" center :src="fileMiladPhoto" thumbnail fluid />
            <h5 v-else class="text-center">
              No Available
            </h5>
            <center>
              <b-button v-if="hasPermission('participant-crm-edit')" variant="primary" size="sm" class="mt-25"
                :disabled="isSubmitModal" @click="$refs.refInputEl0.click()">
                <input ref="refInputEl0" accept="image/jpeg, image/png, image/webp" type="file" class="d-none"
                  @input="inputImageRenderer(0)">
                <feather-icon icon="ImageIcon" />
                <span class="d-none d-sm-inline">Change</span>
              </b-button>

              <b-button v-if="hasPermission('participant-crm-edit') && fileMiladPhoto != null" variant="danger"
                size="sm" @click="deleteFile('Foto Milad')" class="mt-25 ml-50" :disabled="isSubmitModal">
                <b-spinner small v-show="isSubmitModal" />
                <feather-icon icon="Trash2Icon" size="16" />
                <span class="d-none d-sm-inline">Delete</span>
              </b-button>

              <b-button v-if="hasPermission('participant-crm-edit') && FormData.photo[0]" variant="primary" size="sm"
                class="mt-25 ml-50" :disabled="isSubmitModal" @click="uploadMiladPhoto">
                <b-spinner v-show="isSubmitModal" small />
                <feather-icon icon="UploadCloudIcon" size="16" />
                <span class="d-none d-sm-inline">Upload</span>
              </b-button>
            </center>
          </template>
        </b-form>
      </validation-observer>
    </b-modal>
  </div>
</template>

<script>
import {
  BCard,
  BRow,
  BSpinner,
  BCol,
  BFormInput,
  BButton,
  BTable,
  BImg,
  BMedia,
  BAvatar,
  BLink,
  BBadge,
  BDropdown,
  BDropdownItem,
  BPagination,
  BFormCheckbox,
  BFormTextarea,
  BForm,
  BFormGroup,
  BFormInvalidFeedback,
} from 'bootstrap-vue'
import flatPickr from 'vue-flatpickr-component'
import { required, numeric, email } from '@validations'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import vSelect from 'vue-select'
import { ref } from '@vue/composition-api'
import { formatDate, avatarText, capitalize, calculateAge } from '@core/utils/filter'
import useDataList from './useDataList'
import { downloadMiladCard, sendMiladCard, getMiladCardWithPhoto, getCertificateImage, getMiladCardWithoutPhoto, activationReminderMilad,
    getMiladCardWithPhotoUrl, getMiladCardWithoutPhotoUrl, changeOurPhoto, sendPostMiladCard, getImages,
    downloadWhenPost} from '@/network/crm-milad'
import { postData, uploadMiladPhotos, deleteFile, getFiles, getTripSearch } from '@/network/crm-participant'
import { getProvinces, getCities, getDistricts, getSubdistricts, getPostalcodes } from '@/network/address'
import { hasPermission } from '@/auth/utils'
import { convertToBlob} from '@/network/participant'
import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';
import _ from 'lodash'
import VueDragResize from 'vue-drag-resize';
import html2canvas from "html2canvas";
// import 'vue-drag-resize/dist/vue-drag-resize.css';

export default {
  components: {
    BCard,
    BRow,
    BSpinner,
    BCol,
    BForm,
    BFormCheckbox,
    BFormInput,
    BButton,
    flatPickr,
    BImg,
    BTable,
    BMedia,
    BAvatar,
    BLink,
    BBadge,
    BDropdown,
    BDropdownItem,
    BFormTextarea,
    BPagination,
    BFormGroup,
    BFormInvalidFeedback,

    vSelect,
    ValidationProvider,
    ValidationObserver,
    VueDragResize,

    VueCropper
  },
  setup() {
    const genderOptions = [{ label: 'Man', value: 1 }, { label: 'Woman', value: 2 }]
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
      resolveMarriedStatus,
      resolveUserRoleIcon,
      resolveUserStatusVariant,

      // Extra Filters
      statusFilter,
      genderFilter,
      startDateFilter,
      endDateFilter,
      departureDateFilter,
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
      formatDate,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,
      genderOptions,
      umrohTripId,

      // Filter
      avatarText,
      capitalize,
      calculateAge,

      // UI
      resolveGender,
      resolveMarriedStatus,
      resolveUserRoleIcon,
      resolveUserStatusVariant,

      // Extra Filters
      statusFilter,
      genderFilter,
      startDateFilter,
      endDateFilter,
      departureDateFilter,
      hasPermission,
    }
  },
  created() {

  },
  data() {
    const domisili_cities = []
    const domisili_districts = []
    const domisili_subdistricts = []
    const domisili_postalcodes = []
    const umrohTripFilterOptions = []

    getTripSearch({ q: '' }).then(res => {
      this.umrohTripFilterOptions = res.data
    }).catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    getProvinces().then(response => {
      this.provinces = response.data
    }).catch(error => {
      if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
      } else {
        this.$refs.refObsForm.setErrors(error.response.data)
      }
    })
    return {
      loadingImage: false,
      miladMessage: null,
      templateMiladMessage: `Assalamu ‘alaikum Warahmatullahi Wabarakatuh

Barakallahu fii umrik {{gender}} {{1}} 🙏🏻

Semoga seiring bertambahnya usia menjadi pribadi yang lebih baik, bertambah kebaikan dan ketaatan kepada Allah Ta’ala, serta selalu dalam keadaan berlimpah keberkahan. Aamiin Ya Rabbal 'alamin

Salam Hangat,
CRM Jejak Imani 😊🙏🏻`,
      previewMode: 'with_photo',
      accessUpdateParticipantCrm: false,
      filter: {},
      milad: {
        front_title: '', name: '', back_title: '',
      },
      FormData: {
        front_title: '', back_title: '', linkedin_url: '', article_url: '', photo: [],
      },
      fileMiladPhoto: null,
      miladCard: null,
      startDateConfig: {
        wrap: true, // set wrap to true only when using 'input-group'
        altFormat: 'M j, Y',
        altInput: true,
        dateFormat: 'Y-m-d',
        onChange(selectedDates, dateStr, instance) {
          this.startDateFilter = dateStr
        },
      },
      endDateConfig: {
        wrap: true, // set wrap to true only when using 'input-group'
        altFormat: 'M j, Y',
        altInput: true,
        dateFormat: 'Y-m-d',
        onChange(selectedDates, dateStr, instance) {
          this.endDateFilter = dateStr
        },
      },
      accessPreviewMilad: false,
      timestamp: '',
      isSubmitModal: false,
      selectedParticipant: {},
      domisili_cities,
      domisili_districts,
      domisili_subdistricts,
      domisili_postalcodes,
      umrohTripFilterOptions,
      required,
      numeric,
      email,
      imagePath:{},
      editImage:false,
      saveChanges:false,
      isCropped:false,
      imageCropped:"",
      backgroundMiladCard:[],
      overflowImage: 'hidden',
      zIndexFrame: 2,
      isEdit:false,
      savingImgeChanges:false,
      withImageChange:'',
      summon:true
    }
  },
  methods: {
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (item.remind_milad == 2) return 'table-dark'
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
    showUpdateDataCRM(item) {
      this.accessUpdateParticipantCrm = true
      this.loadingImage = true
      this.selectedParticipant = item
      this.FormData.id = item.id
      this.FormData.participant_id = item.participant_id
      this.FormData.photo = []

      getFiles(item.participant_id).then(response => {
        this.isSubmitModal = false
        this.fileMiladPhoto = this.getPhotoUrlByTitle(response.data.files, 'Foto Milad')
        this.loadingImage = false
      }).catch(error => {
        this.verificationModal = false
        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    },
    inputImageRenderer(id) {
      this.FormData.photo[id] = this.$refs.refInputEl0.files[0]
      const file = this.$refs.refInputEl0.files[0]
      const reader = new FileReader()
      reader.addEventListener(
        'load',
        () => {
          switch (id) {
            case 0:
              this.fileMiladPhoto = reader.result
              break;
          }
        },
        false,
      )

      if (file) {
        reader.readAsDataURL(file)
      }
    },
    handelUpdateParticipantCrm(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.onSubmitUpdateCrm()
    },
    onSubmitUpdateCrm() {
      this.$refs.refUpdateCrmForm.validate().then(success => {
        if (!success) return
        this.isSubmitModal = true
        this.FormData.id = this.selectedParticipant.id
        postData(this.FormData).then(response => {
          this.accessUpdateParticipantCrm = false
          this.$swal({
            icon: 'success', title: 'Success', text: 'Data Participant has been updated successfully', timer: 2500, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false,
          })
          this.refetchData()
          this.isSubmitModal = false
        }).catch(error => {
          this.$swal({
            icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning' }, buttonsStyling: false,
          })
          this.isSubmitModal = false
        })
      })
    },
    resetModal() {
      this.previewMode = 'with_photo'
      this.loadingImage = false
      this.accessPreviewCertificate = false
      this.selectedParticipant = []
      this.fileMiladPhoto = null
      this.miladCard = null
      this.certificateImage = []
      this.isCropped = false
      this.imagePath = {}
      this.saveChanges = false
      this.editImage = false
      this.withImageChange = ''
      this.backgroundMiladCard = []
      this.overflowImage = 'hidden'
      this.zIndexFrame = 2
      this.isEdit = false,
      this.summon = true
    },
    uploadMiladPhoto() {
      this.isSubmitModal = true
      const vForm = new FormData()
      vForm.append('participant_id', this.FormData.participant_id)
      vForm.append('title', 'Foto Milad')
      vForm.append('file_upload', this.FormData.photo[0])
      uploadMiladPhotos(vForm).then(response => {
        this.isSubmitModal = false
        this.FormData.photo[0] = null
        this.$swal({
          icon: 'success', title: 'Success', text: `Photo has been uploaded successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false
        })
      }).catch(error => {
        this.isSubmitModal = false
        this.$swal({
          icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false
        })
      })
    },
    getPhotoUrlByTitle(files, key) {
      let filePathUrl = null
      files.forEach(function (item) {
        if (item.title == key) {
          filePathUrl = item.file_path_url
          return
        }
      })
      return filePathUrl
    },
    deleteFile(title) {
      const vForm = new FormData()
      vForm.append('participant_id', this.FormData.id)
      vForm.append('title', title)
      this.$swal({
        title: `Delete ${title}?`,
        text: 'It cannot be reverted',
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
          deleteFile(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: 'File has been deleted successfully', timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.fileMiladPhoto = null
            this.refetchData()
          }).catch(error => {
            this.isSubmitModal = false
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    changePreviewMode() {
      this.loadingImage = true
      this.isEdit = false
      this.withImageChange = ''
      if (this.previewMode === 'with_photo') {
        this.backgroundMiladCard = []
        getImages(this.selectedParticipant.participant_id).then(response => {
            this.summon = true
            if(response.data.status == false){
                this.summon = false
                this.checkIngnoreImage()
                return
            }
            this.miladCard = response.data.images
            this.backgroundMiladCard['background'] = response.data.background
            this.backgroundMiladCard['partOfLeft'] = response.data.partOfLeft
            this.backgroundMiladCard['partOfRight'] = response.data.partOfRight
            this.backgroundMiladCard['partOfBottom'] = response.data.partOfBottom
            this.loadingImage = false
        }).catch(error => {
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        })
        // getMiladCardWithPhoto(this.selectedParticipant.participant_id).then(response => {
        //   this.isSubmitModal = false
        //   const fileURL = window.URL.createObjectURL(new Blob([response.data]))
        //   this.miladCard = fileURL
        //   this.loadingImage = false
        // }).catch(error => {
        //   this.verificationModal = false
        //   this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        // })
      } else {
        getMiladCardWithoutPhoto(this.selectedParticipant.participant_id).then(response => {
          this.summon = false
          this.isSubmitModal = false
          const fileURL = window.URL.createObjectURL(new Blob([response.data]))
          this.miladCard = fileURL
          this.loadingImage = false
        }).catch(error => {
          this.verificationModal = false
          this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        })
      }
    },
    checkIngnoreImage(){
        getMiladCardWithoutPhoto(this.selectedParticipant.participant_id).then(response => {
          this.summon = false
          this.isSubmitModal = false
          const fileURL = window.URL.createObjectURL(new Blob([response.data]))
          this.miladCard = fileURL
          this.loadingImage = false
        }).catch(error => {
          this.verificationModal = false
          this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        })
    },
    showPreviewMilad(item) {
      this.loadingImage = true
      this.accessPreviewMilad = true
      this.selectedParticipant = item
      this.imagePath = item
      var age = calculateAge(item.birth_date);
      var title = "";
      if(age >= 22) {
          title = (item.gender == 1) ? "Bapak" : "Ibu";
      } else {
          title = "Ananda";
      }
      this.miladMessage = this.templateMiladMessage.replace('{{1}}', capitalize(item.name)).replace('{{gender}}', title);
      this.backgroundMiladCard = []
      this.isEdit = false
      this.withImageChange = ''
      this.summon = true
      getImages(item.participant_id).then(response => {
        if(response.data.status == false){
            this.summon = false
            this.checkIngnoreImage()
            return
        }
        this.isSubmitModal = false
        // const fileURL = window.URL.createObjectURL(new Blob([response.data.images]))
        // const backgroundUrl = window.URL.createObjectURL(new Blob([response.data.background]))
        this.miladCard = response.data.images
        this.loadingImage = false
        this.backgroundMiladCard['background'] = response.data.background
        this.backgroundMiladCard['partOfLeft'] = response.data.partOfLeft
        this.backgroundMiladCard['partOfRight'] = response.data.partOfRight
        this.backgroundMiladCard['partOfBottom'] = response.data.partOfBottom
      }).catch(error => {
        this.verificationModal = false
        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    },
    downloadMiladCard(item, lfl='') {
        const vForm = new FormData();
        vForm.append('id', item.participant_id)
        vForm.append('mode', this.previewMode)
        if(lfl!=''){
            if(this.withImageChange!=''){
                vForm.append('image', this.withImageChange)
            }
        }
        this.isEdit = false
        downloadWhenPost(vForm).then(response => {
            const fileURL = window.URL.createObjectURL(new Blob([response.data]))
            const fileLink = document.createElement('a')
            const contentDisposition = response.headers['content-disposition']
            fileLink.href = fileURL;
            let fileName = 'unknown';
            if (contentDisposition) {
            const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
            if (fileNameMatch.length === 2)
                fileName = fileNameMatch[1];
            }
            fileLink.setAttribute('download', fileName);
            document.body.appendChild(fileLink);
            fileLink.click();
            }).catch(error => {
                this.$bvToast.toast(`Error: ${error.response.statusText}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
            })

    //   downloadMiladCard(item.participant_id, this.previewMode, this.withImageChange).then(response => {
    //     const fileURL = window.URL.createObjectURL(new Blob([response.data]))
    //     const fileLink = document.createElement('a')
    //     const contentDisposition = response.headers['content-disposition']
    //     fileLink.href = fileURL;
    //     let fileName = 'unknown';
    //     if (contentDisposition) {
    //       const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
    //       if (fileNameMatch.length === 2)
    //         fileName = fileNameMatch[1];
    //     }
    //     fileLink.setAttribute('download', fileName);
    //     document.body.appendChild(fileLink);
    //     fileLink.click();
    //   }).catch(error => { this.$bvToast.toast(`Error: ${error.response.statusText}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true }) })
    },
    reminderMilad(item) {
      var msgTitle = `Nonaktifkan Reminder Milad untuk ${item.name}?`
      var confirmButtonText = 'Ya, nonaktifkan!'
      var confirmButton = 'btn btn-danger'
      if(item.remind_milad == 2) {
        msgTitle = `Aktifkan Reminder Milad untuk ${item.name}?`
        confirmButtonText = 'Ya, aktifkan!'
        confirmButton = 'btn btn-info'
      }
      const vForm = new FormData()
      vForm.append('participant_id', item.id)
      this.$swal({
        title: msgTitle,
        text: 'Lakukan perubahan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        customClass: {
          confirmButton: confirmButton,
          cancelButton: 'btn btn-outline-primary ml-1',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          activationReminderMilad(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: 'Reminder Milad sudah dinonaktifkan', timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.fileMiladPhoto = null
            this.refetchData()
          }).catch(error => {
            this.isSubmitModal = false
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    getDomisiliCities(value) {
      // get all city data
      getCities(value).then(response => {
        this.domisili_cities = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getDomisiliDistricts(value) {
      // get all district data
      getDistricts(value).then(response => {
        this.domisili_districts = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getDomisiliSubdistricts(value) {
      // get all subdistrict data
      getSubdistricts(value).then(response => {
        this.domisili_subdistricts = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getDomisiliPostalcodes(value) {
      // get all subdistrict data
      getPostalcodes(value).then(response => {
        this.domisili_postalcodes = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    sendMiladCard(item) {
        this.sendPostMiladCard(item)
    //   sendMiladCard(item.participant_id, this.previewMode).then(response => {
    //     this.$swal({ icon: 'success', title: 'Success', text: `System is now sending Milad Card to Whatsapp.`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
    //   }).catch(error => { this.$bvToast.toast(`Error: ${error.response.statusText}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true }) })
    },
    sendPostMiladCard(item){
      const vForm = new FormData();
      vForm.append('id', item.participant_id)
      vForm.append('mode', this.previewMode)
      if(this.withImageChange!=''){
        const image = ""
        image = this.withImageChange
        vForm.append('image', image)
      }
      sendPostMiladCard(vForm).then(response => {
           this.$swal({ icon: 'success', title: 'Success', text: `System is now sending Milad Card to Whatsapp.`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
        }).catch(error => { this.$bvToast.toast(`Error: ${error.response.statusText}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true }) })
    },
    handleClickOutside(event) {
        const element = document.getElementById('images-edit');
        if (element && !element.contains(event.target)){
            this.overflowImage = 'hidden'
            this.zIndexFrame = 2
            this.isEdit = true
        }
    },
    opendend(){
        this.overflowImage = 'unset'
        this.zIndexFrame = 4
        this.isEdit = false
        if(this.withImageChange==''){
            this.withImageChange = ''
        }
    },
    saveChangesNew() {
      this.savingImgeChanges = true
      const div = this.$refs.captureDiv;
      html2canvas(div).then(canvas => {

        const newWidth = 560;
        const newHeight = 560;

        const resizedCanvas = document.createElement("canvas");
        const context = resizedCanvas.getContext("2d");

        // Set ukuran canvas yang baru
        resizedCanvas.width = newWidth;
        resizedCanvas.height = newHeight;

        context.drawImage(canvas, 0, 0, newWidth, newHeight);
        this.withImageChange = canvas.toDataURL()
        this.isEdit = false
        this.savingImgeChanges = false
      });
    }
  },
  mounted() {
    // Add the event listener when the component is mounted
        document.body.addEventListener('click', this.handleClickOutside);
    },
    beforeDestroy() {
        // Remove the event listener before the component is destroyed
        document.body.removeEventListener('click', this.handleClickOutside);
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';

.image-box {
  height: 150px;
}
.preview-editor {
  width: 100%;
  height: 400px;
  overflow: hidden;
}
.btn-edit{
    position: absolute;
    top:10px;
    right:10px;
    z-index: 2;
}
.pages{
    width: 100%;
    height: 100%;
    position: relative;
    display: grid;
    justify-content: center;
}
.background-milad-card{
    width: 366px;
    height: 651px;
    object-fit: cover;
    object-position: center;
}
.rules-trigger{
    border-top-left-radius: 123px;
    border-top-right-radius: 123px;
    position: absolute;
    width: 238px;
    height: 273px;
    left: 64px;
    top: 16%;
    max-width: 238px;
    max-height: 273px;
}
.milad-image{
    width: inherit;
    height: inherit;
    // object-fit: cover;
    // object-position: center;
}
.partOfLeft{
    margin-top: -2px;
    width: inherit;
    height: inherit;
    object-fit: cover;
    object-position: center;
}

.right-image{
    position: absolute;
    right: 16px;
    top: 168px;
    z-index: 3;
    width: 75px;
    /* height: 49px; */
    border-radius: 50%;
    overflow: hidden;
}

.bottom-image{
    position: absolute;
    left: 19px;
    top: 340px;
    z-index: 3;
    width: 61px;
    /* height: 63px; */
    border-radius: 100%;
}

.left-image{
    position: absolute;
    left: 42px;
    top: 151px;
    z-index: 3;
    width: 79px;
    border-radius: 50%;
    overflow: hidden;
}
.bn-save-changes{
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 99;
}
</style>
