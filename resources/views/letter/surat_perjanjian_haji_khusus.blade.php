<?php
use App\Support\NumberFormat;
use App\Support\General;
use Carbon\Carbon;
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
            font: 12.8pt "Mulish";
            line-height: 15pt;
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

        .text-nowrap {
            text-wrap: nowrap;
            white-space: nowrap;
        }

        table.bordered td,
        table.bordered th {
            padding: 6px;
            font-size: 11pt;
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
    <title>SURAT PERJANJIAN HAJI KHUSUS PT. JEJAK IMANI BERKAH BERSAMA</title>
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
                        <h3 class="mt-5 mb-0">SURAT PERJANJIAN<br>ANTARA<br>PENYELENGGARA IBADAH HAJI KHUSUS<br>DENGAN<br>CALON JEMAAH IBADAH HAJI KHUSUS<br>TAHUN {{ $letterYearHijriah }}/{{ $letterYearMasehi }} M</h3>
                    </div>

                    <div class="left mt-5">
                        <p>Pada Hari Ini {{ $hari_ini }} Tanggal {{ date('d') }} {{ $bulan_ini }} Tahun {{ date('Y') }} ({{ $ejaan_tahun }}), kami yang bertanda tangan di bawah ini:</p>
                        <table width="100%" style="margin-left:24px">
                            <tr>
                                <td width="28%">Nama</td>
                                <td width="2%">:</td>
                                <td class="uppercase">H. M. Rizaldy Latief</td>
                            </tr>
                            <tr>
                                <td>NIK</td>
                                <td>:</td>
                                <td>3674030609880009</td>
                            </tr>
                            <tr>
                                <td valign="top">Alamat</td>
                                <td valign="top">:</td>
                                <td class="uppercase">Jl. Taruna VI No.79 Komplek Taruna Parahyangan RT.006 RW.002 Pasir Endah Ujung Berung, Bandung - Jawa Barat.</td>
                            </tr>
                            <tr>
                                <td>No. Pin Siskohat</td>
                                <td>:</td>
                                <td>3487</td>
                            </tr>
                        </table>
                    </div>

                    <div class="left mt">
                        <p>Dalam hal ini bertindak untuk dan atas nama <span class="fw-bold">PT Jejak Imani Berkah Bersama</span> sebagai Penyelenggara Ibadah Haji Khusus berdasarkan Surat Keputusan Kementerian Agama RI No. 394 tahun 2021 selanjutnya disebut sebagai <span class="fw-bold">PIHAK PERTAMA</span>.</p>
                        <table width="100%" style="margin-left:24px">
                            <tr>
                                <td width="28%">Nama</td>
                                <td width="2%">:</td>
                                <td class="uppercase">{{ strtoupper($participant->name) }}</td>
                            </tr>
                            <tr>
                                <td width="28%">Umur</td>
                                <td width="2%">:</td>
                                <td class="uppercase">{{ Carbon::parse($participant->birth_date)->age }} tahun</td>
                            </tr>
                            <tr>
                                <td>Pekerjaan/Jabatan</td>
                                <td>:</td>
                                <td class="uppercase">{{ $participant->job }}</td>
                            </tr>
                            <tr>
                                <td valign="top">Alamat</td>
                                <td valign="top">:</td>
                                <td class="uppercase">{{ $address_short }}</td>
                            </tr>
                            <tr>
                                <td valign="top">Kelurahan</td>
                                <td valign="top">:</td>
                                <td class="uppercase">{{ $participant->ktp_kelurahan }}</td>
                            </tr>
                            <tr>
                                <td valign="top">Kecamatan</td>
                                <td valign="top">:</td>
                                <td class="uppercase">{{ $participant->ktp_kecamatan }}</td>
                            </tr>
                            <tr>
                                <td valign="top">Kabupaten/Kotamadya</td>
                                <td valign="top">:</td>
                                <td class="uppercase">{{ $participant->ktp_city }}</td>
                            </tr>
                            <tr>
                                <td valign="top">Provinsi</td>
                                <td valign="top">:</td>
                                <td class="uppercase">{{ $participant->ktp_province }}</td>
                            </tr>
                            <tr>
                                <td valign="top">Kode Pos</td>
                                <td valign="top">:</td>
                                <td class="uppercase">{{ $participant->ktp_postalcode }}</td>
                            </tr>
                            <tr>
                                <td>Telepon.</td>
                                <td>:</td>
                                <td class="uppercase">{{ $participant->no_hp }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="left mt">
                        <p>Dalam hal ini bertindak untuk dan atas nama diri sendiri, sebagai jemaah ibadah haji khusus (selanjutnya disebut sebagai <span class="fw-bold">PIHAK KEDUA</span>).</p>
                        <p class="justify">
                            <span class="fw-bold">PIHAK PERTAMA</span> dan <span class="fw-bold">PIHAK KEDUA</span> selanjutnya dapat pula disebut sebagai <span class="fw-bold">“PIHAK”</span> apabila disebut sendiri-sendiri atau <span class="fw-bold">“PARA PIHAK”</span> apabila disebut secara bersama- sama.
                        </p>
                        
                        <br>
                        
                        <p class="justify">
                            <span class="fw-bold">PARA PIHAK</span> sebelumnya dengan ini menerangkan terlebih dahulu:
                        </p>

                        <ol>
                            <li class="justify">
                                Bahwa sesuai dengan Keputusan Direktur Jenderal Bimbingan Masyarakat Islam dan Pelayanan Haji No.:D/405 tahun 2012 tentang perubahan atas keputusan Direktur Jenderal Penyelenggaraan Haji dan Umroh No.:D/186 tahun 2012 tentang petunjuk
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

                    <div class="mt-3"></div>

                    <ul style="list-style:none;padding-left:1em;">
                        <li>Teknis Pelunasan Biaya Penyelenggara Ibadah Haji dan Pengurusan Dokumen Haji Khusus tahun 1436H/2015M Pasal 19 Ayat 1, <span class="fw-bold">PIHAK PERTAMA</span> selaku Penyelenggara Ibadah Haji Khusus antara lain berkewajiban untuk membuat dan menandatangani perjanjian dengan setiap calon jemaah yang berisi hak dan kewajiban PARA PIHAK (selanjutnya disebut sebagai “Perjanjian”).</li>
                    </ul>

                    <ol start="2">
                        <li class="justify">
                            Bahwa Perjanjian yang dibuat dan ditandatangani PARA PIHAK akan mengacu kepada ketentuan tentang Penyelenggaraan Ibadah Haji Khusus dan Ketentuan terkait lainnya yang dikeluarkan oleh Pemerintah Indonesia.
                        </li>
                    </ol>

                    <center>
                        <p>Pasal 1<br><span class="fw-bold">RUANG LINGKUP PERJANJIAN</span></p>
                    </center>
                    <p><span class="fw-bold">PIHAK PERTAMA</span> merupakan pihak Penyelenggara Ibadah Haji Khusus dan <span class="fw-bold">PIHAK KEDUA</span> adalah jemaah haji khusus yang akan mengikuti Paket Pelayanan Haji yang diselenggarakan oleh <span class="fw-bold">PIHAK PERTAMA</span>.</p>

                    <br>

                    <center>
                        <p>Pasal 2<br><span class="fw-bold">KEWAJIBAN <span class="fw-bold">PIHAK PERTAMA</span></span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Mengurus persyaratan kelengkapan administrasi pendaftaran dan penyelesaian dokumen <span class="fw-bold">PIHAK KEDUA</span> sesuai tata cara pendaftaran haji ke Direktorat Pembinaan Haji Kementerian Agama.
                        </li>
                        <li class="justify">
                            Menyerahkan 1 (satu) salinan dari perjanjian kepada Direktorat Pembinaan Haji dan Umrah Kementerian Agama.
                        </li>
                        <li class="justify">
                            Memberikan pelayanan kepada <span class="fw-bold">PIHAK KEDUA</span> sesuai dengan paket biaya yang disepakati.
                        </li>
                        <li class="justify">
                            Menyiapkan petugas pembimbing ibadah dan pelayanan kesehatan untuk <span class="fw-bold">PIHAK KEDUA</span>.
                        </li>
                        <li class="justify">
                            Menyerahkan buku-buku bimbingan manasik haji kepada <span class="fw-bold">PIHAK KEDUA</span>.
                        </li>
                        <li class="justify">
                            Memberangkatkan dan memulangkan <span class="fw-bold">PIHAK KEDUA</span> dengan tiket pergi pulang yang sudah confirmed dan sudah mendapat jaminan dari penerbangan.
                        </li>
                        <li class="justify">
                            Memberikan fasilitas akomodasi hotel kepada <span class="fw-bold">PIHAK KEDUA</span> selama menjalankan ibadah haji, untuk masa tinggal di Makkah dan Madinah dengan jarak hotel dari pagar Masjidil Haram dan Masjid Nabawi tidak melebihi 500 (lima ratus) meter.
                        </li>
                        <li class="justify">
                            Menyediakan pelayanan transportasi bus ber AC, konsumsi prasmanan serta bimbingan ibadah dan ceramah-ceramah kepada <span class="fw-bold">PIHAK KEDUA</span> selama berada di Tanah Suci.
                        </li>
                        <li class="justify">
                            Memberangkatkan <span class="fw-bold">PIHAK KEDUA</span> ke Padang Arafah untuk Wukuf dan membadal haji-kan bagi participant sakit yang tidak mungkin di safari wukufkan.
                        </li>
                        <li class="justify">
                            Mengembalikan biaya yang telah disetorkan oleh <span class="fw-bold">PIHAK KEDUA</span> apabila <span class="fw-bold">PIHAK KEDUA</span> batal berangkat menunaikan ibadah haji dikarenakan sebab apapun <span class="fw-bold">PIHAK KEDUA</span> kepada KEMENTERIAN AGAMA dan dana setoran BPIH Khusus akan dikembalikan ke rekening <span class="fw-bold">PIHAK KEDUA</span> atau apabila <span class="fw-bold">PIHAK KEDUA</span> meninggal dunia dikembalikan ke rekening ahli waris <span class="fw-bold">PIHAK KEDUA</span> (sesuai fatwa waris).
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <div class="mt-3"></div>


                    <ol start="11">
                        <li class="justify">
                            Menjelaskan kepada <span class="fw-bold">PIHAK KEDUA</span> tentang jadwal bimbingan manasik, bimbingan dan/ atau pelayanan kesehatan yang didapatkan, jadwal perjalanan, transportasi dan akomodasi/ hotel, konsumsi dan pelayanan lainnya yang diberikan di Tanah Air maupun di Tanah Suci, sesuai dengan program kegiatan ibadah haji khusus.
                        </li>
                        <li class="justify">
                            Menaati program ibadah haji khusus yang telah disepakati <span class="fw-bold">PIHAK KEDUA</span>.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p>Pasal 3<br><span class="fw-bold">KEWAJIBAN <span class="fw-bold">PIHAK KEDUA</span></span></p>
                    </center>

                    <ol>
                        <li class="justify">
                            Melunasi setoran BPIH kepada <span class="fw-bold">PIHAK PERTAMA</span> sesuai dengan ketentuan Pemerintah Indonesia dan ketentuan <span class="fw-bold">PIHAK PERTAMA</span> yang telah disepakati.
                        </li>
                        <li class="justify">
                            Melengkapi persyaratan yang diminta oleh <span class="fw-bold">PIHAK PERTAMA</span> berupa administrasi dokumen sesuai dengan kebutuhan dokumen yang ditentukan.
                        </li>
                        <li class="justify">
                            Mengikuti bimbingan ibadah haji (manasik) yang diselenggarakan oleh <span class="fw-bold">PIHAK PERTAMA</span> baik selama di Tanah Air maupun selama di Tanah Suci.
                        </li>
                        <li class="justify">
                            Mengikuti program-program kegiatan dan tata tertib yang ditetapkan oleh <span class="fw-bold">PIHAK PERTAMA</span> selama berada di Tanah Suci.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p>Pasal 4<br><span class="fw-bold">HAK <span class="fw-bold">PIHAK PERTAMA</span></span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Menetapkan besarnya BPIH Khusus sesuai dengan ketentuan Pemerintah Indonesia dan perusahaan beserta persyaratan administrasi lainnya.
                        </li>
                        <li class="justify">
                            <span class="fw-bold">PIHAK PERTAMA</span> berhak menolak memberangkatkan <span class="fw-bold">PIHAK KEDUA</span> apabila <span class="fw-bold">PIHAK KEDUA</span> tidak memenuhi pembayaran BPIH Khusus dan persyaratan serta ketentuan-ketentuan yang ditetapkan oleh <span class="fw-bold">PIHAK PERTAMA</span>.
                        </li>
                        <li class="justify">
                            Menentukan waktu-waktu pelaksanaan manasik dan acara-acara lainnya.
                        </li>
                        <li class="justify">
                            Menentukan perusahaan asuransi dan dokter yang merawat <span class="fw-bold">PIHAK KEDUA</span> selama berada di Tanah Suci.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p>Pasal 5<br><span class="fw-bold">HAK <span class="fw-bold">PIHAK KEDUA</span></span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Menyampaikan usulan-usulan kepada <span class="fw-bold">PIHAK PERTAMA</span> demi kelancaran pelaksanaan ibadah haji.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan kesehatan dan perawatan serta pemulangan dari <span class="fw-bold">PIHAK PERTAMA</span>, apabila <span class="fw-bold">PIHAK KEDUA</span> menjalani perawatan di Rumah Sakit Arab Saudi setelah penyelenggaraan ibadah haji.
                        </li>
                        <li class="justify">
                            Mendapatkan klaim asuransi yang ditanggung oleh <span class="fw-bold">PIHAK PERTAMA</span>.
                        </li>
                        <li class="justify">
                            Mendapatkan jaminan kepulangan kembali ke tanah air dari <span class="fw-bold">PIHAK PERTAMA</span> sesuai dengan jadwal yang disepakati PARA PIHAK.
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <div class="mt-3"></div>

                    <center>
                        <p>Pasal 6<br><span class="fw-bold">FORCE MAJEURE</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Tidak ada satupun PIHAK yang harus bertanggung jawab kepada PIHAK lainnya atas kegagalan atau penundaan dari pelaksanaan kewajibannya berdasarkan perjanjian ini pada saat dan merupakan kegagalan yang disebabkan oleh adanya kerusuhan, huru-hara, perang, permusuhan antar negara, peraturan atau kebijaksanaan Pemerintah Indonesia yang baru, embargo, bencana alam, wabah penyakit, pemberontakan, kebakaran, sabotase.
                        </li>
                        <li class="justify">
                            Setiap dan seluruh kerusakan dan kerugian yang diderita oleh salah satu PIHAK sebagai akibat terjadinya Force Majeure tidak menjadi beban dan tanggung jawab PIHAK lainnya.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p>Pasal 7<br><span class="fw-bold">MASA BERLAKU</span></p>
                    </center>
                    <p>Perjanjian ini berlaku efektif terhitung sejak tanggal penandatanganan perjanjian oleh PARA PIHAK dan akan berakhir pada saat <span class="fw-bold">PIHAK KEDUA</span> tiba kembali di Tanah Air.</p>

                    <br>

                    <center>
                        <p>Pasal 8<br><span class="fw-bold">PERNYATAAN DAN JAMINAN</span></p>
                    </center>
                    <p><span class="fw-bold">PIHAK PERTAMA</span> dengan ini menyatakan dan menjamin kepada <span class="fw-bold">PIHAK KEDUA</span> bahwa:</p>
                    <ol>
                        <li class="justify">
                            <span class="fw-bold">PIHAK PERTAMA</span> akan memenuhi seluruh persyaratan baik yang ditentukan oleh Pemerintah Indonesia maupun Pemerintah Arab Saudi, untuk kelancaran Penyelenggaraan Ibadah Haji Khusus.
                        </li>
                        <li class="justify">
                            <span class="fw-bold">PIHAK PERTAMA</span> tidak akan memungut biaya tambahan kepada <span class="fw-bold">PIHAK KEDUA</span> selama berada di Arab Saudi, apabila <span class="fw-bold">PIHAK PERTAMA</span> melakukan pemungutan biaya tersebut, maka <span class="fw-bold">PIHAK KEDUA</span> mempunyai hak untuk melaporkan <span class="fw-bold">PIHAK PERTAMA</span> kepada Direktur Jenderal Penyelenggara Haji dan Umrah.
                        </li>
                        <li class="justify">
                            <span class="fw-bold">PIHAK KEDUA</span> bertanggung jawab sepenuhnya atas seluruh risiko dan biaya yang timbul dari kegiatan yang dilakukan di luar Program Kegiatan Ibadah Haji Khusus.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p>Pasal 9<br><span class="fw-bold">PENYELESAIAN PERSELISIHAN</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            PARA PIHAK sepakat apabila timbul perselisihan yang berkaitan dengan pelaksanaan perjanjian ini akan disesuaikan melalui musyawarah untuk mufakat.
                        </li>
                        <li class="justify">
                            Apabila musyawarah tidak mencapai kata mufakat, maka PARA PIHAK sepakat untuk menunjuk Direktorat Pembinaan Haji dan Umrah Kementerian Agama sebagai mediator.
                        </li>
                        <li class="justify">
                            Apabila melalui kedua cara tersebut di atas (ayat 1&2) perselisihan tidak diselesaikan, maka PARA PIHAK sepakat untuk menyelesaikan melalui Pengadilan Negeri di wilayah tempat domisili <span class="fw-bold">PIHAK PERTAMA</span>.
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <div class="mt-3"></div>

                    <center>
                        <p>Pasal 10<br><span class="fw-bold">PENUTUP</span></p>
                    </center>
                    <p>Dalam melaksanakan perjanjian ini <span class="fw-bold">PIHAK PERTAMA</span> dan <span class="fw-bold">PIHAK KEDUA</span> tunduk kepada peraturan-peraturan perundang-undangan mengenai kegiatan ibadah umroh dan haji yang berlaku.</p>
                    <br>
                    <p>Demikian perjanjian ini dibuat di atas kertas bermaterai Rp. 10.000,- (sepuluh ribu rupiah) pada halaman Pertama, dibuat rangkap 2 (dua) masing-masing melakukan sebagai aslinya dan ditandatangani oleh PARA PIHAK di Tangerang Selatan pada hari dan tanggal sebagaimana disebutkan pada bagian awal Perjanjian ini.</p>

                    <br>

                    <div class="center mt-3">
                        <table width="100%">
                            <tr>
                                <td width="50%" valign="top">
                                    <p><span class="fw-bold"><span class="fw-bold">PIHAK PERTAMA</span>,</span></p>
                                    <p>PT Jejak Imani Berkah Bersama</p>
                                    <br><br><br><br><br>
                                    <p><span style="text-decoration:underline" class="fw-bold uppercase">H. M. Rizaldy Latief</span><br>Direktur Utama</p>
                                </td>
                                <td width="50%" valign="top">
                                    <p><span class="fw-bold"><span class="fw-bold">PIHAK KEDUA</span>,</span></p>
                                    <br><br><br><br><br><br>
                                    <p><span style="text-decoration:underline" class="fw-bold uppercase">{{ ucwords(strtolower($participant->name)) }}</span><br>Calon Jemaah Haji Khusus</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
</body>

</html>
