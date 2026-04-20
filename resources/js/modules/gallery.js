import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';

export function initGallery(root = document) {
  root.querySelectorAll('[data-gallery]').forEach((el) => {
    const id = el.dataset.gallery;
    const sel = (role) =>
      id ? `[data-gallery-${role}="${id}"]` : `[data-gallery-${role}]`;

    new Swiper(el.querySelector('.swiper'), {
      modules: [Navigation],
      slidesPerView: 1,
      spaceBetween: 0,
      navigation: {
        prevEl: root.querySelector(sel('prev')),
        nextEl: root.querySelector(sel('next')),
      },
    });
  });
}
