export function initShy(root = document) {
  const mq = window.matchMedia('(min-width: 768px)');
  const scroller = root.querySelector('main') || window;
  const getY = () => scroller === window ? window.scrollY : scroller.scrollTop;

  root.querySelectorAll('[data-shy]').forEach((el) => {
    let lastY = getY();
    let ticking = false;

    const getThreshold = () => el.offsetHeight;

    const update = () => {
      const y = getY();
      const delta = y - lastY;

      if (mq.matches) {
        el.classList.remove('is-hidden');
        lastY = y;
        ticking = false;
        return;
      }

      if (Math.abs(delta) > getThreshold()) {
        if (delta > 0 && y > 0) {
          el.classList.add('is-hidden');
        } else if (delta < 0) {
          el.classList.remove('is-hidden');
        }
        lastY = y;
      }

      ticking = false;
    };

    const onScroll = () => {
      if (!ticking) {
        requestAnimationFrame(update);
        ticking = true;
      }
    };

    scroller.addEventListener('scroll', onScroll, { passive: true });
    mq.addEventListener('change', () => {
      if (mq.matches) el.classList.remove('is-hidden');
      lastY = getY();
    });
  });
}
