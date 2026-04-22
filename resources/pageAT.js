import { createApp } from 'vue';
import PageAT from './js/PageAT.vue';

const mountEl = document.getElementById('app');
const pageDataEl = document.getElementById('pageat-data');
const pageData = pageDataEl?.textContent ? JSON.parse(pageDataEl.textContent) : {};

createApp(PageAT, {
    circuits: pageData.circuits ?? [],
    assetBase: pageData.assetBase ?? '/',
}).mount('#app');
