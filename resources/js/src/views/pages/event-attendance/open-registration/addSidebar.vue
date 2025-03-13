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

          <!-- Image -->
          <validation-provider #default="{ errors }" vid="image" name="Image">
            <b-form-group label="Image" description="Recommended Size 1080 x 800 px (Format : PNG, JPG, WEBP) Max Size: 1.5MB">
              <b-form-file accept="image/jpeg, image/png, image/webp" v-model="formData.image" @change="onFileChange"
                :state="errors.length > 0 ? false : null" placeholder="Choose a icon or drop it here..."
                drop-placeholder="Drop icon here..." />

                <img v-if="imagePreview" :src="imagePreview" class="img-fluid" />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Event Name -->
          <validation-provider #default="{ errors }" name="Event Name" vid="name" rules="required">
            <b-form-group label="Event Name">
              <b-form-textarea v-model="formData.name" name="name" :state="errors.length > 0 ? false : null" rows="2" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Event Description -->
          <validation-provider #default="{ errors }" name="Event Description" vid="description" rules="required">
            <b-form-group label="Event Description">
              <b-form-textarea v-model="formData.description" name="description" :state="errors.length > 0 ? false : null" rows="2" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Location -->
          <validation-provider #default="{ errors }" name="Location" vid="location" rules="required">
            <b-form-group label="Location">
              <b-form-input v-model="formData.location" name="location" :state="errors.length > 0 ? false : null" trim />
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

          <!-- Event Start At -->
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

          <!-- Speaker -->
          <validation-provider #default="{ errors }" name="Speaker" vid="speaker" rules="required">
            <b-form-group label="Speaker">
              <b-form-input v-model="formData.speaker" name="speaker" :state="errors.length > 0 ? false : null" trim />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Paid Event -->
          <validation-provider #default="{ errors }" name="Paid Event" vid="is_paid_event">
            <b-form-group label="Paid Event">
              <b-form-checkbox v-model="formData.is_paid_event" name="check-button" switch />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Number of Seats -->
          <validation-provider #default="{ errors }" name="Attendee Limit" vid="number_of_seats" rules="numeric">
            <b-form-group label="Attendee Limit">
              <b-form-input v-model="formData.number_of_seats" name="pax" :state="errors.length > 0 ? false : null" trim type="number" />
              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Price -->
          <validation-provider #default="{ errors }" name="Ticket Price" vid="price" rules="numeric">
            <b-form-group label="Ticket Price">
              <cleave v-model="formData.price" class="form-control" :options="optionClave" :state="errors.length > 0 ? false : null" />
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
import { BSidebar, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckbox, BFormTextarea, BFormFile } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/event-open-registration'
import flatPickr from 'vue-flatpickr-component'
import Cleave from 'vue-cleave-component'

export default {
  components: {
    BFormTextarea,
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
    BFormCheckbox,
    vSelect,
    flatPickr,
    Cleave,
    BFormFile,

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
    return {
      isButtonLoading: false,
      required,
      min,
      numeric,
      email,
      formData: {},
      optionClave: {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
      },
      imagePreview: null
    }
  },
  methods: {
    resolveHeader(){
        if(this.copyAttendeeFromEventId == 0)
            return 'Add Event'
        return 'Copy Attendee Into Event'
    },
    onFileChange(e) {
      const file = e.target.files[0];
      this.imagePreview = URL.createObjectURL(file);
    },
    resetUserData() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
      this.imagePreview = null
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          vForm.append(key, this.formData[key])
        }
        vForm.append('copyEventId', this.copyAttendeeFromEventId)
        this.isButtonLoading = true
        postData(vForm).then(response => {
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
