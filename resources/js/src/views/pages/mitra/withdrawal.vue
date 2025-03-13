<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">

        <!-- Table Top -->
        <b-row>

          <!-- Per Page -->
          <b-col cols="12" md="6" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
            <label>Show</label>
            <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
              class="per-page-selector d-inline-block mx-50" />
            <label>entries</label>
          </b-col>

          <!-- Search -->
          <b-col cols="12" md="6">
            <div class="d-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1" placeholder="Search..." />
              <b-button variant="primary" @click="formWithdrawModal = true" v-if="hasPermission('mitra-withdrawal-add-or-edit') && userData.is_mitra == 1">
                <span class="text-nowrap">Ajukan Pencairan Komisi</span>
              </b-button>
            </div>
          </b-col>
        </b-row>
      </div>

      <b-table ref="refTransactionListTable" class="position-relative" :items="fetchTransactions" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- Column: Title -->
        <template #cell(title)="data">
          <b-link :to="{ name: 'mitra-media-detail', params: { id: data.item.id } }"
            class="font-weight-bold d-block text-nowrap">
            {{ data.item.title }}
          </b-link>
        </template>

        <!-- Column: Date -->
        <template #cell(created_at)="data">
          {{ formatDateTime(data.item.created_at) }}
        </template>

        <!-- Column: Status -->
        <template #cell(status)="data">
          <b-badge pill :variant="`light-${resolveTransactionStatusVariant(data.item.status)}`" class="text-capitalize">
            {{ resolveTransactionStatus(data.item.status) }}
          </b-badge>
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

            <b-pagination v-model="currentPage" :total-rows="totalTransactions" :per-page="perPage" first-number last-number
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

    <b-modal v-model="formWithdrawModal" ok-title="Ajukan Pencarian" @hidden="resetModal" :ok-disabled="submitButtonDisabled"
      @ok="handleOkWithdraw" :busy="isSubmitLoading" centered no-close-on-backdrop ok-only
      :title="`Pengajuan Komisi`">

      <div class="mb-3">
          <b-form-checkbox v-model="concentAgree1" @change="concentCheck" value="true">Saya Setuju: <br /><b><i>Pengajuan akan di proses 3 x 24 jam kerja</i></b></b-form-checkbox><br />
          <b-form-checkbox v-model="concentAgree2" @change="concentCheck" value="true">Saya Setuju: <br /><b>Pengajuan pencairan akan di konfirmasi kembali oleh kantor pusat</b></b-form-checkbox>
      </div>
      
      <validation-observer ref="valWithdraw">
          <!-- Form -->
          <b-form class="p-2" @submit.prevent="onSubmitWithdraw">
              <validation-provider #default="{ errors }" vid="message">
                  <b-alert variant="danger" show v-if="errors[0]">
                      <div class="alert-body">
                          {{ errors[0] }}
                      </div>
                  </b-alert>
              </validation-provider>

              <validation-provider #default="{ errors }" name="Nominal" vid="fee" rules="numeric|required|min_value:1">
                  <b-form-group label="Nominal Komisi" description="Masukkan nominal komisi yang akan dicairkan" :state="errors.length > 0 ? false : null">
                      <cleave v-model="formData.amount" class="form-control" :options="optionClave"
                          :state="errors.length > 0 ? false : null" />

                      <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                          {{ errors[0] }}
                      </b-form-invalid-feedback>
                  </b-form-group>
              </validation-provider>

              <validation-provider #default="{ errors }" name="Payment Method" vid="payment_method" rules="required">
                  <b-form-group label="Payment Method" :state="errors.length > 0 ? false : null">
                      <v-select v-model="formData.payment_method" :options="paymentMethodOptions" :reduce="(payment) => payment.value" :clearable="false" />
                      <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                      {{ errors[0] }}
                      </b-form-invalid-feedback>
                  </b-form-group>
              </validation-provider>
          </b-form>
      </validation-observer>
  </b-modal>
  
  </div>
</template>

<script>
import {
  BCard,
  BRow,
  BCol,
  BButton,
  BTable,
  BMedia,
  BAvatar,
  BLink,
  BBadge,
  BDropdown,
  BDropdownItem,
  BPagination,
  BFormInvalidFeedback,
  BForm,
  BFormGroup,
  BInputGroupAppend,
  BInputGroup,
  BFormInput,
  BFormTextarea,
  BFormCheckbox
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import { postWithdraw } from '@/network/mitra'
import { ref } from '@vue/composition-api'
import { avatarText } from '@core/utils/filter'
import useWithdrawList from './useWithdrawList'
import { hasPermission, getUserData } from '@/auth/utils'
import { formatDateTime, formatDate } from '@core/utils/filter'
import vSelect from 'vue-select'
import Cleave from "vue-cleave-component";

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BButton,
    BTable,
    BMedia,
    BAvatar,
    BLink,
    BBadge,
    BDropdown,
    BDropdownItem,
    BPagination,
    BFormInvalidFeedback,
    BForm,
    BFormGroup,
    BInputGroupAppend,
    BInputGroup,
    BFormInput,
    BFormTextarea,
    BFormCheckbox,
    vSelect,
    Cleave,
    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  setup() {
    const paymentMethodOptions = [
        { label: 'Transfer Bank', value: 'transfer_bank' },
        { label: 'Cash', value: 'cash' },
        { label: 'e-Money', value: 'emoney' },
    ]
    const userData = getUserData()

    const {
      fetchTransactions,
      tableColumns,
      perPage,
      currentPage,
      totalTransactions,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refTransactionListTable,
      refetchData,

      // UI
      resolveTransactionStatusVariant,
      resolveTransactionStatus
    } = useWithdrawList()

    return {
      fetchTransactions,
      tableColumns,
      perPage,
      currentPage,
      totalTransactions,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refTransactionListTable,
      refetchData,
      avatarText, hasPermission,
      formatDateTime,
      paymentMethodOptions,
      userData,

      resolveTransactionStatusVariant,
      resolveTransactionStatus
    }
  },
  created() {

  },
  data() {
    return {
      concentAgree1: false, concentAgree2: false,
      required, numeric,
      optionClave: { numeral: true, numeralThousandsGroupStyle: "thousand" },
      formData: {},
      submitButtonDisabled: true,
      isSubmitLoading: false,
      formWithdrawModal: false
    }
  },
  methods : {
    resetModal() {
        this.concentAgree1 = false
        this.concentAgree2 = false
        this.concentCheck()
    },
    concentCheck() {
        if (this.concentAgree1 && this.concentAgree2) {
            this.submitButtonDisabled = false
            return
        }
        this.submitButtonDisabled = true
    },
    handleOkWithdraw(bvModalEvent) {
        bvModalEvent.preventDefault()
        self = this
        self.isSubmitLoading = true
        this.$refs.valWithdraw.validate().then(success => {
            if (!success) return
            postWithdraw(this.formData).then(response => {
                self.formWithdrawModal = false
                self.$swal({ icon: 'success', title: 'Success', text: `Pengajuan pencairan komisi berhasil di submit`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
                this.refetchData()
            }).catch(error => {
                self.isSubmitLoading = false
                if (error.response.data.errors) {
                    self.$refs.valWithdraw.setErrors(error.response.data.errors)
                } else {
                    self.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                }
            })
        });
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
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
