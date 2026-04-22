import { createApp } from 'vue';
import CircuitCatalog from './js/CircuitCatalog.vue';

const mountEl = document.getElementById('app');
const pageDataEl = document.getElementById('circuit-catalog-data');
const pageData = pageDataEl?.textContent ? JSON.parse(pageDataEl.textContent) : {};

createApp(CircuitCatalog, {
    catalog: pageData.catalog ?? {},
    catalogSlug: pageData.catalogSlug ?? '',
    catalogTypes: pageData.catalogTypes ?? {},
    circuits: pageData.circuits ?? [],
    assetBase: pageData.assetBase ?? '/',
}).mount(mountEl);
