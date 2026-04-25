export function initAnimatedLogo(root = document) {
  root.querySelectorAll('[data-animated-logo]').forEach((el) => {
    requestAnimationFrame(() => el.classList.add('is-ready'));
  });
}
