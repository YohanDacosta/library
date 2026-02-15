import { Controller } from '@hotwired/stimulus';

const STORAGE_KEY = 'library_book_cart';
const TUTOR_STORAGE_KEY = 'library_selected_tutor';

export default class extends Controller {
    static targets = [
        'cart', 'cartItems', 'cartCount', 'createBtn', 'booksList',
        'tutorSearch', 'tutorDropdown', 'tutorList', 'selectedTutor', 'clearTutor'
    ];

    connect() {
        this.books = this.loadFromStorage();
        this.selectedTutor = this.loadTutorFromStorage();
        this.tutors = this.loadTutorsData();
        this.updateCartUI();
        this.updateTutorUI();

        // Close dropdown when clicking outside
        this.handleOutsideClick = this.handleOutsideClick.bind(this);
        document.addEventListener('click', this.handleOutsideClick);

        // Listen for loan created event to clear cart
        this.handleLoanCreated = this.handleLoanCreated.bind(this);
        window.addEventListener('loan:created', this.handleLoanCreated);
    }

    disconnect() {
        document.removeEventListener('click', this.handleOutsideClick);
        window.removeEventListener('loan:created', this.handleLoanCreated);
    }

    handleLoanCreated() {
        this.clearCart();
    }

    loadTutorsData() {
        const tutorsData = this.element.dataset.tutors;
        return tutorsData ? JSON.parse(tutorsData) : [];
    }

    loadFromStorage() {
        const stored = sessionStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }

    loadTutorFromStorage() {
        const stored = sessionStorage.getItem(TUTOR_STORAGE_KEY);
        return stored ? JSON.parse(stored) : null;
    }

    saveToStorage() {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(this.books));
    }

    saveTutorToStorage() {
        if (this.selectedTutor) {
            sessionStorage.setItem(TUTOR_STORAGE_KEY, JSON.stringify(this.selectedTutor));
        } else {
            sessionStorage.removeItem(TUTOR_STORAGE_KEY);
        }
    }

    // Tutor search and selection
    handleOutsideClick(event) {
        if (this.hasTutorDropdownTarget && !event.target.closest('[data-tutor-search-container]')) {
            this.tutorDropdownTarget.style.display = 'none';
        }
    }

    onTutorSearchFocus() {
        this.showTutorDropdown();
        this.filterTutors();
    }

    onTutorSearchInput() {
        this.filterTutors();
    }

    showTutorDropdown() {
        if (this.hasTutorDropdownTarget) {
            this.tutorDropdownTarget.style.display = 'block';
        }
    }

    filterTutors() {
        if (!this.hasTutorListTarget) return;

        const searchTerm = this.hasTutorSearchTarget ? this.tutorSearchTarget.value.toLowerCase() : '';
        const filtered = this.tutors.filter(tutor => {
            const fullName = `${tutor.firstName} ${tutor.lastName}`.toLowerCase();
            return fullName.includes(searchTerm);
        });

        this.tutorListTarget.innerHTML = filtered.length > 0
            ? filtered.map(tutor => `
                <button type="button"
                        data-action="click->book-cart#selectTutor"
                        data-book-cart-tutor-id-param="${tutor.id}"
                        data-book-cart-tutor-name-param="${this.escapeHtml(tutor.firstName)} ${this.escapeHtml(tutor.lastName)}"
                        style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: none; cursor: pointer; font-size: 13px; color: #374151;"
                        onmouseover="this.style.background='#f3f4f6'"
                        onmouseout="this.style.background='transparent'">
                    ${this.escapeHtml(tutor.firstName)} ${this.escapeHtml(tutor.lastName)}
                </button>
            `).join('')
            : '<p style="padding: 12px; text-align: center; color: #6b7280; font-size: 13px; margin: 0;">No se encontraron tutores</p>';
    }

    selectTutor(event) {
        this.selectedTutor = {
            id: event.params.tutorId,
            name: event.params.tutorName
        };
        this.saveTutorToStorage();
        this.updateTutorUI();

        if (this.hasTutorDropdownTarget) {
            this.tutorDropdownTarget.style.display = 'none';
        }
        if (this.hasTutorSearchTarget) {
            this.tutorSearchTarget.value = '';
        }
    }

    clearTutorSelection() {
        this.selectedTutor = null;
        this.saveTutorToStorage();
        this.updateTutorUI();
    }

    updateTutorUI() {
        if (this.hasSelectedTutorTarget) {
            if (this.selectedTutor) {
                this.selectedTutorTarget.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 16px; height: 16px; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span style="font-size: 13px; font-weight: 500; color: #166534;">${this.escapeHtml(this.selectedTutor.name)}</span>
                        </div>
                        <button type="button"
                                data-action="click->book-cart#clearTutorSelection"
                                style="background: none; border: none; cursor: pointer; padding: 2px; color: #16a34a;"
                                onmouseover="this.style.color='#dc2626'"
                                onmouseout="this.style.color='#16a34a'">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                this.selectedTutorTarget.style.display = 'block';
            } else {
                this.selectedTutorTarget.innerHTML = '';
                this.selectedTutorTarget.style.display = 'none';
            }
        }

        // Show/hide search input based on selection
        if (this.hasTutorSearchTarget) {
            this.tutorSearchTarget.closest('[data-tutor-search-container]').style.display =
                this.selectedTutor ? 'none' : 'block';
        }
    }

    addBook(event) {
        const bookData = {
            id: event.params.id,
            title: event.params.title,
            author: event.params.author,
            code: event.params.code
        };

        if (this.books.some(b => b.id === bookData.id)) {
            this.showAlreadyAdded(event.currentTarget);
            return;
        }

        this.books = [...this.books, bookData];
        this.saveToStorage();
        this.updateCartUI();
        this.showAddedFeedback(event.currentTarget);
    }

    removeBook(event) {
        const bookId = event.params.id;
        this.books = this.books.filter(b => b.id !== bookId);
        this.saveToStorage();
        this.updateCartUI();
    }

    clearCart() {
        this.books = [];
        this.selectedTutor = null;
        sessionStorage.removeItem(STORAGE_KEY);
        sessionStorage.removeItem(TUTOR_STORAGE_KEY);
        this.updateCartUI();
        this.updateTutorUI();
    }

    updateCartUI() {
        const count = this.books.length;

        if (this.hasCartTarget) {
            this.cartTarget.style.display = count > 0 ? 'flex' : 'none';
        }

        if (this.hasCartCountTarget) {
            this.cartCountTarget.textContent = count;
        }

        if (this.hasCartItemsTarget) {
            this.cartItemsTarget.innerHTML = this.books.map(book => `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f9fafb; border-radius: 8px;">
                    <div style="min-width: 0; flex: 1;">
                        <p style="font-size: 13px; font-weight: 500; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${this.escapeHtml(book.title)}</p>
                        <p style="font-size: 11px; color: #6b7280; margin: 2px 0 0 0;">${this.escapeHtml(book.author)}</p>
                    </div>
                    <button type="button"
                            data-action="click->book-cart#removeBook"
                            data-book-cart-id-param="${book.id}"
                            style="background: none; border: none; cursor: pointer; padding: 4px; color: #9ca3af; flex-shrink: 0;"
                            onmouseover="this.style.color='#ef4444'"
                            onmouseout="this.style.color='#9ca3af'">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `).join('');
        }

        if (this.hasCreateBtnTarget) {
            this.createBtnTarget.disabled = count === 0;
            this.createBtnTarget.style.opacity = count === 0 ? '0.5' : '1';
            this.createBtnTarget.style.cursor = count === 0 ? 'not-allowed' : 'pointer';
        }
    }

    openLoanModal() {
        if (this.books.length === 0) return;

        // Populate books list in modal
        if (this.hasBooksListTarget) {
            this.booksListTarget.innerHTML = this.books.map(book => `
                <input type="hidden" name="books[]" value="${book.id}">
                <div style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px;">
                    <span style="color: #111827; font-weight: 500;">${this.escapeHtml(book.title)}</span>
                    <span style="color: #6b7280;">- ${this.escapeHtml(book.code)}</span>
                </div>
            `).join('');
        }

        const modal = document.querySelector('#createLoanModal');
        if (modal) {
            // Pre-select tutor if one is selected in cart
            if (this.selectedTutor) {
                const tutorSelect = modal.querySelector('[data-loan-form-target="tutor"]');
                if (tutorSelect) {
                    tutorSelect.value = this.selectedTutor.id;
                    // Trigger change event to filter students
                    tutorSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    showAddedFeedback(element) {
        element.style.outline = '2px solid #9333ea';
        element.style.outlineOffset = '2px';
        setTimeout(() => {
            element.style.outline = 'none';
        }, 500);
    }

    showAlreadyAdded(element) {
        element.style.outline = '2px solid #f59e0b';
        element.style.outlineOffset = '2px';
        setTimeout(() => {
            element.style.outline = 'none';
        }, 500);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
