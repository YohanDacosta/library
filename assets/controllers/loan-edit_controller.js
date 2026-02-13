import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        id: String,
        student: String,
        book: String,
        tutor: String,
        loanDate: String,
        returnDate: String,
        status: String
    }

    openModal(event) {
        // Prevent if clicking on action button
        if (event.target.closest('button')) return;

        const modal = document.querySelector('#editLoanModal');

        // Populate read-only fields
        document.getElementById('editLoanId').value = this.idValue;
        document.getElementById('editLoanStudent').textContent = this.studentValue;
        document.getElementById('editLoanBook').textContent = this.bookValue;
        document.getElementById('editLoanTutor').textContent = this.tutorValue;

        // Populate editable fields
        document.getElementById('editLoanDate').value = this.loanDateValue;
        document.getElementById('editReturnDate').value = this.returnDateValue;
        document.getElementById('editLoanStatus').value = this.statusValue;

        // Show/hide "Mark as Returned" button based on current status
        const markReturnedBtn = document.getElementById('markReturnedBtn');
        markReturnedBtn.style.display = this.statusValue === 'returned' ? 'none' : 'flex';

        // Open modal
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';

        // Close on overlay click
        modal.addEventListener('click', this.handleOverlayClick);

        // Close on Escape
        document.addEventListener('keydown', this.handleEscape);
    }

    markAsReturned() {
        // Set status to returned
        document.getElementById('editLoanStatus').value = 'returned';

        // Set return date to today
        document.getElementById('editReturnDate').value = new Date().toISOString().split('T')[0];

        // Hide the button
        document.getElementById('markReturnedBtn').style.display = 'none';
    }

    handleOverlayClick = (event) => {
        if (event.target.classList.contains('modal-overlay')) {
            this.closeModal();
        }
    }

    handleEscape = (event) => {
        if (event.key === 'Escape') {
            this.closeModal();
        }
    }

    closeModal() {
        const modal = document.querySelector('#editLoanModal');
        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
            modal.removeEventListener('click', this.handleOverlayClick);
            document.removeEventListener('keydown', this.handleEscape);
        }
    }
}
