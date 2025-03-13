<template>
  <b-card>
  <b-tabs>
       <!-- Tab: Detail Account -->
        <b-tab active>
            <template #title >
            <feather-icon
                icon="UserIcon"
                size="16"
                class="mr-0 mr-sm-50"
            />
            <span class="d-none d-sm-inline">Account</span>
            </template>
            <p class="pt-1">
                <!-- Form -->
                <b-form @submit.prevent="onSubmit" >
                <b-media class="mb-2">
                    <template #aside>
                    <b-avatar :src="formData.profile_thumbnail" :text="avatarText(formData.name)" size="90px" rounded />
                    </template>
                    <h4 class="mb-1">
                    {{ formData.name }}
                    </h4>
                    <div class="d-flex flex-wrap">
                    <b-button v-if="hasPermission('participant-add-or-edit')" variant="primary" @click="$refs.refInputEl.click()">
                        <input ref="refInputEl" type="file" accept="image/jpeg, image/png, image/webp" class="d-none" @input="inputImageRenderer">
                        <span class="d-none d-sm-inline">Change</span>
                        <feather-icon icon="EditIcon" class="d-inline d-sm-none" />
                    </b-button>
                    </div>
                </b-media>
                <!-- BODY -->
                <validation-observer ref="refObsForm">
                    <validation-provider #default="{ errors }" vid="message">
                    <b-alert variant="danger" show v-if="errors[0]">
                        <div class="alert-body">
                        {{ errors[0] }}
                        </div>
                    </b-alert>
                    </validation-provider>

                    <h3 class="mt-3 mb-2">Biodata</h3>
                    <b-row>
                    <!-- Title -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Title" vid="title">
                        <b-form-group label="Title" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.title" :options="titleOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Gelar Nama Depan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Gelar Nama Depan" vid="front_title">
                        <b-form-group label="Gelar Nama Depan">
                            <b-form-input v-model="formData.front_title" name="front_title" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Name -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Full Name" vid="name">
                        <b-form-group label="Full Name">
                            <b-form-input v-model="formData.name" name="name" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Gelar Nama Belakang -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Gelar Nama Belakang" vid="back_title">
                        <b-form-group label="Gelar Nama Belakang">
                            <b-form-input v-model="formData.back_title" name="back_title" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Nama Ayah -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Nama Ayah" vid="fathers_name">
                        <b-form-group label="Nama Ayah">
                            <b-form-input id="fathers_name" v-model="formData.fathers_name" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Gender -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Gender" vid="gender">
                        <b-form-group label="Gender" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.gender" :options="genderOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Birth Place -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Birth Place" vid="birth_place">
                        <b-form-group label="Birth Place">
                            <b-form-input id="birth_place" v-model="formData.birth_place" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Birth Date -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" vid="birth_date" name="Birth Date">
                        <b-form-group label="Birth Date" :state="errors.length > 0 ? false : null">
                            <flat-pickr v-model="formData.birth_date" class="form-control" />

                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kewarganegaraan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kewarganegaraan" vid="nationality">
                        <b-form-group label="Kewarganegaraan">
                            <v-select id="nationality" v-model="formData.nationality" :options="nationalityOptions"
                            :clearable="false" :reduce="(label) => label.value" />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Status Pernikahan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Status Pernikahan" vid="married_status">
                        <b-form-group label="Status Pernikahan" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.married_status" :options="marriedOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Wajib Buku Nikah -->
                    <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Wajib Buku Nikah" vid="wedding_book_required">
                          <b-form-group label="Wajib Buku Nikah">
                          <b-form-checkbox v-model="formData.wedding_book_required" name="check-button" switch />

                          <b-form-invalid-feedback>
                              {{ errors[0] }}
                          </b-form-invalid-feedback>
                          </b-form-group>
                      </validation-provider>
                    </b-col>
                    
                    <!-- NIK -->
                    <b-col cols="12" md="4" v-if="formData.nationality == 'WNI'">
                        <validation-provider #default="{ errors }" name="NIK (KTP)" vid="nik" rules="numeric">
                        <b-form-group label="NIK (KTP)" label-for="nik">
                            <b-form-input v-model="formData.nik" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- NIK -->
                    <b-col cols="12" md="4" v-if="formData.nationality == 'WNA'">
                        <validation-provider #default="{ errors }" name="KITAS" vid="kitas" rules="numeric">
                        <b-form-group label="KITAS" label-for="kitas">
                            <b-form-input v-model="formData.kitas" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>

                    <h3 class="mt-3 mb-2">Informasi Kontak</h3>
                    <b-row>
                    <!-- No HP -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="No HP" vid="no_hp" rules="numeric|max:14">
                        <b-form-group label="No HP" label-for="no_hp">
                            <b-form-input v-model="formData.no_hp" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Email -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Email" vid="email" rules="email">
                        <b-form-group label="Email" label-for="email">
                            <b-form-input v-model="formData.email" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Instagram -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Instagram" vid="instagram">
                        <b-form-group label="Instagram">
                            <b-form-input id="instagram" v-model="formData.instagram" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>

                    <h3 class="mt-3 mb-2">Alamat Sesuai KTP</h3>
                    <b-row>
                    <!-- Provinsi -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Provinsi" vid="ktp_province">
                        <b-form-group label="Provinsi" :state="errors.length > 0 ? false : null">
                            <v-select @input="getKTPCities" v-model="formData.ktp_province" :options="provinces" :clearable="false"
                            :reduce="province => province.province" label="province" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kota -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kota" vid="ktp_city">
                        <b-form-group label="Kota" :state="errors.length > 0 ? false : null">
                            <v-select @input="getKTPDistricts" v-model="formData.ktp_city" :options="ktp_cities" :clearable="false"
                            :reduce="city => city.city" label="city" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kecamatan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kecamatan" vid="ktp_kecamatan">
                        <b-form-group label="Kecamatan" :state="errors.length > 0 ? false : null">
                            <v-select @input="getKTPSubdistricts" v-model="formData.ktp_kecamatan" :options="ktp_districts" :clearable="false"
                            :reduce="district => district.district" label="district" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kelurahan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kelurahan" vid="ktp_kelurahan">
                        <b-form-group label="Kelurahan" :state="errors.length > 0 ? false : null">
                            <v-select @input="getKTPPostalcodes" v-model="formData.ktp_kelurahan" :options="ktp_subdistricts" :clearable="false"
                            :reduce="subdistrict => subdistrict.subdistrict" label="subdistrict" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kode Pos -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kode Pos" vid="ktp_postalcode">
                        <b-form-group label="Kode Pos" :state="errors.length > 0 ? false : null">
                            <b-form-input v-model="formData.ktp_postalcode" type="number" :state="errors.length > 0 ? false : null" trim />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Alamat Lengkap Sesuai KTP -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Alamat Lengkap Sesuai KTP" vid="ktp_address">
                        <b-form-group label="Alamat Lengkap Sesuai KTP">
                            <b-form-textarea id="ktp_address" v-model="formData.ktp_address" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>

                    <h3 class="mt-3 mb-2">Alamat Domisili / Alamat Pengiriman</h3>
                    <b-row>
                    <!-- Provinsi -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Provinsi" vid="home_province">
                        <b-form-group label="Provinsi" :state="errors.length > 0 ? false : null">
                            <v-select @input="getDomisiliCities" v-model="formData.home_province" :options="provinces" :clearable="false"
                            :reduce="province => province.province" label="province" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kota -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kota" vid="home_city">
                        <b-form-group label="Kota" :state="errors.length > 0 ? false : null">
                            <v-select @input="getDomisiliDistricts" v-model="formData.home_city" :options="domisili_cities" :clearable="false"
                            :reduce="city => city.city" label="city" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kecamatan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kecamatan" vid="home_kecamatan">
                        <b-form-group label="Kecamatan" :state="errors.length > 0 ? false : null">
                            <v-select @input="getDomisiliSubdistricts" v-model="formData.home_kecamatan" :options="domisili_districts" :clearable="false"
                            :reduce="district => district.district" label="district" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kelurahan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kelurahan" vid="home_kelurahan">
                        <b-form-group label="Kelurahan" :state="errors.length > 0 ? false : null">
                            <v-select @input="getDomisiliPostalcodes" v-model="formData.home_kelurahan" :options="domisili_subdistricts" :clearable="false"
                            :reduce="subdistrict => subdistrict.subdistrict" label="subdistrict" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Kode Pos -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Kode Pos" vid="home_postalcode">
                        <b-form-group label="Kode Pos" :state="errors.length > 0 ? false : null">
                            <b-form-input v-model="formData.home_postalcode" type="number" :state="errors.length > 0 ? false : null" trim />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Alamat Lengkap Domisili -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Alamat Lengkap Domisili" vid="home_address">
                        <b-form-group label="Alamat Lengkap Domisili">
                            <b-form-textarea id="home_address" v-model="formData.home_address" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>

                    <h3 class="mt-3 mb-2">Pendidikan dan Pekerjaan</h3>
                    <b-row>
                    <!-- Pendidikan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Pendidikan" vid="education">
                        <b-form-group label="Pendidikan" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.education" :options="educationOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Apakah Participant ini Dokter? -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Apakah Participant ini Dokter?" vid="is_doctor">
                        <b-form-group label="Apakah Participant ini Dokter?" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.is_doctor" :options="yesNoOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Spesialis -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Spesialis" vid="doctor_specialist">
                        <b-form-group label="Spesialis">
                            <b-form-input id="doctor_specialist" v-model="formData.doctor_specialist" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Sertifikat Dokter -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Sertifikat Dokter" vid="doctor_evidence">
                        <b-form-group label="Sertifikat Dokter">
                            <b-form-input id="doctor_evidence" v-model="formData.doctor_evidence" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Pekerjaan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Pekerjaan" vid="job">
                        <b-form-group label="Pekerjaan" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.job" :options="jobOptions" :clearable="false"
                            :reduce="label => label.name" label="name" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Nama Perusahaan / Instansi -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Nama Perusahaan / Instansi" vid="company_name">
                        <b-form-group label="Nama Perusahaan / Instansi">
                            <b-form-input id="company_name" v-model="formData.company_name" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>

                    <h3 class="mt-3 mb-2">Riwayat Kesehatan</h3>
                    <b-row>
                    <!-- Ukuran Badan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Ukuran Badan" vid="body_size">
                        <b-form-group label="Ukuran Badan">
                            <v-select v-model="formData.body_size" :options="bodySizeOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Chest Size -->
                    <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Lingkar Dada" vid="chest_size" rules="numeric">
                          <b-form-group label="Lingkar Dada" label-for="chest_size">
                          <b-form-input v-model="formData.chest_size" :state="errors.length > 0 ? false : null" trim />

                          <b-form-invalid-feedback>
                              {{ errors[0] }}
                          </b-form-invalid-feedback>
                          </b-form-group>
                      </validation-provider>
                    </b-col>

                    <!-- Body Height -->
                    <b-col cols="12" md="4">
                      <validation-provider #default="{ errors }" name="Tinggi Badan" vid="body_height" rules="numeric">
                          <b-form-group label="Tinggi Badan" label-for="body_height">
                          <b-form-input v-model="formData.body_height" :state="errors.length > 0 ? false : null" trim />

                          <b-form-invalid-feedback>
                              {{ errors[0] }}
                          </b-form-invalid-feedback>
                          </b-form-group>
                      </validation-provider>
                    </b-col>

                    <!-- Golongan Darah -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Golongan Darah" vid="blood_type">
                        <b-form-group label="Golongan Darah" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.blood_type" :options="bloodOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Riwayat Penyakit -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Riwayat Penyakit" vid="medical_record">
                        <b-form-group label="Riwayat Penyakit">
                            <b-form-input id="medical_record" v-model="formData.medical_record" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Keterangan Penyakit -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Keterangan Penyakit" vid="medical_description">
                        <b-form-group label="Keterangan Penyakit">
                            <b-form-textarea id="medical_description" v-model="formData.medical_description" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>

                    <h3 class="mt-3 mb-2">Kontak Darurat</h3>
                    <b-row>
                    <!-- Nomor Kontak Darurat -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Nomor Kontak Darurat" vid="emergency_contact" rules="numeric">
                        <b-form-group label="Nomor Kontak Darurat" label-for="emergency_contact">
                            <b-form-input v-model="formData.emergency_contact" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Nama Kontak Darurat -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Nama Kontak Darurat" vid="emergency_contact_name">
                        <b-form-group label="Nama Kontak Darurat">
                            <b-form-input id="emergency_contact_name" v-model="formData.emergency_contact_name" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Relasi / Hubungan -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Relasi / Hubungan" vid="emergency_relation">
                        <b-form-group label="Relasi / Hubungan">
                            <b-form-input id="emergency_relation" v-model="formData.emergency_relation" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Alamat Kontak Darurat -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Alamat Kontak Darurat" vid="emergency_address">
                        <b-form-group label="Alamat Kontak Darurat">
                            <b-form-textarea id="emergency_address" v-model="formData.emergency_address" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>

                    <h3 class="mt-3 mb-2">Informasi Passport</h3>
                    <b-row>
                    <!-- Punya Passport? -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Punya Passport?" vid="have_passport">
                        <b-form-group label="Punya Passport?" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.have_passport" :options="yesNoOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>

                    <div v-if="formData.have_passport == 2">
                    <b-button variant="primary">Ajukan Pembuatan Passport</b-button>
                    </div>
                    <div v-if="formData.have_passport === 1">
                    <b-row>
                      <!-- Nama di Passport -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" name="Nama di Passport" vid="name_in_passport">
                        <b-form-group label="Nama di Passport">
                            <b-form-input id="name_in_passport" v-model="formData.name_in_passport" :state="errors.length > 0 ? false : null"
                            trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- No Passport -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" vid="no_passport" name="No Passport">
                        <b-form-group label="No Passport" label-for="no_passport">
                            <b-form-input v-model="formData.no_passport" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Catatan Revisi Data -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" vid="participant_notes" name="Catatan Revisi Data">
                        <b-form-group label="Catatan Revisi Data" label-for="participant_notes">
                            <b-form-input v-model="formData.participant_notes" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Tanggal Terbit -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" vid="passport_published_date" name="Tanggal Terbit">
                        <b-form-group label="Tanggal Terbit" :state="errors.length > 0 ? false : null">
                            <flat-pickr v-model="formData.passport_published_date" class="form-control" />

                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Berlaku Sampai -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" vid="passport_expired_date" name="Berlaku Sampai">
                        <b-form-group label="Berlaku Sampai" :state="errors.length > 0 ? false : null">
                            <flat-pickr v-model="formData.passport_expired_date" class="form-control" />

                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>

                    <!-- Diterbitkan Oleh -->
                    <b-col cols="12" md="4">
                        <validation-provider #default="{ errors }" vid="passport_held_by" name="Diterbitkan Oleh">
                        <b-form-group label="Diterbitkan Oleh" label-for="passport_held_by">
                            <b-form-input v-model="formData.passport_held_by" :state="errors.length > 0 ? false : null" trim />

                            <b-form-invalid-feedback>
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>
                    </div>

                    <b-row class="mt-3">
                    <!-- Status Data -->
                    <b-col cols="12" md="6">
                        <validation-provider #default="{ errors }" name="Status Data" vid="participant_status">
                        <b-form-group label="Status Data" :state="errors.length > 0 ? false : null">
                            <v-select v-model="formData.participant_status" :options="dataStatusOptions" :clearable="false"
                            :reduce="label => label.value" />
                            <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                            {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        </validation-provider>
                    </b-col>
                    </b-row>
                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                    <b-button v-if="hasPermission('participant-add-or-edit')" v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="primary" class="mr-2" type="submit">
                        Save Changes
                    </b-button>
                    <b-button @click="$router.go(-1)" v-ripple.400="'rgba(186, 191, 199, 0.15)'" type="button"
                        variant="outline-secondary">
                        Back
                    </b-button>
                    </div>

                </validation-observer>
                </b-form>
            </p>
        </b-tab>

        <!-- Tab: Files -->
        <b-tab>
          <template #title >
            <feather-icon
                icon="FolderIcon"
                size="16"
                class="mr-0 mr-sm-50"
            />
            <span class="d-none d-sm-inline">Files</span>
          </template>
          <p class="pt-1">
            <files :participant.sync="formData" />
          </p>
        </b-tab>

    </b-tabs>
  </b-card>
</template>

<script>
import { BTab, BTabs, BCard, BLink, BFormInvalidFeedback, BButton, BMedia, BAvatar, BAlert, BForm, BRow, BCol, BFormGroup, BFormInput, BFormTextarea, BFormFile, BFormCheckbox } from 'bootstrap-vue'
import { getDetail, getJobSearch } from '@/network/participant'
import { getProvinces, getCities, getDistricts, getSubdistricts, getPostalcodes } from '@/network/address'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { avatarText } from '@core/utils/filter'
import { postData } from '@/network/participant'
import { hasPermission } from '@/auth/utils'
import files from './files.vue'

export default {
  components: {
    BCard,
    BTab,
    BTabs,
    BLink,
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
    BFormInput,
    BFormTextarea,
    BFormFile,
    BFormCheckbox,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
    files
  },
  directives: {
    Ripple,
  },
  setup() {
    const genderOptions = [{ label: 'Man', value: 1 }, { label: 'Woman', value: 2 }]
    const titleOptions = [{ label: 'Mr', value: 'Mr' }, { label: 'Ms', value: 'Ms' }, { label: 'Mrs', value: 'Mrs' }, { label: 'Mstr', value: 'Mstr' }, { label: 'Miss', value: 'Miss' }]
    const marriedOptions = [{ label: 'Menikah', value: 1 }, { label: 'Belum Menikah', value: 2 }, { label: 'Janda', value: 3 }, { label: 'Duda', value: 4 }]
    const educationOptions = [{ label: 'SD/MI', value: 'SD/MI' }, { label: 'SMP/MTS', value: 'SMP/MTS' }, { label: 'SMA/MA', value: 'SMA/MA' }, { label: 'D1', value: 'D1' }, { label: 'D2', value: 'D2' }, { label: 'D3', value: 'D3' }, { label: 'D4/S1', value: 'D4/S1' }, { label: 'S2', value: 'S2' }, { label: 'S3', value: 'S3' }, { label: 'BELUM SEKOLAH', value: 'BELUM SEKOLAH' }]
    const yesNoOptions = [{ label: 'Yes', value: 1 }, { label: 'No', value: 2 }]
    const bloodOptions = [{ label: 'A+', value: 'A+' }, { label: 'A-', value: 'A-' }, { label: 'B+', value: 'B+' }, { label: 'B-', value: 'B-' }, { label: 'AB+', value: 'AB+' }, { label: 'AB-', value: 'AB-' }, { label: 'O+', value: 'O+' }, { label: 'O-', value: 'O-' }, { label: 'Tidak Tahu', value: 'Tidak Tahu' }]
    const bodySizeOptions = [{ label: 'XS (Balita 0-5 tahun)', value: 'XS' }, { label: 'S (Anak 6-12 tahun)', value: 'S' }, { label: 'L (All Size)', value: 'L' }, { label: 'XL (Jumbo)', value: 'XL' }]
    const dataStatusOptions = [{ label: 'Lengkap', value: 1 }, { label: 'Belum Ada Passport', value: 2 }, { label: 'Passport Expired', value: 3 }, { label: 'Data Butuh Perbaikan / Penambahan Nama', value: 4 }, { label: 'Data Dikunci', value: 5 }]
    const nationalityOptions = [{ label: "WNI", value: "WNI" }, { label: "WNA", value: "WNA" }];

    return {
      avatarText, genderOptions, titleOptions, marriedOptions, educationOptions, yesNoOptions, bloodOptions, dataStatusOptions, hasPermission, bodySizeOptions, nationalityOptions
    }
  },

  methods: {
    totalTransaction: function (transactions) {
      return transactions.reduce((acc, val) => {
        return acc + parseInt(val.total_transaction_conv);
      }, 0);
    },
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
          this.$bvToast.toast('Participant has been changed successfully', {
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
    getKTPCities(value) {
      // get all city data
      getCities(value).then(response => {
        this.ktp_cities = response.data;
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getKTPDistricts(value) {
      // get all district data
      getDistricts(value).then(response => {
        this.ktp_districts = response.data;
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getKTPSubdistricts(value) {
      // get all subdistrict data
      getSubdistricts(value).then(response => {
        this.ktp_subdistricts = response.data;
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getKTPPostalcodes(value) {
      // get all subdistrict data
      getPostalcodes(value, this.formData.ktp_kecamatan).then(response => {
        this.ktp_postalcodes = response.data;
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
    getDomisiliCities(value) {
      // get all city data
      getCities(value).then(response => {
        this.domisili_cities = response.data;
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
        this.domisili_districts = response.data;
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
        this.domisili_subdistricts = response.data;
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
      getPostalcodes(value, this.formData.home_kecamatan).then(response => {
        this.domisili_postalcodes = response.data;
      }).catch(error => {
        if (error.response.data.errors) {
          this.$refs.refObsForm.setErrors(error.response.data.errors)
        } else {
          this.$refs.refObsForm.setErrors(error.response.data)
        }
      })
    },
  },
  data() {
    const formData = {}
    const provinces = []
    const ktp_cities = []
    const ktp_districts = []
    const ktp_subdistricts = []
    const ktp_postalcodes = []
    const domisili_cities = []
    const domisili_districts = []
    const domisili_subdistricts = []
    const domisili_postalcodes = []
    const transactions = []
    const jobOptions = []
    const id = parseInt(this.$route.params.id) || 0
    if (id == 0) this.$router.back()
    getDetail(id).then(response => {
      this.formData = response.data
      this.formData['participant_id'] = this.formData.id
      if(this.formData.ktp_province) {
        this.getKTPCities(this.formData.ktp_province);
      }
      if(this.formData.ktp_city) {
        this.getKTPDistricts(this.formData.ktp_city);
      }
      if(this.formData.ktp_kecamatan) {
        this.getKTPSubdistricts(this.formData.ktp_kecamatan);
      }
      if(this.formData.ktp_kelurahan) {
        this.getKTPPostalcodes(this.formData.ktp_kelurahan);
      }
      if(this.formData.home_province) {
        this.getDomisiliCities(this.formData.home_province);
      }
      if(this.formData.home_city) {
        this.getDomisiliDistricts(this.formData.home_city);
      }
      if(this.formData.home_kecamatan) {
        this.getDomisiliSubdistricts(this.formData.home_kecamatan);
      }
      if(this.formData.home_kelurahan) {
        this.getDomisiliPostalcodes(this.formData.home_kelurahan);
      }
    }).catch(error => {
      if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
      } else {
        this.$refs.refObsForm.setErrors(error.response.data)
      }
    })

    // get all provinces data
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

    return {
      formData, required, numeric, email, provinces, ktp_cities, ktp_districts, ktp_subdistricts, ktp_postalcodes, domisili_cities, domisili_districts, domisili_subdistricts, domisili_postalcodes, transactions, jobOptions
    }
  }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
