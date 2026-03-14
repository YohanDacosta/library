import {Controller} from '@hotwired/stimulus';

const BOOK_CART_STORAGE_KEY = 'library_book_cart';
const TUTOR_STORAGE_KEY = 'library_selected_tutor';

export default class extends Controller {
    static targets = ['tutor', 'student', 'loanDate', 'returnDate', 'submitBtn', 'errorMessage', 'booksList'];
    static values = {
        studentsUrl: String,
        createUrl: String
    }

    connect() {
        // Set default loan date to today
        if (this.hasLoanDateTarget) {
            const today = new Date();
            this.loanDateTarget.value = this.formatDate(today);

            // Set return date to 2 weeks from today
            if (this.hasReturnDateTarget) {
                this.setReturnDateFromLoanDate(today);
            }
        }
    }

    // Called when loan date changes
    updateReturnDate() {
        if (this.hasLoanDateTarget && this.hasReturnDateTarget && this.loanDateTarget.value) {
            const loanDate = new Date(this.loanDateTarget.value);
            this.setReturnDateFromLoanDate(loanDate);
        }
    }

    setReturnDateFromLoanDate(loanDate) {
        const returnDate = new Date(loanDate);
        returnDate.setDate(returnDate.getDate() + 14); // Add 2 weeks
        this.returnDateTarget.value = this.formatDate(returnDate);
    }

    formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    async filterStudents() {
        const tutorId = this.tutorTarget.value;
        const studentSelect = this.studentTarget;

        if (!tutorId) {
            studentSelect.innerHTML = '<option value="">Primero seleccione un tutor...</option>';
            studentSelect.disabled = true;
            return;
        }

        // Show loading state
        studentSelect.disabled = true;
        studentSelect.innerHTML = '<option value="">Cargando estudiantes...</option>';

        console.log(tutorId);

        try {
            // Fetch students from the API
            const url = `${this.studentsUrlValue}/${tutorId}`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Error al cargar estudiantes');
            }

            const students = await response.json();

            // Enable and populate student select
            studentSelect.disabled = false;

            if (students.length > 0) {
                studentSelect.innerHTML = '<option value="">Seleccionar estudiante...</option>';
                students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = student.name;
                    studentSelect.appendChild(option);
                });
            } else {
                studentSelect.innerHTML = '<option value="">No hay estudiantes para este tutor</option>';
            }
        } catch (error) {
            console.error('Error fetching students:', error);
            studentSelect.innerHTML = '<option value="">Error al cargar estudiantes</option>';
            studentSelect.disabled = true;
        }
    }

    async submit(event) {
        event.preventDefault();
        this.clearErrors();

        // Get books from sessionStorage
        const books = this.getBooksFromStorage();

        // Validate
        const errors = this.validate(books);
        if (errors.length > 0) {
            this.showErrors(errors);
            return;
        }

        // Prepare data
        const data = {
            tutorId: this.tutorTarget.value,
            studentId: this.studentTarget.value,
            bookIds: books.map(b => b.id),
            loanDate: this.loanDateTarget.value,
            returnDate: this.returnDateTarget.value
        };

        // Show loading state
        this.setLoading(true);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const response = await fetch(this.createUrlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            console.log(result);

            if (!response.ok) {
                throw new Error(result.error || 'Error al crear el préstamo');
            }

            // Success - clear cart and close modal
            this.onSuccess(result);

        } catch (error) {
            console.error('Error creating loan:', error);
            this.showErrors([error.message]);
        } finally {
            this.setLoading(false);
        }
    }

    validate(books) {
        const errors = [];

        if (!this.tutorTarget.value) {
            errors.push('Debe seleccionar un tutor');
        }
        if (!this.studentTarget.value) {
            errors.push('Debe seleccionar un estudiante');
        }
        if (books.length === 0) {
            errors.push('Debe seleccionar al menos un libro');
        }
        if (!this.loanDateTarget.value) {
            errors.push('Debe ingresar la fecha de préstamo');
        }
        if (!this.returnDateTarget.value) {
            errors.push('Debe ingresar la fecha de devolución');
        }

        return errors;
    }

    getBooksFromStorage() {
        const stored = sessionStorage.getItem(BOOK_CART_STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }

    clearCartStorage() {
        sessionStorage.removeItem(BOOK_CART_STORAGE_KEY);
        sessionStorage.removeItem(TUTOR_STORAGE_KEY);
    }

    showErrors(errors) {
        if (this.hasErrorMessageTarget) {
            this.errorMessageTarget.innerHTML = errors.map(e =>
                `<p style="margin: 0 0 4px 0;">${this.escapeHtml(e)}</p>`
            ).join('');
            this.errorMessageTarget.style.display = 'block';
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    clearErrors() {
        if (this.hasErrorMessageTarget) {
            this.errorMessageTarget.innerHTML = '';
            this.errorMessageTarget.style.display = 'none';
        }
    }

    setLoading(isLoading) {
        if (this.hasSubmitBtnTarget) {
            this.submitBtnTarget.disabled = isLoading;
            this.submitBtnTarget.textContent = isLoading ? 'Creando...' : 'Crear Préstamo';
        }
    }

    onSuccess(result) {
        // Clear sessionStorage
        this.clearCartStorage();

        // Dispatch event for book-cart controller to update UI
        window.dispatchEvent(new CustomEvent('loan:created', { detail: result }));

        // Close modal
        const modal = this.element;
        modal.classList.remove('open');
        document.body.style.overflow = '';

        // Reset form
        this.resetForm();
    }

    resetForm() {
        if (this.hasTutorTarget) this.tutorTarget.value = '';
        if (this.hasStudentTarget) {
            this.studentTarget.innerHTML = '<option value="">Primero seleccione un tutor...</option>';
            this.studentTarget.disabled = true;
        }
        if (this.hasBooksListTarget) this.booksListTarget.innerHTML = '';

        // Reset dates to defaults
        const today = new Date();
        if (this.hasLoanDateTarget) this.loanDateTarget.value = this.formatDate(today);
        if (this.hasReturnDateTarget) this.setReturnDateFromLoanDate(today);
    }
}
