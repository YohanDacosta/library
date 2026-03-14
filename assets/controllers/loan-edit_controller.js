import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        id: String,
        student: String,
        books: String,
        tutor: String,
        loanDate: String,
        returnDate: String,
        status: String,
        updateUrl: String
    }

    connect() {
        this.bookStatuses = {};
    }

    openModal(event) {
        // Prevent if clicking on action button
        if (event.target.closest('button')) return;

        const modal = document.querySelector('#editLoanModal');

        // Populate read-only fields
        document.getElementById('editLoanId').value = this.idValue;
        document.getElementById('editLoanStudent').textContent = this.studentValue;
        document.getElementById('editLoanTutor').textContent = this.tutorValue;

        // Parse and populate books with status controls
        this.books = this.booksValue ? JSON.parse(this.booksValue) : [];
        this.bookStatuses = {};
        this.originalBookStatuses = {}; // Store original statuses to track changes
        this.books.forEach(book => {
            this.bookStatuses[book.id] = book.status;
            this.originalBookStatuses[book.id] = book.status;
        });
        this.renderBooks();

        // Populate editable fields
        document.getElementById('editLoanDate').value = this.loanDateValue;
        document.getElementById('editReturnDate').value = this.returnDateValue;
        document.getElementById('editLoanStatus').value = this.statusValue;

        // Show/hide "Mark as Returned" button - only show if there are books in 'loaned' status
        const markReturnedBtn = document.getElementById('markReturnedBtn');
        if (markReturnedBtn) {
            const hasLoanedBooks = Object.values(this.bookStatuses).some(s => s === 'loaned');
            markReturnedBtn.style.display = hasLoanedBooks ? 'flex' : 'none';
            // Remove old listener and add new one
            markReturnedBtn.replaceWith(markReturnedBtn.cloneNode(true));
            document.getElementById('markReturnedBtn').addEventListener('click', () => this.markAsReturned());
        }

        // Connect submit button
        const submitBtn = document.getElementById('submitLoanBtn');
        if (submitBtn) {
            submitBtn.onclick = () => this.submitForm();
        }

        // Open modal
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';

        // Close on overlay click
        modal.addEventListener('click', this.handleOverlayClick);

        // Close on Escape
        document.addEventListener('keydown', this.handleEscape);
    }

    renderBooks() {
        const booksContainer = document.getElementById('editLoanBooks');

        const statusConfig = {
            loaned: { label: 'Prestado', bg: '#dbeafe', color: '#1e40af' },
            available: { label: 'Devolver', bg: '#dcfce7', color: '#166534' },
            lost: { label: 'Perdido', bg: '#fee2e2', color: '#991b1b' },
            repaired: { label: 'Reparación', bg: '#f3e8ff', color: '#7c3aed' }
        };

        booksContainer.innerHTML = this.books.map(book => {
            const currentStatus = this.bookStatuses[book.id];
            const isEditable = currentStatus === 'loaned';

            return `
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px;" data-book-id="${book.id}">
                    <div style="display: flex; align-items: center; justify-content: space-between;${isEditable ? ' margin-bottom: 10px;' : ''}">
                        <span style="font-size: 14px; font-weight: 500; color: #111827;">${this.escapeHtml(book.title)}</span>
                        <span class="book-status-badge" style="font-size: 11px; padding: 2px 8px; background: ${statusConfig[currentStatus]?.bg || '#f3f4f6'}; color: ${statusConfig[currentStatus]?.color || '#374151'}; border-radius: 9999px; font-weight: 500;">
                            ${statusConfig[currentStatus]?.label || currentStatus}
                        </span>
                    </div>
                    ${isEditable ? `
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                        ${this.renderStatusButton(book.id, 'loaned', 'Mantener', currentStatus)}
                        ${this.renderStatusButton(book.id, 'available', 'Devolver', currentStatus)}
                        ${this.renderStatusButton(book.id, 'lost', 'Perdido', currentStatus)}
                        ${this.renderStatusButton(book.id, 'repaired', 'Reparación', currentStatus)}
                    </div>
                    ` : ''}
                </div>
            `;
        }).join('');

        // Add click listeners to buttons
        booksContainer.querySelectorAll('[data-status-btn]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const bookId = e.target.dataset.bookId;
                const newStatus = e.target.dataset.status;
                this.updateBookStatus(bookId, newStatus);
            });
        });
    }

    renderStatusButton(bookId, status, label, currentStatus) {
        const isActive = currentStatus === status;
        const baseStyle = `
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid;
            transition: all 0.15s;
        `;

        const styles = {
            loaned: {
                active: 'background: #1e40af; color: white; border-color: #1e40af;',
                inactive: 'background: white; color: #1e40af; border-color: #93c5fd;'
            },
            available: {
                active: 'background: #166534; color: white; border-color: #166534;',
                inactive: 'background: white; color: #166534; border-color: #86efac;'
            },
            lost: {
                active: 'background: #991b1b; color: white; border-color: #991b1b;',
                inactive: 'background: white; color: #991b1b; border-color: #fecaca;'
            },
            repaired: {
                active: 'background: #7c3aed; color: white; border-color: #7c3aed;',
                inactive: 'background: white; color: #7c3aed; border-color: #c4b5fd;'
            }
        };

        const style = isActive ? styles[status].active : styles[status].inactive;

        return `
            <button type="button"
                    data-status-btn
                    data-book-id="${bookId}"
                    data-status="${status}"
                    style="${baseStyle} ${style}">
                ${label}
            </button>
        `;
    }

    updateBookStatus(bookId, newStatus) {
        // Only allow changes if current status is 'loaned'
        if (this.bookStatuses[bookId] !== 'loaned') {
            return;
        }

        this.bookStatuses[bookId] = newStatus;
        this.renderBooks();

        const loanStatusSelect = document.getElementById('editLoanStatus');
        const returnDateInput = document.getElementById('editReturnDate');
        const markReturnedBtn = document.getElementById('markReturnedBtn');

        // Check if there are still books in 'loaned' status
        const hasLoanedBooks = Object.values(this.bookStatuses).some(s => s === 'loaned');
        // Check if all books are returned (available)
        const allReturned = Object.values(this.bookStatuses).every(s => s === 'available');

        if (!hasLoanedBooks) {
            // No more loaned books - set status to returned
            loanStatusSelect.value = 'returned';
            if (!returnDateInput.value) {
                returnDateInput.value = new Date().toISOString().split('T')[0];
            }
            if (markReturnedBtn) {
                markReturnedBtn.style.display = 'none';
            }
        } else {
            // There are still loaned books
            if (loanStatusSelect.value === 'returned') {
                loanStatusSelect.value = 'active';
                returnDateInput.value = '';
            }
            if (markReturnedBtn) {
                markReturnedBtn.style.display = 'flex';
            }
        }
    }

    markAsReturned() {
        // Set only 'loaned' books to available
        Object.keys(this.bookStatuses).forEach(bookId => {
            if (this.bookStatuses[bookId] === 'loaned') {
                this.bookStatuses[bookId] = 'available';
            }
        });
        this.renderBooks();

        // Set loan status to returned
        document.getElementById('editLoanStatus').value = 'returned';

        // Set return date to today
        document.getElementById('editReturnDate').value = new Date().toISOString().split('T')[0];

        // Hide the button
        const markReturnedBtn = document.getElementById('markReturnedBtn');
        if (markReturnedBtn) {
            markReturnedBtn.style.display = 'none';
        }
    }

    getBookStatusChanges() {
        // Only return books whose status actually changed
        return Object.entries(this.bookStatuses)
            .filter(([bookId, status]) => status !== this.originalBookStatuses[bookId])
            .map(([bookId, status]) => ({
                bookId,
                status
            }));
    }

    async submitForm() {
        const status = document.getElementById('editLoanStatus').value;
        const returnDate = document.getElementById('editReturnDate').value;
        const hasLoanedBooks = Object.values(this.bookStatuses).some(s => s === 'loaned');
        const anyBookDelivered = Object.values(this.bookStatuses).some(s => s !== 'loaned');

        // Validation: if any book was delivered (returned/lost/repaired), return date is required
        if (anyBookDelivered && !returnDate) {
            this.showErrorMessage('La fecha de devolución es obligatoria cuando hay libros entregados');
            return;
        }

        // Validation: if status is "returned", no books should be in 'loaned' status
        if (status === 'returned' && hasLoanedBooks) {
            this.showErrorMessage('Todos los libros deben estar entregados para marcar el préstamo como devuelto');
            return;
        }

        // Validation: if no books are loaned (all delivered), status must be "returned"
        if (!hasLoanedBooks && status !== 'returned') {
            this.showErrorMessage('Si todos los libros están entregados, el estado debe ser "Devuelto"');
            return;
        }

        const submitBtn = document.getElementById('submitLoanBtn');
        const originalText = submitBtn ? submitBtn.textContent : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';
        }

        const data = {
            loanId: document.getElementById('editLoanId').value,
            status: status,
            loanDate: document.getElementById('editLoanDate').value,
            returnDate: returnDate,
            books: this.getBookStatusChanges()
        };

        try {
            const response = await fetch('/loan/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Error al actualizar el préstamo');
            }

            // Success
            this.showSuccessMessage('Préstamo actualizado correctamente');
            this.closeModal();

            // Reload page to reflect changes
            setTimeout(() => window.location.reload(), 1000);

        } catch (error) {
            console.error('Error updating loan:', error);
            this.showErrorMessage(error.message);
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }
    }

    showSuccessMessage(message) {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: #166534;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        toast.innerHTML = `
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            ${this.escapeHtml(message)}
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    showErrorMessage(message) {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: #991b1b;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        toast.innerHTML = `
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            ${this.escapeHtml(message)}
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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
