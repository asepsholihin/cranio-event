<?php
use App\Support\NumberFormat;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        @font-face {
            font-family: 'Mulish';
            src: url({{ storage_path('private_assets/fonts/Mulish-Regular.ttf') }}) format("truetype");
        }

        @font-face {
            font-family: 'Mulish-Bold';
            src: url({{ storage_path('private_assets/fonts/Mulish-Bold.ttf') }}) format("truetype");
        }

        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font: 13.2pt "Mulish";
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            font-family: "Mulish-Bold";
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        .page {
            position: relative;
            background-color: #ffffff;
        }

        .main {
            position: relative;
            padding: 3.5cm 3cm 0 3cm;
        }

        .letter-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1000;
        }

        .letter-bg img {
            width: 26, 16cm;
            height: 37cm;
        }

        .subpage {
            position: relative;
            height: 29.7cm;
            z-index: 999;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .justify {
            text-align: justify;
        }

        .mt {
            margin-top: 10px;
        }

        .mt-2 {
            margin-top: 24px;
        }

        .mt-5 {
            margin-top: 2.4em;
        }

        .mt-3 {
            margin-top: 1.2em;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        p {
            margin-top: 0;
            margin-bottom: 8px;
        }

        .underline {
            text-decoration: underline;
        }

        .fw-bold {
            font-family: "Mulish-Bold";
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        p.indent {
            text-indent: 42px;
        }

        table.bordered td,
        table.bordered th {
            padding: 6px;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 21cm;
                height: 29.7cm;
                font: 12pt "Times New Roman";
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }

        ol {
            padding-left: 1em;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
    <title>SURAT PERJANJIAN PERJALANAN IBADAH HAJI FURODA PT. JEJAK IMANI BERKAH BERSAMA</title>
</head>

<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <div class="center">
                        <h4 class="mt-5 mb-0">SURAT PERJANJIAN PERJALANAN IBADAH HAJI FURODA<br>PT. JEJAK IMANI BERKAH
                            BERSAMA</h4>
                    </div>

                    <div class="left mt-5">
                        <p>Yang bertanda tangan di bawah ini:</p>
                        <table width="100%">
                            <tr>
                                <td width="28%">NAMA</td>
                                <td width="2%">:</td>
                                <td class="uppercase">H. M. Rizaldy Latief</td>
                            </tr>
                            <tr>
                                <td>NIK</td>
                                <td>:</td>
                                <td>3674030609880009</td>
                            </tr>
                            <tr>
                                <td valign="top">ALAMAT</td>
                                <td valign="top">:</td>
                                <td class="uppercase">Jl. Taruna VI No.79 Komplek Taruna Parahyangan RT.006 RW.002 Pasir Endah Ujung Berung, Bandung - Jawa Barat.</td>
                            </tr>
                            <tr>
                                <td>JABATAN</td>
                                <td>:</td>
                                <td class="uppercase">Direktur Utama</td>
                            </tr>
                        </table>
                    </div>

                    <div class="left mt">
                        <p>Dalam hal ini bertindak untuk dan atas nama <span class="fw-bold">PT. Jejak Imani Berkah
                                Bersama</span>, yang selanjutnya disebut sebagai <span class="fw-bold">“Pihak
                                Pertama”</span>.</p>
                        <table width="100%">
                            <tr>
                                <td valign="top" width="28%">NAMA</td>
                                <td valign="top" width="2%">:</td>
                                <td class="uppercase">{{ strtoupper($participant->name) }}</td>
                            </tr>
                            <tr>
                                <td valign="top">NIK</td>
                                <td valign="top">:</td>
                                <td>{{ $participant->kitas_number ?? $participant->nik }}</td>
                            </tr>
                            <tr>
                                <td valign="top">ALAMAT</td>
                                <td valign="top">:</td>
                                <td class="uppercase">{{ $address }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="left mt">
                        <p>Dalam hal ini bertindak untuk dan atas nama pribadi, yang selanjutnya disebut sebagai <span
                                class="fw-bold">“Pihak Kedua”</span>.</p>
                        <p class="justify">
                            Secara bersama-sama Pihak Pertama dan Pihak Kedua disebut <span class="fw-bold">“Para
                                Pihak”</span>.
                        </p>
                        <p class="justify">
                            Para Pihak sepakat atas penyelenggaraan perjalanan ibadah haji dengan ketentuan dan
                            syarat-syarat yang telah ditetapkan dan diatur di dalam Perjanjian ini.
                        </p>

                        <br>

                        <center>
                            <p><span class="fw-bold">Pasal 1<br>Bentuk Penyelenggaraan</span></p>
                        </center>
                        <ol>
                            <li class="justify">
                                Pihak Pertama merupakan Penyelenggara Ibadah Haji Khusus (PIHK) yang telah mendapatkan
                                izin resmi dari Kementerian Agama RI No. 394 Tahun 2021 tentang Izin Operasional PT.
                                Jejak Imani Berkah Bersama sebagai Penyelenggara Ibadah Haji Khusus.
                            </li>
                            <li class="justify">
                                Para Pihak telah sepakat bahwa Pihak Kedua telah menunjuk Pihak Pertama dan Pihak
                                Pertama untuk kepentingan Pihak Kedua telah bersedia untuk menyediakan Penyelenggaraan
                                Ibadah Haji Furoda.
                            </li>
                            <li class="justify">
                                Pihak Kedua telah menerima dan setuju atas penawaran Penyelenggaraan Ibadah Haji Furoda
                                dan Biaya Penyelenggaraan Ibadah Haji Furoda yang telah diajukan oleh Pihak Pertama.
                            </li>
                        </ol>

                        <br>

                        <center>
                            <p><span class="fw-bold">Pasal 2<br>Penyelenggaraan Ibadah Haji Furoda</span></p>
                        </center>
                        <ol>
                            <li class="justify">
                                Penyelenggaraan Ibadah Haji Furoda yang diberikan Pihak Pertama kepada Pihak Kedua
                                adalah sebagai berikut:
                            </li>
                        </ol>
                    </div>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>
                    <table width="100%">
                        <tr>
                            <td valign="top">Nama Program</td>
                            <td valign="top">:</td>
                            <td valign="top">Haji Furoda Jejak Imani</td>
                        </tr>
                        <tr>
                            <td valign="top">Tgl. Keberangkatan</td>
                            <td valign="top">:</td>
                            <td valign="top">Sesuai pada saat Visa Haji Furoda keluar</td>
                        </tr>
                        <tr>
                            <td valign="top">Paket</td>
                            <td valign="top">:</td>
                            <td valign="top">Haji Furoda</td>
                        </tr>
                        <tr>
                            <td valign="top">Fasilitas & Perlengkapan</td>
                            <td valign="top">:</td>
                            <td valign="top">Terlampir</td>
                        </tr>
                    </table>
                    <ol start="2">
                        <li class="justify">
                            Keberangkatan Haji Furoda Jejak Imani akan tetap dilaksanakan dan akan dikelola langsung
                            oleh Jejak Imani tanpa minimal kuota participant.
                        </li>
                        <li class="justify">
                            Tahun keberangkatan, Biaya Penyelenggaraan Ibadah Haji Furoda, dan fasilitas dapat berubah
                            sewaktu-waktu menurut kondisi/regulasi yang berlaku pada maskapai/pemerintah.
                        </li>
                    </ol>
                    <center>
                        <p><span class="fw-bold">Pasal 3<br>Pendaftaran</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pendaftaran dikatakan sah apabila Pihak Kedua telah melakukan pembayaran uang muka (<i>down
                                payment</i>) sebesar USD 11.000,- (sebelas ribu US Dolar).
                        </li>
                        <li class="justify">
                            Pihak Kedua mengisi formulir pendaftaran haji Furoda yang disediakan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Pihak Kedua menyerahkan Surat Keterangan Sehat dari Puskesmas setempat dan hasil pemeriksaan
                            laboratorium serta hasil pemeriksaan rontgen dari Rumah Sakit/Klinik. Diserahkan menjelang
                            waktu keberangkatan.
                        </li>
                        <li class="justify">
                            Pihak Kedua melengkapi dokumen-dokumen sebagai berikut:
                            <ul class="alphabet">
                                <li class="justify">
                                    Paspor dengan masa berlaku tidak kurang dari 9 (sembilan) bulan dari tanggal
                                    keberangkatan.
                                </li>
                                <li class="justify">
                                    Pas Foto berwarna latar belakang putih dengan fokus wajah 80% tidak memakai kacamata
                                    hitam, tidak memakai pakaian dinas dan untuk Wanita harus memakai jilbab. Ukuran
                                    foto : 2x3 = 5 lembar, 3x4 = 20 lembar dan 4x6 = 10 lembar.
                                </li>
                                <li class="justify">
                                    Fotocopy Kartu Pelajar dan Surat Keterangan Sekolah bagi Peserta Pelajar.
                                </li>
                                <li class="justify">
                                    Fotocopy Akte Lahir untuk Peserta Anak.
                                </li>
                                <li class="justify">
                                    Fotocopy KTP, Kartu Keluarga dan Fotocopy Buku Nikah (bagi Suami Istri)
                                    masing-masing 5 lembar.
                                </li>
                                <li class="justify">
                                    Fotocopy Passpor 5 lembar.
                                </li>
                                <li class="justify">
                                    Menandatangani Surat Kuasa dan Surat Pernyataan.
                                </li>
                            </ul>
                        </li>
                        <li class="justify">
                            Harga menggunakan US Dolar, akan disesuaikan dengan kurs pada tahun keberangkatan.
                        </li>
                        <li class="justify">
                            Apabila Pihak Kedua berusia di atas 70 (tujuh puluh) tahun atau memiliki keterbatasan fungsi
                            anggota tubuh atau indera atau keterbatasan secara mental, wajib didampingi oleh anggota
                            keluarga, teman atau saudara yang bertanggung jawab selama perjalanan.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 4<br>Pembayaran</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pembayaran Tahap I (pertama) berupa uang muka (<i>Down Payment</i>) Biaya Penyelenggaraan
                            Ibadah Haji Furoda sebesar USD 11.000,- (sebelas ribu US Dolar).
                        </li>
                        <li class="justify">
                            Pembayaran Tahap II (kedua) sebesar USD 5.000,- (lima ribu US Dolar) dilakukan pada saat
                            proses pengajuan VISA Haji Furoda, dan/atau.
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <ol start="3">
                        <li class="justify">
                            Pelunasan dilakukan pada H-35 keberangkatan.
                        </li>
                        <li class="justify">
                            Pembayaran Pihak Kedua dapat dinyatakan sah apabila dilakukan dengan transfer ke rekening
                            resmi Pihak Pertama yang ditunjuk oleh Pihak Pertama.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 5<br>Pembatalan</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Jika visa Haji Furoda/Haji Mandiri tidak disetujui oleh Pihak Kerajaan Arab Saudi, maka
                            pembayaran yang sudah dilakukan oleh Pihak Kedua akan dikembalikan 100% (seratus persen).
                        </li>
                        <li class="justify">
                            Jika terjadi pembatalan oleh Pihak Kedua sebelum Visa Furoda keluar, maka Pihak Kedua
                            dikenakan biaya pembatalan sebesar USD 2.500,- (dua ribu lima ratus US Dolar).
                        </li>
                        <li class="justify">
                            Jika terjadi pembatalan oleh Pihak Kedua setelah visa Furoda keluar, maka Pihak Kedua akan
                            dikenakan biaya pembatalan sebagai berikut:
                            <ul class="alphabet">
                                <li class="justify">
                                    Pembatalan setelah visa disetujui oleh Kerajaan Arab Saudi, dikenakan 100% (seratus
                                    persen) dari DP (<i>Down Payment</i>).
                                </li>
                                <li class="justify">
                                    Pembatalan setelah pelunasan akomodasi (tiket, hotel, maktab, dan akomodasi
                                    lainnya), dikenakan 100% (seratus persen) dari harga paket.
                                </li>
                            </ul>
                        </li>
                        <li class="justify">
                            Pembatalan yang dilakukan oleh salah satu pihak karena bencana alam, perang, wabah penyakit,
                            aksi teroris atau keadaan <i>Force Majeure</i> lainnya, maka ketentuan-ketentuan pembatalan
                            tergantung pada kebijakan pemerintah, maskapai, hotel dan agen-agen lainnya baik di dalam
                            maupun luar negeri.
                        </li>
                        <li class="justify">
                            Pihak Pertama berhak membatalkan pendaftaran Pihak Kedua yang belum melakukan pembayaran
                            uang muka (<i>down payment</i>) atau pelunasan sampai dengan batas waktu yang telah
                            ditentukan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Jika Pihak Kedua meninggal dunia sebelum tanggal keberangkatan, maka biaya pembatalan
                            mengacu pada point (1) dan (2).
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 6<br>Visa</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Kedutaan dan/atau Kerajaan Arab Saudi mempunyai keputusan mutlak atas pengajuan visa dan
                            Pihak Pertama tidak bisa menentukan kepastian visa Pihak Kedua ditolak atau diterima.
                        </li>
                        <li class="justify">
                            Pihak Pertama hanya bisa membantu mengajukan/memproses permohonan visa tersebut sesuai
                            prosedur dan dokumen yang diperlukan.
                        </li>
                        <li class="justify">
                            Penolakan dokumen oleh Kedutaan atau revisi kelengkapan dokumen pengajuan visa merupakan
                            keputusan Kedutaan.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 7<br>Pengembalian Uang (<i>Refund</i>)</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pembayaran yang sudah dilakukan oleh Pihak Kedua akan dikembalikan 100% (seratus persen)
                            kepada Pihak Kedua, jika visa Haji Furoda/Haji Mandiri tidak disetujui oleh Pihak Kerajaan
                            Arab Saudi.
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <ol start="2">
                        <li class="justify">
                            Tiket pesawat udara, kereta api, dan transportasi lainnya serta akomodasi yang tidak
                            terpakai tidak dapat diuangkan kembali (<i>non-refundable</i>).
                        </li>
                        <li class="justify">
                            Bila ada pelayanan dalam paket yang tidak digunakan oleh Pihak Kedua dikarenakan berhalangan
                            atau sakit selama perjalanan, Pihak Kedua tidak berhak menuntut uang kembali.
                        </li>
                        <li class="justify">
                            Bila Pihak Kedua tidak diijinkan masuk atau dikenakan tindakan deportasi oleh pihak imigrasi
                            Negara setempat (walaupun sudah memiliki visa), atau yang ditolak oleh perusahaan
                            penerbangan, atau dalam perjalanan menderita sakit, atau ada kelainan jiwa, atau dalam
                            perjalanan mengalami kecelakaan, yang terpaksa harus kembali atau menyimpang dari perjalanan
                            yang telah ditentukan dalam acara perjalanan, atau terpaksa membatalkan sebagian/hampir
                            seluruh perjalanan setelah keberangkatan, tidak berhak atas pengembalian uang atau bentuk
                            pengembalian lain apapun atas jasa-jasa yang belum atau tidak digunakan.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 8<br>Hak Pihak Pertama</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Menentukan Paket Penyelenggaraan Ibadah Haji Furoda dan Biaya Penyelenggaraan Ibadah Haji
                            Furoda.
                        </li>
                        <li class="justify">
                            Apabila tidak ada teman sekamar (berempat), maka calon haji akan dikenakan biaya paket
                            sesuai teman sekamar yang ada (harga paket sekamar berdua atau bertiga).
                        </li>
                        <li class="justify">
                            Harga paket bersifat sementara, harga tetap akan diinformasikan pada tahun keberangkatan.
                        </li>
                        <li class="justify">
                            Demi kenyamanan dan kelancaran perencanaan perjalanan, Pihak Pertama berhak meminta Pihak
                            Kedua untuk keluar dari rombongan apabila yang Pihak Kedua mencoba membuat kerusuhan,
                            mengacaukan acara perjalanan, meminta dengan paksa, dan memberikan informasi yang tidak
                            benar mengenai acara perjalanan, dll.
                        </li>
                        <li class="justify">
                            Mengganti hotel-hotel yang akan digunakan berhubung hotel tersebut sudah penuh dan mengganti
                            dengan hotel lain yang setaraf sesuai dengan pertimbangan.
                        </li>
                        <li class="justify">
                            Pihak Pertama berhak menagih selisih Biaya Penyelenggaraan Ibadah Haji Furoda kepada Pihak
                            Kedua jika terjadi kenaikan harga tiket, hotel (termasuk penyesuaian tipe kamar, sebagaimana
                            point 2 di atas), <i>airport tax</i>, dll.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 9<br>Kewajiban Pihak Pertama</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Memberikan bimbingan haji meliputi bimbingan manasik dan perjalanan haji.
                        </li>
                        <li class="justify">
                            Memberikan pelayanan transportasi yaitu pemberangkatan ke dan dari Arab Saudi dan selama di
                            Arab Saudi.
                        </li>
                        <li class="justify">
                            Memberikan pelayanan akomodasi dan konsumsi selama berada di Arab Saudi.
                        </li>
                        <li class="justify">
                            Memberikan pembinaan, pelayananan dan perlindungan kesehatan sebelum pemberangkatan ke dan
                            dari Arab Saudi dan selama di Arab Saudi.
                        </li>
                        <li class="justify">
                            Memberikan perlindungan berupa Asuransi Perjalanan Ibadah Haji.
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <center>
                        <p><span class="fw-bold">Pasal 10<br>Hak Pihak Kedua</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Mendapatkan bimbingan ibadah haji meliputi bimbingan manasik dan perjalanan ibadah haji.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan transportasi yaitu pemberangkatan ke dan dari Arab Saudi dan selama di
                            Arab Saudi.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan akomodasi dan konsumsi selama berada di Arab Saudi.
                        </li>
                        <li class="justify">
                            Mendapatkan pembimbing Haji atau Muthawwif yang sudah berpengalaman.
                        </li>
                        <li class="justify">
                            Mendapatkan pembinaan, pelayanan dan perlindungan kesehatan sebelum pemberangkatan ke dan
                            dari Arab Saudi dan selama di Arab Saudi.
                        </li>
                        <li class="justify">
                            Mendapatkan perlindungan berupa Asuransi Perjalanan Ibadah Haji.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan administrasi dan dokumen perjalanan ibadah haji, seperti visa dan
                            dokumen lain yang dianggap perlu.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 11<br>Kewajiban Pihak Kedua</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Membayar/melunasi Biaya Penyelenggaraan Ibadah Haji Furoda yang telah ditentukan oleh Pihak
                            Pertama, termasuk selisih biaya yang diakibatkan kenaikan harga tiket, hotel (termasuk
                            penyesuaian tipe kamar, sebagaimana Pasal 8 point 2), <i>airport tax</i>, dll.
                        </li>
                        <li class="justify">
                            Mengisi formulir pendaftaran dan melengkapi persyaratan dokumen perjalanan ibadah Haji
                            dengan data yang benar dan dapat dipertanggungjawabkan.
                        </li>
                        <li class="justify">
                            Mengikuti rangkaian kegiatan manasik haji yang telah dijadwalkan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Tidak melakukan <i>walimatul hajj (walimatussafar)</i> sebelum visa keluar.
                        </li>
                        <li class="justify">
                            Menjaga kenyamanan, kelancaran dan ketertiban perjalanan ibadah haji Furoda dengan mengikuti
                            rangkaian/jadwal perjalanan yang sudah disusun/diagendakan oleh Pihak Pertama.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 12<br>Batas Waktu Tinggal (<i>Overstay</i>)</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pihak Kedua bersedia berangkat dan pulang sesuai dengan jadwal yang sudah ditentukan oleh
                            Pihak Pertama.
                        </li>
                        <li class="justify">
                            Pihak Kedua tidak diperkenankan untuk melewati batas waktu tinggal (<i>overstay</i>), baik
                            di Makkah maupun di Madinah.
                        </li>
                        <li class="justify">
                            Jika Pihak Kedua melewati batas waktu tinggal (<i>overstay</i>), maka segala sesuatu yang
                            terjadi bukan menjadi tanggung jawab dari Pihak Pertama.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 13<br>Radiophone</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pihak Pertama bersedia/sanggup meminjamkan radiophone kepada Pihak Kedua selama jadwal
                            kegiatan Haji Furoda berlangsung.
                        </li>
                        <li class="justify">
                            Pihak Kedua menerima pinjaman radiophone selama jadwal kegiatan Haji Furoda berlangsung.
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <ol start="3">
                        <li class="justify">
                            Radiophone dikembalikan oleh Pihak Kedua dalam keadaan baik, lengkap dan utuh (seperti pada
                            saat awal radiophone diterima oleh Pihak Kedua) setelah semua rangkaian jadwal kegiatan Haji
                            Furoda selesai.
                        </li>
                        <li class="justify">
                            Apabila terjadi kerusakan atau kehilangan radiophone yang digunakan oleh Pihak Kedua karena
                            kelalaian atau kelupaan, maka Pihak Kedua berkewajiban membayar ganti rugi sebesar
                            Rp.750.000,- (tujuh ratus lima puluh ribu rupiah) sebagai pengganti atas radiophone yang
                            rusak atau hilang.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 14<br>Ketentuan Khusus</span></p>
                    </center>
                    <p>Pihak Pertama tidak bertanggungjawab dan tidak bisa dituntut atas beberapa kondisi sebagai
                        berikut:</p>
                    <ol>
                        <li class="justify">
                            Participant terpapar Covid-19 dalam pelaksanaan perjalanan ibadah Haji selama di tanah air, dalam
                            perjalanan, selama di Arab Saudi, hingga kembali di tanah air.
                        </li>
                        <li class="justify">
                            Perubahan atau pembatalan keberangkatan karena adanya participant dalam satu <i>flight</i>
                            keberangkatan yang terpapar Covid-19 dan/atau karena keputusan sepihak dari otoritas Arab
                            Saudi.
                        </li>
                        <li class="justify">
                            Perubahan acara perjalanan (<i>itinerary</i>) sebagai akibat adanya penerapan/penyesuaian
                            protokol Covid-19 dan atau keputusan sepihak dari otoritas Arab Saudi.
                        </li>
                        <li class="justify">
                            Biaya yang muncul akibat kondisi pada ayat (2) dan (3) di atas, menjadi tanggung jawab
                            participant.
                        </li>
                        <li class="justify">
                            Kecelakaan, kehilangan koper dan keterlambatan tibanya koper akibat tindakan pihak maskapai
                            penerbangan atau alat pengangkutan lainnya, maka standar penggantian didasarkan pada
                            ketentuan maskapai penerbangan internasional atau penyedia jasa pengangkutan yang digunakan.
                        </li>
                        <li class="justify">
                            Kehilangan barang pribadi, koper, titipan barang di bandara, hotel dan tindakan kriminal
                            yang menimpa Pihak Kedua selama perjalanan.
                        </li>
                        <li class="justify">
                            Keterlambatan atau pembatalan jadwal penerbangan, dan seluruh kejadian yang terjadi di luar
                            kuasa Pihak Pertama.
                        </li>
                        <li class="justify">
                            Perubahan atau berkurangnya acara perjalanan akibat dari bencana alam, kerusuhan dan lain
                            sebagainya yang bersifat <i>Force Majeure</i>.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 15<br>Deviasi</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Deviasi adalah perubahan, perpanjangan, penambahan/penyimpanan rute perjalanan di luar rute
                            perjalanan yang telah dijadwalkan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Deviasi dapat diproses apabila participant sudah melakukan pembayaran pertama (uang muka/<i>down
                                payment</i>) dan melampirkan fotocopy paspor.
                        </li>
                        <li class="justify">
                            Deviasi dapat dilakukan apabila jumlah participant yang berangkat dan yang pulang telah memenuhi
                            kuota dari ketentuan maskapai penerbangan.
                        </li>
                        <li class="justify">
                            Apabila deviasi sudah disetujui, maka akan dikenakan biaya sesuai dengan ketentuan maskapai
                            penerbangan dan tidak dapat kembali ke jadwal semula.
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <ol start="5">
                        <li class="justify">
                            Pihak Pertama tidak menjamin konfirmasi pesawat, hotel dan sebagainya bila participant
                            menghendaki perpanjangan jadwal Haji. Apabila permintaan deviasi tidak dapat disetujui oleh
                            pihak maskapai penerbangan, maka participant secara otomatis akan kembali ke jadwal semula.
                        </li>
                        <li class="justify">
                            Deviasi yang akan mempersingkat jadwal paket perjalanan, tidak diberikan pengurangan biaya
                            dari Biaya Penyelenggaraan Ibadah Haji Khusus standar semula.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 16<br>Lain-lain</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Hal-hal yang belum diatur dalam Perjanjian ini akan diatur kemudian dan menjadi satu
                            kesatuan dan bagian yang tidak terpisahkan dari Perjanjian ini.
                        </li>
                        <li class="justify">
                            Apabila terjadi perselisihan sehubungan dengan apa yang telah ditentukan dalam Perjanjian
                            ini, maka Para Pihak setuju untuk mengutamakan penyelesaian secara musyawarah dan bersifat
                            kekeluargaan.
                        </li>
                        <li class="justify">
                            Apabila musyawarah sebagaimana tersebut di atas tidak tercapai kesepakatan, maka
                            perselisihan tersebut harus diselesaikan melalui Pengadilan Negeri Tangerang Selatan.
                        </li>
                        <li class="justify">
                            Perjanjian ini tunduk dan ditafsirkan berdasarkan hukum yang berlaku di Republik Indonesia.
                        </li>
                    </ol>

                    <br>

                    <div class="center mt-3">
                        <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                        <table width="100%">
                            <tr>
                                <td width="50%" valign="top">
                                    <p><span class="fw-bold">Pihak Pertama</span></p>
                                    <br><br><br><br><br>
                                    <p><span style="text-decoration:underline" class="fw-bold">H. M. Rizaldy Latief</span><br>Direktur Utama</p>
                                    <p>PT. JEJAK IMANI BERKAH BERSAMA</p>
                                </td>
                                <td width="50%" valign="top">
                                    <p><span class="fw-bold">Pihak Kedua</span></p>
                                    <br><br>{{ $materai }}<br><br><br>
                                    <p><span style="text-decoration:underline"
                                            class="fw-bold">{{ ucwords(strtolower($participant->name)) }}</span></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <br><br>

                    <p><span class="fw-bold">Lampiran 1. Fasilitas Haji Furoda Jejak Imani</span></p>
                    <table class="bordered" border cellspacing="0" cellpadding="0">
                        <tr>
                            <th>No</th>
                            <th>Fasilitas</th>
                            <th>Keterangan</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>
                                Penginapan:<br>
                                - Transit<br>
                                - Makkah<br>
                                - Madinah<br>
                            </td>
                            <td align="center">
                                <br>
                                Hotel/Apartemen Transit<br>
                                Setaraf Bintang 5<br>
                                Setaraf Bintang 5<br>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Maktab Arafah & Mina</td>
                            <td align="center">Maktab Furoda</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Visa</td>
                            <td align="center">Visa Furoda</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Maskapai</td>
                            <td align="center">Saudia Airlines / Garuda / Qatar / Oman / Etihad</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Manasik</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Handling</td>
                            <td align="center">Indonesia dan Saudi</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Asuransi Perjalanan</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td>Air Zam-zam 5 liter</td>
                            <td align="center">Bergantung Kebijakan Arab Saudi untuk Visa Furoda</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
