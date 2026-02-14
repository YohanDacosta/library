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
}
