export function initAnimatedLogo(root = document) {
  root.querySelectorAll('[data-logo]').forEach((el) => {
    requestAnimationFrame(() => el.classList.add('is-ready'));
  });
}
