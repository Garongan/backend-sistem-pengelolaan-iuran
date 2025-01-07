<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Bulanan</title>
    <style>
        .container {
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 50px;
        }
        .title {
            padding-top: 10px;
            margin: 0;
        }
        .subtitle {
            padding-bottom: 10px;
            margin: 0;
        }
        table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: black;
            color: white;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body class="container">
    <h1 class="title">Laporan Bulanan - {{ $month }}/{{ $year }}</h1>
    <p class="subtitle">Perumahan Permata Tlogomas</p>
    <table>
        <tr>
            <th>Kategori</th>
            <th>Jumlah</th>
        </tr>
        <tr>
            <td>Pemasukan</td>
            <td>Rp {{ number_format($income, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pengeluaran</td>
            <td>Rp {{ number_format($spending, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Saldo</td>
            <td>Rp {{ number_format($balance, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h1 class="title">Detail Laporan Pemasukan Bulanan - {{ $month }}/{{ $year }}</h1>
    <table>
        <tr>
            <th>No.</th>
            <th>Nama Pembayar</th>
            <th>Tanggal</th>
            <th>Tipe Pemasukan</th>
            <th>Jumlah</th>
            <th>Status</th>
        </tr>
        @foreach ($incomes as $index => $item)
        <tr>
            <td>{{ $index }}</td>
            <td>{{ $item['resident']->fullname }}</td>
            <td>{{ $item['period'] }}</td>
            <td>{{ strtoupper($item['payment_type'] == 'security' ? 'satpam' : 'kebersihan') }}</td>
            <td>Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
            <td>{{ $item['is_paid_off'] ? 'LUNAS' : 'BELUM LUNAS'}}</td>
        </tr>
        @endforeach
    </table>

    <h1 class="title">Detail Laporan Pengeluaran Bulanan - {{ $month }}/{{ $year }}</h1>
    <table>
        <tr>
            <th>No.</th>
            <th>Deskripsi</th>
            <th>Tanggal</th>
            <th>Jumlah</th>
        </tr>
        @foreach ($spendings as $index => $item)
        <tr>
            <td>{{ $index }}</td>
            <td>{{ $item['description'] }}</td>
            <td>{{ $item['date'] }}</td>
            <td>Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
</body>

</html>
