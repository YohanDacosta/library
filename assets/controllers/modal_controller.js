import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    open(event) {
        const targetSelector = event.params.target || event.currentTarget.dataset.modalTargetParam;
        const modal = document.querySelector(targetSelector);
        if (modal) {
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';

            // Close on overlay click
            modal.addEventListener('click', this.handleOverlayClick);

            // Close on Escape
            document.addEventListener('keydown', this.handleEscape);
        }
    }

    close() {
        const modal = document.querySelector('.modal-overlay.open');
        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
            modal.removeEventListener('click', this.handleOverlayClick);
            document.removeEventListener('keydown', this.handleEscape);
        }
    }

    stopPropagation(event) {
        event.stopPropagation();
    }

    handleOverlayClick = (event) => {
        if (event.target.classList.contains('modal-overlay')) {
            this.close();
        }
    }

    handleEscape = (event) => {
        if (event.key === 'Escape') {
            this.close();
        }
    }
}
