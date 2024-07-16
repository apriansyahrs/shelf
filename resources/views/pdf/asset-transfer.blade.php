<!DOCTYPE html>
<html>
<head>
    <title>{{ $assetTransfer->status }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 150px;
        }
        .header div {
            text-align: right;
        }
        .details, .table-container {
            margin-bottom: 20px;
        }
        .details p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .signature-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            border: none;
        }
        .signature-table p {
            margin: 0;
        }
        .footer {
            text-align: center;
            position: fixed;
            bottom: 10px;
            width: 100%;
        }
    </style>
</head>
<body>
    {{-- <div class="header">
        <img src="path/to/logo.png" alt="Company Logo">
        <div>
            <h2>CV. MAJU TECNOLOGI</h2>
            <p>Rukan Mangga Dua Square Blok H No. 18</p>
            <p>Jl. Gunung Sahari Raya No.1</p>
            <p>Kel. Ancol / Kec. Pademangan, Kota Jakarta Utara, DKI Jakarta 14430</p>
        </div>
    </div> --}}

    <h1>{{ $assetTransfer->status }}</h1>
    <h2>Nomor: {{ $assetTransfer->letter_number }}</h2>

    <div class="details">
        <p>Pada hari ini, {{ \Carbon\Carbon::parse($assetTransfer->created_at)->translatedFormat('l, d F Y') }}, Karyawan yang bertanda tangan di bawah ini :</p>
        <p><strong>Nama:</strong> {{ $assetTransfer->toUser->name }}</p>
        <p><strong>Jabatan:</strong> {{ optional($assetTransfer->toUser->jobTitle)->title }}</p>

        @if($assetTransfer->status === 'BERITA ACARA SERAH TERIMA')
            <p><strong>Menyatakan telah menerima Aset Perusahaan dari :</strong></p>
        @elseif($assetTransfer->status === 'BERITA ACARA PENGALIHAN BARANG')
            <p><strong>Menyatakan telah menerima Aset Perusahaan dari :</strong></p>
        @elseif($assetTransfer->status === 'BERITA ACARA PENGEMBALIAN BARANG')
            <p><strong>Menyatakan telah menerima Aset Perusahaan dari :</strong></p>
        @endif

        <p><strong>Nama:</strong> {{ $assetTransfer->fromUser->name }}</p>
        <p><strong>Jabatan:</strong> {{ optional($assetTransfer->fromUser->jobTitle)->title }}</p>
    </div>

    <p>Adapun Aset Perusahaan yang diserahkan kepada Karyawan antara lain berupa :</p>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Kategori</th>
                    <th>Merek/Type</th>
                    <th>Serial Number/IMEI</th>
                    <th>Perlengkapan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assetTransfer->details as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->asset->category->name }}</td>
                        <td>{{ $detail->asset->brand->name }} {{ $detail->asset->type }}</td>
                        <td>{{ $detail->asset->serial_number }}</td>
                        <td>{{ $detail->equipment }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p>Demikian {{ $assetTransfer->status }} ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.</p>

    <table class="signature-table" style="border: none;">
        <tr>
            <td>
                <p>Penerima</p>
                <br><br><br><br>
                <p><strong>{{ $assetTransfer->toUser->name }}</strong></p>
            </td>
            <td>
                <p>Pemberi</p>
                <br><br><br><br>
                <p><strong>{{ $assetTransfer->fromUser->name }}</strong></p>
            </td>
            @if($assetTransfer->status === 'BERITA ACARA PENGEMBALIAN BARANG')
                <td>
                    <p>Mengetahui</p>
                    <br><br><br><br>
                    <p><strong>ARLENI</strong></p>
                </td>
            @endif
        </tr>
    </table>
</body>
</html>
