import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse';
import { initGallery } from './modules/gallery';
import { initAnimatedLogo } from './modules/animated-logo';

Alpine.plugin(Collapse);
Alpine.start();

initGallery();
initAnimatedLogo();
