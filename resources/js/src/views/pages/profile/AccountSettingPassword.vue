<template>
  <b-card>
    <!-- form -->
    <validation-observer ref="obsVal" #default="{ invalid }">
      <b-form class="auth-login-form mt-2" @submit.prevent="changePassword">

        <validation-provider #default="{ errors }" vid="message">
          <b-alert variant="danger" show v-if="errors[0]">
            <div class="alert-body">
              {{ errors[0] }}
            </div>
          </b-alert>
        </validation-provider>
        <b-row>
          <!-- old password -->
          <b-col md="6">
            <b-form-group label="Current Password" label-for="account-old-password">
              <validation-provider #default="{ errors }" name="Current Password" vid="current_password"
                rules="required|min:7">
                <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid' : null">
                  <b-form-input id="account-old-password" v-model="current_password" name="current_password"
                    :state="errors.length > 0 ? false : null" :type="passwordFieldTypeOld"
                    placeholder="Current Password" />
                  <b-input-group-append is-text>
                    <feather-icon :icon="passwordToggleIconOld" class="cursor-pointer" @click="togglePasswordOld" />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <!--/ old password -->
        </b-row>
        <b-row>
          <!-- new password -->
          <b-col md="6">
            <b-form-group label-for="account-new-password" label="New Password">
              <validation-provider #default="{ errors }" name="New Password" vid="password" rules="required|min:7">
                <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid' : null">
                  <b-form-input id="account-new-password" v-model="password" :state="errors.length > 0 ? false : null"
                    :type="passwordFieldTypeNew" name="password" placeholder="New Password" />
                  <b-input-group-append is-text>
                    <feather-icon :icon="passwordToggleIconNew" class="cursor-pointer" @click="togglePasswordNew" />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <!--/ new password -->

          <!-- retype password -->
          <b-col md="6">
            <b-form-group label-for="account-retype-new-password" label="Retype New Password">
              <validation-provider #default="{ errors }" name="Retype New Password" vid="password_confirmation"
                rules="required|confirmed:password">
                <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid' : null">
                  <b-form-input id="account-retype-new-password" v-model="password_confirmation"
                    :state="errors.length > 0 ? false : null" :type="passwordFieldTypeRetype" name="password_confirmation"
                    placeholder="Retype New Password" />
                  <b-input-group-append is-text>
                    <feather-icon :icon="passwordToggleIconRetype" class="cursor-pointer"
                      @click="togglePasswordRetype" />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <!--/ retype password -->

          <!-- buttons -->
          <b-col cols="12">
            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mt-1 mr-1" type="submit"
              :disabled="invalid">
              <b-spinner small v-show="isButtonLoading" />
              Save changes
            </b-button>
            <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="reset" variant="outline-secondary" class="mt-1"
              @click="resetValidation">
              Reset
            </b-button>
          </b-col>
          <!--/ buttons -->

        </b-row>
      </b-form>
    </validation-observer>
  </b-card>
</template>

<script>
import {
  BButton, BForm, BFormGroup, BFormInput, BRow, BCol, BCard, BInputGroup, BInputGroupAppend, BSpinner
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import Ripple from 'vue-ripple-directive'
import { required, confirmed, min } from '@validations'
import { updatePassword } from '@/network/profile'

export default {
  components: {
    BButton,
    BSpinner,
    BForm,
    BFormGroup,
    BFormInput,
    BRow,
    BCol,
    BCard,
    BInputGroup,
    BInputGroupAppend,
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  data() {
    return {
      isButtonLoading: false,
      current_password: '',
      password: '',
      password_confirmation: '',
      passwordFieldTypeOld: 'password',
      passwordFieldTypeNew: 'password',
      passwordFieldTypeRetype: 'password',

      //validation
      required,
      confirmed,
      min
    }
  },
  computed: {
    passwordToggleIconOld() {
      return this.passwordFieldTypeOld === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    passwordToggleIconNew() {
      return this.passwordFieldTypeNew === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    passwordToggleIconRetype() {
      return this.passwordFieldTypeRetype === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
  },
  methods: {
    togglePasswordOld() {
      this.passwordFieldTypeOld = this.passwordFieldTypeOld === 'password' ? 'text' : 'password'
    },
    togglePasswordNew() {
      this.passwordFieldTypeNew = this.passwordFieldTypeNew === 'password' ? 'text' : 'password'
    },
    togglePasswordRetype() {
      this.passwordFieldTypeRetype = this.passwordFieldTypeRetype === 'password' ? 'text' : 'password'
    },
    changePassword() {
      this.$refs.obsVal.validate().then(success => {
        if (!success) return
        this.invalid = true
        this.isButtonLoading = true
        updatePassword({
          'current_password': this.current_password,
          'password': this.password,
          'password_confirmation': this.password_confirmation
        })
          .then(response => {
            this.current_password = ''
            this.password = ''
            this.password_confirmation = ''
            this.$refs.obsVal.reset()
            this.$bvToast.toast(`Your Password has been changed successfully`, {
              title: `Success`,
              variant: 'primary',
              toaster: 'b-toaster-top-center',
              solid: true,
            })
            this.isButtonLoading = false
          })
          .catch(error => {
            if (error.response.data.errors) {
              this.$refs.obsVal.setErrors(error.response.data.errors)
            }
            else {
              this.$refs.obsVal.setErrors(error.response.data)
            }
            this.isButtonLoading = false
          })
      })
    },
    resetValidation() {
      this.$refs.obsVal.reset()
    }
  },
}
</script>
