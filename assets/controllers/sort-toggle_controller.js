import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'label'];
    static values = {
        param: {type: String, default: 'sort'},
        active: {type: String, default: 'oldest'}
    };

    connect() {
        // Check if sort is active from URL
        const urlParams = new URLSearchParams(window.location.search);
        this.isActive = urlParams.get(this.paramValue) === this.activeValue;
        this.updateButtonStyle();
        this.updateLabel();
    }

    toggle() {
        const url = new URL(window.location.href);

        if (this.isActive) {
            // Remove sort param (back to default: recent)
            url.searchParams.delete(this.paramValue);
        } else {
            // Add sort param (oldest first)
            url.searchParams.set(this.paramValue, this.activeValue);
        }

        // Reset to page 1 when changing sort
        url.searchParams.delete('page');

        // Navigate to the new URL
        window.location.href = url.toString();
    }

    updateButtonStyle() {
        if (!this.hasButtonTarget) return;

        if (this.isActive) {
            // Active state - purple background (showing oldest)
            this.buttonTarget.style.background = '#9333ea';
            this.buttonTarget.style.borderColor = '#9333ea';
            this.buttonTarget.style.color = 'white';
        } else {
            // Inactive state - white background (showing recent)
            this.buttonTarget.style.background = 'white';
            this.buttonTarget.style.borderColor = '#e5e7eb';
            this.buttonTarget.style.color = '#374151';
        }
    }

    updateLabel() {
        if (!this.hasLabelTarget) return;

        // Update label to show current state
        this.labelTarget.textContent = this.isActive ? 'Mais antigos' : 'Mais recentes';
    }
}
