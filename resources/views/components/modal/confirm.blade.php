<div wire:ignore.self class="modal fade" id="confirmModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <p class="fs-2 my-1">Yakin ingin menghapus data ini?</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button wire:click="deleteData" wire:loading.attr="disabled" class="btn btn-danger">
          Hapus
          <span wire:loading class="spinner-border spinner-border-sm ms-2"></span>
        </button>
      </div>
    </div>
  </div>
</div>