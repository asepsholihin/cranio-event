<template>
  <b-card>
    <b-tabs>
      <!-- Tab: Detail Account -->
      <b-tab active>
          <template #title >
          <feather-icon
              icon="UserIcon"
              size="16"
              class="mr-0 mr-sm-50"
          />
          <span class="d-none d-sm-inline">Account</span>
          </template>
          <p class="pt-1">
              <!-- Form -->
              <b-form @submit.prevent="onSubmit" >
              <div class="mb-2">
                  <b-avatar :src="formData.profile_thumbnail" :text="avatarText(formData.name)" size="120px" rounded />
                  <b-button @click="editImage" variant="link">
                    <feather-icon icon="Edit2Icon" size="16"/> Edit
                  </b-button>
                  <h4 class="mb-1 mt-2" >
                  {{ formData.name }}
                  </h4>
                  <h5>JI CODE: {{ formData.ji_code }}</h5>
              </div>
              <!-- BODY -->
              <validation-observer ref="refObsForm">
                  <validation-provider #default="{ errors }" vid="message">
                  <b-alert variant="danger" show v-if="errors[0]">
                      <div class="alert-body">
                      {{ errors[0] }}
                      </div>
                  </b-alert>
                  </validation-provider>

                  <h3 class="mt-3 mb-2">Biodata</h3>
                  <b-row>
                    <!-- Name -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Full Name" vid="name" rules="required">
                        <b-form-group label="Full Name">
                            <b-form-input v-model="formData.name" name="name" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Gender -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Gender" vid="gender" rules="required">
                        <b-form-group label="Gender" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.gender" :options="genderOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Birth Date -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" vid="birth_date" rules="required" name="Birth Date">
                        <b-form-group label="Birth Date" :state="errors.length > 0 ? false : null">
                            <flat-pickr v-model="formData.birth_date" class="form-control" />

                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- NIK -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="NIK (KTP)" vid="nik" rules="numeric|min:16">
                        <b-form-group label="NIK (KTP)" label-for="nik">
                            <b-form-input v-model="formData.nik" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                  </b-row>

                  <h3 class="mt-3 mb-2">Informasi Kontak</h3>
                  <b-row>
                  <!-- No HP -->
                  <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="No HP" vid="no_hp" rules="numeric|max:14">
                      <b-form-group label="No HP" label-for="no_hp">
                          <b-form-input v-model="formData.no_hp" :state="errors.length > 0 ? false : null" trim />

                          <b-form-invalid-feedback>
                          {{ errors[0] }}
                          </b-form-invalid-feedback>
                      </b-form-group>
                      </validation-provider>
                  </b-col>

                  <!-- Email -->
                  <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Email" vid="email" rules="email">
                      <b-form-group label="Email" label-for="email">
                          <b-form-input v-model="formData.email" :state="errors.length > 0 ? false : null" trim />

                          <b-form-invalid-feedback>
                          {{ errors[0] }}
                          </b-form-invalid-feedback>
                      </b-form-group>
                      </validation-provider>
                  </b-col>

                  <!-- Instagram -->
                  <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Instagram" vid="instagram">
                      <b-form-group label="Instagram">
                          <b-form-input id="instagram" v-model="formData.instagram" :state="errors.length > 0 ? false : null"
                          trim />

                          <b-form-invalid-feedback>
                          {{ errors[0] }}
                          </b-form-invalid-feedback>
                      </b-form-group>
                      </validation-provider>
                  </b-col>
                  
                  <!-- Facebook -->
                  <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Facebook" vid="facebook">
                      <b-form-group label="Facebook">
                          <b-form-input id="facebook" v-model="formData.facebook" :state="errors.length > 0 ? false : null"
                          trim />

                          <b-form-invalid-feedback>
                          {{ errors[0] }}
                          </b-form-invalid-feedback>
                      </b-form-group>
                      </validation-provider>
                  </b-col>
                  
                  <!-- Twitter -->
                  <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Twitter" vid="twitter">
                      <b-form-group label="Twitter">
                          <b-form-input id="twitter" v-model="formData.twitter" :state="errors.length > 0 ? false : null"
                          trim />

                          <b-form-invalid-feedback>
                          {{ errors[0] }}
                          </b-form-invalid-feedback>
                      </b-form-group>
                      </validation-provider>
                  </b-col>
                  
                  <!-- LinkedIn -->
                  <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="LinkedIn" vid="linkedin">
                      <b-form-group label="LinkedIn">
                          <b-form-input id="linkedin" v-model="formData.linkedin" :state="errors.length > 0 ? false : null"
                          trim />

                          <b-form-invalid-feedback>
                          {{ errors[0] }}
                          </b-form-invalid-feedback>
                      </b-form-group>
                      </validation-provider>
                  </b-col>
                  </b-row>

                  <h3 class="mt-3 mb-2">Alamat Domisili / Perlengkapan</h3>
                  <b-row>
                    <!-- Provinsi -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Provinsi" vid="province">
                        <b-form-group label="Provinsi" :state="errors.length > 0 ? false : null">
                            <v-select @input="getKTPCities" v-model="formData.province" :options="provinces" :clearable="false"
                            :reduce="province => province.province" label="province" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kota -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kota" vid="city">
                        <b-form-group label="Kota" :state="errors.length > 0 ? false : null">
                            <v-select @input="getDomisiliDistricts" v-model="formData.city" :options="cities" :clearable="false"
                            :reduce="city => city.city" label="city" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kecamatan -->
                    <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Kecamatan" vid="home_kecamatan">
                        <b-form-group label="Kecamatan" :state="errors.length > 0 ? false : null">
                          <v-select @input="getDomisiliSubdistricts" v-model="formData.home_kecamatan"
                            :options="domisili_districts" :clearable="false" :reduce="district => district.district"
                            label="district" />
                          <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </validation-provider>
                    </b-col>
                    
                    <!-- Kelurahan -->
                    <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Kelurahan" vid="home_kelurahan">
                        <b-form-group label="Kelurahan" :state="errors.length > 0 ? false : null">
                          <v-select @input="getDomisiliPostalcodes" v-model="formData.home_kelurahan"
                            :options="domisili_subdistricts" :clearable="false" :reduce="subdistrict => subdistrict.subdistrict"
                            label="subdistrict" />
                          <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </validation-provider>
                    </b-col>

                    <!-- Kode Pos -->
                    <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Kode Pos" vid="home_postalcode">
                        <b-form-group label="Kode Pos" :state="errors.length > 0 ? false : null">
                          <v-select taggable v-model="formData.home_postalcode" :options="domisili_postalcodes" :clearable="false"
                            :reduce="postalcode => postalcode.postalcode" label="postalcode" />

                          <b-form-invalid-feedback>
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </validation-provider>
                    </b-col>

                    <!-- Alamat Lengkap -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Alamat Lengkap" vid="address">
                        <b-form-group label="Alamat Lengkap">
                            <b-form-textarea id="address" v-model="formData.address" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                  </b-row>

                  <h3 class="mt-3 mb-2">Pendidikan dan Pekerjaan</h3>
                  <b-row>
                    <!-- Pendidikan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Pendidikan" vid="education" rules="required">
                        <b-form-group label="Pendidikan" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.education" :options="educationOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Pekerjaan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Pekerjaan" vid="job" rules="required">
                        <b-form-group label="Pekerjaan" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.job" :options="jobOptions" :clearable="false"
                            :reduce="label => label.name" label="name" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                        
                        <validation-provider #default="{ errors }" name="Detail Pekerjaan" vid="job_description">
                        <b-form-group label="Detail Pekerjaan">
                            <b-form-textarea id="job_description" v-model="formData.job_description" :state="errors.length > 0 ? false : null"
                            trim />
                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                  </b-row>
                  <!-- Form Actions -->
                  <div class="d-flex mt-2">
                  <b-button v-if="hasPermission('participant-crm-edit')" v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit">
                      Save Changes
                  </b-button>
                  <b-button @click="$router.go(-1)" v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button"
                      variant="outline-secondary">
                      Back
                  </b-button>
                  </div>

              </validation-observer>
              </b-form>
          </p>
      </b-tab>

      <!-- Tab: Transaction History -->
      <b-tab>
          <template #title >
          <feather-icon
              icon="FolderIcon"
              size="16"
              class="mr-0 mr-sm-50"
          />
          <span class="d-none d-sm-inline">Transaction History</span>
          </template>
          <p class="pt-1">
            <div>
              <h4>{{ formData.name }}</h4>
              <h5>JI CODE: {{ formData.ji_code }}</h5>
              <p class="font-weight-bold">Total Transaction: Rp. {{ totalTransaction(transactions).toLocaleString() }}</p>

              <table class="table mt-3">
                <thead>
                  <tr>
                    <th class="align-middle">Nama Paket</th>
                    <th class="align-middle">Tanggal Keberangkatan</th>
                    <th class="align-middle">Booking Order</th>
                    <th class="align-middle text-right">Total Diskon</th>
                    <th class="align-middle text-right">Total Transaction</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="(item, key) in transactions">
                    <tr>
                      <td>{{ item.name }}</td>
                      <td>{{ item.title }}</td>
                      <td>
                        <template v-if="item.sales_name">
                          <div>
                            {{ item.sales_name }}<br>
                            {{ item.booking_order_no }}
                          </div>
                        </template>
                      </td>
                      <td class="text-right text-nowrap">{{ item.currency }} {{ parseInt(item.discount).toLocaleString() }}</td>
                      <td class="text-right text-nowrap">{{ item.currency }} {{ parseInt(item.total_transaction).toLocaleString() }} {{ (item.total_transaction_conv != item.total_transaction) ? '(IDR '+item.total_transaction_conv.toLocaleString()+')' : '' }}</td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </p>
      </b-tab>

      <!-- Tab: Files -->
      <b-tab>
          <template #title >
          <feather-icon
              icon="FolderIcon"
              size="16"
              class="mr-0 mr-sm-50"
          />
          <span class="d-none d-sm-inline">Files</span>
          </template>
          <p class="pt-1">
            <files :participant.sync="formData" />
          </p>
      </b-tab>
    </b-tabs>
    
    <b-modal v-model="imageEditorModalShow" ok-title="Simpan" size="xl" centered no-close-on-backdrop ok-only
      @ok="handleSaveImageEditor" :busy="isLoading">
        <template #modal-title>
            <h4>{{ formData.name }}</h4>
        </template>
        <b-row class="align-items-center">
            <b-col sm="6">
                <vue-cropper
                    ref="cropper"
                    :src="formData.profile_thumbnail"
                    crossorigin="anonymous"
                    :checkCrossOrigin="false"
                    :checkOrientation="false"
                    preview=".preview-editor"
                    class="wrap-editor"
                />
                <div class="justify-content-center mt-1">
                    <b-button size="sm" @click.prevent="zoom(0.2)" class="mr-25 mb-25" variant="primary">
                        Zoom in
                    </b-button>
                    <b-button size="sm" @click.prevent="zoom(-0.2)" class="mr-25 mb-25" variant="primary">
                        Zoom out
                    </b-button>
                    <b-button size="sm" @click.prevent="setRatio(2/3)" class="mr-25 mb-25" variant="primary">
                        2:3
                    </b-button>
                    <b-button size="sm" @click.prevent="setRatio(3/4)" class="mr-25 mb-25" variant="primary">
                        3:4
                    </b-button>
                    <b-button size="sm" @click.prevent="rotate(90)" class="mr-25 mb-25" variant="primary">
                        Rotate 90
                    </b-button>
                    <b-button size="sm" @click.prevent="rotate(-90)" class="mr-25 mb-25" variant="primary">
                        Rotate -90deg
                    </b-button>
                    <b-button size="sm" ref="flipX" @click.prevent="flipX" class="mr-25 mb-25" variant="primary">
                        Flip X
                    </b-button>
                    <b-button size="sm" ref="flipY" @click.prevent="flipY" class="mr-25 mb-25" variant="primary">
                        Flip Y
                    </b-button>
                    <b-button size="sm" @click.prevent="reset" variant="primary">
                        Reset
                    </b-button>
                    <!-- <b-button @click.prevent="removeBackgroundImage()" variant="primary">
                        Hapus Background
                    </b-button> -->
                </div>
            </b-col>
            <b-col sm="6">
                <div class="preview-editor m-auto"></div>
            </b-col>
        </b-row>
    </b-modal>
  </b-card>
</template>

<script>
import { BTab, BTabs, BCard, BLink, BFormInvalidFeedback, BButton, BMedia, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormTextarea, BFormFile, BFormCheckbox } from 'bootstrap-vue'
import { getDetail, postData, getTransactions } from '@/network/crm-participant'
import { uploadPasPhoto, getJobSearch } from '@/network/participant'
import { getProvinces, getCities, getDistricts, getSubdistricts, getPostalcodes } from '@/network/address'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { avatarText } from '@core/utils/filter'
import { hasPermission } from '@/auth/utils'
import files from './files.vue'
import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';

export default {
  components: {
    BCard,
    BTab,
    BTabs,
    BLink,
    BFormInvalidFeedback,
    BButton,
    BMedia,
    vSelect,
    flatPickr,
    BAvatar,
    BAlert,
    BForm,
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BFormTextarea,
    BFormFile,
    BFormCheckbox,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
    files,
    VueCropper
  },
  directives: {
    Ripple,
  },
  setup() {
    const genderOptions = [{ label: 'Man', value: 1 }, { label: 'Woman', value: 2 }]
    const titleOptions = [{ label: 'Mr', value: 'Mr' }, { label: 'Ms', value: 'Ms' }, { label: 'Mrs', value: 'Mrs' }, { label: 'Mstr', value: 'Mstr' }, { label: 'Miss', value: 'Miss' }]
    const educationOptions = [{ label: 'SD/MI', value: 'SD/MI' }, { label: 'SMP/MTS', value: 'SMP/MTS' }, { label: 'SMA/MA', value: 'SMA/MA' }, { label: 'D1', value: 'D1' }, { label: 'D2', value: 'D2' }, { label: 'D3', value: 'D3' }, { label: 'D4/S1', value: 'D4/S1' }, { label: 'S2', value: 'S2' }, { label: 'S3', value: 'S3' }, { label: 'BELUM SEKOLAH', value: 'BELUM SEKOLAH' }]
    const yesNoOptions = [{ label: 'Yes', value: 1 }, { label: 'No', value: 2 }]
    
    return {
      avatarText, genderOptions, titleOptions, educationOptions, yesNoOptions, hasPermission
    }
  },

  methods: {
    totalTransaction: function (transactions) {
      return transactions.reduce((acc, val) => {
        return acc + parseInt(val.total_transaction_conv);
      }, 0);
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          if (this.formData[key] != null)
            vForm.append(key, this.formData[key])
        }
        postData(vForm).then(response => {
          this.$bvToast.toast('Participant has been changed successfully', {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
        })
          .catch(error => {
            if (error.response.data.errors) {
              this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
              this.$refs.refObsForm.setErrors(error.response.data)
            }
          })
      })
    },
    getKTPCities(value) {
      // get all city data
      getCities(value).then(response => {
        this.cities = response.data;
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
      getPostalcodes(value, this.formData.home_kecamatan).then(response => {
        this.domisili_postalcodes = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getListTransactions(participantId) {
      getTransactions({participantId:participantId}).then(response => {
        this.transactions = response.data;
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    editImage() {
      this.imageEditorModalShow = true
    },
    setRatio(ratio) {   
      this.$refs.cropper.setAspectRatio(ratio);
    },
    zoom(percent) {
      this.$refs.cropper.relativeZoom(percent);
    },
    rotate(deg) {
      this.$refs.cropper.rotate(deg);
    },
    flipX() {
      const dom = this.$refs.flipX;
      let scale = dom.getAttribute('data-scale');
      scale = scale ? -scale : -1;
      this.$refs.cropper.scaleX(scale);
      dom.setAttribute('data-scale', scale);
    },
    flipY() {
      const dom = this.$refs.flipY;
      let scale = dom.getAttribute('data-scale');
      scale = scale ? -scale : -1;
      this.$refs.cropper.scaleY(scale);
      dom.setAttribute('data-scale', scale);
    },
    reset() {
      this.$refs.cropper.reset();
    },
    removeBackgroundImage() {
    },
    handleSaveImageEditor(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.isLoading = true

      this.$refs.cropper.getCroppedCanvas().toBlob((blob) => {
        const vForm = new FormData()
        vForm.append('file_upload', blob);
        vForm.append('id', this.formData.participant_id)
        uploadPasPhoto(vForm).then(response => {
          this.isLoading = false
          this.$swal({ icon: 'success', title: 'Success', text: `Photo has been uploaded successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
        }).catch(error => {
          this.isLoading = false
          this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        })
      })
    }
  },
  data() {
    const formData = {}
    const provinces = []
    const cities = []
    const domisili_districts = []
    const domisili_subdistricts = []
    const domisili_postalcodes = []
    const transactions = []
    const jobOptions = []
    const id = parseInt(this.$route.params.id) || 0
    if (id == 0) this.$router.back()
    getDetail(id).then(response => {
      this.formData = response.data
      this.formData.education = response.data.education ?? response.data.j_education
      if(this.formData.participant_id) {
        this.getListTransactions(this.formData.participant_id)
      }
      if(this.formData.province) {
        this.getKTPCities(this.formData.province)
      }
      if(this.formData.city) {
        this.getDomisiliDistricts(this.formData.city);
      }
      if(this.formData.home_kecamatan) {
        this.getDomisiliSubdistricts(this.formData.home_kecamatan);
      }
      if(this.formData.home_kelurahan) {
        this.getDomisiliPostalcodes(this.formData.home_kelurahan);
      }
    }).catch(error => {
      if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
      } else {
        this.$refs.refObsForm.setErrors(error.response.data)
      }
    })

    // get all provinces data
    getProvinces().then(response => {
      this.provinces = response.data;
    }).catch(error => {
      if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
      } else {
        this.$refs.refObsForm.setErrors(error.response.data)
      }
    })

    getJobSearch().then(response => {
        this.jobOptions = response.data
    }).catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    return {
      formData, required, numeric, email, provinces, cities, transactions,
      domisili_districts,
      domisili_subdistricts,
      domisili_postalcodes,
      jobOptions,
      imageAspectRatio: 1,
      imageEditorModalShow: false,
      isLoading: false,
    }
  }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';

.wrap-editor {
    height: 480px;  
}
.preview-editor {
  width: 100%;
  height: 400px;
  overflow: hidden;
}
.box-select {
    position: absolute;
    right: 0;
    top: 6px;
}
.btn-view {
    top: 0;
    bottom: 0;
    width: 80px;
    height: 32px;
    right: 0;
    left: 0;
    margin: auto;
}
</style>
