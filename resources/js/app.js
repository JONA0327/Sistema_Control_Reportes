import './bootstrap';

import Alpine from 'alpinejs';
import { audioReport } from './report-audio';

window.Alpine = Alpine;

Alpine.data('audioReport', audioReport);

Alpine.start();
