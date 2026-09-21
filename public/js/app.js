const errorSummary = document.querySelector('#error-summary');
if (errorSummary) {
    errorSummary.querySelectorAll('a').forEach((link) => {
        if (!document.getElementById(link.hash.slice(1))) link.replaceWith(document.createTextNode(link.textContent));
    });
    errorSummary.focus();
}
document.querySelector('[data-print]')?.addEventListener('click', () => window.print());

const booking = document.querySelector('[data-booking-form]');
if (booking) {
    const date = booking.querySelector('[name=reservation_date]');
    const guests = booking.querySelector('[name=guest_count]');
    const slots = booking.querySelector('[name=time_slot_id]');
    const save = booking.querySelector('[data-save]');
    const notice = booking.querySelector('[data-availability-notice]');
    const summaryDate = document.querySelector('[data-summary-date]');
    const summaryGuests = document.querySelector('[data-summary-guests]');
    const summarySlot = document.querySelector('[data-summary-slot]');
    const updateSummary = () => {
        summaryDate.textContent = date.value ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'Asia/Jakarta' }).format(new Date(date.value + 'T12:00:00+07:00')) : 'Belum dipilih';
        summaryGuests.textContent = guests.value + ' orang';
        summarySlot.textContent = slots.value ? slots.selectedOptions[0].textContent.split(' WIB')[0] : 'Belum dipilih';
    };
    const resetAvailability = () => {
        slots.value = '';
        slots.disabled = true;
        save.disabled = true;
        notice.className = 'notice neutral';
        notice.textContent = 'Jadwal berubah. Cek ketersediaan kembali untuk memilih jam.';
        updateSummary();
    };
    date.addEventListener('change', resetAvailability);
    guests.addEventListener('change', resetAvailability);
    slots.addEventListener('change', updateSummary);
    updateSummary();
}

let dialog;
let pendingForm;
let pendingSubmitter;
let returnFocus;

document.addEventListener('submit', (event) => {
    const form = event.target;
    const submitter = event.submitter;
    if (form.hasAttribute('data-confirm') && form.dataset.approved !== 'yes') {
        event.preventDefault();
        pendingForm = form;
        pendingSubmitter = submitter;
        returnFocus = submitter;
        if (!dialog) {
            dialog = document.createElement('dialog');
            dialog.setAttribute('aria-labelledby', 'confirm-heading');
            dialog.innerHTML = '<h2 id="confirm-heading">Konfirmasi tindakan</h2><p data-message></p><div class="actions"><button type="button" class="button secondary" data-dismiss>Kembali</button><button type="button" class="button" data-approve>Lanjutkan</button></div>';
            document.body.append(dialog);
            dialog.querySelector('[data-dismiss]').addEventListener('click', () => dialog.close());
            dialog.querySelector('[data-approve]').addEventListener('click', () => {
                pendingForm.dataset.approved = 'yes';
                dialog.close();
                pendingForm.requestSubmit(pendingSubmitter);
            });
            dialog.addEventListener('close', () => returnFocus?.focus());
        }
        dialog.querySelector('[data-message]').textContent = form.dataset.confirm;
        dialog.querySelector('#confirm-heading').textContent = form.dataset.confirmHeading || 'Konfirmasi tindakan';
        dialog.querySelector('[data-approve]').textContent = form.dataset.confirmLabel || 'Lanjutkan';
        dialog.classList.toggle('refund-dialog', form.dataset.confirmLabel === 'Catat pengembalian');
        dialog.showModal();
        dialog.querySelector('[data-dismiss]').focus();
        return;
    }
    if (form.dataset.submitting === 'yes') {
        event.preventDefault();
        return;
    }
    form.dataset.submitting = 'yes';
    if (submitter?.dataset.progress) {
        const status = document.createElement('p');
        status.className = 'small muted';
        status.setAttribute('role', 'status');
        status.textContent = submitter.dataset.progress;
        form.append(status);
        setTimeout(() => {
            submitter.textContent = submitter.dataset.progress;
            submitter.disabled = true;
        }, 0);
    }
});
window.addEventListener('pageshow', (event) => {
    if (event.persisted) window.location.reload();
});
