import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';
import * as GoogleMaps from './google-maps';
import * as PropertyManagement from './property-management';

// Import Google Maps Services package
// import { Client } from '@googlemaps/google-maps-services-js';

// Đặt Chart.js thành một biến toàn cục để có thể sử dụng trong các file blade
window.Chart = Chart;

// Make Bootstrap available globally
window.bootstrap = bootstrap;

// Make Google Maps functions available globally
window.GoogleMaps = GoogleMaps;

// Make Property Management functions available globally
window.PropertyManagement = PropertyManagement;

