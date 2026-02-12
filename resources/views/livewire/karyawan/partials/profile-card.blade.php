<div class="col-lg-4 col-md-4 col-sm-12 d-flex">
  <div class="card w-100 h-100">
    <div class="card-body d-flex flex-column text-center">

      <div class="mb-3">
        <img
          src="{{ asset($karyawan->img) }}"
          alt="Foto {{ $karyawan->nama }}"
          @click="showImageModal = true"
          class="card-img-top"
          style="max-width: 150px; height: auto; display: block; margin: 0 auto; border-radius: 5px; cursor: pointer;"
          loading="lazy"
          wire:ignore
          wire:key="profile-image-{{ $karyawan->id }}"
        />
      </div>

      <div class="fw-bold text-uppercase">{{ $karyawan->nama }}</div>
      <div class="text-muted text-uppercase">{{ $karyawan->nik }}</div>

      <div class="my-3">
        <span class="badge text-uppercase {{ $karyawan->status === 'aktif' ? 'bg-success text-light' : 'bg-danger text-light' }}">
          {{ $karyawan->status }}
        </span>
        
      </div>

      <div class="mt-auto">
        <div class="list-group list-group-flush text-start">
          <div class="list-group-item">
            <div class="d-flex justify-content-between">
              <span class="text-muted">Kategori</span>
              <span class="text-uppercase">{{ $karyawan->kategori?->nama ?? '-' }}</span>
            </div>

            @if($karyawan->kategori?->nama == 'pkwt')
            <div class="d-flex justify-content-between">
              <span class="text-muted">Kontrak ke</span>
              <span class="text-uppercase">{{ $kontrakKe ?? '-' }}</span>
            </div>
            @endif
          </div>
          <div class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Penempatan</span>
            <span class="text-uppercase">{{ $karyawan->lokasi?->nama ?? '-' }}</span>
          </div>
          <div class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Jabatan</span>
            <span class="text-uppercase">{{ $karyawan->jabatan?->nama ?? '-' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>