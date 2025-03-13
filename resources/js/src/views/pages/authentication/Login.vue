<template>
  <div class="auth-wrapper auth-v2">
    <b-row class="auth-inner m-0">

      <!-- Left Text-->
      <b-col
        lg="4"
        class="d-none d-lg-flex align-items-center p-5"
      >
      </b-col>
      <!-- /Left Text-->

      <!-- Login-->
      <b-col
        lg="4"
        class="d-flex align-items-center auth-bg px-2 p-lg-5"
      >
        <b-col
          sm="8"
          md="6"
          lg="12"
          class="px-xl-2 mx-auto"
        >

    <center>
        <h2 class="brand-text text-primary mb-1">Sistem</h2>
        <b-img :src="logoApp" height="120" />
    </center>

          <!-- form -->
          <validation-observer
            ref="loginForm"
            #default="{invalid}"
          >
            <b-form
              class="auth-login-form mt-2"
              @submit.prevent="login"
            >

            <validation-provider #default="{ errors }" vid="message" >
                <b-alert variant="danger" show v-if="errors[0]">
                    <div class="alert-body">
                        {{errors[0]}}
                    </div>
                </b-alert>
            </validation-provider>
              <!-- email -->
              <b-form-group
                label="Email"
                label-for="login-email"
              >
                <validation-provider
                  #default="{ errors }"
                  name="Email"
                  vid="email"
                  rules="required"
                >
                  <b-form-input
                    id="login-email"
                    ref="email"
                    v-model="userEmail"
                    :state="errors.length > 0 ? false:null"
                    name="login-email"
                    placeholder=""
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- forgot password -->
              <b-form-group>
                <div class="d-flex justify-content-between">
                  <label for="login-password">Password</label>
                  <b-link :to="{name:'auth-forgot-password'}">
                    <small>Forgot Password?</small>
                  </b-link>
                </div>
                <validation-provider
                  #default="{ errors }"
                  name="Password"
                  vid="password"
                  rules="required"
                >
                  <b-input-group
                    class="input-group-merge"
                    :class="errors.length > 0 ? 'is-invalid':null"
                  >
                    <b-form-input
                      id="login-password"
                      v-model="password"
                      :state="errors.length > 0 ? false:null"
                      class="form-control-merge"
                      :type="passwordFieldType"
                      name="login-password"
                      placeholder="Password"
                    />
                    <b-input-group-append is-text>
                      <feather-icon
                        class="cursor-pointer"
                        :icon="passwordToggleIcon"
                        @click="togglePasswordVisibility"
                      />
                    </b-input-group-append>
                  </b-input-group>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- checkbox -->
              <b-form-group>
                <b-form-checkbox
                  id="remember-me"
                  v-model="rememberMe"
                  name="checkbox-1"
                >
                  Remember Me
                </b-form-checkbox>
              </b-form-group>

              <!-- submit buttons -->
              <b-button
                type="submit"
                variant="primary"
                block
                :disabled="invalid"
              > <b-spinner small v-show="isButtonLoading" />
                Sign in
              </b-button>
            </b-form>

          </validation-observer>

        </b-col>
      </b-col>
    <!-- /Login-->
    </b-row>
  </div>
</template>

<script>
/* eslint-disable global-require */
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import {
  BRow,
  BCol,
  BLink,
  BSpinner,
  BFormGroup,
  BFormInput,
  BInputGroupAppend,
  BInputGroup,
  BFormCheckbox,
  BCardText,
  BCardTitle,
  BImg,
  BForm,
  BButton,
  BAlert,
  VBTooltip,
} from 'bootstrap-vue'
import authService from '@/auth/service/'
import { required, email } from '@validations'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import { getHomeRouteForLoggedInUser, authUser } from '@/auth/utils'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
  directives: {
    'b-tooltip': VBTooltip,
  },
  components: {
    BRow,
    BCol,
    BLink,
    BSpinner,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BCardText,
    BCardTitle,
    BImg,
    BForm,
    BButton,
    BAlert,
    ValidationProvider,
    ValidationObserver,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      rememberMe: false,
      isButtonLoading: false,
      password: '',
      userEmail: '',
      logoApp: require('@/assets/images/logo/logo.png'),

      // validation rules
      required,
      email,
    }
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    }
  },
  methods: {
    login() {
      this.$refs.loginForm.validate().then(success => {
        if (success) {
            this.invalid = true
            this.isButtonLoading = true
            authService
                .login({
                    email: this.userEmail,
                    password: this.password,
                    remember: this.rememberMe,
                })
                .then(response => {
                    const user = response.data.authUser
                    localStorage.setItem(authUser, JSON.stringify(user))

                    this.$router.replace(getHomeRouteForLoggedInUser()).then(() => {
                        this.$toast({
                            component: ToastificationContent,
                            position: 'top-center',
                            props: {
                                title: `Assalamu'alaikum, `,
                                text: response.data.authUser.name,
                                icon: 'UserIcon',
                                variant: 'primary',
                            },
                        })
                    })
                })
                .catch(error => {
                    this.isButtonLoading = false
                    if (error.response.data.errors)
                        this.$refs.loginForm.setErrors(error.response.data.errors)
                    else
                        this.$refs.loginForm.setErrors(error.response.data)
                    this.$refs.email.focus()
                })
        }
      })
    },
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/pages/page-auth.scss';
</style>
