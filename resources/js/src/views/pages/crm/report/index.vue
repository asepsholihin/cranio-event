<template>
    <b-card>

        <b-tabs>
            <b-tab title="Transaksi Participant" active>
                <p class="pt-1">
                    <div class="mb-4">
                        <b-row>
                            <b-col cols="12" md="4">
                                <label>Filter Date</label>
                                <div class="input-group">
                                    <flat-pickr v-model="dateFilter" @on-change="getChartData" class="form-control" :config="{ altInput: true, mode: 'range' }" placeholder="Select date" />
                                    <div class="input-group-append">
                                    <button class="btn btn-danger" type="button" title="Clear" @click="clearDate">
                                        <feather-icon icon="Trash2Icon" />
                                    </button>
                                    </div>
                                </div>
                            </b-col>
                            <b-col cols="12" md="4">
                                <label>Filter Type</label>
                                <v-select v-model="perType" @input="getChartData" :options="['All Data', 'Data by sistem', 'Data Backdate']" :clearable="true" />
                            </b-col>
                        </b-row>
                    </div>
                    <b-row class="mb-2">
                        <b-col cols="12" class="mb-2">
                            <h4>Pertumbuhan Participant</h4>
                            <apexchart type="line" height="340" :options="chartParticipantGrowthOptions" :series="chartParticipantGrowthSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <div class="d-lg-flex justify-content-between mb-2">
                                <h4>Total Trip</h4>
                                <div>
                                    <b-dropdown size="sm" :disabled="isLoading" variant="gradient-primary" v-if="hasPermission('participant-crm-view')">
                                        <template #button-content>
                                            <b-spinner small v-show="isLoading" /> Lihat Participant
                                        </template>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTrip: 1 }}">
                                            1 Trip
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTrip: 2 }}">
                                            2 Trip
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTrip: 3 }}">
                                            3 Trip
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTrip: 4 }}">
                                            4 Trip
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTrip: 5 }}">
                                            5 Trip
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTrip: 6 }}">
                                            Lebih dari 5 Trip
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </div>
                            <apexchart type="pie" height="250" :options="chartTotalTripOptions" :series="chartTotalTripSeries"/>
                        </b-col>
                        <b-col cols="12" md="6"class="mb-2">
                            <div class="d-lg-flex justify-content-between mb-2">
                                <h4>Total Transaction</h4>
                                <div>
                                    <b-dropdown size="sm" :disabled="isLoading" variant="gradient-primary" v-if="hasPermission('participant-crm-view')">
                                        <template #button-content>
                                            <b-spinner small v-show="isLoading" /> Lihat Participant
                                        </template>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTransaction: 1 }}">
                                            Dibawah 100 Juta
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTransaction: 2 }}">
                                            100 Juta - 200 Juta
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTransaction: 3 }}">
                                            200 Juta - 300 Juta
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTransaction: 4 }}">
                                            300 Juta - 400 Juta
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTransaction: 5 }}">
                                            400 Juta - 500 Juta
                                        </b-dropdown-item>
                                        <b-dropdown-item :to="{ name: 'participant-crm', query: { totalTransaction: 6 }}">
                                            Lebih dari 500 Juta
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </div>
                            <apexchart type="pie" height="250" :options="chartTotalTransactionOptions" :series="chartTotalTransactionSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <h4>Package</h4>
                            <apexchart type="bar" height="340" :options="chartPackageOptions" :series="chartPackageSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <h4>10 Keberangkatan Terbanyak</h4>
                            <apexchart type="bar" height="340" :options="chartTripOptions" :series="chartTripSeries"/>
                        </b-col>
                    </b-row>
                </p>
            </b-tab>
            <b-tab title="Demografis">
                <p class="pt-1">
                    <div class="mb-4">
                        <b-row>
                            <b-col cols="12" md="4">
                                <label>Filter Year</label>
                                <v-select v-model="yearFilter" :options="yearOptions" @input="getChartDemografis" :reduce="val => val.value" placeholder="Select year" />
                            </b-col>
                        </b-row>
                    </div>
                    <b-row class="mb-2">
                        <b-col cols="12" md="6" class="mb-2">
                            <h4>Gender</h4>
                            <apexchart type="donut" height="340" :options="chartGenderOptions" :series="chartGenderSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <h4>Usia</h4>
                            <apexchart type="donut" height="340" :options="chartAgeOptions" :series="chartAgeSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <h4>Pendidikan</h4>
                            <apexchart type="donut" height="340" :options="chartEducationOptions" :series="chartEducationSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <h4>Pekerjaan</h4>
                            <apexchart type="donut" height="340" :options="chartJobOptions" :series="chartJobSeries"/>
                        </b-col>
                    </b-row>
                </p>
            </b-tab>
            <b-tab title="Geografis">
                <p class="pt-1">
                    <b-row class="mb-2">
                        <b-col cols="12" md="6" class="mb-2">
                            <h4>10 Kota Terbanyak</h4>
                            <apexchart type="bar" height="340" :options="chartCityOptions" :series="chartCitySeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <h4>10 Provinsi Terbanyak</h4>
                            <apexchart type="bar" height="340" :options="chartProvinceOptions" :series="chartProvinceSeries"/>
                        </b-col>
                    </b-row>
                </p>
            </b-tab>
            <b-tab title="Lain-lain">
                <p class="pt-1">
                    <b-row class="mb-2">
                        <b-col cols="12" md="6" class="mb-2">
                            <div class="d-lg-flex justify-content-between mb-2">
                                <h4>Pemilik Akun</h4>
                                <div>
                                    <b-dropdown size="sm" :disabled="isLoading" text="Tarik Data" variant="gradient-primary" v-if="hasPermission('participant-crm-view')">
                                        <template #button-content>
                                            <b-spinner small v-show="isLoading" /> Tarik Data
                                        </template>
                                        <b-dropdown-item @click="exportParticipant('parent_account')">
                                            Pemilik Akun
                                        </b-dropdown-item>
                                        <b-dropdown-item @click="exportParticipant('not_parent_account')">
                                            Bukan
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </div>
                            <apexchart type="donut" height="340" :options="chartHasAccountOptions" :series="chartHasAccountSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <div class="d-lg-flex justify-content-between mb-2">
                                <h4>Memiliki No HP</h4>
                                <div>
                                    <b-dropdown size="sm" :disabled="isLoading" variant="gradient-primary" v-if="hasPermission('participant-crm-view')">
                                        <template #button-content>
                                            <b-spinner small v-show="isLoading" /> Tarik Data
                                        </template>
                                        <b-dropdown-item @click="exportParticipant('has_phone')">
                                            Memiliki No HP
                                        </b-dropdown-item>
                                        <b-dropdown-item @click="exportParticipant('has_not_phone')">
                                            Tidak
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </div>
                            <apexchart type="donut" height="340" :options="chartHasPhoneOptions" :series="chartHasPhoneSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <div class="d-lg-flex justify-content-between mb-2">
                                <h4>Memiliki Instagram</h4>
                                <div>
                                    <b-dropdown size="sm" :disabled="isLoading" variant="gradient-primary" v-if="hasPermission('participant-crm-view')">
                                        <template #button-content>
                                            <b-spinner small v-show="isLoading" /> Tarik Data
                                        </template>
                                        <b-dropdown-item @click="exportParticipant('has_instagram')">
                                            Memiliki Instagram
                                        </b-dropdown-item>
                                        <b-dropdown-item @click="exportParticipant('has_not_instagram')">
                                            Tidak
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </div>
                            <apexchart type="donut" height="340" :options="chartHasInstagramOptions" :series="chartHasInstagramSeries"/>
                        </b-col>
                        <b-col cols="12" md="6" class="mb-2">
                            <div class="d-lg-flex justify-content-between mb-2">
                                <h4>Memiliki LinkedIn</h4>
                                <div>
                                    <b-dropdown size="sm" :disabled="isLoading" variant="gradient-primary" v-if="hasPermission('participant-crm-view')">
                                        <template #button-content>
                                            <b-spinner small v-show="isLoading" /> Tarik Data
                                        </template>
                                        <b-dropdown-item @click="exportParticipant('has_linkedin')">
                                            Memiliki LinkedIn
                                        </b-dropdown-item>
                                        <b-dropdown-item @click="exportParticipant('has_not_linkedin')">
                                            Tidak
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </div>
                            <apexchart type="donut" height="340" :options="chartHasLinkedinOptions" :series="chartHasLinkedinSeries"/>
                        </b-col>
                        <b-col cols="6" class="mb-2">
                            <h4>Lain lain</h4>
                            <apexchart type="donut" height="340" :options="chartOtherOptions" :series="chartOtherSeries"/>
                        </b-col>
                    </b-row>
                </p>
            </b-tab>

            <b-tab title="Repetisi">
                <p class="pt-1">
                    <div class="mb-4">
                        <b-row>
                            <b-col cols="12" md="4">
                                <label>Filter by number of trips</label>
                                <v-select v-model="numberSeatsFilter" placeholder="please select number of trips"
                                @input="getRepetisi" :options="totalTrips" :reduce="val => val.key" label="name" :clearable="true" />
                            </b-col>
                            <b-col cols="12" md="4">
                                <label>Filter By Year</label>
                                <v-select v-model="multipleYearFilter" placeholder="please select year"
                                @input="changeRepetisi" multiple :options="yearOptions" :clearable="true" />
                            </b-col>
                        </b-row>
                    </div>
                    <b-row class="mb-2">
                        <b-col cols="12" md="12" class="mb-2">
                            <h4>Repetisi</h4>
                            <apexchart type="bar" height="440" :options="chartReputasiOptions" :series="chartReputasiSeries"/>
                        </b-col>
                    </b-row>
                </p>
            </b-tab>
            <b-tab title="Trend Participant">
                <p class="pt-1">
                    <div class="mb-4">
                        <b-row>
                            <b-col cols="12" md="4">
                                <label>Filter by Product Category</label>
                                <v-select v-model="productTrendFilter" placeholder="please select product category"
                                :options="productTrendOptions" @input="getTrendTabsParticipant" :reduce="val => val.id" label="name" :clearable="true" />
                            </b-col>
                            <b-col cols="12" md="4">
                                <label>Filter By Package</label>
                                <v-select v-model="packageTrendFilter" placeholder="please select package"
                                :options="packageFilterOptions" @input="getTrendTabsParticipant" :clearable="true" />
                            </b-col>
                            <b-col cols="12" md="4">
                                <label>Filter By Year</label>
                                <v-select v-model="yearTrendFilter" placeholder="please select year"
                                :options="yearOptions" @input="changeYearTrend" multiple :clearable="true" />
                            </b-col>
                            <b-col cols="12" class="mt-2" md="4">
                                <label>Filter By Data Source</label>
                                <v-select v-model="dataSource" placeholder="please select package"
                                :options="[{id:1, label: 'All'}, {id:2, label: 'Data Sistem'}, {id:3, label: 'Data Backdate'}]" @input="getTrendTabsParticipant" :reduce="val => val.id" :clearable="true" />
                            </b-col>
                        </b-row>
                    </div>
                    <b-row class="mb-2">
                        <b-col cols="12" md="12" class="mb-2" style="position:relative">
                            <h4>Trend Repeat Participant dan Total Participant per Tahun</h4>
                            <apexchart type="bar" height="440" :options="chartTrendParticipantOptions" :series="chartTrendParticipantSeries"
                                @dataPointMouseEnter="onBarHover"
                                @dataPointMouseLeave="onBarMouseLeave" />

                            <div v-if="tooltipVisible"
                                :style="{ top: tooltipPosition.top + 'px', left: tooltipPosition.left + 'px' }"
                                class="custom-tooltip shadow-lg card card-body" >
                                <h5><strong>Summary of {{ (tooltipData.name) ? tooltipData.name : '' }} (Total)</strong></h5>
                                <div class="d-flex justify-content-between" v-for="(item, index) in tooltipData.value">
                                    <h5>{{ item.name }}</h5>
                                    <h5 v-if="tooltipData.series == 0">{{ item.count }}</h5>
                                    <h5 v-else>{{ item.count_repeat }}</h5>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="12" md="12" class="mb-2" style="position: relative;">
                            <h4>Trend Paket Pilihan Participant per Tahun</h4>
                            <apexchart type="bar" height="440" :options="chartTrendPackageOptions" :series="chartTrendPackageSeries"
                            @dataPointMouseEnter="onBarHover"
                            @dataPointMouseLeave="onBarMouseLeave" />

                            <div v-if="tooltipVisiblePackage"
                                :style="{ top: tooltipPosition.top + 'px', left: tooltipPosition.left + 'px' }"
                                class="custom-tooltip shadow-lg card card-body" >
                                <h5><strong>Summary of {{ (tooltipDataPackage.name) ? tooltipDataPackage.name : '' }} (Total)</strong></h5>
                                <div class="d-flex justify-content-between" v-for="(item, index) in tooltipDataPackage.value">
                                    <h5>{{ item.name }}</h5>
                                    <h5>{{ item.count }}</h5>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="12" md="12" class="mb-2" style="position: relative;">
                            <h4>Pertumbuhan Participant Jejak Imani</h4>
                            <apexchart type="bar" height="440" :options="chartGrowthOptions" :series="chartGrowthSeries"/>

                            <div v-if="tooltipVisibleG"
                                :style="{ top: tooltipPosition.top + 'px', left: tooltipPosition.left + 'px' }"
                                class="custom-tooltip shadow-lg card card-body" >
                                <h5><strong>Summary of {{ (tooltipDataG.name) ? tooltipDataG.name : '' }} (Total)</strong></h5>
                                <div class="d-flex justify-content-between" v-for="(item, index) in tooltipDataG.value">
                                    <h5>{{ item.name }}</h5>
                                    <h5>{{ item.count }}</h5>
                                </div>
                            </div>
                        </b-col>
                        <b-col cols="12" md="12" class="mb-2">
                            <h4>Trend Participant Berdasarkan Product Category</h4>
                            <apexchart type="bar" height="440" :options="chartTrendProductOptions" :series="chartTrendProductSeries"/>
                            <div v-if="tooltipVisibleProduct"
                                :style="{ top: tooltipPosition.top + 'px', left: tooltipPosition.left + 'px' }"
                                class="custom-tooltip shadow-lg card card-body" >
                                <h5><strong>Summary of {{ (tooltipDataProduct.name) ? tooltipDataProduct.name : '' }} (Total)</strong></h5>
                                <div class="d-flex justify-content-between" v-for="(item, index) in tooltipDataProduct.value">
                                    <h5>{{ item.name }}</h5>
                                    <h5>{{ item.count }}</h5>
                                </div>
                            </div>
                        </b-col>
                    </b-row>
                </p>
            </b-tab>
        </b-tabs>

    </b-card>
</template>

<script>
    import {
        BTab,
        BTabs,
        BCard,
        BRow,
        BCol,
        BFormInput,
        BButton,
        BTable,
        BMedia,
        BAvatar,
        BLink,
        BBadge,
        BDropdown,
        BDropdownItem,
        BPagination,
        BSpinner
    } from 'bootstrap-vue'
    import { hasPermission } from '@/auth/utils'
    import {
        getChartTotalTrip, getChartTotalTransaction, getChartPackage, getChartTopTrip,
        getChartGender, getChartAge, getChartJob, getChartEducation, getChartTopCity, getChartTopProvince, getChartOther,
        getChartHasAccount, getChartHasPhone, getChartHasInstagram, getChartHasLinkedin, getChartParticipantGrowth, getChartRepetisi,
        getTrendParticipant, getTrendPackage, getTrendGrowth, getTrendProduct
    } from '@/network/crm-report'
    import { exportParticipant } from '@/network/crm-participant'
    import flatPickr from 'vue-flatpickr-component'
    import vSelect from 'vue-select'
    import { getCategorySearch } from "@/network/catalog";

    export default {
        components: {
            BTab,
            BTabs,
            BCard,
            BRow,
            BCol,
            BFormInput,
            BButton,
            BTable,
            BMedia,
            BAvatar,
            BLink,
            BBadge,
            BDropdown,
            BDropdownItem,
            BPagination,
            BSpinner,
            flatPickr,
            vSelect,
        },
        setup() {

        let max = new Date().getFullYear() + 1
        const yearOptions = []
        let min = 2019
        for (let i = max; i >= min; i--) {
        let year = { 'label': i, 'value': i }
            yearOptions.push(year)
        }

        return {
            hasPermission, yearOptions
        }
        },
        data() {
            const chartTotalTripOptions = {}
            const chartTotalTripSeries = []
            const chartTotalTransactionOptions = {}
            const chartTotalTransactionSeries = []
            const chartPackageOptions = {}
            const chartReputasiOptions = {}
            const chartPackageSeries = []
            const chartReputasiSeries = []
            const chartTripOptions = {}
            const chartTripSeries = []
            const chartGenderOptions = {}
            const chartGenderSeries = []
            const chartAgeOptions = {}
            const chartAgeSeries = []
            const chartJobOptions = {}
            const chartJobSeries = []
            const chartEducationOptions = {}
            const chartEducationSeries = []
            const chartCityOptions = {}
            const chartCitySeries = []
            const chartProvinceOptions = {}
            const chartProvinceSeries = []
            const chartOtherOptions = {}
            const chartOtherSeries = []
            const chartHasAccountOptions = {}
            const chartHasAccountSeries = []
            const chartHasPhoneOptions = {}
            const chartHasPhoneSeries = []
            const chartHasInstagramOptions = {}
            const chartHasInstagramSeries = []
            const chartHasLinkedinOptions = {}
            const chartHasLinkedinSeries = []
            const chartParticipantGrowthOptions = {}
            const chartParticipantGrowthSeries = []

            this.getChartData()
            this.getChartDemografis()
            this.getRepetisi()
            this.getTrendTabsParticipant()

            getChartTopCity().then(response => {
                this.chartCityOptions = {
                    chart: {
                        id: 'chart-city',
                    },
                    colors: ["#8c0095"],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: response.data.categories,
                    },
                    labels: response.data.categories,
                }
                this.chartCitySeries = response.data.data
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            getChartTopProvince().then(response => {
                this.chartProvinceOptions = {
                    chart: {
                        id: 'chart-province',
                    },
                    colors: ["#8c0095"],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: response.data.categories,
                    },
                    labels: response.data.categories,
                }
                this.chartProvinceSeries = response.data.data
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            getChartOther().then(response => {
                this.chartOtherOptions = {
                    chart: {
                        id: 'chart-other',
                        toolbar: {
                            show: true
                        }
                    },
                    dataLabels: {
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                    labels: response.data.categories,
                }
                this.chartOtherSeries = response.data.data
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            getChartHasAccount().then(response => {
                this.chartHasAccountOptions = {
                    chart: {
                        id: 'chart-has-account',
                        toolbar: {
                            show: true
                        }
                    },
                    colors: ["#8c0095","#000000"],
                    dataLabels: {
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                    labels: response.data.categories,
                }
                this.chartHasAccountSeries = response.data.data
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            getChartHasPhone().then(response => {
                this.chartHasPhoneOptions = {
                    chart: {
                        id: 'chart-has-phone',
                        toolbar: {
                            show: true
                        }
                    },
                    colors: ["#8c0095","#000000"],
                    dataLabels: {
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                    labels: response.data.categories,
                }
                this.chartHasPhoneSeries = response.data.data
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            getChartHasInstagram().then(response => {
                this.chartHasInstagramOptions = {
                    chart: {
                        id: 'chart-has-phone',
                        toolbar: {
                            show: true
                        }
                    },
                    colors: ["#8c0095","#000000"],
                    dataLabels: {
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                    labels: response.data.categories,
                }
                this.chartHasInstagramSeries = response.data.data
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            getChartHasLinkedin().then(response => {
                this.chartHasLinkedinOptions = {
                    chart: {
                        id: 'chart-has-phone',
                        toolbar: {
                            show: true
                        }
                    },
                    colors: ["#8c0095","#000000"],
                    dataLabels: {
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                    labels: response.data.categories,
                }
                this.chartHasLinkedinSeries = response.data.data
            }).catch(error => {
                this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
            })

            const totalTrips = [
                { name: '1 Trip', key: 1 },
                { name: '2 Trip', key: 2 },
                { name: '3 Trip', key: 3 },
                { name: '4 Trip', key: 4 },
                { name: '5 Trip', key: 5 },
                { name: 'Lebih Dari 5 Trip', key: 6 },
            ]

            const productTrendOptions = []

            const packageFilterOptions = []

            getCategorySearch().then(response => {
                this.productTrendOptions = response.data;
            }).catch(error => {
                if (error.response.data.errors) {
                    this.$refs.refObsForm.setErrors(error.response.data.errors)
                } else {
                    this.$refs.refObsForm.setErrors(error.response.data)
                }
            })

            return {
                chartTotalTripOptions,
                chartTotalTripSeries,
                chartTotalTransactionOptions,
                chartTotalTransactionSeries,
                chartPackageOptions,
                chartReputasiOptions,
                chartPackageSeries,
                chartReputasiSeries,
                chartTripOptions,
                chartTripSeries,
                chartGenderOptions,
                chartGenderSeries,
                chartAgeOptions,
                chartAgeSeries,
                chartJobOptions,
                chartJobSeries,
                chartEducationOptions,
                chartEducationSeries,
                chartCityOptions,
                chartCitySeries,
                chartProvinceOptions,
                chartProvinceSeries,
                chartOtherOptions,
                chartOtherSeries,
                chartHasAccountOptions,
                chartHasAccountSeries,
                chartHasPhoneOptions,
                chartHasPhoneSeries,
                chartHasInstagramOptions,
                chartHasInstagramSeries,
                chartHasLinkedinOptions,
                chartHasLinkedinSeries,
                chartParticipantGrowthOptions,
                chartParticipantGrowthSeries,
                isLoading: false,
                dateFilter: "",
                yearFilter: "",
                perType: "All",
                numberSeatsFilter:'',
                multipleYearFilter:[],
                totalTrips,
                // Trend
                chartTrendParticipantOptions:{},
                chartTrendPackageOptions:{},
                chartGrowthOptions:{},
                chartTrendProductOptions:{},

                // Trend Series
                chartTrendParticipantSeries:[],
                chartTrendPackageSeries:[],
                chartGrowthSeries:[],
                chartTrendProductSeries:[],

                // Option Trend
                productTrendOptions,
                packageFilterOptions,
                productTrendFilter:'',
                packageTrendFilter:'',
                yearTrendFilter:'',
                dataSource: '',

                // toltip
                tooltipVisible: false,
                tooltipPosition: { top: 0, left: 0 },
                tooltipData: null,
                summaryTrenParticipant:[],

                // toltip package
                tooltipVisiblePackage: false,
                tooltipDataPackage: null,
                summaryTrenPackage:[],

                // toltip growth
                tooltipVisibleG: false,
                tooltipDataG: null,
                summaryTrenG:[],

                // toltip product
                tooltipVisibleProduct: false,
                tooltipDataProduct: null,
                summaryTrenProduct:[]

            }
        },
        methods : {
            clearDate() {
                this.dateFilter = null
            },
            getChartData() {
                getChartParticipantGrowth({params: {date: this.dateFilter, type: this.perType}}).then(response => {
                    var labels = []
                    var data = []
                    response.data.forEach(element => {
                        labels.push(element.title)
                        data.push(element.pax)
                    });

                    this.chartParticipantGrowthOptions = {
                        chart: {
                            id: 'chart-participant-growth',
                        },
                        dataLabels: {
                            enabled: true,
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        colors: ['#8c0095'],
                        labels: labels,
                    }
                    this.chartParticipantGrowthSeries = [{
                        name: "Total Pax",
                        data: data
                    }]
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getChartTotalTrip({params: {date: this.dateFilter, type: this.perType}}).then(response => {
                    this.chartTotalTripOptions = {
                        chart: {
                            id: 'chart-total-trip',
                            toolbar: {
                                show: true
                            }
                        },
                        labels: response.data.categories,
                    }
                    this.chartTotalTripSeries = response.data.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getChartTotalTransaction({params: {date: this.dateFilter, type: this.perType}}).then(response => {
                    this.chartTotalTransactionOptions = {
                        chart: {
                            id: 'chart-total-transaction',
                            toolbar: {
                                show: true
                            }
                        },
                        labels: response.data.categories,
                    }
                    this.chartTotalTransactionSeries = response.data.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getChartPackage({params: {date: this.dateFilter, type: this.perType}}).then(response => {
                    this.chartPackageOptions = {
                        chart: {
                            id: 'chart-package',
                        },
                        colors: ["#8c0095"],
                        labels: response.data.categories,
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    position: "top"
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -30,
                            style: {
                                fontSize: "14px",
                                colors: ["#304758"]
                            },
                            background: {
                                enabled: true,
                                foreColor: '#fff'
                            }
                        },
                    }
                    this.chartPackageSeries = response.data.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getChartTopTrip({params: {date: this.dateFilter, type: this.perType}}).then(response => {
                    this.chartTripOptions = {
                        chart: {
                            id: 'chart-trip',
                        },
                        colors: ["#8c0095"],
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                horizontal: true,
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: response.data.categories,
                        },
                        labels: response.data.categories,
                    }
                    this.chartTripSeries = response.data.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })
            },
            getChartDemografis() {
                getChartGender({params: {year: this.yearFilter, type: this.perType}}).then(response => {
                    this.chartGenderOptions = {
                        chart: {
                            id: 'chart-gender',
                            toolbar: {
                                show: true
                            }
                        },
                        labels: response.data.categories,
                    }
                    this.chartGenderSeries = response.data.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getChartAge({params: {year: this.yearFilter}}).then(response => {
                    this.chartAgeOptions = {
                        chart: {
                            id: 'chart-age',
                            toolbar: {
                                show: true
                            }
                        },
                        labels: response.data.categories,
                    }
                    this.chartAgeSeries = response.data.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getChartJob({params: {year: this.yearFilter}}).then(response => {
                    this.chartJobOptions = {
                        chart: {
                            id: 'chart-job',
                            width: '100%',
                            toolbar: {
                                show: true
                            },
                            responsive: [
                                {
                                    breakpoint: 550,
                                    options: {
                                            chart: {
                                            width: '100%' // Ensures responsiveness on smaller screens
                                        }
                                    }
                                }
                            ]
                        },
                        labels: response.data.categories,
                    }
                    this.chartJobSeries = response.data.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getChartEducation({params: {year: this.yearFilter}}).then(response => {
                    this.chartEducationOptions = {
                        chart: {
                            id: 'chart-education',
                            toolbar: {
                                show: true
                            }
                        },
                        labels: response.data.categories,
                    }
                    this.chartEducationSeries = response.data.data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })
            },
            changeRepetisi(){
                if(this.multipleYearFilter == ''){
                    this.getRepetisi()
                }else{
                    if(this.multipleYearFilter){
                        if(this.multipleYearFilter.length > 2){
                            this.multipleYearFilter.splice(0, 1)
                            this.multipleYearFilter.splice(0, 1)
                        }
                        if(this.multipleYearFilter.length == 2){
                            this.getRepetisi()
                        }
                        if(this.multipleYearFilter.length == 1){
                            this.getRepetisi()
                        }
                    }else{
                        this.getRepetisi()
                    }
                }
            },
            getRepetisi(){
                getChartRepetisi({params: { number_seats: this.numberSeatsFilter, years: this.multipleYearFilter }}).then(response => {
                    this.chartReputasiOptions = {
                        chart: {
                            id: 'chart-repetisi',
                        },
                        legend: {
                            position: 'top'
                        },
                        colors: ["#344CB7", "#8c0095"],
                        labels: response.data.categories,
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    position: "top"
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -30,
                            style: {
                                fontSize: "14px",
                                colors: ["#304758"]
                            },
                            background: {
                                enabled: true,
                                foreColor: '#fff'
                            }
                        },
                    }
                    this.chartReputasiSeries = response.data.data[0].data
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })
            },
            changeYearTrend(){
                if(this.yearTrendFilter == ''){
                    this.getTrendTabsParticipant()
                }else{
                    if(this.yearTrendFilter){
                        if(this.yearTrendFilter.length > 2){
                            this.yearTrendFilter.splice(0, 1)
                            this.yearTrendFilter.splice(0, 1)
                        }
                        if(this.yearTrendFilter.length == 2){
                            this.getTrendTabsParticipant()
                        }
                        if(this.yearTrendFilter.length == 1){
                            this.getTrendTabsParticipant()
                        }
                    }else{
                        this.getTrendTabsParticipant()
                    }
                }
            },
            getTrendTabsParticipant(){
                getTrendParticipant({params: { product: this.productTrendFilter, package: this.productTrendFilter, years: this.yearTrendFilter, source: this.dataSource }}).then(response => {
                    this.chartTrendParticipantOptions = {
                        chart: {
                            id: 'chart-trend-participant',
                            events: {
                                dataPointMouseEnter: this.onBarHover,
                                dataPointMouseLeave: this.onBarMouseLeave,
                            },
                        },
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            enabled: false,
                        },
                        colors: ["#8c0095", "#344CB7"],
                        labels: response.data.categories,
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    position: "top"
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -30,
                            style: {
                                fontSize: "14px",
                                colors: ["#304758"]
                            },
                            background: {
                                enabled: true,
                                foreColor: '#fff'
                            }
                        },
                    }
                    this.chartTrendParticipantSeries = response.data.data[0].data
                    this.summaryTrenParticipant = response.data.summary
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getTrendPackage({params: { product: this.productTrendFilter, package: this.productTrendFilter, years: this.yearTrendFilter, source: this.dataSource }}).then(response => {
                    this.chartTrendPackageOptions = {
                        chart: {
                            id: 'chart-trend-package',
                            events: {
                                dataPointMouseEnter: this.onBarHover,
                                dataPointMouseLeave: this.onBarMouseLeave,
                            },
                        },
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            enabled: false,
                        },
                        colors: ["#344CB7", "#18dcff", "#BE3144", "#f0932b", "#cd84f1", "#a4b0be"],
                        labels: response.data.categories,
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    position: "top"
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -30,
                            style: {
                                fontSize: "14px",
                                colors: ["#304758"]
                            },
                            background: {
                                enabled: true,
                                foreColor: '#fff'
                            }
                        },
                    }
                    this.chartTrendPackageSeries = response.data.data[0].data
                    this.summaryTrenPackage = response.data.summary
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getTrendGrowth({params: { product: this.productTrendFilter, package: this.productTrendFilter, years: this.yearTrendFilter, source: this.dataSource }}).then(response => {
                    this.chartGrowthOptions = {
                        chart: {
                            id: 'chart-trend-growth',
                            events: {
                                dataPointMouseEnter: this.onBarHover,
                                dataPointMouseLeave: this.onBarMouseLeave,
                            },
                        },
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            enabled: false,
                        },
                        colors: ["#344CB7", "#8c0095", "#BE3144", "#80C4E9"],
                        labels: response.data.categories,
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    position: "top"
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -30,
                            style: {
                                fontSize: "14px",
                                colors: ["#304758"]
                            },
                            background: {
                                enabled: true,
                                foreColor: '#fff'
                            }
                        },
                    }
                    this.chartGrowthSeries = response.data.data[0].data
                    this.summaryTrenG = response.data.summary
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })

                getTrendProduct({params: { product: this.productTrendFilter, package: this.productTrendFilter, years: this.yearTrendFilter, source: this.dataSource }}).then(response => {
                    this.chartTrendProductOptions = {
                        chart: {
                            id: 'chart-trend-product',
                            events: {
                                dataPointMouseEnter: this.onBarHover,
                                dataPointMouseLeave: this.onBarMouseLeave,
                            },
                        },
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            enabled: false,
                        },
                        colors: ["#344CB7", "#8c0095", "#BE3144", "#80C4E9", "#6bb042", "#f5954f"],
                        labels: response.data.categories,
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    position: "top"
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -30,
                            style: {
                                fontSize: "14px",
                                colors: ["#304758"]
                            },
                            background: {
                                enabled: true,
                                foreColor: '#fff'
                            }
                        },
                    }
                    this.chartTrendProductSeries = response.data.data[0].data
                    this.summaryTrenProduct = response.data.summary
                }).catch(error => {
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })
            },
            exportParticipant(params) {
                this.isLoading = true
                const vForm = {}
                if(params == "parent_account") {
                    vForm.parentAccount = 1
                }
                if(params == "not_parent_account") {
                    vForm.notParentAccount = 1
                }
                if(params == "has_phone") {
                    vForm.hasPhone = 1
                }
                if(params == "has_not_phone") {
                    vForm.hasNotPhone = 1
                }
                if(params == "has_instagram") {
                    vForm.hasInstagram = 1
                }
                if(params == "has_not_instagram") {
                    vForm.hasNotInstagram = 1
                }
                if(params == "has_linkedin") {
                    vForm.hasLinkedIn = 1
                }
                if(params == "has_not_linkedin") {
                    vForm.hasNotLinkedIn = 1
                }
                exportParticipant(vForm).then(response => {
                    this.isLoading = false
                    window.location = response.data.downloadLink
                }).catch(error => {
                    this.isLoading = false
                    this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
                })
            },
            viewParticipant(params) {
                console.log(params)
            },
            onBarHover(event, chartContext, config) {
                const dataPointIndex = config.dataPointIndex;
                const dataValue = config.w.config.labels[dataPointIndex];
                const dataPointSeries = config.seriesIndex;

                let dataItem = []

                if(config.w.config.chart.id == 'chart-trend-participant'){
                    this.summaryTrenParticipant.forEach(value => {
                        if(value.years == dataValue){
                            dataItem.push(value.item)
                        }
                    })
                    this.tooltipData = { name: dataValue, value: dataItem[0], series: dataPointSeries};
                    this.tooltipVisible = true;
                }
                if(config.w.config.chart.id == 'chart-trend-package'){
                    this.summaryTrenPackage.forEach(value => {
                        if(value.years == dataValue){
                            if(dataPointSeries == 0){
                                dataItem.push(value.item.part_1)
                            }
                            if(dataPointSeries == 1){
                                dataItem.push(value.item.part_2)
                            }
                            if(dataPointSeries == 2){
                                dataItem.push(value.item.part_3)
                            }
                            if(dataPointSeries == 3){
                                dataItem.push(value.item.part_4)
                            }
                            if(dataPointSeries == 4){
                                dataItem.push(value.item.part_5)
                            }
                            if(dataPointSeries == 5){
                                dataItem.push(value.item.part_6)
                            }
                        }
                    })
                    this.tooltipDataPackage = { name: dataValue, value: dataItem[0] };
                    this.tooltipVisiblePackage = true;
                }
                if(config.w.config.chart.id == 'chart-trend-growth'){
                    this.summaryTrenG.forEach(value => {
                        if(value.years == dataValue){
                            dataItem.push(value.item)
                        }
                    })
                    this.tooltipDataG = { name: dataValue, value: dataItem[0] };
                    this.tooltipVisibleG = true;
                }
                if(config.w.config.chart.id == 'chart-trend-product'){
                    this.summaryTrenProduct.forEach(value => {
                        if(value.years == dataValue){
                            dataItem.push(value.item)
                        }
                    })
                    this.tooltipDataProduct = { name: dataValue, value: dataItem[0] };
                    this.tooltipVisibleProduct = true;
                }

                this.tooltipPosition = {
                    top: 210,
                    left: event.pageX - 350,
                };
            },
            onBarMouseLeave() {
                this.tooltipVisible = false;
                this.tooltipVisiblePackage = false;
                this.tooltipVisibleProduct = false;
                this.tooltipVisibleG = false
            },
        },
    }
</script>

<style lang="scss" scoped>
.per-page-selector {
    width: 90px;
}

.custom-tooltip {
  position: absolute;
  background-color: white;
  color: black;
  padding: 10px;
  border:1px solid black;
  border-radius: 5px;
  pointer-events: none;
  transition: transform 0.1s ease-in-out;
  transform: translate(-50%, -50%);
  z-index: 10;
}

.custom-tooltip strong {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
}

.custom-tooltip div {
  font-size: 12px;
}
</style>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import "~@resources/scss/vue/libs/vue-flatpicker.scss";
</style>
