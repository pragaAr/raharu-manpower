<div class="col-lg-4 col-md-12 col-sm-6 d-flex">
  <div class="card w-100 h-100">
    <div class="card-header">
      <h4 class="card-title text-uppercase text-secondary">#Kepegawaian</h4>
    </div>

    <div class="card-body p-2">
      <table class="table table-fixed table-borderless table-vcenter">
        <tbody>
          <tr>
            <td style="width:35%">Tgl Masuk</td>
            <td style="width:5%">:</td>
            <td>{{ optional($karyawan->tgl_masuk)->format('d-m-Y') ?? '-' }}</td>
          </tr>
          <tr>
            <td style="width:35%">Divisi</td>
            <td style="width:5%">:</td>
            <td class="text-uppercase">{{ $karyawan->jabatan?->unit?->divisi?->nama ?? '-' }}</td>
          </tr>
          <tr>
            <td style="width:35%">Unit</td>
            <td style="width:5%">:</td>
            <td class="text-uppercase">{{ $karyawan->jabatan?->unit?->nama ?? '-' }}</td>
          </tr>
          <tr>
            <td style="width:35%">Tgl Efektif</td>
            <td style="width:5%">:</td>
            <td>{{ optional($karyawan->tgl_masuk)->format('d-m-Y') ?? '-' }}</td>
          </tr>
          <tr>
            <td style="width:35%">Tgl Keluar</td>
            <td style="width:5%">:</td>
            <td>{{ optional($karyawan->tgl_keluar)->format('d-m-Y') ?? '-' }}</td>
          </tr>
          <tr>
            <td style="width:35%">TMK</td>
            <td style="width:5%">:</td>
            <td>{{ optional($karyawan->kontrakTerakhir?->tgl_mulai)->format('d-m-Y') ?? '-' }}</td>
          </tr>
          <tr>
            <td style="width:35%">THK</td>
            <td style="width:5%">:</td>
            <td>{{ optional($karyawan->kontrakTerakhir?->tgl_selesai)->format('d-m-Y') ?? '-' }}</td>
          </tr>
          <tr>
            <td style="width:35%">BPJS TK</td>
            <td style="width:5%">:</td>
            <td>{{ $karyawan->bpjs_tk }}</td>
          </tr>
          <tr>
            <td style="width:35%">BPJS KS</td>
            <td style="width:5%">:</td>
            <td>{{ $karyawan->bpjs_ks }}</td>
          </tr>
          <tr>
            <td style="width:35%">Penetapan</td>
            <td style="width:5%">:</td>
            <td>{{ $karyawan->tgl_penetapan != '' ? date('d-m-Y', strtotime($karyawan->tgl_penetapan)) : '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>