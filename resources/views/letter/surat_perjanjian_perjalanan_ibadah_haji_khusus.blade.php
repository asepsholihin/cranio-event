<?php
use App\Support\NumberFormat;
$hotelMadinah = 5;
if(str_contains($packageUmrohTrip->name, "Ruby")) {
    $hotelMadinah = 3;
}

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

        .mt-4 {
            margin-top: 1.8em;
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
        ol.empty {
            list-style:none;
            padding-left: 1em;
        }
        ul.alphabet {
            list-style-type: lower-alpha;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
    <title>SURAT PERJANJIAN PERJALANAN IBADAH HAJI KHUSUS PT. JEJAK IMANI BERKAH BERSAMA</title>
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
                        <h4 class="mt-4 mb-0">SURAT PERJANJIAN PERJALANAN IBADAH HAJI KHUSUS<br>PT. JEJAK IMANI BERKAH
                            BERSAMA</h4>
                    </div>

                    <div class="left mt-5">
                        <p>Yang bertanda tangan di bawah ini:</p>
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
                                <td>Jabatan</td>
                                <td>:</td>
                                <td class="uppercase">Direktur Utama</td>
                            </tr>
                        </table>
                    </div>

                    <div class="left mt">
                        <p>Dalam hal ini bertindak untuk dan atas nama <span class="fw-bold">PT. Jejak Imani Berkah
                                Bersama</span>, yang selanjutnya disebut sebagai <span class="fw-bold">“Pihak
                                Pertama”</span>.</p>
                        <table width="100%" style="margin-left:24px">
                            <tr>
                                <td valign="top" width="28%">Nama</td>
                                <td valign="top" width="2%">:</td>
                                <td class="uppercase">{{ strtoupper($participant->name) }}</td>
                            </tr>
                            <tr>
                                <td valign="top">NIK</td>
                                <td valign="top">:</td>
                                <td class="uppercase">{{ $participant->kitas_number ?? $participant->nik }}</td>
                            </tr>
                            <tr>
                                <td valign="top">Alamat</td>
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
                                Ibadah Haji Khusus.
                            </li>
                            <li class="justify">
                                Pihak Kedua telah menerima dan setuju atas penawaran Penyelenggaraan Ibadah Haji Khusus
                                dan Biaya Penyelenggaraan Ibadah Haji Khusus yang telah diajukan oleh Pihak Pertama.
                            </li>
                        </ol>

                        <br>

                        <center>
                            <p><span class="fw-bold">Pasal 2<br>Penyelenggaraan Ibadah Haji Khusus</span></p>
                        </center>
                        <ol>
                            <li class="justify">
                                Penyelenggaraan Ibadah Haji Khusus yang diberikan Pihak Pertama kepada Pihak Kedua
                                adalah sebagai berikut:<br>
                                <table width="100%" style="margin-left:24px;margin-bottom:12px;">
                                    <tr>
                                        <td valign="top">Nama Program</td>
                                        <td valign="top">:</td>
                                        <td valign="top">Haji Khusus Jejak Imani</td>
                                    </tr>
                                    <tr>
                                        <td valign="top">Tgl. Keberangkatan</td>
                                        <td valign="top">:</td>
                                        <td valign="top">Sesuai alokasi kuota keberangkatan dari Kemenag RI</td>
                                    </tr>
                                    <tr>
                                        <td valign="top">Paket</td>
                                        <td valign="top">:</td>
                                        <td valign="top">Haji Khusus</td>
                                    </tr>
                                </table>
                            </li>
                            <li class="justify">
                                Tahun keberangkatan, Biaya Penyelenggaraan Ibadah Haji Khusus, dan fasilitas dapat berubah sewaktu-waktu menurut kondisi/regulasi yang berlaku pada maskapai/pemerintah.
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
                    
                    <center>
                        <p><span class="fw-bold">Pasal 3<br>Pendaftaran</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pendaftaran dikatakan sah apabila Pihak Kedua telah melakukan pembayaran uang muka (<i>down payment</i>) sebesar USD 5.000 (lima ribu US Dolar).
                        </li>
                        <li class="justify">
                            Pihak Kedua mengisi formulir pendaftaran haji Furoda yang disediakan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Pihak Kedua menyerahkan Surat Keterangan Sehat dari puskesmas setempat dan hasil pemeriksaan laboratorium serta hasil pemeriksaan rontgen dari rumah sakit/klinik.
                        </li>
                        <li class="justify">
                            Pihak Kedua melengkapi dokumen-dokumen sebagai berikut:
                            <ul class="alphabet">
                                <li class="justify">
                                    Paspor dengan masa berlaku tidak kurang dari 9 (sembilan) bulan dari tanggal keberangkatan.
                                </li>
                                <li class="justify">
                                    Pasfoto berwarna latar belakang putih dengan fokus wajah 80% tidak memakai kacamata
                                    hitam, tidak memakai pakaian dinas dan untuk wanita harus memakai jilbab. Ukuran foto : 2x3 = 5 lembar, 3x4 = 20 lembar dan 4x6 = 10 lembar.
                                </li>
                                <li class="justify">
                                    Fotokopi Kartu Pelajar dan Surat Keterangan Sekolah bagi Peserta Pelajar.
                                </li>
                                <li class="justify">
                                    Fotokopi Akte Lahir untuk Peserta Anak.
                                </li>
                                <li class="justify">
                                    Fotokopi KTP, Kartu Keluarga dan fotokopi Buku Nikah (bagi Suami Istri) masing-masing 5 lembar.
                                </li>
                                <li class="justify">
                                    Foto kopi Paspor 5 lembar.
                                </li>
                                <li class="justify">
                                    BPJS Kesehatan.
                                </li>
                                <li class="justify">
                                    Menandatangani Surat Kuasa dan Surat Pernyataan.
                                </li>
                            </ul>
                        </li>
                        <li class="justify">
                            Biaya pelunasan Haji Khusus mengikuti harga paket pada tahun keberangkatan.
                        </li>
                        <li class="justify">
                            Estimasi masa tunggu Haji Khusus mengikuti kebijakan kuota haji dari Kemenag RI.
                        </li>
                        <li class="justify">
                            Harga menggunakan US Dolar akan disesuaikan dengan kurs pada tahun pendaftaran dan tahun keberangkatan.
                        </li>
                        <li class="justify">
                            Apabila jemaah melakukan pembatalan, maka akan dikenakan biaya administrasi sesuai dengan ketentuan yang berlaku di Jejak Imani.
                        </li>
                        <li class="justify">
                            Apabila Pihak Kedua berusia di atas 70 (tujuh puluh) tahun atau memiliki keterbatasan fungsi anggota tubuh atau indera atau keterbatasan secara mental, wajib didampingi oleh anggota keluarga, teman atau saudara yang bertanggung jawab selama perjalanan.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 4<br>Pembayaran</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pembayaran pertama berupa uang muka (<i>down payment</i>) Biaya Penyelenggaraan Ibadah Haji (BPIH) sebesar USD 5.000 (lima ribu US Dolar) untuk mendapatkan nomor porsi.
                        </li>
                        <li class="justify">
                            Pelunasan haji dilakukan pada tahun keberangkatan berdasarkan informasi resmi dari Kemenag RI.
                        </li>
                        <li class="justify">
                            Pembayaran Pihak Kedua dapat dinyatakan sah apabila dilakukan dengan transfer ke rekening resmi Pihak Pertama.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 5<br>Pembatalan</span></p>
                    </center>

                    <ol>
                        <li class="justify">
                            Jika terjadi pembatalan oleh Pihak Pertama, maka Pihak Pertama akan mengembalikan seluruh Biaya Penyelenggaraan Ibadah Haji Khusus yang telah disetorkan Pihak Kedua kepada Pihak Pertama.
                        </li>
                        <li class="justify">
                            Jika terjadi pembatalan oleh Pihak Kedua sebelum tanggal keberangkatan, maka Pihak Kedua akan dikenakan biaya pembatalan sebagai berikut:
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

                    <ol class="empty">
                        <li>
                            <ul class="alphabet">
                                <li class="justify">
                                    Pembatalan dari setelah pendaftaran (uang muka diterima Pihak Pertama) sampai dengan 6 (enam) bulan sebelum tanggal keberangkatan, biaya pembatalan sebesar USD 1.000,- (seribu US Dolar).
                                </li>
                                <li class="justify">
                                    Pembatalan dari 6 (enam) sampai dengan 3 (tiga) bulan sebelum tanggal keberangkatan, biaya pembatalan sebesar 50% (lima puluh persen) dari Biaya Penyelenggaraan Haji Khusus.
                                </li>
                                <li class="justify">
                                    Pembatalan dari 3 (tiga) sampai dengan 2 (dua) bulan sebelum tanggal keberangkatan, biaya pembatalan sebesar 75% (tujuh puluh lima persen) dari Biaya Penyelenggaraan Ibadah Haji Khusus.
                                </li>
                                <li class="justify">
                                    Pembatalan kurang dari 2 (dua) bulan sebelum tanggal keberangkatan, biaya pembatalan sebesar 100% (seratus persen) dari Biaya Penyelenggaraan Ibadah Haji Khusus.
                                </li>
                            </ul>
                        </li>
                    </ol>
                    
                    <ol start="3">
                        <li class="justify">
                            Biaya pembatalan tersebut (point 2 di atas), berlaku juga bagi:
                            <ul class="alphabet">
                                <li class="justify">
                                    Pihak Kedua meninggal dunia dan nomor porsi haji nya tidak dimanfaatkan oleh ahli waris (suami, istri, ayah, ibu, anak kandung atau saudara kandung),
                                </li>
                                <li class="justify">
                                    Pihak Kedua terlambat memberikan persyaratan dari batas waktu yang telah ditentukan dan mengakibatkan Pihak Kedua tidak dapat berangkat tepat pada waktunya karena permohonan haji masih diproses oleh pihak yang berwenang (Kemenag RI).
                                </li>
                            </ul>
                        </li>
                        <li class="justify">
                            Pihak Kedua melakukan pindah travel (Pindah PIN) akan dikenakan biaya pembatalan sebesar USD 1.000,- (seribu US Dolar).
                        </li>
                        <li class="justify">
                            Pembatalan yang dilakukan oleh salah satu pihak karena <i>Force Majeure</i> seperti kerusuhan, huru-hara, perang, permusuhan antar negara, peraturan atau kebijaksanaan Pemerintah Indonesia yang baru, embargo, bencana alam, wabah penyakit, pemberontakan, kebakaran, sabotase, maka ketentuan-ketentuan pembatalan tergantung pada kebijakan pemerintah, maskapai, hotel dan agen-agen lainnya baik di dalam maupun luar negeri.
                        </li>
                        <li class="justify">
                            Pihak Pertama berhak membatalkan pendaftaran Pihak Kedua yang belum melakukan pembayaran uang muka (<i>down payment</i>) atau pelunasan sampai dengan batas waktu yang telah ditentukan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Selama masa tunggu Haji Khusus, Pihak Kedua dapat mengalihkan keberangkatannya menjadi Ibadah Haji Furoda bersama Pihak Pertama, maka pembayaran uang muka (<i>down payment</i>) Pihak Kedua dapat dialihkan ke keberangkatan Ibadah Haji Furoda Pihak Pertama. Hal tersebut dengan syarat Pihak Kedua harus membatalkan terlebih dahulu nomor porsi Haji Khusus di Kemenag RI yang prosesnya dapat dibantu oleh Pihak Pertama.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 6<br>Nomor Porsi Haji</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Kemenag RI mempunyai keputusan mutlak atas pengajuan haji dan Pihak Pertama tidak bisa menentukan kepastian haji Pihak Kedua ditolak atau diterima.
                        </li>
                        <li class="justify">
                            Pihak Pertama hanya bisa membantu mengajukan/memproses permohonan haji tersebut sesuai prosedur dan dokumen yang diperlukan.
                        </li>
                        <li class="justify">
                            Penolakan dokumen oleh Kemenag RI atau revisi kelengkapan dokumen pengajuan haji merupakan keputusan Kemenag RI.
                        </li>
                        <li class="justify">
                            Segala keterlambatan kelengkapan dokumen bukan menjadi tanggung jawab Pihak Pertama.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 7<br>Prosedur dan Persyaratan Pindah PIN Haji (Pindah Travel Haji)</span></p>
                    </center>
                    
                    Jika Pihak Kedua mengajukan pemindahan Penyelenggara Ibadah Haji Khusus (PIHK) lama ke Penyelenggara Ibadah Haji Khusus (PIHK) baru, maka persyaratannya sebagai berikut:
                    <ol type="a" style="margin-left:8px;">
                        <li class="justify">
                            Menyerahkan fotokopi KTP.
                        </li>
                        <li class="justify">
                            Bukti BPIH (Biaya Penyelenggaraan Ibadah Haji) Khusus yang asli.
                        </li>
                        <li class="justify">
                            SPPH (Surat Pendaftaran Pergi Haji) yang asli.
                        </li>
                    </ul>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <div class="mt-3"></div>

                    <ol type="a" start="4" style="margin-left:8px;">
                        <li class="justify">
                            Bukti setor Kementerian Agama RI/atau Bank yang asli.
                        </li>
                        <li class="justify">
                            Surat pernyataan perpindahan dan surat pindah PIN Haji dari travel lama.
                        </li>
                        <li class="justify">
                            Brosur haji travel lama.
                        </li>
                        <li class="justify">
                            Surat pernyataan dari jemaah.
                        </li>
                        <li class="justify">
                            Surat pernyataan mutlak dari travel lama.
                        </li>
                        <li class="justify">
                            Dikenakan biaya sebesar USD 1.000,- (seribu US Dolar)
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 8<br>Pengembalian Uang (<i>Refund</i>)</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pengembalian dana (<i>refund</i>) atas kondisi:
                            <ul class="alphabet">
                                <li class="justify">
                                    Pembatalan dari setelah pendaftaran (uang muka diterima Pihak Pertama) sampai dengan 6 (enam) bulan sebelum tanggal keberangkatan, pengembalian dana sebesar uang muka dikurangi biaya pembatalan sebesar USD 1.000,- (seribu US Dolar).
                                </li>
                                <li class="justify">
                                    Pembatalan dari 6 (enam) sampai dengan 3 (tiga) bulan sebelum tanggal keberangkatan, pengembalian dana sebesar 50% (lima puluh persen) dari Biaya Penyelenggaraan Haji Khusus.
                                </li>
                                <li class="justify">
                                    Pembatalan dari 3 (tiga) sampai dengan 2 (dua) bulan sebelum tanggal keberangkatan, pengembalian dana sebesar 25% (dua puluh lima persen) dari Biaya Penyelenggaraan Ibadah Haji Khusus.
                                </li>
                                <li class="justify">
                                    Pembatalan kurang dari 2 (dua) bulan sebelum tanggal keberangkatan, tidak ada pengembalian dana dari Biaya Penyelenggaraan Ibadah Haji Khusus.
                                </li>
                                <li class="justify">
                                    Pihak Kedua melakukan pindah travel (Pindah PIN), pengembalian dana sebesar uang muka dikurangi biaya pembatalan sebesar USD 1.000,- (seribu US Dolar).
                                </li>
                            </ul>
                        </li>
                        <li class="justify">
                            Tiket pesawat udara, kereta api, dan transportasi lainnya serta akomodasi yang tidak terpakai tidak dapat diuangkan kembali (<i>non-refundable</i>).
                        </li>
                        <li class="justify">
                            Bila Pihak Kedua sakit permanen atau meninggal dunia dan Nomor Porsi Haji nya tidak dimanfaatkan oleh ahli waris, akan mengacu kepada pasal pembatalan.
                        </li>
                        <li class="justify">
                            Bila ada pelayanan dalam paket yang tidak digunakan oleh Pihak Kedua dikarenakan berhalangan atau sakit selama perjalanan, Pihak Kedua tidak berhak menuntut uang kembali.
                        </li>
                        <li class="justify">
                            Bila Pihak Kedua tidak diizinkan masuk atau dikenakan tindakan deportasi oleh pihak imigrasi negara setempat (walaupun sudah memiliki visa), atau yang ditolak oleh perusahaan penerbangan, atau dalam perjalanan menderita sakit, atau ada kelainan jiwa, atau dalam perjalanan mengalami kecelakaan, yang terpaksa harus kembali atau menyimpang dari perjalanan yang telah ditentukan dalam acara perjalanan, atau terpaksa membatalkan sebagian/hampir seluruh perjalanan setelah keberangkatan, tidak berhak atas pengembalian uang atau bentuk pengembalian lain apapun atas jasa-jasa yang belum atau tidak digunakan.
                        </li>
                    </ol>
                    
                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 9<br>Hak Pihak Pertama</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Menentukan Paket Penyelenggaraan Ibadah Haji Khusus dan Biaya Penyelenggaraan Ibadah Haji Khusus.
                        </li>
                        <li class="justify">
                            Apabila tidak ada teman sekamar (berempat), maka calon haji akan dikenakan biaya paket sesuai teman sekamar yang ada (harga paket sekamar berdua atau bertiga).
                        </li>
                        <li class="justify">
                            Harga paket bersifat sementara, harga tetap akan diinformasikan pada tahun berjalan.
                        </li>
                        <li class="justify">
                            Demi kenyamanan dan kelancaran perencanaan perjalanan, Pihak Pertama berhak meminta Pihak Kedua untuk keluar dari rombongan apabila Pihak Kedua mencoba membuat kerusuhan, mengacaukan acara perjalanan, meminta dengan paksa, dan memberikan informasi yang tidak benar mengenai acara perjalanan, dll.
                        </li>
                        <li class="justify">
                            Mengganti hotel-hotel yang akan digunakan berhubung hotel tersebut sudah penuh dan mengganti dengan hotel lain yang setaraf sesuai dengan pertimbangan dan konfirmasi.
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

                    <ol start="6">
                        <li class="justify">
                            Pihak Pertama berhak menagih selisih Biaya Penyelenggaraan Ibadah Haji Khusus kepada Pihak Kedua jika terjadi kenaikan harga tiket, hotel, <i>airport tax</i>, dll. Pemberitahuan penyesuaian Biaya Penyelenggaraan Ibadah Haji Khusus diinformasikan oleh Pihak Pertama kepada Pihak Kedua selambat-lambatnya 3 (tiga) bulan sebelum tanggal keberangkatan.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 10<br>Kewajiban Pihak Pertama</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Memberikan bimbingan haji meliputi bimbingan manasik dan perjalanan haji.
                        </li>
                        <li class="justify">
                            Memberikan pelayanan transportasi yaitu pemberangkatan ke dan dari Arab Saudi dan selama di Arab Saudi.
                        </li>
                        <li class="justify">
                            Memberikan pelayanan akomodasi dan konsumsi selama berada di Arab Saudi.
                        </li>
                        <li class="justify">
                            Memberikan pembinaan, pelayanan dan perlindungan kesehatan sebelum pemberangkatan ke dan dari Arab Saudi dan selama di Arab Saudi.
                        </li>
                        <li class="justify">
                            Memberikan perlindungan berupa Asuransi Perjalanan Ibadah Haji.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 11<br>Hak Pihak Kedua</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Mendapatkan bimbingan ibadah haji meliputi bimbingan manasik dan perjalanan ibadah haji.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan transportasi yaitu pemberangkatan ke dan dari Arab Saudi dan selama di Arab Saudi.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan akomodasi dan konsumsi selama berada di Arab Saudi.
                        </li>
                        <li class="justify">
                            Mendapatkan pembimbing Haji atau Muthawwif yang sudah berpengalaman.
                        </li>
                        <li class="justify">
                            Mendapatkan pembinaan, pelayanan dan perlindungan kesehatan sebelum pemberangkatan ke dan dari Arab Saudi dan selama di Arab Saudi.
                        </li>
                        <li class="justify">
                            Mendapatkan perlindungan berupa Asuransi Perjalanan Ibadah Haji.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan administrasi dan dokumen perjalanan ibadah haji, seperti visa dan dokumen lain yang dianggap perlu.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 12<br>Kewajiban Pihak Kedua</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Membayar/melunasi Biaya Penyelenggaraan Ibadah Haji Khusus yang telah ditentukan oleh Pihak Pertama, termasuk selisih biaya yang diakibatkan kenaikan harga tiket, hotel, <i>airport tax</i>, dll.
                        </li>
                        <li class="justify">
                            Mengisi formulir pendaftaran dan melengkapi persyaratan dokumen perjalanan ibadah haji dengan data yang benar dan dapat dipertanggungjawabkan.
                        </li>
                        <li class="justify">
                            Menjaga kenyamanan, kelancaran dan ketertiban perjalanan ibadah haji khusus dengan mengikuti rangkaian/jadwal perjalanan yang sudah disusun/diagendakan oleh Pihak Pertama.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 13<br>Ketentuan Khusus</span></p>
                    </center>

                    Pihak Pertama tidak bertanggung jawab dan tidak bisa dituntut atas beberapa kondisi sebagai berikut:
                    <ol>
                        <li class="justify">
                            Kecelakaan, kehilangan koper dan keterlambatan tibanya koper akibat tindakan pihak maskapai penerbangan atau alat pengangkutan lainnya, maka standar penggantian didasarkan pada ketentuan maskapai penerbangan internasional atau penyedia jasa pengangkutanyang digunakan.
                        </li>
                        <li class="justify">
                            Kehilangan barang pribadi, koper, titipan barang di bandara, hotel dan tindakan kriminal yang menimpa Pihak Kedua selama perjalanan.
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

                    <ol start="3">
                        <li class="justify">
                            Keterlambatan atau pembatalan jadwal penerbangan, dan seluruh kejadian yang terjadi di luar kuasa Pihak Pertama.
                        </li>
                        <li class="justify">
                            Perubahan atau berkurangnya acara perjalanan akibat dari bencana alam, kerusuhan dan lain sebagainya yang bersifat <i>Force Majeure</i>.
                        </li>
                    </ol>
                    
                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 14<br>Deviasi</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Deviasi adalah perubahan, perpanjangan, penambahan/penyimpangan rute perjalanan di luar rute perjalanan yang telah dijadwalkan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Deviasi dapat diproses apabila jemaah sudah melakukan pembayaran pertama (uang muka/<i>down payment</i>) dan melampirkan fotokopi paspor.
                        </li>
                        <li class="justify">
                            Deviasi dapat dilakukan apabila jumlah jemaah yang berangkat dan yang pulang telah memenuhi kuota dari ketentuan maskapai penerbangan.
                        </li>
                        <li class="justify">
                            Apabila deviasi sudah disetujui, maka akan dikenakan biaya sesuai dengan ketentuan maskapai penerbangan dan tidak dapat kembali ke jadwal semula.
                        </li>
                        <li class="justify">
                            Pihak Pertama tidak menjamin konfirmasi pesawat, hotel dan sebagainya bila jemaah menghendaki perpanjangan jadwal haji. Apabila permintaan deviasi tidak dapat disetujui oleh pihak maskapai penerbangan, maka jemaah secara otomatis akan kembali ke jadwal semula.
                        </li>
                        <li class="justify">
                            Deviasi yang akan mempersingkat jadwal paket perjalanan, tidak diberikan pengurangan biaya dari Biaya Penyelenggaraan Ibadah Haji Khusus standar semula.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 15<br>Lain-lain</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Hal-hal yang belum diatur dalam perjanjian ini akan diatur kemudian dan menjadi satu kesatuan dan bagian yang tidak terpisahkan dari perjanjian ini.
                        </li>
                        <li class="justify">
                            Apabila terjadi perselisihan sehubungan dengan apa yang telah ditentukan dalam perjanjian ini, maka Para Pihak setuju untuk mengutamakan penyelesaian secara musyawarah dan bersifat kekeluargaan.
                        </li>
                        <li class="justify">
                            Apabila musyawarah sebagaimana tersebut di atas tidak tercapai kesepakatan, maka perselisihan tersebut harus diselesaikan melalui Pengadilan Negeri Tangerang Selatan.
                        </li>
                        <li class="justify">
                            Perjanjian ini tunduk dan ditafsirkan berdasarkan hukum yang berlaku di Republik Indonesia.
                        </li>
                    </ol>

                    <br>

                    <div class="center mt-3">
                        <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                        <br>
                        <table width="100%">
                            <tr>
                                <td width="50%" valign="top">
                                    <p><span class="fw-bold">Pihak Pertama</span></p>
                                    <br><br><br><br><br>
                                    <p class="uppercase"><span style="text-decoration:underline" class="fw-bold">H. M. Rizaldy Latief</span><br>Direktur Utama</p>
                                </td>
                                <td width="50%" valign="top">
                                    <p><span class="fw-bold">Pihak Kedua</span></p>
                                    <br><br>{{ $materai }}<br><br><br>
                                    <p class="uppercase"><span style="text-decoration:underline" class="fw-bold">{{ ucwords(strtolower($participant->name)) }}</span><br>Participant</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                </div>

                @if(false)
                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <br><br>

                    <p><span class="fw-bold">Lampiran 1. Fasilitas & Perlengkapan Ibadah Haji Khusus</span></p>
                    <table class="bordered" border cellspacing="0" cellpadding="0">
                        <tr style="background: #ccc;">
                            <th rowspan="2">No</th>
                            <th rowspan="2">Fasilitas & Perlengkapan Haji</th>
                            <th colspan="3">Paket</th>
                        </tr>
                        <tr style="background: #ccc;">
                            <th>Sekamar berdua</th>
                            <th>Sekamar bertiga</th>
                            <th>Sekamar berempat</th>
                        </tr>
                        <tr>
                            <td align="center">1.</td>
                            <td>
                                Hotel:<br>
                                - Aziziah<br>
                                - Makkah<br>
                                - Medinah<br>
                                - Arafah & Mina<br>
                            </td>
                            <td align="center">
                                <br>
                                Hotel Transit<br>
                                Bintang 5<br>
                                Bintang {{ $hotelMadinah }}<br>
                                Tenda ber-Ac<br>
                            </td>
                            <td align="center">
                                <br>
                                Hotel Transit<br>
                                Bintang 5<br>
                                Bintang {{ $hotelMadinah }}<br>
                                Tenda ber-Ac<br>
                            </td>
                            <td align="center">
                                <br>
                                Hotel Transit<br>
                                Bintang 5<br>
                                Bintang {{ $hotelMadinah }}<br>
                                Tenda ber-Ac<br>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">2.</td>
                            <td>Biaya ONH Kemenag RI</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td align="center">3.</td>
                            <td>Visa</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td align="center">4.</td>
                            <td>Manasik</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td align="center">5.</td>
                            <td>Handling</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td align="center">6.</td>
                            <td>Asuransi Perjalanan Ibadah Haji</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td align="center">7.</td>
                            <td>Passport Case</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td align="center">8.</td>
                            <td>Air Zam-zam 5 liter (pada saat kepulangan)</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                            <td align="center">Ya</td>
                        </tr>
                        <tr>
                            <td align="center">9.</td>
                            <td>
                                Perlengkapan Haji:<br>
                                - Ihram (Laki-laki)/Mukena (Perempuan)<br>
                                - Tas kabin<br>
                                - Bahan Batik Seragam<br>
                                - Koper<br>
                                - Buku Panduan<br>
                                - Sajadah<br>
                                - Kacamata<br>
                                - Payung<br>
                                - Tas Passport<br>
                                - ID Card<br>
                                - Masker<br>
                                - Sabuk Ihram<br>
                                - Kantong Sandal<br>
                                - Buku Doa<br>
                            </td>
                            <td align="center">
                                <br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                            </td>
                            <td align="center">
                            <br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                            </td>
                            <td align="center">
                                <br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                                Ya<br>
                            </td>
                        </tr>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
