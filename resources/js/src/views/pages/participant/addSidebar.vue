<template>
  <b-sidebar id="add-new-sidebar" :visible="isAddSidebarActive" bg-variant="white" sidebar-class="sidebar-lg" shadow
    backdrop no-close-on-backdrop no-header right @hidden="resetUserData"
    @change="(val) => $emit('update:is-add-sidebar-active', val)">
    <template #default="{ hide }">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
        <h5 class="mb-0">
          Add New Participant
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

          <h4>Biodata Participant</h4>
          <hr>

          <!-- Title -->
          <validation-provider #default="{ errors }" name="Title" vid="title" rules="required">
            <b-form-group label="Title" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.title" :options="titleOptions" :clearable="false"
                :reduce="label => label.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Gelar Nama Depan -->
          <validation-provider #default="{ errors }" name="Gelar Nama Depan" vid="front_title">
            <b-form-group label="Gelar Nama Depan">
              <b-form-input v-model="formData.front_title" name="front_title" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Name -->
          <validation-provider #default="{ errors }" name="Full Name" vid="name" rules="required">
            <b-form-group label="Full Name">
              <b-form-input v-model="formData.name" name="name" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Gelar Nama Belakang -->
          <validation-provider #default="{ errors }" name="Gelar Nama Belakang" vid="back_title">
            <b-form-group label="Gelar Nama Belakang">
              <b-form-input v-model="formData.back_title" name="back_title" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Nama Ayah -->
          <validation-provider #default="{ errors }" name="Nama Ayah" vid="fathers_name" rules="required">
            <b-form-group label="Nama Ayah">
              <b-form-input id="fathers_name" v-model="formData.fathers_name" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Gender -->
          <validation-provider #default="{ errors }" name="Gender" vid="gender" rules="required">
            <b-form-group label="Gender" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.gender" :options="genderOptions" :clearable="false"
                :reduce="label => label.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Birth Place -->
          <validation-provider #default="{ errors }" name="Birth Place" vid="birth_place" rules="required">
            <b-form-group label="Birth Place">
              <b-form-input id="birth_place" v-model="formData.birth_place" :state="errors.length > 0 ? false : null"
                trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Birth Date -->
          <validation-provider #default="{ errors }" vid="birth_date" rules="required" name="Birth Date">
            <b-form-group label="Birth Date" :state="errors.length > 0 ? false : null" description="Pastikan format tanggal DD MM YYYY">
              <flat-pickr v-model="formData.birth_date" class="form-control" :config="birthDateConfig" />

              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- NIK -->
          <validation-provider #default="{ errors }" name="NIK (KTP)" vid="nik" rules="numeric">
            <b-form-group label="NIK (KTP)" label-for="nik">
              <b-form-input v-model="formData.nik" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Status Pernikahan -->
          <validation-provider #default="{ errors }" name="Status Pernikahan" vid="married_status" rules="required">
            <b-form-group label="Status Pernikahan" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.married_status" :options="marriedOptions" :clearable="false"
              :reduce="label => label.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Wajib Buku Nikah -->
          <validation-provider #default="{ errors }" name="Wajib Buku Nikah" vid="wedding_book_required">
              <b-form-group label="Wajib Buku Nikah">
              <b-form-checkbox v-model="formData.wedding_book_required" name="check-button" switch />

              <b-form-invalid-feedback>
                  {{ errors[0] }}
              </b-form-invalid-feedback>
              </b-form-group>
          </validation-provider>

          <!-- Kewarganegaraan -->
          <validation-provider #default="{ errors }" name="Kewarganegaraan" vid="nationality" rules="required">
            <b-form-group label="Kewarganegaraan">
              <b-form-input id="nationality" v-model="formData.nationality" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <h4 class="mt-3">Informasi Kontak</h4>
          <hr>

          <!-- No HP -->
          <validation-provider #default="{ errors }" name="No HP" vid="no_hp" rules="numeric|max:14">
            <b-form-group label="No HP" label-for="no_hp">
              <b-form-input v-model="formData.no_hp" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
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

          <!-- Instagram -->
          <validation-provider #default="{ errors }" name="Instagram" vid="instagram">
            <b-form-group label="Instagram">
              <b-form-input id="instagram" v-model="formData.instagram" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <h4 class="mt-3">Alamat Sesuai KTP</h4>
          <hr>

          <!-- Province -->
          <validation-provider #default="{ errors }" name="Provinsi" vid="ktp_province" rules="required">
            <b-form-group label="Provinsi" :state="errors.length > 0 ? false : null">
              <v-select @input="getKTPCities" v-model="formData.ktp_province" :options="provinces" :clearable="false"
              :reduce="province => province.province" label="province" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Kota -->
          <validation-provider #default="{ errors }" name="Kota" vid="ktp_city" rules="required">
            <b-form-group label="Kota" :state="errors.length > 0 ? false : null">
              <v-select @input="getKTPDistricts" v-model="formData.ktp_city" :options="ktp_cities" :clearable="false"
              :reduce="city => city.city" label="city" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Kecamatan -->
          <validation-provider #default="{ errors }" name="Kecamatan" vid="ktp_kecamatan" rules="required">
            <b-form-group label="Kecamatan" :state="errors.length > 0 ? false : null">
              <v-select @input="getKTPSubdistricts" v-model="formData.ktp_kecamatan" :options="ktp_districts" :clearable="false"
              :reduce="district => district.district" label="district" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Kelurahan -->
          <validation-provider #default="{ errors }" name="Kelurahan" vid="ktp_kelurahan" rules="required">
            <b-form-group label="Kelurahan" :state="errors.length > 0 ? false : null">
              <v-select @input="getKTPPostalcodes" v-model="formData.ktp_kelurahan" :options="ktp_subdistricts" :clearable="false"
              :reduce="subdistrict => subdistrict.subdistrict" label="subdistrict" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Kode Pos -->
          <validation-provider #default="{ errors }" name="Kode Pos" vid="ktp_postalcode" rules="required">
            <b-form-group label="Kode Pos">
              <v-select taggable v-model="formData.ktp_postalcode" :options="ktp_postalcodes" :clearable="false" :reduce="postalcode => postalcode.postalcode" label="postalcode" />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Alamat Lengkap Sesuai KTP -->
          <validation-provider #default="{ errors }" name="Alamat Lengkap Sesuai KTP" vid="ktp_address" rules="required">
            <b-form-group label="Alamat Lengkap Sesuai KTP">
              <b-form-textarea id="ktp_address" v-model="formData.ktp_address" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <h4 class="mt-3">Alamat Domisili / Alamat Pengiriman</h4>
          <hr>

          <!-- Provinsi -->
          <validation-provider #default="{ errors }" name="Provinsi" vid="home_province" rules="required">
            <b-form-group label="Provinsi" :state="errors.length > 0 ? false : null">
              <v-select @input="getDomisiliCities" v-model="formData.home_province" :options="provinces" :clearable="false"
              :reduce="province => province.province" label="province" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Kota -->
          <validation-provider #default="{ errors }" name="Kota" vid="home_city" rules="required">
            <b-form-group label="Kota" :state="errors.length > 0 ? false : null">
              <v-select @input="getDomisiliDistricts" v-model="formData.home_city" :options="domisili_cities" :clearable="false"
              :reduce="city => city.city" label="city" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Kecamatan -->
          <validation-provider #default="{ errors }" name="Kecamatan" vid="home_kecamatan" rules="required">
            <b-form-group label="Kecamatan" :state="errors.length > 0 ? false : null">
              <v-select @input="getDomisiliSubdistricts" v-model="formData.home_kecamatan" :options="domisili_districts" :clearable="false"
              :reduce="district => district.district" label="district" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Kelurahan -->
          <validation-provider #default="{ errors }" name="Kelurahan" vid="home_kelurahan" rules="required">
            <b-form-group label="Kelurahan" :state="errors.length > 0 ? false : null">
              <v-select @input="getDomisiliPostalcodes" v-model="formData.home_kelurahan" :options="domisili_subdistricts" :clearable="false"
              :reduce="subdistrict => subdistrict.subdistrict" label="subdistrict" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Kode Pos -->
          <validation-provider #default="{ errors }" name="Kode Pos" vid="home_postalcode" rules="required">
            <b-form-group label="Kode Pos">
              <v-select taggable v-model="formData.home_postalcode" :options="domisili_postalcodes" :clearable="false" :reduce="postalcode => postalcode.postalcode" label="postalcode" />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Alamat Lengkap Domisili -->
          <validation-provider #default="{ errors }" name="Alamat Lengkap Domisili" vid="home_address" rules="required">
            <b-form-group label="Alamat Lengkap Domisili">
              <b-form-textarea id="home_address" v-model="formData.home_address" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <h4 class="mt-3">Pendidikan dan Pekerjaan</h4>
          <hr>

          <!-- Pendidikan -->
          <validation-provider #default="{ errors }" name="Pendidikan" vid="education" rules="required">
            <b-form-group label="Pendidikan" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.education" :options="educationOptions" :clearable="false"
              :reduce="label => label.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Apakah Participant ini Dokter? -->
          <validation-provider #default="{ errors }" name="Apakah Participant ini Dokter?" vid="is_doctor" rules="required">
            <b-form-group label="Apakah Participant ini Dokter?" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.is_doctor" :options="yesNoOptions" :clearable="false"
              :reduce="label => label.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <div v-if="formData.is_doctor === 1">
            <!-- Spesialis -->
            <validation-provider #default="{ errors }" name="Spesialis" vid="doctor_specialist">
              <b-form-group label="Spesialis">
                <b-form-input id="doctor_specialist" v-model="formData.doctor_specialist" :state="errors.length > 0 ? false : null"
                trim />

                <b-form-invalid-feedback>
                {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Sertifikat Dokter -->
            <validation-provider #default="{ errors }" name="Sertifikat Dokter" vid="doctor_evidence">
              <b-form-group label="Sertifikat Dokter">
                <b-form-input id="doctor_evidence" v-model="formData.doctor_evidence" :state="errors.length > 0 ? false : null"
                trim />

                <b-form-invalid-feedback>
                {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <!-- Pekerjaan -->
          <validation-provider #default="{ errors }" name="Pekerjaan" vid="job" rules="required">
            <b-form-group label="Pekerjaan" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.job" :options="jobOptions" :clearable="false"
              :reduce="label => label.name" label="name" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Nama Perusahaan / Instansi -->
          <validation-provider #default="{ errors }" name="Nama Perusahaan / Instansi" vid="company_name">
            <b-form-group label="Nama Perusahaan / Instansi">
              <b-form-input id="company_name" v-model="formData.company_name" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <h4 class="mt-3">Riwayat Kesehatan</h4>
          <hr>

          <validation-provider #default="{ errors }" name="Ukuran Badan" vid="body_size" rules="required">
            <b-form-group label="Ukuran Badan">
                <v-select v-model="formData.body_size" :options="bodySizeOptions" :clearable="false"
                :reduce="label => label.value" />
                <b-form-invalid-feedback>
                {{ errors[0] }}
                </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Chest Size -->
          <validation-provider #default="{ errors }" name="Lingkar Dada" vid="chest_size" rules="numeric">
              <b-form-group label="Lingkar Dada" label-for="chest_size">
              <b-form-input v-model="formData.chest_size" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                  {{ errors[0] }}
              </b-form-invalid-feedback>
              </b-form-group>
          </validation-provider>

          <!-- Body Height -->
          <validation-provider #default="{ errors }" name="Tinggi Badan" vid="body_height" rules="numeric">
              <b-form-group label="Tinggi Badan" label-for="body_height">
              <b-form-input v-model="formData.body_height" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
                  {{ errors[0] }}
              </b-form-invalid-feedback>
              </b-form-group>
          </validation-provider>
          
          <!-- Golongan Darah -->
          <validation-provider #default="{ errors }" name="Golongan Darah" vid="blood_type">
            <b-form-group label="Golongan Darah" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.blood_type" :options="bloodOptions" :clearable="false"
              :reduce="label => label.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Riwayat Penyakit -->
          <validation-provider #default="{ errors }" name="Riwayat Penyakit" vid="medical_record">
            <b-form-group label="Riwayat Penyakit">
              <b-form-input id="medical_record" v-model="formData.medical_record" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Keterangan Penyakit -->
          <validation-provider #default="{ errors }" name="Keterangan Penyakit" vid="medical_description">
            <b-form-group label="Keterangan Penyakit">
                <b-form-textarea id="medical_description" v-model="formData.medical_description" :state="errors.length > 0 ? false : null"
                trim />

                <b-form-invalid-feedback>
                {{ errors[0] }}
                </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <h4 class="mt-3">Kontak Darurat</h4>
          <hr>

          <!-- Nomor Kontak Darurat -->
          <validation-provider #default="{ errors }" name="Nomor Kontak Darurat" vid="emergency_contact" rules="numeric">
            <b-form-group label="Nomor Kontak Darurat" label-for="emergency_contact">
              <b-form-input v-model="formData.emergency_contact" :state="errors.length > 0 ? false : null" trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Nama Kontak Darurat -->
          <validation-provider #default="{ errors }" name="Nama Kontak Darurat" vid="emergency_contact_name" rules="required">
            <b-form-group label="Nama Kontak Darurat">
              <b-form-input id="emergency_contact_name" v-model="formData.emergency_contact_name" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Relasi / Hubungan -->
          <validation-provider #default="{ errors }" name="Relasi / Hubungan" vid="emergency_relation" rules="required">
            <b-form-group label="Relasi / Hubungan">
              <b-form-input id="emergency_relation" v-model="formData.emergency_relation" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Alamat Kontak Darurat -->
          <validation-provider #default="{ errors }" name="Alamat Kontak Darurat" vid="emergency_address" rules="required">
            <b-form-group label="Alamat Kontak Darurat">
              <b-form-textarea id="emergency_address" v-model="formData.emergency_address" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>


          <!-- Punya Passport? -->
          <validation-provider #default="{ errors }" name="Punya Passport?" vid="have_passport" rules="required">
            <b-form-group label="Punya Passport?" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.have_passport" :options="yesNoOptions" :clearable="false"
              :reduce="label => label.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <div v-if="formData.have_passport === 1">
            <!-- Nama di Passport -->
            <validation-provider #default="{ errors }" name="Nama di Passport" vid="name_in_passport">
              <b-form-group label="Nama di Passport">
                  <b-form-input id="name_in_passport" v-model="formData.name_in_passport" :state="errors.length > 0 ? false : null"
                  trim />

                  <b-form-invalid-feedback>
                  {{ errors[0] }}
                  </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- No Passport -->
            <validation-provider #default="{ errors }" vid="no_passport" name="No Passport">
              <b-form-group label="No Passport" label-for="no_passport">
                  <b-form-input v-model="formData.no_passport" :state="errors.length > 0 ? false : null" trim />

                  <b-form-invalid-feedback>
                  {{ errors[0] }}
                  </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Catatan Revisi Data -->
            <validation-provider #default="{ errors }" vid="participant_notes" name="Catatan Revisi Data">
              <b-form-group label="Catatan Revisi Data" label-for="participant_notes">
                <b-form-input v-model="formData.participant_notes" :state="errors.length > 0 ? false : null" trim />

                <b-form-invalid-feedback>
                {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Tanggal Terbit -->
            <validation-provider #default="{ errors }" vid="passport_published_date" name="Tanggal Terbit">
              <b-form-group label="Tanggal Terbit" :state="errors.length > 0 ? false : null">
                <flat-pickr v-model="formData.passport_published_date" class="form-control" />

                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Berlaku Sampai -->
            <validation-provider #default="{ errors }" vid="passport_expired_date" name="Berlaku Sampai">
              <b-form-group label="Berlaku Sampai" :state="errors.length > 0 ? false : null">
                <flat-pickr v-model="formData.passport_expired_date" class="form-control" />

                <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Diterbitkan Oleh -->
            <validation-provider #default="{ errors }" vid="passport_held_by" name="Diterbitkan Oleh">
              <b-form-group label="Diterbitkan Oleh" label-for="passport_held_by">
                <b-form-input v-model="formData.passport_held_by" :state="errors.length > 0 ? false : null" trim />

                <b-form-invalid-feedback>
                {{ errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </div>

          <!-- Photo -->
          <validation-provider #default="{ errors }" vid="photo" name="Photo">
            <b-form-group label="Pas Photo">
              <b-form-file accept="image/jpeg, image/png, image/webp" v-model="formData.photo"
                :state="errors.length > 0 ? false : null" placeholder="Choose a photo or drop it here..."
                drop-placeholder="Drop photo here..." />

              <b-form-invalid-feedback>
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Status Data -->
          <validation-provider #default="{ errors }" name="Status Data" vid="participant_status" rules="required">
            <b-form-group label="Status Data" :state="errors.length > 0 ? false : null">
              <v-select v-model="formData.participant_status" :options="dataStatusOptions" :clearable="false"
              :reduce="label => label.value" />
              <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          
          <!-- Booking Order -->
          <validation-provider #default="{ errors }" name="Booking Order" vid="suggest_booking_order">
            <b-form-group label="Booking Order">
              <b-form-input id="suggest_booking_order" v-model="formData.suggest_booking_order" :state="errors.length > 0 ? false : null"
              trim />

              <b-form-invalid-feedback>
              {{ errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Package -->
          <validation-provider #default="{ errors }" name="Package" vid="suggest_package" >
            <b-form-group label="Package" :state="errors.length > 0 ? false : null">
                <v-select v-model="formData.suggest_package" :options="packageNameOptions" :clearable="false" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                      {{ errors[0] }}
                  </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>
          
          <!-- Room Type -->
          <validation-provider #default="{ errors }" name="Room Type" vid="suggest_room" >
            <b-form-group label="Room Type" :state="errors.length > 0 ? false : null">
                <v-select v-model="formData.suggest_room" :options="roomTypeOptions" :clearable="false" />
                  <b-form-invalid-feedback :state="errors.length > 0 ? false : null">
                      {{ errors[0] }}
                  </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <!-- Form Actions -->
          <div class="d-flex mt-2 mb-5">
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
import { BSidebar, BForm, BFormGroup, BFormInput, BFormTextarea, BFormFile, BFormInvalidFeedback, BButton, BAlert, BSpinner, BFormCheckbox } from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, numeric, email } from '@validations'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import { postData } from '@/network/participant'
import flatPickr from 'vue-flatpickr-component'
import { getProvinces, getCities, getDistricts, getSubdistricts, getPostalcodes } from '@/network/address'

export default {
  components: {
    BSidebar,
    BForm,
    BFormGroup,
    BFormInput,
    BFormTextarea,
    BFormFile,
    BAlert,
    BFormInvalidFeedback,
    BButton,
    BSpinner,
    vSelect,
    flatPickr,
    BFormCheckbox,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  model: {
    prop: 'isAddSidebarActive',
    event: 'update:is-add-sidebar-active',
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
    titleOptions: {
      type: Array,
      required: true,
    },
    marriedOptions: {
      type: Array,
      required: true,
    },
    educationOptions: {
      type: Array,
      required: true,
    },
    yesNoOptions: {
      type: Array,
      required: true,
    },
    jobOptions: {
      type: Array,
      required: true,
    },
    bloodOptions: {
      type: Array,
      required: true,
    },
    bodySizeOptions: {
      type: Array,
      required: true,
    },
    dataStatusOptions: {
      type: Array,
      required: true,
    },
  },
  data() {
    const provinces = []
    const ktp_cities = []
    const ktp_districts = []
    const ktp_subdistricts = []
    const ktp_postalcodes = []
    const domisili_cities = []
    const domisili_districts = []
    const domisili_subdistricts = []
    const domisili_postalcodes = []
    const roomTypeOptions = [
        { label: 'Double', value: 'double' },
        { label: 'Triple', value: 'triple' },
        { label: 'Quad', value: 'quad' },
    ]

    const packageNameOptions = [
        { label: 'Ruby', value: 'Ruby' },
        { label: 'Emerald', value: 'Emerald' },
        { label: 'Sapphire', value: 'Sapphire' },
        { label: 'VIP', value: 'VIP' },
        { label: 'Plus', value: 'Plus' },
    ]
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

    const birthDateConfig = {
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
    }

    return {
      birthDateConfig,
      isButtonLoading: false,
      required,
      numeric,
      roomTypeOptions,
      packageNameOptions,
      email,
      formData: {},
      provinces, ktp_cities, ktp_districts, ktp_subdistricts, ktp_postalcodes, domisili_cities, domisili_districts, domisili_subdistricts, domisili_postalcodes
    }
  },
  methods: {
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
