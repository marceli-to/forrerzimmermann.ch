import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse';
import { initGallery } from './modules/gallery';

Alpine.plugin(Collapse);
Alpine.start();

initGallery();
