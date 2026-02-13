import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['tutor', 'student', 'book', 'loanDate', 'returnDate'];
    static values = {
        studentsUrl: String
    }

    connect() {
        // Set default loan date to today
        if (this.hasLoanDateTarget) {
            this.loanDateTarget.value = new Date().toISOString().split('T')[0];
        }
    }

    filterStudents() {
        const tutorId = this.tutorTarget.value;
        const studentSelect = this.studentTarget;

        if (!tutorId) {
            studentSelect.innerHTML = '<option value="">Primero seleccione un tutor...</option>';
            studentSelect.disabled = true;
            return;
        }

        // Enable student select
        studentSelect.disabled = false;
        studentSelect.innerHTML = '<option value="">Cargando estudiantes...</option>';

        // Get students data from data attribute (populated by backend)
        const studentsData = this.element.dataset.students;

        if (studentsData) {
            const students = JSON.parse(studentsData);
            const filtered = students.filter(s => s.tutorId === tutorId);

            studentSelect.innerHTML = '<option value="">Seleccionar estudiante...</option>';
            filtered.forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = student.name;
                studentSelect.appendChild(option);
            });
        }
    }
}
