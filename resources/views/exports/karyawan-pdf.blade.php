<!DOCTYPE html>
<html>

<head>
  <title>Data Karyawan</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 4px;
      vertical-align: middle;
    }

    .center{
      text-align:center;
    }
  </style>
</head>

<body>
  <table width="100%" style="margin-bottom:5px;">
    <tr>
      <td style="border:none;">
          <img src="{{ $logoBase64 }}" height="40">
      </td>
      <td align="right" style="font-size:10px; border:none; vertical-align:bottom">
          <strong>Tanggal Export:</strong> {{ date('d-m-Y H:i') }}
      </td>
    </tr>
  </table>

  <hr style="border:none;height:1px;background-color:#2c2c2c;">

  <h2 style="margin-top: 20px; margin-bottom:10px;">Data Karyawan</h2>

  @if($appliedFilters)
    <div style="color: #667382; margin-bottom:10px; text-transform:capitalize">
      @foreach($appliedFilters as $label => $value)
        <small>{{ $label }}: {{ $value }}</small>@if(!$loop->last),@endif
      @endforeach
    </div>
  @endif

  <table>
    <thead>
      <tr class="center">
        <th>No</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>KTP</th>
        <th>Jabatan</th>
        <th>Divisi</th>
        <th>Lokasi</th>
        <th>Kategori</th>
        <th>Agama</th>
        <th>L/P</th>
        <th>Telpon</th>
        <th>Lahir</th>
        <th>Usia</th>
        <th>Pendidikan</th>
        <th>Marital</th>
        <th>Masuk</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data as $index => $item)
      <tr>
        <td class="center">{{ $index + 1 }}</td>
        <td>{{ strtoupper($item->nik) }}</td>
        <td>{{ ucwords($item->nama) }}</td>
        <td>{{ $item->ktp }}</td>
        <td>{{ ucwords($item->jabatan->nama ?? '-') }}</td>
        <td>{{ ucwords($item->jabatan->unit->divisi->nama ?? '-') }}</td>
        <td>{{ ucwords($item->lokasi->nama ?? '-') }}</td>
        <td>{{ strtoupper($item->kategori->nama ?? '-') }}</td>
        <td>{{ ucwords($item->agama) }}</td>
        <td>{{ ucwords($item->jenis_kelamin) }}</td>
        <td>{{ ucwords($item->telpon) }}</td>
        <td>{{ date('d-m-Y', strtotime($item->tgl_lahir)) }}</td>
        <td>{{ $item->usia }} th</td>
        <td>{{ strtoupper($item->pendidikan) }}</td>
        <td>{{ ucwords($item->marital) }}</td>
        <td>{{ $item->tgl_masuk ? date('d-m-Y', strtotime($item->tgl_masuk)) : '-' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>