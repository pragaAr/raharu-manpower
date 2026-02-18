document.addEventListener("shown.bs.modal", function (event) {
    const modal = event.target;
    const input = modal.querySelector("input[autofocus]");
    if (input) input.focus();
});

window.toggleModal = function (modalId, action = "show") {
    const el = document.getElementById(modalId);
    if (!el) return;

    const modal = bootstrap.Modal.getOrCreateInstance(el);
    action === "show" ? modal.show() : modal.hide();
};

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

document.addEventListener("pointerdown", function (e) {
    if (!e.target.matches('input[type="date"], input[type="time"]')) return;

    const el = e.target;

    if (typeof el.showPicker !== "function") return;

    if (el.disabled || el.readOnly) return;

    try {
        el.showPicker();
    } catch (err) {
        console.warn("Gagal memanggil picker", err);
    }
});

function createTomSelectGroup(rootSelector, configs) {
    const root = document.querySelector(rootSelector);
    if (!root) return null;

    const instances = new Map();
    const observers = [];

    const observeError = (selectEl, errEl) => {
        if (!errEl) return;

        const observer = new MutationObserver(() => {
            if (!selectEl.tomselect) return;

            selectEl.tomselect.wrapper.classList.toggle(
                "is-invalid",
                errEl.querySelector("small") !== null,
            );
        });

        observer.observe(errEl, { childList: true, subtree: true });
        observers.push(observer);
    };

    const initTomSelect = (selectEl, hiddenInputId, placeholder, key) => {
        if (selectEl.tomselect) {
            selectEl.tomselect.destroy();
        }

        const ts = new TomSelect(selectEl, {
            allowEmptyOption: true,
            placeholder,
            items: [],
            onChange(value) {
                const hiddenInput = root.querySelector(`#${hiddenInputId}`);
                if (!hiddenInput) return;

                hiddenInput.value = value;
                hiddenInput.dispatchEvent(
                    new Event("input", { bubbles: true }),
                );
            },
        });

        instances.set(key, ts);
    };

    configs.forEach((cfg) => {
        const selectEl = root.querySelector(`#${cfg.selectId}`);
        const errEl = root.querySelector(`#${cfg.errorId}`);
        if (!selectEl) return;

        observeError(selectEl, errEl);
        initTomSelect(
            selectEl,
            cfg.hiddenInputId,
            cfg.placeholder,
            cfg.selectId,
        );
    });

    return {
        destroy() {
            instances.forEach((ts) => ts.destroy());
            observers.forEach((obs) => obs.disconnect());
            instances.clear();
        },

        reset() {
            instances.forEach((ts) => ts.clear());
        },

        refresh(selectId, options, formatter) {
            const selectEl = root.querySelector(`#${selectId}`);
            if (!selectEl) return;

            // destroy existing
            instances.get(selectId)?.destroy();
            instances.delete(selectId);

            // reset options
            selectEl.innerHTML = "";

            options.forEach((item) => {
                const option = document.createElement("option");
                option.value = item.id;
                option.textContent = formatter
                    ? formatter(item)
                    : (item.name ?? item.label ?? item.id);
                selectEl.appendChild(option);
            });

            // re-init
            const cfg = configs.find((c) => c.selectId === selectId);
            if (!cfg) return;

            initTomSelect(
                selectEl,
                cfg.hiddenInputId,
                cfg.placeholder,
                selectId,
            );
        },
        clear(selectId) {
            instances.get(selectId)?.clear(true);
        },
        setValue(selectId, value) {
            instances.get(selectId)?.setValue(value, true);
        },
    };
}
