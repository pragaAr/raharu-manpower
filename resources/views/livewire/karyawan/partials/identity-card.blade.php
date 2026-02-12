<div class="col-lg-4 col-md-8 col-sm-6 d-flex">
  <div class="card w-100 h-100">
    <div class="card-header">
      <h4 class="card-title text-uppercase text-secondary">#Identitas</h4>
    </div>

    <div class="card-body p-2">
      <table class="table table-fixed table-borderless table-vcenter">
        <tbody>
          <tr>
            <td style="width:35%">No KTP</td>
            <td style="width:5%">:</td>
            <td>{{ $karyawan->ktp }}</td>
          </tr>
          <tr>
            <td style="width:35%">Alamat</td>
            <td style="width:5%">:</td>
            <td class="text-capitalize">{{ $karyawan->alamat }}</td>
          </tr>
          <tr>
            <td style="width:35%">Tgl Lahir</td>
            <td style="width:5%">:</td>
            <td>{{ optional($karyawan->tgl_lahir)->format('d-m-Y') ?? '-' }}</td>
          </tr>
          <tr>
            <td style="width:35%">Usia</td>
            <td style="width:5%">:</td>
            <td class="text-capitalize">{{ $karyawan->usia }} th</td>
          </tr>
          <tr>
            <td style="width:35%">JK</td>
            <td style="width:5%">:</td>
            <td class="text-capitalize">{{ $karyawan->jenis_kelamin === 'l' ? 'Laki-laki' : 'Perempuan' }}</td>
          </tr>
          <tr>
            <td style="width:35%">Agama</td>
            <td style="width:5%">:</td>
            <td class="text-capitalize">{{ $karyawan->agama }}</td>
          </tr>
          <tr>
            <td style="width:35%">Telpon</td>
            <td style="width:5%">:</td>
            <td>{{ $karyawan->telpon }}</td>
          </tr>
          <tr>
            <td style="width:35%">Status</td>
            <td style="width:5%">:</td>
            <td class="text-capitalize">{{ $karyawan->marital }}</td>
          </tr>
          <tr>
            <td style="width:35%">Pendidikan</td>
            <td style="width:5%">:</td>
            <td class="text-uppercase">{{ $karyawan->pendidikan }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>