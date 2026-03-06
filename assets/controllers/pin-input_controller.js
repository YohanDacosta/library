import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'submit'];
    static values = {
        length: { type: Number, default: 4 }
    };

    connect() {
        // Auto-focus input on page load
        if (this.hasInputTarget) {
            this.inputTarget.focus();
        }
    }

    onInput(event) {
        const input = event.target;

        // Remove any non-numeric characters
        input.value = input.value.replace(/[^0-9]/g, '');

        // Auto-submit when PIN length is reached
        if (input.value.length === this.lengthValue) {
            this.element.requestSubmit();
        }
    }

    onKeydown(event) {
        // Allow: backspace, delete, tab, escape, enter
        if ([8, 46, 9, 27, 13].includes(event.keyCode)) {
            return;
        }

        // Allow: Ctrl/Cmd + A, C, V, X
        if ((event.ctrlKey || event.metaKey) && [65, 67, 86, 88].includes(event.keyCode)) {
            return;
        }

        // Allow: home, end, left, right
        if (event.keyCode >= 35 && event.keyCode <= 39) {
            return;
        }

        // Block if not a number
        if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) &&
            (event.keyCode < 96 || event.keyCode > 105)) {
            event.preventDefault();
        }
    }
}
