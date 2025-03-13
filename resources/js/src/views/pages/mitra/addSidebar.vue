<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetUserData"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Add New Mitra
        </h5>

        <feather-icon class="ml-1 cursor-pointer" icon="XIcon" size="16" @click="hide" />

      </div>

      <!-- BODY -->
      <validation-observer ref="refObsForm">
        <!-- Form -->
        <b-form class="p-2" @submit.prevent="onSubmit" @reset.prevent="resetUserData">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert variant="danger" show v-if="errors[0]">
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <!-- Participant -->
          <validation-provider #default="{ errors }" name="Participant" vid="participant_id" rules="required">
            <b-form-group label="Participant" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.participant_id" :options="participant" :clearable="false" @input="selectedParticipant"
              :reduce="participant => participant.id" label="name" :filterable="false" @search="onSearch" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Email -->
          <validation-provider #default="{ errors }" name="Email" vid="email" rules="email">
            <b-form-group label="Email" label-for="email">
              <b-form-input v-model="formData.email" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Password -->
          <validation-provider #default="{ errors }" name="Password" vid="password" rules="required">
            <b-form-group label="Password">
              <b-input-group
                    class="input-group-merge"
                    :class="errors.length > 0 ? 'is-invalid':null">
                <b-form-input id="password" :type="passwordFieldType" v-model="formData.password" :state="errors.length > 0 ? false : null"
                trim />
                <b-input-group-append is-text>
                  <feather-icon
                    class="cursor-pointer"
                    :icon="passwordToggleIcon"
                    @click="togglePasswordVisibility"
                  />
                </b-input-group-append>
              </b-input-group>

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Whatsaap -->
          <validation-provider #default="{ errors }" name="Whatsaap" vid="whatsapp" rules="numeric">
            <b-form-group label="Whatsaap" label-for="whatsapp">
              <b-form-input v-model="formData.no_hp" :state="errors.length > 0 ? false : null" trim />

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
import { BSidebar, BForm, BFormGroup, BInputGroupAppend, BInputGroup, BFormInput, BFormTextarea, BFormFile, BFormInvalidFeedback, BButton, BAlert, BSpinner } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData, getParticipantSearch } from '@/network/mitra'
import flatPickr from 'vue-flatpickr-component'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import _ from 'lodash'

export default {
  components: {
    BSidebar,
    BForm,
    BFormGroup,
    BInputGroupAppend,
    BInputGroup,
    BFormInput,
    BFormTextarea,
    BFormFile,
    BAlert,
    BFormInvalidFeedback,
    BButton,
    BSpinner,
    vSelect,
    flatPickr,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  mixins: [togglePasswordVisibility],
  directives: {
    Ripple,
  },
  model: {
    prop: 'isAddSidebarActive',
    event: 'update:is-add-sidebar-active',
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    }
  },
  props: {
    isAddSidebarActive: {
      type: Boolean,
      required: true,
    },
    genderOptions: {
      type: Array,
      required: true,
    },
  },
  data() {
    const participant = []
    return {
      isButtonLoading: false,
      required,
      numeric,
      email,
      formData: {},
      participant,
    }
  },
  methods: {
    onSearch(search, loading) {
        if (search.length) {
            loading(true);
            this.search(loading, search, this);
        }
    },
    search: _.debounce((loading, search, vm) => {
        getParticipantSearch({ q: search })
            .then(res => {
                vm.participant = res.data
                loading(false)
            })
            .catch(error => {
                vm.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                loading(false)
            })
    }, 300),
    selectedParticipant(id) {
      let participant = this.participant.find(
          (participant) => participant.id == id
      );

      this.formData.email = participant.email
      this.formData.no_hp = participant.no_hp
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
        const vForm = new FormData()
        for (var key in this.formData) {
          vForm.append(key, this.formData[key])
        }
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
    },
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
