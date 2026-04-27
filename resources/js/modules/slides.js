import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';

export function initSlides(root = document) {
  root.querySelectorAll('[data-slides]').forEach((el) => {
    const id = el.dataset.slides;
    const sel = (role) => id ? `[data-slides-${role}="${id}"]` : `[data-slides-${role}]`;

    new Swiper(el.querySelector('.swiper'), {
      modules: [Navigation],
      slidesPerView: 1,
      loop: true,
      spaceBetween: 0,
      navigation: {
        prevEl: root.querySelector(sel('prev')),
        nextEl: root.querySelector(sel('next')),
      },
    });
  });
}
