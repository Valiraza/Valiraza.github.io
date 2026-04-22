import { createApp } from 'vue';
import DetailCircuitPage from '../components/DetailCircuitPage.vue';

const mountElement = document.getElementById('detail-circuit-app');

if (mountElement) {
  const initialCircuitScript = document.getElementById('detail-circuit-data');
  let initialCircuit = null;

  if (initialCircuitScript?.textContent) {
    try {
      initialCircuit = JSON.parse(initialCircuitScript.textContent);
    } catch (error) {
      console.error('Impossible de parser le circuit initial.', error);
    }
  }

  createApp(DetailCircuitPage, {
    initialCircuit,
  }).mount(mountElement);
}
