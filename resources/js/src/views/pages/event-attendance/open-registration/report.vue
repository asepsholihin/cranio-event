<template>
    <div>
        <b-card>
            <h3 class="text-default">{{ event.name }} - {{ event.description }}</h3>
            <h4>{{ formatDate(event.event_date) }}</h4>
            <h4>Total Check In: <span class="align-text-top">{{ event.total_checkin }} of {{ event.total_attendance
            }}</span></h4>

            <!-- Table Top -->
            <div>
                <b-row class="mt-5">
                    <b-col cols="12" md="6">
                        <apexchart type="donut" :options="chartOptions" :series="chartSeries" />
                    </b-col>
                    <b-col cols="12" md="6">
                        <apexchart type="donut" :options="chartParticipantOptions" :series="chartParticipantSeries" />
                    </b-col>
                </b-row>

                <b-row class="mt-5">
                    <b-col cols="12" md="6">
                        <h4>Sudah Absen</h4>
                        <ul class="list-unstyled">
                            <li v-for="(value, ij) in participantAttendeeList" :key="ij">
                                {{ ij + 1 }}. {{ value.name }} ({{value.actual_pax}} of {{value.pax}} Pax) - {{ value.no_hp }}
                            </li>
                        </ul>
                    </b-col>
                    <b-col cols="12" md="6">
                        <h4>Belum Absen</h4>
                        <ul class="list-unstyled">
                            <li v-for="(value, i) in participantUnAttendeeList" :key="i">
                                {{ i + 1 }}. {{ value.name }} ({{value.pax}} Pax) - {{ value.no_hp }}
                            </li>
                        </ul>
                    </b-col>
                </b-row>
            </div>

        </b-card>
    </div>
</template>

<script>
import { BCard, BAvatar, BMedia, BRow, BLink, BDropdown, BDropdownItem, BPagination, BTable, BCol, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BAlert, BSpinner, BInputGroup, BInputGroupAppend, BFormCheckboxGroup } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email, min } from '@validations'
import vSelect from 'vue-select'
import { getDetail, getChartAttendance, getChartParticipant } from '@/network/event-open-registration'
import flatPickr from 'vue-flatpickr-component'
import useAttendeeList from './useAttendeeList'
import { hasPermission } from '@/auth/utils'
import { avatarText, formatDateTime, formatDate } from '@core/utils/filter'
import { ref } from '@vue/composition-api'
import { until } from '@vueuse/core'

export default {
    components: {
        BRow,
        BMedia,
        BAvatar,
        BPagination,
        BDropdownItem,
        BDropdown,
        BLink,
        BCol,
        BTable,
        BCard,
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

        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    setup() {
        return {
            avatarText, formatDateTime, formatDate,
            hasPermission, eventId: ""
        }
    },
    data() {
        const event = {}
        const chartOptions = {}
        const chartSeries = []
        const chartParticipantOptions = {}
        const chartParticipantSeries = []
        const chartUnconfirmedOptions = {}
        const chartUnconfirmedSeries = []
        const chartConfirmedOptions = {}
        const chartConfirmedSeries = []
        const participantAttendeeList = []
        const participantUnAttendeeList = []

        const id = this.$route.params.id || ""
        if (id == "") this.$router.back()
        this.eventId = id
        getDetail(id).then(response => {
            this.event = response.data
            this.getChartData()
        }).catch(error => { })
        const refreshDataPage = () => {
            this.refetchData()
            getDetail(this.eventId).then(response => {
                this.event = response.data
            }).catch(error => { })
        }
        return {
            refreshDataPage,
            required,
            min,
            numeric,
            email,
            event,
            chartOptions,
            chartSeries,
            participantAttendeeList,
            participantUnAttendeeList,
            chartParticipantOptions,
            chartParticipantSeries,
        }
    },
    computed: {
    },
    methods: {
        getChartData() {
            getChartAttendance({ params: { eventId: this.eventId } }).then(response => {
                this.chartOptions = {
                    title: {
                        text: 'Total Absensi',
                        align: 'left'
                    },
                    chart: {
                        id: 'chart-attendance',
                        toolbar: {
                            show: true
                        }
                    },
                    labels: response.data.categories,
                }
                this.chartSeries = response.data.data

                response.data.participant.forEach(element => {
                    if (element.actual_pax > 0) {
                        this.participantAttendeeList.push(element)
                    } else {
                        this.participantUnAttendeeList.push(element)
                    }
                });
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            getChartParticipant({ params: { eventId: this.eventId } }).then(response => {
                this.chartParticipantOptions = {
                    title: {
                        text: 'Total Participant dan Non Participant',
                        align: 'left'
                    },
                    chart: {
                        id: 'chart-participant',
                        toolbar: {
                            show: true
                        }
                    },
                    labels: response.data.categories,
                }
                this.chartParticipantSeries = response.data.data
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })
        },
        filterChart() {
            this.getChartData()
        },
    },

}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
