import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.liveSearch = function () {
    return {
        query: '',
        results: [],
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }
            try {
                const response = await fetch(`/libros/buscar?q=${encodeURIComponent(this.query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                this.results = data.results.slice(0, 8);
            } catch (e) {
                this.results = [];
            }
        }
    };
};