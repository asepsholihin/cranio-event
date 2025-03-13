<template>
  <b-card>
    <p class="pt-1">
        <h3 class="mb-2">Biodata</h3>
        
        <b-media class="mb-2">
          <template #aside>
            <b-avatar :src="formData.profile_thumbnail" :text="avatarText(formData.name)" size="90px" rounded />
          </template>
          <h4 class="mb-1">
            {{ formData.name }}
          </h4>
          <p>{{ formData.email }}<br>{{ formData.no_hp }}</p>
        </b-media>

        <div class="mt-5">
          <h3>Riwayat Transaksi</h3>
          
          <b-table ref="refTransactionListTable" class="position-relative" :items="fetchTransactions" responsive hover
            :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
            :sort-desc.sync="isSortDirDesc">
            <!-- Column: totalsalespax -->
            <template #cell(fee)="data">
              <span class="text-nowrap">Rp. {{ Number(data.item.fee).toLocaleString() }}</span>
            </template>

            <template #cell(created_at)="data">
              <span class="text-nowrap">{{ formatDateTime(data.item.created_at) }}</span>
            </template>
          </b-table>
        </div>
    </p>
  </b-card>
</template>

<script>
import { BTab, BTabs, BCard, BLink, BTable, BFormInvalidFeedback, BButton, BMedia, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BInputGroupAppend, BInputGroup, BFormInput, BFormTextarea, BFormFile } from 'bootstrap-vue'
import { getDetail, postData, getParticipantSearch } from '@/network/mitra'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { avatarText } from '@core/utils/filter'
import { hasPermission } from '@/auth/utils'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import _ from 'lodash'
import { formatDateTime, formatDate } from '@core/utils/filter'
import useTransactionList from './useTransactionList'

export default {
  components: {
    BCard,
    BTab,
    BTabs,
    BLink,
    BTable,
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
    BInputGroupAppend,
    BFormInput,
    BInputGroup,
    BFormTextarea,
    BFormFile,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  mixins: [togglePasswordVisibility],
  directives: {
    Ripple,
  },
  setup() {
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
    } = useTransactionList()

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
      formatDateTime
    }
  },
  methods: {
    inputImageRenderer() {
      this.formData.photo = this.$refs.refInputEl.files[0]
      const file = this.$refs.refInputEl.files[0]
      const reader = new FileReader()

      reader.addEventListener(
        'load',
        () => {
          this.formData.profile_thumbnail = reader.result
        },
        false,
      )

      if (file) {
        reader.readAsDataURL(file)
      }
    },
    onSubmit() {
      this.$refs.refObsForm.validate().then(success => {
        if (!success) return
        const vForm = new FormData()
        for (var key in this.formData) {
          if (key == 'profile_thumbnail')
            continue
          if (this.formData[key] != null)
            vForm.append(key, this.formData[key])
        }
        postData(vForm).then(response => {
          this.$bvToast.toast('Mitra has been changed successfully', {
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
  },
  data() {
    const participant = []
    const formData = {}
    const id = parseInt(this.$route.params.id) || 0
    if (id == 0) this.$router.back()

    getDetail(id).then(response => {
      this.formData = response.data
    }).catch(error => {
      if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
      } else {
        this.$refs.refObsForm.setErrors(error.response.data)
      }
    })

    return {
      formData, required, numeric, email, participant,
    }
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    }
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
