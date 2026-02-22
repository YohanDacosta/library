import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dropdown', 'checkbox', 'badge', 'button'];

    connect() {
        // Read selected categories from URL on page load
        this.selectedCategories = new Set();
        const urlParams = new URLSearchParams(window.location.search);
        const categoriesFromUrl = urlParams.getAll('categories[]');
        categoriesFromUrl.forEach(id => this.selectedCategories.add(id));

        // Mark checkboxes as checked based on URL params
        this.checkboxTargets.forEach(checkbox => {
            if (this.selectedCategories.has(checkbox.value)) {
                checkbox.checked = true;
            }
        });

        // Update badge count
        this.updateBadge();

        // Close dropdown when clicking outside
        this.handleClickOutside = this.handleClickOutside.bind(this);
        document.addEventListener('click', this.handleClickOutside);
    }

    disconnect() {
        document.removeEventListener('click', this.handleClickOutside);
    }

    toggleDropdown(event) {
        event.stopPropagation();
        const isOpen = this.dropdownTarget.style.display !== 'none';
        this.dropdownTarget.style.display = isOpen ? 'none' : 'block';
    }

    selectCategory(event) {
        const checkbox = event.target;
        if (checkbox.checked) {
            this.selectedCategories.add(checkbox.value);
        } else {
            this.selectedCategories.delete(checkbox.value);
        }
        this.updateBadge();
    }

    applyFilter() {
        const url = new URL(window.location.href);

        // Remove existing category params
        url.searchParams.delete('categories[]');

        // Add selected categories
        this.selectedCategories.forEach(id => {
            url.searchParams.append('categories[]', id);
        });

        // Reset to page 1 when filtering
        url.searchParams.delete('page');

        // Navigate to the new URL
        window.location.href = url.toString();
    }

    clearFilter() {
        // Uncheck all checkboxes
        this.checkboxTargets.forEach(checkbox => {
            checkbox.checked = false;
        });
        this.selectedCategories.clear();
        this.updateBadge();
    }

    updateBadge() {
        const count = this.selectedCategories.size;
        if (this.hasBadgeTarget) {
            this.badgeTarget.textContent = count;
            this.badgeTarget.style.display = count > 0 ? 'inline-flex' : 'none';
        }
    }

    handleClickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.dropdownTarget.style.display = 'none';
        }
    }
}
