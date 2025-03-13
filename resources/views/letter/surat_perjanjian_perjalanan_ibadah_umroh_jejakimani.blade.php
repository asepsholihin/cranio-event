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
            font: 11.5pt "Mulish";
            line-height: 14pt;
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

        ul.alphabet {
            list-style: lower-alpha;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
    <title>SURAT PERJANJIAN PERJALANAN IBADAH UMRAH PT. JEJAK IMANI BERKAH BERSAMA</title>
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
                        <h4 class="mt-5 mb-0">SURAT PERJANJIAN PERJALANAN IBADAH UMRAH<br>PT. JEJAK IMANI BERKAH BERSAMA
                        </h4>
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
                                <td class="uppercase">{{ $address }}</td>
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
                            Para Pihak sepakat atas penyelenggaraan perjalanan ibadah umrah dengan ketentuan dan
                            syarat-syarat yang telah ditetapkan dan diatur di dalam Perjanjian ini.
                        </p>

                        <br>

                        <center>
                            <p><span class="fw-bold">Pasal 1<br>Bentuk Penyelenggaraan</span></p>
                        </center>
                        <ol>
                            <li class="justify">
                                Pihak Pertama merupakan Penyelenggara Perjalanan Ibadah Umrah (PPIU) yang telah
                                mendapatkan izin resmi dari Kementerian Agama RI No. U.533 Tahun 2020 (d.h No. 924 Tahun
                                2017) untuk menyelenggarakan perjalanan ibadah umrah.
                            </li>
                            <li class="justify">
                                Para Pihak telah sepakat bahwa Pihak Kedua telah menunjuk Pihak Pertama dan Pihak
                                Pertama untuk kepentingan Pihak Kedua telah bersedia untuk menyelenggarakan Paket
                                Perjalanan Ibadah Umrah.
                            </li>
                            <li class="justify">
                                Pihak Kedua telah menerima dan setuju atas penawaran Paket Perjalanan Ibadah Umrah dan
                                Biaya Penyelenggaraan Ibadah Umrah (BPIU) yang telah diajukan oleh Pihak Pertama.
                            </li>
                        </ol>

                        <br>

                        <center>
                            <p><span class="fw-bold">Pasal 2<br>Paket Perjalanan Ibadah Umrah</span></p>
                        </center>
                        <ol>
                            <li class="justify">
                                Paket Perjalanan Ibadah Umrah yang diberikan Pihak Pertama kepada Pihak Kedua adalah
                                sebagai berikut:
                                <table width="100%">
                                    <tr>
                                        <td valign="top">Nama Program</td>
                                        <td valign="top">:</td>
                                        <td valign="top">{{ $umrohTrip->title }}</td>
                                    </tr>
                                    <tr>
                                        <td valign="top">Tgl. Keberangkatan</td>
                                        <td valign="top">:</td>
                                        <td valign="top">
                                            {{ \Carbon\Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y') }}
                                            - {{ \Carbon\Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top">Paket</td>
                                        <td valign="top">:</td>
                                        <td valign="top">{{ $packageUmrohTrip->name }}</td>
                                    </tr>
                                </table>
                            </li>
                            <li class="justify">
                                Jadwal keberangkatan/Biaya Penyelenggaraan Ibadah Umrah/fasilitas dapat berubah
                                sewaktu-waktu menurut kondisi/regulasi yang belaku pada maskapai/pemerintah.
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

                    <center>
                        <p><span class="fw-bold">Pasal 3<br>Pendaftaran</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pendaftaran dikatakan sah apabila Pihak Kedua telah melakukan pembayaran, minimal pembayaran
                            uang muka (<i>down payment</i>).
                        </li>
                        <li class="justify">
                            Pendaftaran tanpa disertai dengan pembayaran uang muka (<i>down payment</i>) bersifat tidak
                            mengikat dan dapat dibatalkan tanpa pemberitahuan terlebih dahulu kepada Pihak Kedua.
                        </li>
                        <li class="justify">
                            Pendaftaran ditutup 15 (lima belas) hari sebelum tanggal keberangkatan atau apabila kuota
                            participant telah terpenuhi.
                        </li>
                        <li class="justify">
                            Pihak Kedua wajib mengisi formulir pendaftaran dan melengkapi dokumen-dokumen sebagai
                            berikut:
                            <ul class="alphabet">
                                <li class="justify">
                                    Paspor dengan masa berlaku tidak kurang dari 12 (dua belas) bulan dari tanggal
                                    keberangkatan.
                                </li>
                                <li class="justify">
                                    Pas Foto berwarna ukuran 4 x 6 dengan fokus wajah 80% sebanyak 4 (empat) lembar.
                                </li>
                                <li class="justify">
                                    Fotocopy Kartu Pelajar dan Surat Keterangan Sekolah bagi Peserta Pelajar.
                                </li>
                                <li class="justify">
                                    Fotocopy KTP, dan Kartu Keluarga.
                                </li>
                                <li class="justify">
                                    Fotocopy Buku Nikah bagi Suami Istri.
                                </li>
                                <li class="justify">
                                    Buku kuning meningitis yang masih berlaku.
                                </li>
                                <li class="justify">
                                    Sertifikat Vaksinasi Covid-19.
                                </li>
                            </ul>
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
                            Pembayaran Pertama berupa uang muka (<i>Down Payment</i>) minimal sebesar Rp10.000.000,00
                            (Sepuluh Juta Rupiah) per participant.
                        </li>
                        <li class="justify">
                            Pelunasan dilakukan maksimal 14 (empat belas) hari sebelum tanggal keberangkatan.
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
                            Jika terjadi pembatalan oleh Pihak Pertama sebelum tanggal keberangkatan, maka Pihak Pertama
                            akan melakukan pengembalian dana sebesar 100% (seratus persen) dari jumlah Biaya Perjalanan
                            Ibadah Umrah yang sudah dibayarkan oleh Pihak Kedua.
                        </li>
                        <li class="justify">
                            Jika terjadi pembatalan oleh Pihak Kedua sebelum tanggal keberangkatan, maka Pihak Kedua
                            akan dikenakan biaya pembatalan sebagai berikut:
                            <ul class="alphabet">
                                <li class="justify">
                                    Uang muka (<i>down payment</i>) tidak dapat dikembalikan (<i>Non-refundable</i>).
                                </li>
                                <li class="justify">
                                    Pembatalan setelah pelunasan visa dan/atau tiket pesawat dan/atau akomodasi lainnya
                                    (termasuk perlengkapan umrah jika telah diterima oleh Pihak Kedua), maka biaya yang
                                    sudah dikeluarkan tersebut tidak dapat dikembalikan (<i>Non-refundable</i>).
                                </li>
                            </ul>
                        </li>
                        <li class="justify">
                            Biaya pembatalan tersebut (point 2), berlaku bagi:
                            <ul class="alphabet">
                                <li class="justify">
                                    Pihak Kedua sakit atau meninggal dunia.
                                </li>
                                <li class="justify">
                                    Pihak Kedua mengganti tanggal keberangkatan atau mengganti paket perjalanan.
                                </li>
                                <li class="justify">
                                    Pihak Kedua terlambat memberikan persyaratan visa dari batas waktu yang telah
                                    ditentukan dan mengakibatkan Pihak Kedua tidak dapat berangkat tepat pada waktunya
                                    karena permohonan visa masih diproses oleh pihak yang berwenang (Kedutaan).
                                </li>
                            </ul>
                        </li>
                        <li class="justify">
                            Pembatalan yang dilakukan oleh salah satu pihak karena bencana alam, perang, wabah penyakit,
                            aksi teroris atau keadaan <i>Force Majeure</i> lainnya, maka ketentuan-ketentuan pembatalan
                            tergantung pada kebijakan pemerintah, maskapai, hotel dan agen-agen lainnya baik di dalam
                            maupun luar negeri.
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
                            Pihak Pertama berhak membatalkan pendaftaran Pihak Kedua yang belum melakukan pembayaran
                            uang muka (<i>down payment</i>) atau pelunasan sampai dengan batas waktu yang telah
                            ditentukan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Bila permohonan visa ditolak oleh pihak yang berwenang, sedangkan tiket sudah diterbitkan
                            sebelum permohonan visa disetujui, karena keharusan sehubungan dengan tenggat waktu yang
                            ditentukan oleh maskapai, maka biaya visa tidak dapat dikembalikan dan Pihak Kedua tetap
                            dikenakan biaya pembatalan dan administrasi sesuai dengan ketentuan pihak maskapai, hotel
                            dan agen di luar negeri.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 6<br>Visa</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Kedutaan mempunyai keputusan mutlak atas pengajuan visa dan Pihak Pertama tidak bisa
                            menentukan kepastian visa Pihak Kedua ditolak atau diterima.
                        </li>
                        <li class="justify">
                            Pihak Pertama hanya bisa membantu mengajukan/memproses permohonan visa tersebut sesuai
                            prosedur dan dokumen yang diperlukan.
                        </li>
                        <li class="justify">
                            Penolakan dokumen oleh Kedutaan atau revisi kelengkapan dokumen pengajuan visa merupakan
                            keputusan Kedutaan.
                        </li>
                        <li class="justify">
                            Segala keterlambatan kelengkapan dokumen bukan menjadi tanggung jawab Pihak Pertama.
                        </li>
                        <li class="justify">
                            Biaya visa tetap harus dibayarkan walaupun visa tidak disetujui oleh Kedutaan, demikian juga
                            jika terdapat biaya lain seperti pembatalan hotel, transportasi di saudi dan/atau tiket
                            pesawat yang terjadi karena adanya tenggat waktu yang belum tentu sesuai dengan waktu
                            penyelesaian proses visa dari Kedutaan, dan juga biaya lainnya, maka akan dibebankan kepada
                            Pihak Kedua.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 7<br>Pengembalian Uang (<i>Refund</i>)</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Pengembalian dana (<i>refund</i>) atas kondisi pada pasal 5 (setelah dikurangi dengan biaya
                            pembatalan), dilakukan kurang dari 1 (satu) bulan setelah Pihak Kedua membatalkan
                            keberangkatanya. Pengembalian dana (refund) oleh Pihak Pertama dilakukan dengan proses
                            transfer ke rekening yang telah ditunjuk oleh Pihak Kedua.
                        </li>
                        <li class="justify">
                            Tiket pesawat udara, kereta api, dan transportasi lainnya serta akomodasi yang tidak
                            terpakai tidak dapat diuangkan kembali (<i>nonrefundable</i>).
                        </li>
                        <li class="justify">
                            Bila Pihak Kedua berhalangan/sakit sebelum tanggal keberangkatan yang dijadwalkan maka
                            pengembalian uang/biaya pembatalan, akan mengacu kepada pasal pembatalan.
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
                            Menentukan Paket Perjalanan Ibadah Umrah dan Biaya Penyelenggaraan Ibadah Umrah.
                        </li>
                        <li class="justify">
                            Menerima formulir pendaftaran dan kelengkapan persyaratan dokumen perjalanan ibadah umrah
                            dengan data yang benar dan dapat dipertanggungjawabkan dari Pihak Kedua.
                        </li>
                        <li class="justify">
                            Menerima pembayaran Biaya Penyelenggaraan Ibadah Umrah dari Pihak Pertama, termasuk selisih
                            biaya yang diakibatkan kenaikan harga tiket, hotel, <i>airport tax</i>, dll.
                        </li>
                    </ol>
                </div>

                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png'))) }}"
                            alt="">
                    </div>

                    <ol start="4">
                        <li class="justify">
                            Demi menjaga kenyamanan, kelancaran dan ketertiban perjalanan ibadah umrah, maka Pihak
                            Pertama berhak untuk:
                            <ul class="alphabet">
                                <li class="justify">
                                    Memberikan opsi penjadwalan ulang (<i>reschedule</i>) atau dialihkan ke paket
                                    konsorsium Amphuri, apabila jumlah peserta tidak memenuhi kuota.
                                </li>
                                <li class="justify">
                                    Mengganti hotel-hotel yang akan digunakan berhubung hotel tersebut sudah penuh dan
                                    mengganti dengan hotel lain yang setaraf sesuai dengan pertimbangan dan konfirmasi.
                                </li>
                                <li class="justify">
                                    Menerbitkan tiket pesawat, dan transportasi lainnya, akomodasi, tiket masuk objek
                                    wisata tanpa melakukan konfirmasi lisan maupun tertulis kepada Pihak Kedua.
                                </li>
                                <li class="justify">
                                    Meminta Pihak Kedua untuk keluar dari rombongan apabila Pihak Kedua mencoba membuat
                                    kerusuhan, mengacaukan acara perjalanan, meminta dengan paksa, dan memberikan
                                    informasi yang tidak benar mengenai acara perjalanan, dll.
                                </li>
                            </ul>
                        </li>
                        <li class="justify">
                            Merubah jadwal umrah sewaktu-waktu mengikuti kondisi yang memungkinkan dengan tanpa
                            mengurangi isi dalam acara perjalanan tersebut.
                        </li>
                        <li class="justify">
                            <span class="fw-bold">Mewajibkan agar Pihak Kedua mematuhi protokol kesehatan yang berlaku
                                selama perjalanan ibadah umrah masa pandemi Covid-19.</span>
                        </li>
                    </ol>

                    <center>
                        <p><span class="fw-bold">Pasal 9<br>Kewajiban Pihak Pertama</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Memberikan bimbingan umrah meliputi bimbingan manasik dan perjalanan umrah.
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
                            <span class="fw-bold">Memberikan perlindungan berupa Asuransi Perjalanan Ibadah
                                Umrah.</span>
                        </li>
                        <li class="justify">
                            Memberikan pelayanan administrasi dan dokumen perjalanan umrah seperti visa dan dokumen lain
                            yang dianggap perlu.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 10<br>Hak Pihak Kedua</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Mendapatkan bimbingan ibadah umrah meliputi bimbingan manasik dan perjalanan umrah.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan transportasi yaitu pemberangkatan ke dan dari Arab Saudi dan selama di
                            Arab Saudi.
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan akomodasi dan konsumsi selama berada di Arab Saudi.
                        </li>
                        <li class="justify">
                            Mendapatkan pembinaan, pelayanan dan perlindungan kesehatan sebelum pemberangkatan ke dan
                            dari Arab Saudi dan selama di Arab Saudi.
                        </li>
                        <li class="justify">
                            <span class="fw-bold">Mendapatkan perlindungan berupa Asuransi Perjalanan Ibadah
                                Umrah(terlampir).</span>
                        </li>
                        <li class="justify">
                            Mendapatkan pelayanan administrasi dan dokumen perjalanan umrah, seperti visa dan dokumen
                            lain yang dianggap perlu.
                        </li>
                    </ol>

                    <br>

                    <center>
                        <p><span class="fw-bold">Pasal 11<br>Kewajiban Pihak Kedua</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Menyetujui Paket Perjalanan Ibadah Umrah dan Biaya Penyelenggaraan Ibadah Umrah yang telah
                            ditetapkan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Mengisi formulir pendaftaran dan melengkapi persyaratan dokumen perjalanan ibadah umrah
                            dengan data yang benar dan dapat dipertanggungjawabkan.
                        </li>
                        <li class="justify">
                            Menandatangani Surat Perjanjian Perjalanan Ibadah Umrah dan Surat Pernyataan serta
                            menyerahkannya kepada Pihak Pertama.
                        </li>
                        <li class="justify">
                            Membayar/melunasi Biaya Penyelenggaraan Ibadah Umrah yang telah ditentukan oleh Pihak
                            Pertama, termasuk selisih biaya yang diakibatkan kenaikan harga tiket, hotel, <i>airport
                                tax</i>, dll.
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
                            Demi menjaga kenyamanan, kelancaran dan ketertiban perjalanan ibadah umrah, Pihak Kedua
                            berkewajiban mengikuti rangkaian/jadwal perjalanan yang sudah disusun/diagendakan oleh Pihak
                            Pertama.
                        </li>
                        <li class="justify">
                            <span class="fw-bold">Mematuhi protokol kesehatan yang berlaku selama perjalanan ibadah
                                umrah masa pandemi Covid-19.</span>
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
                            kegiatan umrah berlangsung.
                        </li>
                        <li class="justify">
                            Pihak Kedua menerima pinjaman radiophone umrah selama jadwal kegiatan umrah berlangsung.
                        </li>
                        <li class="justify">
                            Radiophone dikembalikan oleh Pihak Kedua dalam keadaan baik, lengkap dan utuh (seperti pada
                            saat awal radiophone diterima oleh Pihak Kedua) setelah semua rangkaian jadwal kegiatan
                            umrah selesai.
                        </li>
                        <li class="justify">
                            Apabila terjadi kerusakan atau kehilangan radiophone yang digunakan oleh Pihak Kedua karena
                            kelalaian atau kelupaan, maka Pihak Kedua berkewajiban membayar ganti rugi sebesar
                            Rp.850.000,- (depalan ratus lima puluh ribu rupiah) sebagai pengganti atas radiophone yang
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
                            Participant terpapar Covid-19 dalam pelaksanaan perjalanan ibadah umrah selama di tanah air,
                            dalam perjalanan, selama di Arab Saudi, hingga kembali di tanah air.
                        </li>
                        <li class="justify">
                            Perubahan atau pembatalan keberangkatan karena adanya participant dalam satu <i>flight</i>
                            keberangkatan yang terpapar Covid-19 dan atau karena keputusan sepihak dari otoritas Arab
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
                            Perubahan atau berkurangnya acara perjalanan (<i>itinerary</i>) akibat dari bencana alam,
                            kerusuhan dan lain sebagainya yang bersifat <i>Force Majeure</i>.
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
                        <p><span class="fw-bold">Pasal 15<br>Deviasi</span></p>
                    </center>
                    <ol>
                        <li class="justify">
                            Deviasi adalah perubahan, perpanjangan, penambahan/penyimpanan rute perjalanan di luar rute
                            perjalanan yang telah dijadwalkan oleh Pihak Pertama.
                        </li>
                        <li class="justify">
                            Deviasi dapat diproses apabila participant sudah melakukan pembayaran pertama (uang muka/<i>down
                                payment</i>) dan melampirkan fotokopi paspor.
                        </li>
                        <li class="justify">
                            Deviasi dapat dilakukan apabila jumlah participant yang berangkat dan yang pulang telah memenuhi
                            kuota dari ketentuan maskapai penerbangan.
                        </li>
                        <li class="justify">
                            Apabila deviasi sudah disetujui, maka akan dikenakan biaya sesuai dengan ketentuan maskapai
                            penerbangan dan tidak dapat kembali ke jadwal semula.
                        </li>
                        <li class="justify">
                            Pihak Pertama tidak menjamin konfirmasi pesawat, hotel dan sebagainya bila participant
                            menghendaki perpanjangan jadwal umrah. Apabila permintaan deviasi tidak dapat disetujui oleh
                            pihak maskapai penerbangan, maka participant secara otomatis akan kembali ke jadwal semula.
                        </li>
                        <li class="justify">
                            Deviasi yang akan mempersingkat jadwal paket perjalanan, tidak diberikan pengurangan biaya
                            dari biaya paket standar semula.
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
                                <td width="50%">
                                    <p><span class="fw-bold">Pihak Pertama</span></p>
                                    <p><span class="fw-bold">PT. JEJAK IMANI BERKAH BERSAMA</span></p>
                                    <br><br><br><br>
                                    <p><span style="text-decoration:underline" class="fw-bold uppercase">H. M. RIZALDY LATIEF</span><br>Direktur Utama</p>
                                </td>
                                <td width="50%">
                                    <p><span class="fw-bold">Pihak Kedua</span></p>
                                    @if(isset($signed))
                                    <img src="{{ $signed }}" height="100" alt="">
                                    @else
                                    <br><br><br><br><br>
                                    @endif
                                    <p><span style="text-decoration:underline" class="fw-bold uppercase">{{ strtoupper($participant->name) }}</span><br>Participant</p>
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
