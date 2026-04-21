import Alpine from 'alpinejs';

// =============================================================
// Alpine.js — lightweight reactivity for the case-study filters
// and anchor smooth-scrolling. The mobile-nav toggle lives inline
// as `x-data` in views/partials/header.twig (scoped to the header
// wrapper so the overlay and the nav share the same `open` state).
// =============================================================
window.Alpine = Alpine;

// ---------- Case Study Filters ----------
Alpine.data('caseStudyFilters', () => ({
  activeIndustry: 'all',
  activeTechnology: 'all',

  setIndustry(slug) {
    this.activeIndustry = slug;
  },
  setTechnology(slug) {
    this.activeTechnology = slug;
  },

  isVisible(industries, technologies) {
    const industryMatch =
      this.activeIndustry === 'all' ||
      industries.includes(this.activeIndustry);

    const technologyMatch =
      this.activeTechnology === 'all' ||
      technologies.includes(this.activeTechnology);

    return industryMatch && technologyMatch;
  },

  // Used by the archive empty-state to hide the "no results"
  // block unless every card is filtered out. Reads the DOM
  // instead of tracking state so it stays in sync with the
  // rendered `x-show` result.
  visibleCount() {
    return document.querySelectorAll(
      '[x-show*="isVisible"]:not([style*="display: none"])'
    ).length;
  },
}));

// ---------- Smooth scroll to anchors ----------
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', (e) => {
    e.preventDefault();
    const target = document.querySelector(anchor.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

Alpine.start();
