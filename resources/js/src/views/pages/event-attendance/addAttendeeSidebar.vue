<template>
  <b-sidebar id="add-attendee-sidebar" :visible="isAddAttendeeSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetUserData" @shown="resetUserData"
    @change="(val) => $emit('update:is-add-attendee-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">Add Attendee</h5>
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
          <validation-provider #default="{ errors }" name="Participant" vid="event" rules="required">
            <b-form-group label="Participant" :state="errors.length > 0 ? false : null">
                <v-select v-model="formData.participant_id" :options="participant" :filterable="false" @search="onSearch" :reduce="name => name.id" label="name">
                  <template slot="no-options">
                    Type to search Participant..
                  </template>
                  <template slot="option" slot-scope="option">
                      <b-media vertical-align="center">
                        <template #aside>
                        <b-avatar size="45" :src="option.profile_thumbnail" :text="avatarText(option.name)"
                            :variant="`light-primary`" />
                        </template>
                        {{ option.name }}<br/>{{ formatDate(option.birth_date) }}<br/>{{option.no_hp}}
                    </b-media>
                  </template>
                  <template slot="selected-option" slot-scope="option">
                    <div class="selected d-center">
                       <b-media vertical-align="center">
                            <template #aside>
                            <b-avatar size="45" :src="option.profile_thumbnail" :text="avatarText(option.name)"
                                :variant="`light-primary`" />
                            </template>
                            {{ option.name }}<br/>{{ formatDate(option.birth_date) }}<br/>{{option.no_hp}}
                        </b-media>
                    </div>
                  </template>
                </v-select>

                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
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
import { BSidebar, BAvatar, BMedia, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postAttendee, getParticipantSearch } from '@/network/event-attendance'
import flatPickr from 'vue-flatpickr-component'
import { avatarText, formatDate } from '@core/utils/filter'
import _ from 'lodash';

export default {
  components: {
    BMedia,
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
    vSelect,
    flatPickr,
    BAvatar,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  model: {
    prop: 'isAddAttendeeSidebarActive',
    event: 'update:is-add-attendee-sidebar-active',
  },
  props: {
    eventAttendance: {
      type: Object,
      required: true,
    },
    isAddAttendeeSidebarActive: {
      type: Boolean,
      required: true,
    }
  },
  data() {
    return {
      password: '',
      passwordFieldTypeNew: 'password',
      isButtonLoading: false,
      required,
      min,
      numeric,
      email,
      avatarText,
      formatDate,
      formData: {},
      participant: []
    }
  },
  computed: {
    passwordToggleIconNew() {
      return this.passwordFieldTypeNew === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
  },
  methods: {
    togglePasswordNew() {
      this.passwordFieldTypeNew = this.passwordFieldTypeNew === 'password' ? 'text' : 'password'
    },
    resetUserData() {
      for (var key in this.formData) {
        this.formData[key] = null;
      }
      this.$refs.refObsForm.reset()
    },
    onSearch(search, loading) {
      if(search.length) {
        loading(true);
        this.search(loading, search, this);
      }
    },
    search: _.debounce((loading, search, vm) => {
        getParticipantSearch({q:search})
            .then(res =>{
                vm.participant = res.data
                loading(false)
            })
            .catch(error => {
                vm.$bvToast.toast(`Error: ${error.response.data.message}`, {title: `Error`,variant: 'danger',toaster: 'b-toaster-top-center',solid: true})
                loading(false)
            })
        }, 300),
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          vForm.append(key, this.formData[key])
        }
        vForm.append('event_id', this.eventAttendance.id)
        vForm.append('act', 'add')
        this.isButtonLoading = true
        postAttendee(vForm).then(response => {
          this.$bvToast.toast(`It has been added successfully`, {
            title: `Success`,
            variant: 'primary',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.$emit('refetch-data')
          this.$emit('update:is-add-attendee-sidebar-active', false)
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
