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
							<b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1"
								placeholder="Search..." />
						</div>
					</b-col>
				</b-row>

			</div>

			<b-table ref="refUserListTable" class="position-relative" :items="fetchUsers" responsive hover
				:fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty
				empty-text="No matching records found" :sort-desc.sync="isSortDirDesc">

				<!-- Column: PurchaseRequest No -->
				<template #cell(purchase_request_number)="data">
					{{ data.item.purchase_request_number }}<br>
					<b-button size="sm" variant="primary" @click="downloadPurchaseRequest(data.item.id)" class="mt-50 mr-50">
						<span class="text-nowrap">
							<feather-icon icon="DownloadCloudIcon" class="mr-25" /> PDF
						</span>
					</b-button>
				</template>

				<!-- Column: Name -->
				<template #cell(name)="data">
					{{ data.item.name }}<br>
					{{ data.item.no_hp }}
				</template>

				<!-- Column: Payment Status -->
				<template #cell(transaction_status)="data">
					<b-badge pill :variant="`light-${resolveTransactionStatusVariant(data.item.transaction_status)}`"
						class="text-capitalize">
						{{ data.item.transaction_status }}
					</b-badge>
				</template>

				<!-- Column: Total Payment -->
				<template #cell(total_payable)="data">
					Rp. {{ data.item.total_payable.toLocaleString() }}
				</template>

				<!-- Column: Transaction Date -->
				<template #cell(transaction_date)="data">
					{{ formatDate(data.item.transaction_date) }}
				</template>

				<!-- Column: Action -->
				<template #cell(actions)="data">
					<div>
						<span class="text-nowrap">
							<b-button size="sm" variant="outline-primary" pill @click="showTransactionDetail(data.item)">
								<span class="text-nowrap">Detail</span>
							</b-button>
							<b-button size="sm" variant="outline-danger" pill @click="deleteTransaction(data.item)" v-if="hasPermission('event-ticket-transaction-add-or-edit')">
								<span class="text-nowrap">Delete</span>
							</b-button>
						</span>
					</div>
				</template>

			</b-table>
			<div class="mx-2 mb-2">
				<b-row>

					<b-col cols="12" sm="6"
						class="d-flex align-items-center justify-content-center justify-content-sm-start">
						<span class="text-muted">Showing {{ dataMeta.from }} to {{ dataMeta.to }} of {{ dataMeta.of }}
							entries</span>
					</b-col>
					<!-- Pagination -->
					<b-col cols="12" sm="6" class="d-flex align-items-center justify-content-center justify-content-sm-end">

						<b-pagination v-model="currentPage" :total-rows="totalUsers" :per-page="perPage" first-number
							last-number class="mb-0 mt-1 mt-sm-0" prev-class="prev-item" next-class="next-item">
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

		<b-modal size="lg" v-model="detailTransactionModal" ok-title="Close"
            centered no-close-on-backdrop ok-only>
            <h5 class="text-primary">Transaction Detail {{ transactionDetail.transaction_id }}</h5>
            <b-row>
                <b-col sm="6">
                    Event Name:
					<p class="font-weight-bold">{{ transactionDetail.event_name }}</p>
                </b-col>
                <b-col sm="6">
                    Event Date :
					<p class="font-weight-bold">{{ transactionDetail.event_date }}</p>
                </b-col>
                <b-col sm="6">
                    Transaction Date :
					<p class="font-weight-bold">{{ formatDate(transactionDetail.transaction_date) }}</p>
                </b-col>
                <b-col sm="6">
                    Transaction Status :
					<p class="font-weight-bold">{{ transactionDetail.transaction_status }}</p>
                </b-col>
                <b-col sm="6">
                    Full Name :
					<p class="font-weight-bold">{{ transactionDetail.name }}</p>
                </b-col>
                <b-col sm="6">
                    Whatsapp :
					<p class="font-weight-bold">{{ transactionDetail.no_hp }}</p>
                </b-col>
                <b-col sm="6">
                    Email :
					<p class="font-weight-bold">{{ transactionDetail.email }}</p>
                </b-col>
                <b-col sm="6">
                    Alumni Jejak Imani :
					<p class="font-weight-bold">{{ (transactionDetail.is_alumni) ? 'Iya' : 'Bukan' }}</p>
                </b-col>
                <b-col sm="6">
                    Total Ticket :
					<p class="font-weight-bold">{{ transactionDetail.pax }} Tickets</p>
                </b-col>
                <b-col sm="6">
                    Amount :
					<p>Rp. {{ Number(transactionDetail.total_payable).toLocaleString() }}</p>
                </b-col>
                <b-col sm="6">
                    Email Status (Invoice) :
					<p class="font-weight-bold">{{ (transactionDetail.send_email_invoice == 1) ? `Sent` : 'Unavailable' }}</p>
                </b-col>
                <b-col sm="6">
                    QR Status
					<p class="font-weight-bold mb-0">{{ (transactionDetail.send_ticket == 1) ? `Sent` : 'Unavailable' }}</p>
					<b-button variant="primary" size="sm" v-if="transactionDetail.send_ticket == 1" @click="resendTicket(transactionDetail)">Resend</b-button>
                </b-col>
            </b-row>

            <hr>

            <h5 class="text-primary mb-2">Transaction Log</h5>
            <b-row v-for="(log, index) in transactionDetail.logs" :key="index">
                <b-col sm="6">
                    <p class="mb-1">{{ formatDateTime(log.created_at) }}</p>
                </b-col>
                <b-col sm="6">
                    <p class="mb-1">{{ log.transaction_status }}</p>
                </b-col>
            </b-row>
            <hr>

        </b-modal>
	</div>
</template>

<script>
import { BCard, BRow, BCol, BButton, BTable, BMedia, BAvatar, BLink, BBadge, BDropdown, BDropdownItem, BPagination, BForm, BFormInput, BFormGroup, BFormInvalidFeedback, BImg, BSpinner } from 'bootstrap-vue'
import vSelect from 'vue-select'
import Cleave from 'vue-cleave-component'
import flatPickr from 'vue-flatpickr-component'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, min_value, email } from '@validations'
import { formatDate, formatDateTime } from '@core/utils/filter'
import _ from 'lodash'
import useDataList from './useDataList'
import { hasPermission } from '@/auth/utils'
import { postData, deleteData, getDetail, resendTicket } from '@/network/event-ticket-transaction'
import Ripple from 'vue-ripple-directive'

export default {
	components: {
		vSelect, Cleave, flatPickr,
		BCard, BRow, BCol, BButton, BTable, BMedia, BAvatar, BLink, BBadge, BDropdown, BDropdownItem, BPagination,
		ValidationProvider, ValidationObserver, BForm, BFormInput, BFormGroup, BFormInvalidFeedback, BImg, BSpinner
	},
	directives: {
		Ripple,
	},
	setup() {

		const {
			fetchUsers,
			tableColumns,
			perPage,
			currentPage,
			totalUsers,
			dataMeta,
			perPageOptions,
			searchQuery,
			sortBy,
			isSortDirDesc,
			refUserListTable,
			refetchData,

			// UI
			resolveTransactionStatusVariant,

			// Extra Filters
			statusFilter,
		} = useDataList()

		return {
			fetchUsers,
			tableColumns,
			perPage,
			currentPage,
			totalUsers,
			dataMeta,
			perPageOptions,
			searchQuery,
			sortBy,
			isSortDirDesc,
			refUserListTable,
			refetchData,

			// UI
			resolveTransactionStatusVariant,
			formatDateTime,
			formatDate,

			// Extra Filters
			statusFilter,

			hasPermission,
		}
	},
	created() {

	},
	data() {
		return {
			required, numeric, min_value, email,
			isLoadingBusy: false,
			isButtonLoading: false,
			optionClave: {
				numeral: true,
				numeralThousandsGroupStyle: 'thousand',
			},
			detailTransactionModal: false,
			transactionDetail: {}
		}
	},
	methods: {
		showTransactionDetail(item) {
			this.detailTransactionModal = true
			getDetail(item.id).then(response => {
				this.transactionDetail = response.data
			}).catch(error => {
				if (error.response.data.errors) {
					this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
				} else {
					this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
				}
			})
		},
		resendTicket(item) {
			this.$swal({
				title: `Kirim ulang QRCode?`,
				text: "QRCode akan dikirim ulang ke whatsapp participant",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, kirim ulang',
				customClass: {
					confirmButton: 'btn btn-danger',
					cancelButton: 'btn btn-outline-primary ml-1',
				},
				buttonsStyling: false,
			}).then(result => {
				if (result.value) {
					const vForm = {}
					vForm['event_id'] = item.event_id
					vForm['no_hp'] = item.no_hp
					vForm['pax'] = item.pax
					resendTicket(vForm).then(response => {
						this.$swal({ icon: 'success', title: 'Success', text: `QRcode sudah dikirim ulang`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
					}).catch(error => {
						if (error.response.data.errors) {
							this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
						} else {
							this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
						}
					})
				}
			})
		},
		deleteTransaction(item) {
			this.$swal({
				title: `Delete PurchaseRequest ${item.purchase_request_number}?`,
				text: "It cannot be reverted",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, delete it!',
				customClass: {
					confirmButton: 'btn btn-danger',
					cancelButton: 'btn btn-outline-primary ml-1',
				},
				buttonsStyling: false,
			}).then(result => {
				if (result.value) {
					deleteData(item.id).then(response => {
						this.refetchData()
					})
				}
			})
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
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
