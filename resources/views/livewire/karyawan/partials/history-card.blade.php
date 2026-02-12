<div class="mt-3">
  <div class="row row-cards">
    <div class="col-md-12">
      <ul class="timeline">
        @foreach ($history as $row)
          <li class="timeline-event">
            @php
              $class = 'bg-twitter-lt';
              if ($row->jenis == 'mutasi data') {
                $class = 'bg-warning';
              } elseif ($row->jenis == 'nonaktif') {
                $class = 'bg-danger';
              } elseif ($row->jenis == 'reaktifasi') {
                $class = 'bg-purple';
              }elseif ($row->jenis == 'penambahan data') {
                $class = 'bg-success';
              }
            @endphp

            <div class="timeline-event-icon {{ $class }} text-white">
              <svg xmlns="http://www.w3.org/2000/svg" 
              width="24" 
              height="24" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2" 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              class="icon icon-tabler icons-tabler-outline icon-tabler-history">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 8l0 4l2 2" />
                <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
              </svg>
            </div>
            <div class="card timeline-event-card">
              
              <div class="card-body">
                <div class="text-secondary float-end">
                  {{ $row->created_at->format('d-m-Y H:i:s') }}
                </div>
                <h4 class="text-capitalize">
                  {{ $row->jenis }}
                </h4>

                <table class="table table-borderless">
                  <tbody style="font-size:13px">
                    @if ($row->jenis == 'penambahan data')
                      <tr>
                        <td class="text-uppercase" style="width:35%; padding:10px;">
                          <strong>nik</strong>
                        </td>
                        <td class="text-center" style="width:5%; padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ $row->nik }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-uppercase" style="padding:10px;">
                          <strong>nama</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ $row->nama_karyawan }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-uppercase" style="padding:10px;">
                          <strong>telpon</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ $row->telpon }}
                        </td>
                      </tr>
                      <tr>
                        <td class='text-uppercase' style="padding:10px;">
                          <strong>kategori</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ $row->kategori_nama }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-uppercase" style="padding:10px;">
                          <strong>penempatan</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ $row->lokasi_nama }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-uppercase" style="padding:10px;">
                          <strong>divisi</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ $row->divisi_nama ? $row->divisi_nama : '-' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-uppercase" style="padding:10px;">
                          <strong>unit</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ $row->unit_nama ? $row->unit_nama : '-' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-uppercase" style="padding:10px;">
                          <strong>jabatan</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ $row->jabatan_nama ? $row->jabatan_nama : '-' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-uppercase" style="padding:10px;">
                          <strong>tanggal efektif</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ optional($row->tgl_efektif)->format('d-m-Y') ?? '-' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-uppercase" style="padding:10px;">
                          <strong>tmk / thk</strong>
                        </td>
                        <td class="text-center" style="padding:10px;">:</td>
                        <td class="text-uppercase" style="padding:10px;">
                          {{ optional($row->tgl_mulai)->format('d-m-Y') ?? '-' }} / {{ optional($row->tgl_selesai)->format('d-m-Y') ?? '-' }} 
                        </td>
                      </tr>

                      @if ($row->kategori_nama === 'kontrak')
                        <tr>
                          <td class="text-uppercase" style="padding:10px;">
                            <strong>kontrak ke</strong>
                          </td>
                          <td class="text-center" style="padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            1
                          </td>
                        </tr>
                      @endif

                      @if ($row->keterangan)
                        <tr>
                          <td class="text-uppercase" style="padding:10px;">
                            <strong>keterangan</strong>
                          </td>
                          <td class="text-center" style="padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->keterangan }}
                          </td>
                        </tr>
                      @endif

                    @else
                      @if ($row->nik)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>nik</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->nik }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->kategori_nama)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>kategori</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->kategori_nama }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->divisi_nama)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>divisi</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->divisi_nama }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->lokasi_nama)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>lokasi</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->lokasi_nama }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->jabatan_nama)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>jabatan</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->jabatan_nama }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->telpon)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>telpon</strong>
                          </td>
                          <td class="text-center" style="padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->telpon }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->tgl_masuk)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>tanggal efektif</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ optional($row->tgl_masuk)->format('d-m-Y') ?? '-' }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->tgl_mulai || $row->tgl_selesai)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>tmk / thk</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ optional($row->tgl_mulai)->format('d-m-Y') ?? '-' }} / {{ optional($row->tgl_selesai)->format('d-m-Y') ?? '-' }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->tgl_keluar)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>tanggal keluar</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ optional($row->tgl_keluar)->format('d-m-Y') ?? '-' }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->status && $row->status != 'aktif')
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>status</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->status }}
                          </td>
                        </tr>
                      @endif

                      @if (!empty($row->kontrak_ke))
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>kontrak ke</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->kontrak_ke }}
                          </td>
                        </tr>
                      @endif

                      @if ($row->keterangan)
                        <tr>
                          <td class="text-uppercase" style="width:35%; padding:10px;">
                            <strong>keterangan</strong>
                          </td>
                          <td class="text-center" style="width:5%; padding:10px;">:</td>
                          <td class="text-uppercase" style="padding:10px;">
                            {{ $row->keterangan }}
                          </td>
                        </tr>
                      @endif
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </li>
        @endforeach
      </ul>
      @if ($historyLimit < $totalHistory)
        <div class="mt-4 text-center">
          <button wire:click="loadMore" class="btn btn-transparent w-100" style="outline: none; border-radius: 0; border: none; background: transparent; box-shadow: none;" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="loadMore">
              Muat Lebih Banyak...
            </span>
            <span wire:loading wire:target="loadMore">
              <span class="spinner-border spinner-border-sm me-2" role="status"></span>
              Memuat...
            </span>
          </button>
        </div>
      @endif
    </div>
  </div>
</div>
