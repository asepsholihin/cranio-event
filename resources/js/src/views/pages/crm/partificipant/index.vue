<template>
  <div>

    <import-sidebar :is-import-sidebar-active.sync="isImportSidebarActive"
      @refetch-data="refetchData" v-if="hasPermission('participant-crm-add')" />

    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <template>
        <div class="m-2">
          <b-row class="mb-md-1 mb-2">
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Sort Data By</label>
              <v-select v-model="sortDataByFilter" placeholder="Select data" :options="sortByOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
          </b-row>
          <b-row class="mb-md-1 mb-2">
            <b-col cols="12">
              <h4>Transaksi Participant</h4>
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Total Trips</label>
              <v-select v-model="totalTripFilter" placeholder="Select total trip" :options="totalTripOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Total Transaction</label>
              <v-select v-model="totalTransactionFilter" placeholder="Select total transaction" :options="transactionOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Filter by Trip</label>
              <v-select v-model="trip" placeholder="Select trip" :options="umrohTripFilterOptions"
                :filterable="false" @search="onSearch" :reduce="name => name.latest_trip_name" label="latest_trip_name" />
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Filter by Package</label>
              <v-select v-model="packageFilter" placeholder="Select package" :options="packageFilterOptions"
                :filterable="true" :reduce="name => name.latest_trip_package" label="latest_trip_package" />
            </b-col>
          </b-row>
          <b-row class="mb-md-1 mb-2">
            <b-col cols="12">
              <h4>Demografis</h4>
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Gender</label>
              <v-select v-model="genderFilter" placeholder="Pilih gender" :options="genderOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Usia</label>
              <v-select v-model="ageFilter" placeholder="Pilih Usia" :options="ageOptions" class="w-100" :reduce="val => val.value" />
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Filter by Job</label>
              <v-select v-model="jobFilter" placeholder="Pilih Pekerjaan" :options="jobOptions" :reduce="val => val.name" label="name" />
            </b-col>
          </b-row>
          <b-row class="mb-md-1 mb-2">
            <b-col cols="12">
              <h4>Geografis</h4>
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Filter by City</label>
              <v-select v-model="cityFilter" placeholder="Select city" :options="cityFilterOptions"
                :filterable="true" :reduce="name => name.city" label="city" />
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Filter by Province</label>
              <v-select v-model="provinceFilter" placeholder="Select province" :options="provinceFilterOptions"
                :filterable="true" :reduce="name => name.province" label="province" />
            </b-col>
          </b-row>
          <b-row class="mb-md-1 mb-2">
            <b-col cols="12">
              <h4>Lain-lain</h4>
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Filter by Total Account</label>
              <v-select v-model="totalAccountFilter" placeholder="Select total account" :options="totalAccountFilterOptions"
                :filterable="true" :reduce="name => name.parent_account" label="parent_account" />
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Search by Participant</label>
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block"
                placeholder="Search participant" />
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Filter Date</label>
              <div class="input-group">
                <flat-pickr v-model="dateFilter" class="form-control" :config="{ altInput: true, mode: 'range' }" placeholder="Select date" />
                <div class="input-group-append">
                <button class="btn btn-danger" type="button" title="Clear" @click="clearDate">
                  <feather-icon icon="Trash2Icon" />
                </button>
                </div>
              </div>
            </b-col>
            <b-col cols="12" md="4" class="mb-md-1 mb-2">
              <label>Filter Year</label>
              <div class="input-group w-100">
                <v-select v-model="yearFilter" class="w-100" placeholder="Select Year" :options="years"
                    :filterable="true" :reduce="name => name.name" label="name"/>
              </div>
            </b-col>
            <b-col cols="12" class="mb-md-1 mb-2">
              <b-form-checkbox v-model="parentAccountFilter" value="1" unchecked-value="" inline>
                Pemilik Akun
              </b-form-checkbox>
              <b-form-checkbox v-model="hasPhoneFilter" value="1" unchecked-value="" inline>
                Memiliki No HP
              </b-form-checkbox>
              <b-form-checkbox v-model="hasInstagramFilter" value="1" unchecked-value="" inline>
                Memiliki Instagram
              </b-form-checkbox>
              <b-form-checkbox v-model="hasLinkedInFilter" value="1" unchecked-value="" inline>
                Memiliki LinkedIn
              </b-form-checkbox>
              <!-- <b-form-checkbox v-model="needMergeFilter" value="1" unchecked-value="" inline>
                Perlu Merger Data
              </b-form-checkbox> -->
              <b-form-checkbox v-model="oldDataFilter" value="1" unchecked-value="" inline>
                Data Lama
              </b-form-checkbox>
            </b-col>
          </b-row>
        </div>
      </template>

      <div class="m-2">

        <!-- Table Top -->
        <b-row>

          <!-- Per Page -->
          <b-col cols="12" md="6" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
            <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
              class="per-page-selector d-inline-block mx-50" />
            <span class="text-muted">Showing {{ dataMeta.from }} to {{ dataMeta.to }} of {{ dataMeta.of }}
              entries</span>
          </b-col>
          <!-- Search -->
          <b-col cols="12" md="6">
            <div class="d-lg-flex align-items-center justify-content-end">
              <b-overlay :show="isSubmitModal" rounded opacity="0.6" spinner-small spinner-variant="primary" class="d-inline-block">
                <b-dropdown right class="mr-1" variant="gradient-primary" :disabled="isSubmitModal" v-if="hasPermission('participant-crm-download')">
                  <template #button-content>
                      <feather-icon icon="DownloadCloudIcon" /> Download
                  </template>
                  <b-dropdown-item @click="exportParticipant()">
                    Data Excel
                  </b-dropdown-item>
                  <b-dropdown-item @click="downloadCertificate()">
                    Certificate
                  </b-dropdown-item>
                </b-dropdown>
              </b-overlay>
              <b-button class="mr-1" variant="primary" @click="isImportSidebarActive = true" v-if="hasPermission('participant-crm-import')" :disabled="isSubmitModal">
                <span class="text-nowrap"><feather-icon icon="UploadCloudIcon" /> Import</span>
              </b-button>
              <!-- <b-button class="mr-1" variant="info" @click="updateDatabase()" v-if="hasPermission('participant-crm-add')" :disabled="isSubmitModal">
                <span class="text-nowrap"><feather-icon icon="RefreshCwIcon" /> Update Database</span>
              </b-button>
              <b-button variant="info" @click="updateDatabaseHajiKhusus()" v-if="hasPermission('participant-crm-add')" :disabled="isSubmitModal">
                <span class="text-nowrap"><feather-icon icon="RefreshCwIcon" /> Update Database Haji</span>
              </b-button> -->
            </div>
          </b-col>
        </b-row>
      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchUsers" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc" :tbody-tr-class="rowClass">

        <!-- Column: Name -->
        <template #cell(name)="data">
          <b-media vertical-align="center">
            <template #aside>
              <b-avatar size="40" :src="data.item.profile_thumbnail" :text="avatarText(data.item.name)"
                :variant="`light-primary`" :to="{ name: 'participant-crm-detail', params: { id: data.item.participant_id } }" />
            </template>
            <template v-if="data.item.parent_account > 0">
              <span class="badge badge-pill badge-info" @click="viewParentAccount(data.item)">Pemilik Akun ({{data.item.parent_account}})</span>
            </template>
            <small class="text-danger"><em>{{ data.item.participant_id ? '' : 'Unlinked Participant' }}</em></small>
            <div v-if="data.item.nomor_porsi"><small class="text-primary font-weight-bold">Nomor Porsi: {{ data.item.nomor_porsi }}</small></div>
            <b-link :to="{ name: 'participant-crm-detail', params: { id: data.item.participant_id } }" :disabled="data.item.participant_id == null"
              class="font-weight-bold d-block">
              <span class="text-nowrap">{{ (data.item.front_title != null && data.item.front_title != "" && data.item.front_title != "-") ? data.item.front_title : "" }} {{ data.item.name.toUpperCase() }} {{ (data.item.back_title != null && data.item.back_title != "" && data.item.back_title != "-") ? data.item.back_title : "" }}</span>
            </b-link>
            <small>NIK: {{ data.item.nik }}</small><br>
            <small>Gender: {{ resolveGender(data.item.gender) }}</small>
            <div class="mt-50 text-nowrap">
              <b-button @click="mergeData(data.item)" v-if="data.item.need_merge" variant="warning" size="sm">Merge</b-button>
              <b-button @click="removeMergeData(data.item)" v-if="data.item.need_merge" variant="success" size="sm">Tidak perlu merge</b-button>
            </div>
          </b-media>
        </template>

        <template #cell(birth_date)="data">
          <b-media vertical-align="center">
            <span class="text-nowrap">{{ data.item.birth_date ? formatDate(data.item.birth_date) : '' }}</span>
          </b-media>
        </template>

        <template #cell(total_transaction)="data">
          <b-media vertical-align="center">
            <span class="text-nowrap">{{ 'Rp.' }} {{ data.item.total_transaction ? parseInt(data.item.total_transaction).toLocaleString() : 0 }}</span>
          </b-media>
        </template>

        <template #cell(total_trip)="data">
          <b-media vertical-align="center">
            <span class="text-nowrap">{{ (data.item.total_trip) ? data.item.total_trip + ' Trips' : 0 }}</span>
          </b-media>
        </template>

        <template #cell(latest_trip_name)="data">
          <b-media>
            <span class="text-nowrap">{{ data.item.latest_trip_name }}</span>
          </b-media>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item v-if="hasPermission('participant-crm-edit') && data.item.need_merge" @click="mergeData(data.item)">
              <feather-icon icon="GitMergeIcon" />
              <span class="align-middle ml-50">Merge Data</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="hasPermission('participant-crm-edit')" @click="setParentAccount(data.item)">
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Parent Account</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="hasPermission('participant-crm-edit') && data.item.participant_id != null" @click="showUpdateDataCRM(data.item)">
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Update Data</span>
            </b-dropdown-item>
            <b-dropdown-item @click="showPreviewCertificate(data.item)" v-if="data.item.participant_id != null">
              <feather-icon icon="EyeIcon" />
              <span class="align-middle ml-50">Preview Sertifikat</span>
            </b-dropdown-item>
            <b-dropdown-item @click="generateCertificate(data.item.participant_id)" v-if="data.item.participant_id != null">
              <feather-icon icon="DownloadIcon" />
              <span class="align-middle ml-50">Download Sertifikat</span>
            </b-dropdown-item>
            <!-- <b-dropdown-item>
              <feather-icon icon="UserCheckIcon" v-if="data.item.participant_id != null"/>
              <span class="align-middle ml-50">Transaction</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="hasPermission('participant-crm-view')">
              <feather-icon icon="Trash2Icon" />
              <span class="align-middle ml-50">Trip History</span>
            </b-dropdown-item> -->
          </b-dropdown>
        </template>

      </b-table>
      <div class="mx-2 mb-2">
        <b-row>

          <!-- Pagination -->
          <b-col cols="12" class="d-flex align-items-center justify-content-end">
            <b-pagination v-model="currentPage" :total-rows="totalUsers" :per-page="perPage" first-number last-number
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

    <b-modal id="modal-lg" size="lg" v-model="accessPreviewCertificate" :busy="isSubmitModal" centered
      no-close-on-backdrop @hidden="resetModal">
      <template #modal-title>
        <h3>Preview Ucapan Milad</h3>
      </template>
      <b-row>
        <b-col cols="12">
          <template v-if="certificateImage">
            <div v-if="!loadingImage" class="overflow-auto">
              <iframe :src="certificateImage" style="width: 100%;height: 300px; border: none;">
              Oops! an error has occurred.
              </iframe>
            </div>
            <template v-if="loadingImage">
              <div class="d-flex image-box justify-content-center align-items-center">
                <b-spinner v-if="loadingImage" @hidden="resetModal" class="" variant="primary" key="primary"></b-spinner>
              </div>
            </template>
          </template>
          <template v-else>
            <div class="image-box d-flex align-items-center justify-content-center">
              <p>Tidak ada sertifikat</p>
            </div>
          </template>
        </b-col>
      </b-row>
      <template #modal-footer>
        <b-button variant="success" size="md" class="mt-25" @click="accessPreviewCertificate = false"
          :disabled="isSubmitModal">
          <span class="d-none d-sm-inline">Cancel</span>
        </b-button>
      </template>
    </b-modal>

    <b-modal v-model="accessUpdateParticipantCrm" ok-title="Update Data Participant" :busy="isSubmitModal" centered
      no-close-on-backdrop @ok="handelUpdateParticipantCrm" @hidden="resetModal">
      <template #modal-title>
        <h3>Participant CRM Information</h3>
      </template>
      <validation-observer ref="refUpdateCrmForm">
        <b-form class="p-2" @submit.prevent="onSubmitAccessLogin">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert v-if="errors[0]" variant="danger" show>
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <b-row>
            <b-col cols="12">
              <label for="">JI Code</label>
              <p class="font-weight-bold">
                {{ selectedParticipant.ji_code }}
              </p>
            </b-col>
            <b-col cols="12">
              <label for="">Participant Name</label>
              <p class="font-weight-bold">
                {{ selectedParticipant.name }}
              </p>
            </b-col>
            <b-col cols="12">
              <label for="">Participant Level</label>
              <p class="font-weight-bold">
                {{ selectedParticipant.participant_level }}
              </p>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Front Title" vid="front_title">
                <b-form-group label="Front Title">
                  <b-form-input v-model="FormData.front_title" type="text" :state="errors.length > 0 ? false : null"
                    trim />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Name In Certificate" vid="name_in_certificate">
                <b-form-group label="Name In Certificate">
                  <b-form-input v-model="FormData.name_in_certificate" type="text"
                    :state="errors.length > 0 ? false : null" trim />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Back Title" vid="back_title">
                <b-form-group label="Back Title">
                  <b-form-input v-model="FormData.back_title" type="text" :state="errors.length > 0 ? false : null"
                    trim />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" vid="birth_date" rules="required" name="Birth Date">
                <b-form-group label="Birth Date" :state="errors.length > 0 ? false : null" description="Pastikan format tanggal DD MM YYYY">
                  <flat-pickr v-model="FormData.birth_date" class="form-control" :config="birthDateConfig" />

                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <b-row>
                <b-col cols="12">
                  <validation-provider #default="{ errors }" name="Code" vid="country_code" rules="required">
                      <b-form-group label="Code" :state="errors.length > 0 ? false : null">
                          <v-select v-model="FormData.country_code" placeholder="Select Country"
                              :options="countryCodes" :filterable="true" :reduce="(name) => name.code" label="country">
                              <template slot="no-options">Type to search country.. </template>
                              <template slot="option" slot-scope="option">
                                  <b-media vertical-align="center">{{ option.country }} +{{
                                      option.code }}</b-media>
                              </template>
                              <template slot="selected-option" slot-scope="option">
                                  <div class="selected d-center">
                                      <b-media vertical-align="center">{{ option.code }}</b-media>
                                  </div>
                              </template>
                          </v-select>
                          <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                              {{ errors[0] }}
                          </b-form-invalid-feedback>
                      </b-form-group>
                  </validation-provider>
                </b-col>
                <b-col cols="8">
                  <validation-provider #default="{ errors }" name="No HP" vid="no_hp" rules="numeric|max:14">
                    <b-form-group label="No HP" label-for="no_hp" description="Contoh: 8121234567">
                      <b-form-input v-model="FormData.no_hp" :state="errors.length > 0 ? false : null" trim />

                      <b-form-invalid-feedback>
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                  </b-col>
              </b-row>
            </b-col>
            <!-- Email -->
            <b-col cols="12" md="12">
              <validation-provider #default="{ errors }" name="Email" vid="email" rules="email">
                <b-form-group label="Email" label-for="email">
                  <b-form-input v-model="FormData.email" :state="errors.length > 0 ? false : null" trim />

                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Instagram" vid="instagram">
                <b-form-group label="Instagram">
                  <b-form-input v-model="FormData.instagram" type="text" :state="errors.length > 0 ? false : null"
                    trim />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Twitter" vid="twitter">
                <b-form-group label="Twitter">
                  <b-form-input v-model="FormData.twitter" type="text" :state="errors.length > 0 ? false : null"
                    trim />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Linked In Url" vid="linkedin_url">
                <b-form-group label="Linked In Url">
                  <b-form-input v-model="FormData.linkedin_url" type="text" :state="errors.length > 0 ? false : null"
                    trim />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Article Url" vid="article_url">
                <b-form-group label="Article Url">
                  <b-form-input v-model="FormData.artice_url" type="text" :state="errors.length > 0 ? false : null"
                    trim />
                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>

          </b-row>
          <h4 class="mt-3">Alamat Domisili / Alamat Pengiriman</h4>
          <hr>
          <b-row>
            <b-col cols="12">
              <!-- Provinsi -->
              <validation-provider #default="{ errors }" name="Provinsi" vid="home_province">
                <b-form-group label="Provinsi" :state="errors.length > 0 ? false : null">
                  <v-select @input="getDomisiliCities" v-model="FormData.home_province" :options="provinces"
                    :clearable="false" :reduce="province => province.province" label="province" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <!-- Kota -->
              <validation-provider #default="{ errors }" name="Kota" vid="home_city">
                <b-form-group label="Kota" :state="errors.length > 0 ? false : null">
                  <v-select @input="getDomisiliDistricts" v-model="FormData.home_city" :options="domisili_cities"
                    :clearable="false" :reduce="city => city.city" label="city" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <!-- Kecamatan -->
              <validation-provider #default="{ errors }" name="Kecamatan" vid="home_kecamatan">
                <b-form-group label="Kecamatan" :state="errors.length > 0 ? false : null">
                  <v-select @input="getDomisiliSubdistricts" v-model="FormData.home_kecamatan"
                    :options="domisili_districts" :clearable="false" :reduce="district => district.district"
                    label="district" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <!-- Kelurahan -->
              <validation-provider #default="{ errors }" name="Kelurahan" vid="home_kelurahan">
                <b-form-group label="Kelurahan" :state="errors.length > 0 ? false : null">
                  <v-select @input="getDomisiliPostalcodes" v-model="FormData.home_kelurahan"
                    :options="domisili_subdistricts" :clearable="false" :reduce="subdistrict => subdistrict.subdistrict"
                    label="subdistrict" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <!-- Kode Pos -->
              <validation-provider #default="{ errors }" name="Kode Pos" vid="home_postalcode">
                <b-form-group label="Kode Pos">
                  <v-select taggable v-model="FormData.home_postalcode" :options="domisili_postalcodes" :clearable="false"
                    :reduce="postalcode => postalcode.postalcode" label="postalcode" />

                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="12">
              <!-- Alamat Lengkap Domisili -->
              <validation-provider #default="{ errors }" name="Alamat Lengkap Domisili" vid="home_address" rules="required">
                <b-form-group label="Alamat Lengkap Domisili">
                  <b-form-textarea id="home_address" v-model="FormData.home_address"
                    :state="errors.length > 0 ? false : null" trim />

                  <b-form-invalid-feedback>
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="12" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
              <h5>Milad Photo</h5>
            </b-col>
          </b-row>
          <b-img v-if="fileMiladPhoto != null" center :src="fileMiladPhoto" thumbnail fluid />
          <h5 v-else class="text-center">
            No Available
          </h5>
          <center>
            <b-button v-if="hasPermission('participant-crm-edit')" variant="primary" size="sm" class="mt-25"
              :disabled="isSubmitModal" @click="$refs.refInputEl0.click()">
              <input ref="refInputEl0" accept="image/jpeg, image/png, image/webp" type="file" class="d-none"
                @input="inputImageRenderer()">
              <feather-icon icon="ImageIcon" />
              <span class="d-none d-sm-inline">Change</span>
            </b-button>

            <b-button v-if="hasPermission('participant-crm-edit') && fileMiladPhoto != null" variant="danger" size="sm"
              @click="deleteFile('Foto Milad')" class="mt-25 ml-50" :disabled="isSubmitModal">
              <b-spinner small v-show="isSubmitModal" />
              <feather-icon icon="Trash2Icon" size="16" />
              <span class="d-none d-sm-inline">Delete</span>
            </b-button>

            <b-button v-if="hasPermission('participant-crm-edit') && FormData.photo" variant="primary" size="sm"
              class="mt-25 ml-50" :disabled="isSubmitModal" @click="uploadMiladPhoto">
              <b-spinner v-show="isSubmitModal" small />
              <feather-icon icon="UploadCloudIcon" size="16" />
              <span class="d-none d-sm-inline">Upload</span>
            </b-button>
          </center>
          <center>
            <p><small style="font-size">Recommended Size : 600px x 600px (JPG or PNG) Max Size: 1.5MB</small></p>
          </center>
        </b-form>
      </validation-observer>
    </b-modal>

    <b-modal v-model="mergeParticipantForm" ok-title="Merge Data Participant" :busy="isSubmitModal" centered
      no-close-on-backdrop @ok="handelMergeParticipant" @hidden="resetModal" size="lg">
      <template #modal-title>
        <h3>Merge Participant</h3>
      </template>

      <b-alert variant="danger" show>
        <div class="alert-body">
          Warning, You are not allowed to undone this process
        </div>
      </b-alert>

      <validation-observer ref="refMergeParticipantForm">
        <b-form class="p-2" @submit.prevent="onSubmitMergeParticipant">
          <validation-provider #default="{ errors }" vid="message">
            <b-alert v-if="errors[0]" variant="danger" show>
              <div class="alert-body">
                {{ errors[0] }}
              </div>
            </b-alert>
          </validation-provider>

          <b-row>
            <b-col cols="12">
              <validation-provider #default="{ errors }" name="Parent Participant" vid="participant_crm_id" rules="required">
                <b-form-group label="Select Parent Participant">
                  <template v-if="loadingParticipantMerge">
                    <div class="d-flex image-box justify-content-center align-items-center">
                      <b-spinner v-if="loadingParticipantMerge" class="" variant="primary" key="primary"></b-spinner>
                    </div>
                  </template>
                  <b-form-radio-group
                    v-model="formDataMerge.participant_crm_id"
                    :state="errors.length > 0 ? false : null"
                    buttons
                    button-variant="outline-primary"
                    class="w-100"
                    trim
                    stacked>
                    <template v-for="option in listParticipantMerge">
                      <b-form-radio :value="option.id" :key="option.name">
                        <div class="mb-1">
                          <small v-if="option.participant_id == null" class="text-danger"><em>Unlinked</em></small>
                          <small v-if="option.participant_id" class="text-success"><em>Verified Participant</em></small>
                        </div>
                        <div class="mb-50">{{ option.name }}</div>
                        <div>{{ option.last_booking_order_no}}</div>
                        <h4>{{ option.latest_trip_name}}</h4>
                      </b-form-radio>
                    </template>
                  </b-form-radio-group>
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>

          </b-row>
        </b-form>
      </validation-observer>
    </b-modal>

    <b-modal v-model="viewParentAccountModal" ok-title="Tutup" acentered
      no-close-on-backdrop @hidden="resetModal">
      <template #modal-title>
        <h3>History Keberangkatan</h3>
      </template>
      <b-spinner small v-show="isLoading" />
      <ul>
        <template v-for="(trip, index) in tripHistories">
          <li>{{ trip }}</li>
        </template>
      </ul>

    </b-modal>
  </div>
</template>

<script>
import {
  BCard,
  BRow,
  BCol,
  BFormInput,
  BTable,
  BButton,
  BMedia,
  BAvatar,
  BLink,
  BImg,
  BDropdown,
  BFormTextarea,
  BDropdownItem,
  BPagination,
  BForm,
  BFormGroup,
  BSpinner,
  BFormInvalidFeedback,
  BFormRadioGroup,
  BFormRadio,
  BAlert,
  BOverlay,
  BFormCheckbox
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import { ref } from '@vue/composition-api'
import { required, numeric, email } from '@validations'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import _ from 'lodash'
import { formatDate, formatDateShort, avatarText, yearsLastAndStart} from '@core/utils/filter'
import useDataList from './useDataList'
import { postData, getDetail, uploadMiladPhotos, deleteFile, getFiles, getCertificateImage, downloadCertificate, exportParticipant, getTripSearch, deleteData, getParticipantCertificate, getParticipantCertificatePreview, getListParticipantMerge, postMergeParticipant, postRemoveMergeParticipant, getFilterCities, getFilterTotalAccount, getFilterPackages, getFilterJobs, getFilterProvinces, setParentAccount, viewParentAccount, updateDatabaseCRM } from '@/network/crm-participant'
import { hasPermission } from '@/auth/utils'
import { getProvinces, getCities, getDistricts, getSubdistricts, getPostalcodes } from '@/network/address'
import { getJobSearch } from '@/network/participant'
import importSidebar from './importSidebar.vue'
import { getCountryCodes } from "@/network/web-settings";
import flatPickr from 'vue-flatpickr-component'

export default {
  components: {
    importSidebar,

    BCard,
    BRow,
    BCol,
    BForm,
    BFormInput,
    BTable,
    BMedia,
    BSpinner,
    BButton,
    BImg,
    BAvatar,
    BLink,
    BFormTextarea,
    BDropdown,
    BDropdownItem,
    BPagination,
    BFormGroup,
    BFormInvalidFeedback,
    BFormRadioGroup,
    BFormRadio,
    BAlert,
    BOverlay,
    BFormCheckbox,

    vSelect,
    flatPickr,
    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  setup() {
    const isImportSidebarActive = ref(false)
    const sortByOptions = [
      { label: '- Sort By Transaction Terbesar', value: 1 },
      { label: '- Sort By Trip Terbanyak', value: 2 },
      { label: '- Sort By Umrah dan Haji Furoda', value: 3 },
      { label: '- Sort By Umrah dan Haji Khusus', value: 4 },
      { label: '- Sort By Umrah dan Islamic Tours', value: 5 },
      { label: '- Sort By Milad Terdekat', value: 6 },
      { label: '- Sort By Participant Terbaru', value: 7 },
      { label: '- Sort By Participant Lama', value: 8 }]
    const levelOptions = [{ label: '- 1', value: 1 }, { label: '- 2', value: 2 }]
    const totalTripOptions = [
      { label: '1 trips', value: 1 },
      { label: '2 trips', value: 2 },
      { label: '3 trips', value: 3 },
      { label: '4 trips', value: 4 },
      { label: '5 trips', value: 5 },
      { label: 'Lebih dari 5 trips', value: 6 }
    ]
    const transactionOptions = [
      { label: 'Dibawah 100 Juta', value: 1 },
      { label: '100 Juta - 200 Juta', value: 2 },
      { label: '200 Juta - 300 Juta', value: 3 },
      { label: '300 Juta - 400 Juta', value: 4 },
      { label: '400 Juta - 500 Juta', value: 5 },
      { label: 'Lebih dari 500 Juta', value: 6 },
    ]
    const productOptions = [
      { lable: 'Umroh', value: 1 },
      { lable: 'Haji', value: 2 },
      { lable: 'Haji dan Umroh', value: 3 }
    ]
    const genderOptions = [{ label: 'Men', value: 1 }, { label: 'Women', value: 2 }]
    const ageOptions = [
      { label: 'Dibawah 18', value: 1 },
      { label: '18 - 25', value: 2 },
      { label: '25 - 35', value: 3 },
      { label: '35 - 60', value: 4 },
      { label: 'Lebih dari 60', value: 5 }
    ]
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
      trip,

      // UI
      resolveGender,
      resolveUserRoleIcon,
      resolveUserStatusVariant,

      // Extra Filters
      levelFilter,
      genderFilter,
      totalTripFilter,
      cityFilter,
      totalTransactionFilter,
      totalAccountFilter,
      parentAccountFilter,
      hasPhoneFilter,
      hasInstagramFilter,
      hasLinkedInFilter,
      needMergeFilter,
      oldDataFilter,
      productFilter,
      sortDataByFilter,
      packageFilter,
      ageFilter,
      jobFilter,
      provinceFilter,
      dateFilter,
      yearFilter
    } = useDataList()

    return {
      // Sidebar
      isImportSidebarActive,

      fetchUsers,
      formatDate,
      formatDateShort,
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
      sortByOptions,
      genderOptions,
      totalTripOptions,
      transactionOptions,
      productOptions,
      levelOptions,
      trip,
      ageOptions,

      // Filter
      avatarText,

      // UI
      resolveGender,
      resolveUserRoleIcon,
      resolveUserStatusVariant,

      // Extra Filters
      sortDataByFilter,
      levelFilter,
      genderFilter,
      totalTripFilter,
      cityFilter,
      totalTransactionFilter,
      totalAccountFilter,
      parentAccountFilter,
      hasPhoneFilter,
      hasInstagramFilter,
      hasLinkedInFilter,
      needMergeFilter,
      oldDataFilter,
      productFilter,
      packageFilter,
      ageFilter,
      jobFilter,
      provinceFilter,
      dateFilter,
      hasPermission,
      yearsLastAndStart,
      yearFilter
    }
  },
  data() {
    const domisili_cities = []
    const domisili_districts = []
    const domisili_subdistricts = []
    const domisili_postalcodes = []
    const umrohTripFilterOptions = []
    const cityFilterOptions = []
    const totalAccountFilterOptions = []
    const packageFilterOptions = []
    // const jobFilterOptions = []
    const provinceFilterOptions = []
    const jobOptions = []

    getTripSearch({ q: '' }).then(res => {
      this.umrohTripFilterOptions = res.data
    }).catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    getFilterCities({ q: '' }).then(res => {
      this.cityFilterOptions = res.data
    }).catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    getFilterProvinces({ q: '' }).then(res => {
      this.provinceFilterOptions = res.data
    }).catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    getFilterPackages({ q: '' }).then(res => {
      this.packageFilterOptions = res.data
    }).catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    // getFilterJobs({ q: '' }).then(res => {
    //   this.jobFilterOptions = res.data
    // }).catch(error => {
    //   this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    // })

    getFilterTotalAccount({ q: '' }).then(res => {
      this.totalAccountFilterOptions = res.data
    }).catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    getProvinces().then(response => {
      this.provinces = response.data;
    }).catch(error => {
      if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
      } else {
        this.$refs.refObsForm.setErrors(error.response.data)
      }
    })

    getJobSearch().then(response => {
        this.jobOptions = response.data
    }).catch(error => {
        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })

    const years = this.yearsLastAndStart(4)
    return {
      birthDateConfig: {
        altInput: true,
        altFormat: 'Y-m-d',
        dateFormat: 'Y-m-d',
        allowInput: true,
        parseDate: (datestr, format) => {
          return new Date(datestr.replace(/(\d{2}) (\d{2}) (\d{4})/, "$2/$1/$3"));
        },
        onOpen: function (selectedDates, dateStr, instance) {
          instance.setDate(instance.input.value, false);
        }
      },
      certificateImage: null,
      accessPreviewCertificate: false,
      filter: {},
      accessUpdateParticipantCrm: false,
      FormData: {
        country_code: "62", front_title: '', back_title: '', instagram: '', twitter: '', linkedin_url: '', article_url: '', photo: [],
      },
      formDataMerge: { participant_crm_id: null },
      fileMiladPhoto: null,
      loadingImage: false,
      isSubmitModal: false,
      selectedParticipant: {},
      domisili_cities,
      domisili_districts,
      domisili_subdistricts,
      domisili_postalcodes,
      umrohTripFilterOptions,
      required,
      numeric,
      email,
      mergeParticipantForm: false,
      loadingParticipantMerge: false,
      listParticipantMerge: [],
      cityFilterOptions,
      totalAccountFilterOptions,
      packageFilterOptions,
      // jobFilterOptions,
      jobOptions,
      provinceFilterOptions,
      countryCodes: [],
      isLoading: false,
      viewParentAccountModal: false,
      tripHistories: [],
      years
    }
  },
  mounted(){
    this.totalTripFilter = this.$route.query.totalTrip;
    this.totalTransactionFilter = this.$route.query.totalTransaction;
  },
  methods: {
    clearDate() {
      this.dateFilter = null
    },
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (item.need_merge == 1) return 'table-warning'
    },
    showPreviewCertificate(item) {
      // this.accessPreviewCertificate = true
      // this.loadingImage = true
      // getCertificateImage(item.participant_id).then(response => {
      //   this.isSubmitModal = false
      //   this.certificateImage = response.data.photo
      //   this.loadingImage = false
      // }).catch(error => {
      //   this.verificationModal = false
      //   this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      // })
      window.open(getParticipantCertificatePreview(item.participant_id), '_blank');
    },
    mergeData(item) {
      this.mergeParticipantForm = true
      this.loadingParticipantMerge = true
      getListParticipantMerge({reference: item.reference}).then(response => {
        this.loadingParticipantMerge = false
        this.listParticipantMerge = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    removeMergeData(item) {
      this.loadingParticipantMerge = true
      this.formDataMerge['participant_crm_id'] = item.id
      postRemoveMergeParticipant(this.formDataMerge).then(response => {
        this.$swal({
          icon: 'success', title: 'Success', text: 'Data Participant has been updated successfully', timer: 2500, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false,
        })
        this.refetchData()
        this.isSubmitModal = false
      }).catch(error => {
        this.$swal({
          icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning' }, buttonsStyling: false,
        })
        this.isSubmitModal = false
      })
    },
    setParentAccount(item) {
      this.$swal({
        title: `Jadikan Pemilik Akun?`,
        text: 'Apakah anda yakin ' + item.name + ' akan dijadikan pemilik akun?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Iya',
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-outline-primary ml-1',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          this.loadingParticipantMerge = true
          this.formDataMerge['participant_crm_id'] = item.id
          setParentAccount(this.formDataMerge).then(response => {
            this.$swal({
              icon: 'success', title: 'Success', text: 'Data Participant has been updated successfully', timer: 2500, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false,
            })
            this.refetchData()
            this.isSubmitModal = false
          }).catch(error => {
            this.$swal({
              icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning' }, buttonsStyling: false,
            })
            this.isSubmitModal = false
          })
        }
      })
    },
    showUpdateDataCRM(item) {
      this.accessUpdateParticipantCrm = true
      this.selectedParticipant = item
      this.FormData.id = item.participant_id
      this.FormData.photo = []

      getDetail(item.participant_id).then(response => {
        this.FormData = response.data
        if(this.FormData.home_province) {
          this.getDomisiliCities(this.FormData.home_province);
        }
        if(this.FormData.home_city) {
          this.getDomisiliDistricts(this.FormData.home_city);
        }
        if(this.FormData.home_kecamatan) {
          this.getDomisiliSubdistricts(this.FormData.home_kecamatan);
        }
        if(this.FormData.home_kelurahan) {
          this.getDomisiliPostalcodes(this.FormData.home_kelurahan);
        }
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })

      getCountryCodes({})
      .then((response) => {
          this.countryCodes = response.data;
          this.FormData.country_code= "62"
      })
      .catch((error) => {
          this.$swal({
              icon: "error",
              title: "Error",
              text: `${error.response.data.message}`,
              timer: 3500,
              customClass: { confirmButton: "btn btn-warning" },
              buttonsStyling: false,
          });
      });

      getFiles(item.participant_id).then(response => {
        this.isSubmitModal = false
        this.fileMiladPhoto = this.getPhotoUrlByTitle(response.data.files, 'Foto Milad')
      }).catch(error => {
        this.verificationModal = false
        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    },
    inputImageRenderer() {
      this.FormData.photo = this.$refs.refInputEl0.files[0]
      const file = this.$refs.refInputEl0.files[0]
      const reader = new FileReader()
      reader.addEventListener(
        'load',
        () => {
          this.fileMiladPhoto = reader.result
        },
        false,
      )

      if (file) {
        reader.readAsDataURL(file)
      }
    },
    handelMergeParticipant(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.$swal({
        title: `This participant will merged?`,
        text: 'It cannot be reverted',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, merge it!',
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-outline-primary ml-1',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          this.onSubmitMergeParticipant()
        }
      })
    },
    onSubmitMergeParticipant() {
      this.$refs.refMergeParticipantForm.validate().then(success => {
        if (!success) return
        this.isSubmitModal = true
        postMergeParticipant(this.formDataMerge).then(response => {
          this.mergeParticipantForm = false
          this.$swal({
            icon: 'success', title: 'Success', text: 'Data Participant has been updated successfully', timer: 2500, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false,
          })
          this.refetchData()
          this.isSubmitModal = false
        }).catch(error => {
          this.$swal({
            icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning' }, buttonsStyling: false,
          })
          this.isSubmitModal = false
        })
      })
    },
    handelUpdateParticipantCrm(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.onSubmitUpdateCrm()
    },
    onSubmitUpdateCrm() {
      this.$refs.refUpdateCrmForm.validate().then(success => {
        if (!success) return
        this.isSubmitModal = true
        postData(this.FormData).then(response => {
          this.accessUpdateParticipantCrm = false
          this.$swal({
            icon: 'success', title: 'Success', text: 'Data Participant has been updated successfully', timer: 2500, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false,
          })
          this.refetchData()
          this.isSubmitModal = false
        }).catch(error => {
          this.$swal({
            icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning' }, buttonsStyling: false,
          })
          this.isSubmitModal = false
        })
      })
    },
    onSearch(search, loading) {
      loading(true)
      this.search(loading, search, this);
    },
    search: _.debounce((loading, search, vm) => {
      getTripSearch({ q: search })
        .then(res => {
          vm.umrohTripFilterOptions = res.data
          loading(false)
        })
        .catch(error => {
          vm.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
          loading(false)
        })
    }, 300),
    resetModal() {
      this.certificateImage = null
      this.loadingImage = false
      this.accessPreviewCertificate = false,
      this.mergeParticipantForm = false
      this.listParticipantMerge = []
      this.formDataMerge = { participant_crm_id: null }
      this.FormData = { country_code: "62" }
      this.tripHistories = []
    },
    uploadMiladPhoto() {
      this.isSubmitModal = true
      const vForm = new FormData()
      vForm.append('participant_id', this.FormData.id)
      vForm.append('title', 'Foto Milad')
      vForm.append('file_upload', this.FormData.photo)
      uploadMiladPhotos(vForm).then(response => {
        this.isSubmitModal = false
        this.FormData.photo = null
        this.$swal({
          icon: 'success', title: 'Success', text: `Photo has been uploaded successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false
        })
      }).catch(error => {
        this.isSubmitModal = false
        this.$swal({
          icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false
        })
      })
    },
    getPhotoUrlByTitle(files, key) {
      let filePathUrl = null
      files.forEach(function (item) {
        if (item.title == key) {
          filePathUrl = item.file_path_url
          return
        }
      })
      return filePathUrl
    },
    deleteParticipant(item) {
      this.$swal({
        title: `Delete Participant ${item.name}?`,
        text: 'It cannot be reverted',
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
    deleteFile(title) {
      const vForm = new FormData()
      vForm.append('participant_id', this.FormData.id)
      vForm.append('title', title)
      this.$swal({
        title: `Delete ${title}?`,
        text: 'It cannot be reverted',
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
          deleteFile(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: 'File has been deleted successfully', timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.fileMiladPhoto = null
            this.FormData.photo = null
            this.refetchData()
          }).catch(error => {
            this.isSubmitModal = false
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    getDomisiliCities(value) {
      // get all city data
      getCities(value).then(response => {
        this.domisili_cities = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getDomisiliDistricts(value) {
      // get all district data
      getDistricts(value).then(response => {
        this.domisili_districts = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getDomisiliSubdistricts(value) {
      // get all subdistrict data
      getSubdistricts(value).then(response => {
        this.domisili_subdistricts = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getDomisiliPostalcodes(value) {
      // get all subdistrict data
      getPostalcodes(value, this.FormData.home_kecamatan).then(response => {
        this.domisili_postalcodes = response.data
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    exportParticipant() {
      this.isSubmitModal = true
      const vForm = {}
      vForm.trip = this.trip
      vForm.gender = this.genderFilter
      vForm.totalTrip = this.totalTripFilter
      vForm.city = this.cityFilter
      vForm.province = this.provinceFilter
      vForm.package = this.packageFilter
      vForm.age = this.ageFilter
      vForm.job = this.jobFilter
      vForm.totalTransaction = this.totalTransactionFilter
      vForm.totalAccount = this.totalAccountFilter
      vForm.parentAccount = this.parentAccountFilter
      vForm.hasPhone = this.hasPhoneFilter
      vForm.hasInstagram = this.hasInstagramFilter
      vForm.hasLinkedIn = this.hasLinkedInFilter
      vForm.needMerge = this.needMergeFilter
      vForm.oldData = this.oldDataFilter
      vForm.date = this.dateFilter
      vForm.year = this.yearFilter
      exportParticipant(vForm).then(response => {
          this.isSubmitModal = false
          const fileURL = window.URL.createObjectURL(new Blob([response.data]))
          const fileLink = document.createElement('a')
          const contentDisposition = response.headers['content-disposition']
          fileLink.href = fileURL;
          let fileName = 'unknown';
          if (contentDisposition) {
              contentDisposition.replace(/['"]+/g, '')
              const fileNameMatch = contentDisposition.match(/filename=(.+)/);
              if (fileNameMatch.length === 2)
                  fileName = fileNameMatch[1];
          }
          fileLink.setAttribute('download', fileName);
          document.body.appendChild(fileLink);
          fileLink.click();
      }).catch(error => {
          this.isSubmitModal = false
          this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    },
    downloadCertificate() {
      if(this.trip == null) {
        this.$swal({ icon: 'error', title: 'Error', text: `Please select trip`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        return
      }
      const vForm = {}
      vForm.trip = this.trip
      downloadCertificate(vForm).then(response => {
          this.$swal({ icon: 'success', title: 'Success', text: `System is Now Downloading File and When it's Finished, System will Send Download Link to email`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
      }).catch(error => { this.$bvToast.toast(`Error: ${error.response.statusText}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true }) })
    },
    generateCertificate(participantId) {
        const vForm = {}
        getParticipantCertificate(participantId, vForm).then(response => {
            const fileURL = window.URL.createObjectURL(new Blob([response.data]))
            const fileLink = document.createElement('a')
            const contentDisposition = response.headers['content-disposition']
            fileLink.href = fileURL;
            let fileName = 'unknown';
            if (contentDisposition) {
                const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
                if (fileNameMatch.length === 2)
                    fileName = fileNameMatch[1];
            }
            fileLink.setAttribute('download', fileName);
            document.body.appendChild(fileLink);
            fileLink.click();
        }).catch(error => { this.$bvToast.toast(`Error: ${error.response.statusText}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true }) })
    },
    viewParentAccount(item) {
      this.isLoading = true
      this.viewParentAccountModal = true
      viewParentAccount(item.id).then(response => {
        this.isLoading = false
        this.tripHistories = response.data
      }).catch(error => {
        this.isLoading = false
        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    },
    updateDatabase() {
      this.isSubmitModal = true
      const vForm = {}
      vForm.update = true
      updateDatabaseCRM(vForm).then(response => {
          this.isSubmitModal = false
          this.refetchData()
      }).catch(error => {
          this.isSubmitModal = false
          this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    },
    updateDatabaseHajiKhusus() {
      this.isSubmitModal = true
      const vForm = {}
      vForm.haji = true
      updateDatabaseCRM(vForm).then(response => {
          this.isSubmitModal = false
          this.refetchData()
      }).catch(error => {
          this.isSubmitModal = false
          this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    }
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
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
