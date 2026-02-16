// Autofocus input ketika modal terbuka
document.addEventListener("shown.bs.modal", function (event) {
    const modal = event.target;
    const input = modal.querySelector("input[autofocus]");
    if (input) input.focus();
});

// Toggle modal (Livewire friendly)
window.toggleModal = function (modalId, action = "show") {
    const el = document.getElementById(modalId);
    if (!el) return;

    const modal = bootstrap.Modal.getOrCreateInstance(el);
    action === "show" ? modal.show() : modal.hide();
};

// Blur active element saat modal ditutup
window.blurActiveElementOnModalHide = function (modals) {
    if (!modals) return;

    if (!Array.isArray(modals)) {
        modals = [modals];
    }

    modals.forEach((modalEl) => {
        if (modalEl instanceof HTMLElement) {
            modalEl.addEventListener("hide.bs.modal", () => {
                if (document.activeElement instanceof HTMLElement) {
                    document.activeElement.blur();
                }
            });
        }
    });
};

window.bindTomSelectModalValidation = function ({
    modalEl,
    selectEl,
    errorEl,
}) {
    if (!modalEl) return;

    let observer = null;

    const bindObserver = () => {
        if (!selectEl || !errorEl) return;
        if (!selectEl.tomselect) return;

        const checkError = () => {
            selectEl.tomselect.wrapper.classList.toggle(
                "is-invalid",
                !!errorEl.querySelector("small"),
            );
        };

        checkError();

        observer = new MutationObserver(checkError);

        observer.observe(errorEl, {
            childList: true,
            subtree: true,
        });
    };

    const cleanup = () => {
        if (observer) {
            observer.disconnect();
            observer = null;
        }

        if (selectEl && selectEl.tomselect) {
            selectEl.tomselect.wrapper.classList.remove("is-invalid");
        }
    };

    modalEl.addEventListener("shown.bs.modal", bindObserver);

    modalEl.addEventListener("hidden.bs.modal", cleanup);
};

function initDatePickers(ids) {
    const targetIds = Array.isArray(ids) ? ids : [ids];

    targetIds.forEach((id) => {
        const element = document.getElementById(id);

        if (element) {
            element.addEventListener("click", function () {
                if (typeof this.showPicker === "function") {
                    try {
                        this.showPicker();
                    } catch (e) {
                        console.warn("Gagal memanggil picker", e);
                    }
                }
            });
        }
    });
}
