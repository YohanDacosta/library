import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['result', 'filter'];
    static values = {
        url: String,
    }

    initialize() {
        this.timeout = null;
    }

    handleSearch(ev) {
        const query = ev.currentTarget.value.trim();

        clearTimeout(this.timeout);

        if (query.length < 2) {
            this.hideResults();
            return;
        }

        // Mostrar loading inmediatamente
        this.showLoading();

        this.timeout = setTimeout(() => {
            this.performSearch(query);
        }, 300);
    }

    showLoading() {
        this.resultTarget.innerHTML = `
            <div style="padding: 24px 32px; display: flex; align-items: center; gap: 12px;">
                <svg style="width: 20px; height: 20px; color: #9333ea; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span style="font-size: 14px; color: #6b7280;">Buscando estudiantes...</span>
            </div>
            <style>
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            </style>
        `;
        this.showResults();
    }

    async performSearch(query) {
        try {
            const params = new URLSearchParams({
                q: query,
                preview: 1,
            });

            const response = await fetch(`${this.urlValue}?${params.toString()}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            this.resultTarget.innerHTML = await response.text();
            this.showResults();

        } catch (error) {
            console.error('Error fetching search results:', error);
            this.resultTarget.innerHTML = `
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Error al cargar resultados</p>
                    <p class="text-xs text-gray-500 mt-1">Por favor, intenta nuevamente</p>
                </div>
            `;
            this.showResults();
        }
    }

    showResults() {
        this.resultTarget.classList.remove('hidden');
    }

    hideResults() {
        this.resultTarget.classList.add('hidden');
        this.resultTarget.innerHTML = '';
    }

    connect() {
        this.boundHideOnClickOutside = this.hideOnClickOutside.bind(this);
        document.addEventListener('click', this.boundHideOnClickOutside);
    }

    disconnect() {
        clearTimeout(this.timeout);
        document.removeEventListener('click', this.boundHideOnClickOutside);
    }

    hideOnClickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.hideResults();
        }
    }
}
