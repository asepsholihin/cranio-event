<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetUserData" @shown="resetUserData"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">{{resolveHeader()}}</h5>
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


        <!-- Event -->
          <validation-provider #default="{ errors }" name="Event" vid="event" rules="required">
            <b-form-group label="Event" :state="errors.length > 0 ? false : null">
                <v-select v-model="formData.event" :options="[{ label: 'Seminar', value: 'Seminar' }, {label: 'Workshop', value: 'Workshop'}]" :clearable="false" :reduce="label => label.value" />
                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                </b-form-invalid-feedback>
                </b-form-group>
           </validation-provider>

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
          <validation-provider #default="{ errors }" name="Location" vid="location" rules="required">
            <b-form-group label="Location">
              <b-form-input v-model="formData.location" name="name" :state="errors.length > 0 ? false : null" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

            <!-- Hotel Hotel -->
          <validation-provider #default="{ errors }" name="Transit Hotel Name" vid="transit_hotel_id">
            <b-form-group label="Transit Hotel Name">
              <v-select v-model="formData.transit_hotel_id" :options="transitOptions" :filterable="false" :reduce="hotel => hotel.id" :clearable="true" label="name" />
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

          <!-- Event Akbar -->
          <validation-provider #default="{ errors }" name="Event Akbar" vid="event_akbar">
            <b-form-group label="Apakah Event Akbar?">
              <b-form-checkbox v-model="formData.event_akbar" name="check-button" switch />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

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

        </b-form>
      </validation-observer>
    </template>
  </b-sidebar>
</template>

<script>
import { BSidebar, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup, BFormCheckbox } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, min } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import _ from 'lodash'
import { postData } from '@/network/event-attendance'
import { getHotelSearch } from '@/network/hotel'
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
    prop: ['isAddSidebarActive','copyAttendeeFromEventId'],
    event: 'update:is-add-sidebar-active',
  },
  props: {
    isAddSidebarActive: {
      type: Boolean,
      required: true,
    },
    copyAttendeeFromEventId: {
      required: true,
    }
  },
  data() {
    const transitOptions = []

    getHotelSearch().then(response => {
        this.transitOptions = response.data;
    }).catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })


    return {
      isButtonLoading: false,
      required,
      min,
      numeric,
      formData: { open_gate: [] },
      transitOptions,
    }
  },
  methods: {
    resolveHeader(){
        if(this.copyAttendeeFromEventId == 0)
            return 'Add Event'
        return 'Copy Attendee Into Event'
    },
    resetUserData() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        this.formData.copyEventId = this.copyAttendeeFromEventId
        this.isButtonLoading = true
        postData(this.formData).then(response => {
          this.$bvToast.toast(`${this.formData.name} has been added successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.$emit('refetch-data')
          this.$emit('update:is-add-sidebar-active', false)
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

#add-new-sidebar {
  .vs__dropdown-menu {
    max-height: 200px !important;
  }
}
</style>
