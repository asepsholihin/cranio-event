<template>
  <b-sidebar id="edit-sidebar" :visible="isEditSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetUserData" @shown="loadData"
    @change="(val) => $emit('update:is-edit-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">Edit Event
        </h5>
        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />
      </div>

      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetUserData" autocomplete="nope">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <div v-if="!isLoadedData"><b-spinner small v-show="isButtonLoading" /> Loading data...</div>

          <div v-if="isLoadedData">
            <!-- Event -->
            <validation-provider #default="{ errors }" name="Event" vid="event" rules="required">
              <b-form-group label="Event" :state="errors.length > 0 ? false : null">
                  <v-select v-model="formData.event" :options="[{ label: 'Manasik Umroh', value: 'Manasik Umroh' }, { label: 'Manasik Haji', value: 'Manasik Haji' }, {label: 'Pengajian', value: 'Pengajian'}]" :clearable="false" :reduce="label => label.value" v-on:input="changeEvent($event)" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                      {{ errors[0] }}
                  </b-form-invalid-feedback>
                  </b-form-group>
            </validation-provider>

            <div v-if="formData.event === 'Manasik Umroh' || formData.event === 'Manasik Haji'|| formData.event === 'Pengajian'">
              <!-- Umroh Trip -->
              <validation-provider #default="{ errors }" name="Trip Umroh/Haji" vid="umroh_trip_id">
                <b-form-group label="Trip Umroh/Haji" :state="errors.length > 0 ? false : null">
                  <v-select v-model="formData.umroh_trip_id" @search="onSearch" :options="umrohTrips" :filterable="false" :reduce="trip => trip.id" :clearable="false" label="title" @input="getPackages" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                  {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </div>

            <!-- Event Name -->
            <validation-provider #default="{ errors }" name="Event Name" vid="name" rules="required">
              <b-form-group label="Event Name">
                <b-form-input v-model="formData.name" name="name" :state="errors.length > 0 ? false : null" trim />

                <b-form-invalid-feedback>
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Location -->
            <validation-provider #default="{ errors }" name="Manasik Hotel Name" vid="location" rules="required">
              <b-form-group label="Manasik Hotel Name">
                <v-select v-model="formData.location" :options="manasikOptions" :filterable="false" :reduce="hotel => hotel.hotel_name" :clearable="true" label="hotel_name" />
                <b-form-invalid-feedback>
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

              <!-- Hotel Hotel -->
            <validation-provider #default="{ errors }" name="Transit Hotel Name" vid="transit_hotel_id">
              <b-form-group label="Transit Hotel Name">
                <v-select v-model="formData.transit_hotel_id" :options="transitOptions" :filterable="false" :reduce="hotel => hotel.id" :clearable="true" label="hotel_name" />
                <b-form-invalid-feedback>
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Event Date -->
            <validation-provider #default="{ errors }" vid="event_date" rules="required" name="Event Date">
              <b-form-group label="Event Date" :state="errors.length > 0 ? false : null">
                <flat-pickr v-model="formData.event_date" class="form-control" />
                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Event At -->
            <validation-provider #default="{ errors }" vid="event_at" rules="required" name="Event Start At">
              <b-form-group label="Event Start At" :state="errors.length > 0 ? false : null">
                <flat-pickr :config="{ enableTime: true,noCalendar: true,dateFormat: 'H:i'}" v-model="formData.event_at" class="form-control" />
                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Event End At -->
            <validation-provider #default="{ errors }" vid="event_end_at" rules="required" name="Event End At">
              <b-form-group label="Event End At" :state="errors.length > 0 ? false : null">
                <flat-pickr :config="{ enableTime: true,noCalendar: true,dateFormat: 'H:i'}" v-model="formData.event_end_at" class="form-control" />
                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Split -->
            <validation-provider #default="{ errors }" name="Pisahkan Open Gate" vid="split">
              <b-form-group label="Pisahkan Open Gate">
                <b-form-checkbox v-model="formData.open_gate_split" name="check-button" switch />

                <b-form-invalid-feedback>
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Event Akbar -->
            <validation-provider #default="{ errors }" name="Event Akbar" vid="event_akbar">
              <b-form-group label="Apakah Event Akbar?">
                <b-form-checkbox v-model="formData.event_akbar" name="check-button" switch />

                <b-form-invalid-feedback>
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <template v-if="formData.open_gate_split" v-for="(pack, index) in formData.open_gate">
              <validation-provider #default="{ errors }" :vid="`open_event_at`+index" rules="required" :name="`Open Gate - ` + pack.package_name">
                <b-form-group :label="`Open Gate - ` + pack.package_name" :state="errors.length > 0 ? false : null">
                  <flat-pickr :config="{ enableTime: true,noCalendar: true,dateFormat: 'H:i'}" v-model="formData.open_gate[index].open_gate_at" class="form-control" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </template>

            <!-- Form Actions -->
            <div class="d-flex mt-2">
              <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit" :disabled="isButtonLoading">
                <b-spinner small v-show="isButtonLoading" /> Add
              </b-button>
              <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button" variant="outline-secondary"
                @click="hide">
                Cancel
              </b-button>
            </div>
          </div>

        </b-form>
      </validation-observer>
    </template>
  </b-sidebar>
</template>

<script>
import { BSidebar, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup, BFormCheckbox } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import _ from 'lodash'
import { getDetail, postData, getUmrohTrip } from '@/network/event-attendance'
import { getTransitHotel, getManasikHotel } from '@/network/master-hotel-event'
import { getPackages } from '@/network/booking-order'
import flatPickr from 'vue-flatpickr-component'

export default {
  components: {
    BSidebar,
    BForm,
    BFormGroup,
    BFormInput,
    BAlert,
    BFormInvalidFeedback,
    BButton,
    BSpinner,
    BInputGroup,
    BInputGroupAppend,
    BFormCheckboxGroup,
    BFormCheckbox,
    vSelect,
    flatPickr,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  model: {
    prop: ['isEditSidebarActive','eventId'],
    event: 'update:is-edit-sidebar-active',
  },
  props: {
    isEditSidebarActive: {
      type: Boolean,
      required: true,
    },
    eventId: {
      required: true,
    }
  },
  data() {
    const umrohTrips = []
    const transitOptions = []
    const manasikOptions = []

    getTransitHotel().then(response => {
        this.transitOptions = response.data;
    }).catch(error => {
        if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
        this.$refs.refObsForm.setErrors(error.response.data)
        }
    })

    getManasikHotel().then(response => {
        this.manasikOptions = response.data;
    }).catch(error => {
        if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
        this.$refs.refObsForm.setErrors(error.response.data)
        }
    })

    return {
      password: '',
      passwordFieldTypeNew: 'password',
      isButtonLoading: false,
      required,
      min,
      numeric,
      email,
      formData: { open_gate: [] },
      umrohTrips,
      manasikOptions,
      transitOptions,
      packageOptions: [],
      isLoadedData: false
    }
  },
  methods: {
    loadData() {
      this.isButtonLoading = true
      getDetail(this.eventId).then(response => {
        this.formData = response.data
        this.formData['open_gate_split'] = (response.data['open_gate_split'] == 1) ? true : false
        this.formData['open_gate'] = JSON.parse(response.data['open_gate'])
        var qwords = ''
        if(this.formData['event'] == "Manasik Haji") {
          qwords = 'haji'
        }
        getUmrohTrip({q:qwords}).then(response => {
          this.umrohTrips = response.data;
        }).catch(error => {
          this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })

        this.isLoadedData = true
        this.isButtonLoading = false
        // console.log(JSON.parse(response.data.open_gate))
      }).catch(error => {
        this.isButtonLoading = false
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
      })
    },
    changeEvent(opt) {
        this.formData.umroh_trip_id = null
        var qwords = ''
        if(opt == "Manasik Haji") {
          qwords = 'haji'
        }
        getUmrohTrip({q:qwords}).then(response => {
          this.umrohTrips = response.data;
        }).catch(error => {
          this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })
    },
    getPackages(value) {
      getPackages(value).then((response) => {
          this.packageOptions = response.data.packages;
          var arrPackage = []
          this.packageOptions.forEach(element => {
            arrPackage.push({
              package_id: element.id,
              package_name: element.name,
              open_gate_at: ""
            })
          });
          this.formData.open_gate = arrPackage
      })
      .catch((error) => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
      });
    },
    onSearch(search, loading) {
        loading(true);
        this.search(loading, search, this);
    },
    search: _.debounce((loading, search, vm) => {
        getUmrohTrip({ q: search })
            .then(res => {
                vm.umrohTrips = res.data
                loading(false)
            })
            .catch(error => {
                vm.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                loading(false)
            })
    }, 300),
    resetUserData() {
      this.isLoadedData = false
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        this.formData.eventId = this.eventId
        this.isButtonLoading = true
        postData(this.formData).then(response => {
          this.$bvToast.toast(`${this.formData.name} has been added successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.$emit('refetch-data')
          this.$emit('update:is-edit-sidebar-active', false)
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
    }
  },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';

#edit-sidebar {
  .vs__dropdown-menu {
    max-height: 200px !important;
  }
}
</style>
