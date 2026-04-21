/**
 * Tailwind CSS configuration — Snazzy Sprocket design system.
 *
 * Tokens mirror the Figma "Color Palette", "Typography", "Spacing Scale",
 * "Components", and "Layout" specs so designers and engineers speak the
 * same language. Names in comments below match Figma labels 1:1.
 *
 * @type {import('tailwindcss').Config}
 */
const plugin = require('tailwindcss/plugin');
const defaultTheme = require('tailwindcss/defaultTheme');

// Full 50–950 scale derived from the Figma "Primary dark palette".
// Exposed under `ink-*` so numeric shades (e.g. `bg-ink-800`) are
// available alongside the semantic aliases (`bg-ink-light`, `text-slate`).
const inkScale = {
  50:  '#F4F6FB', // Paper
  100: '#E8ECF4', // Fog
  200: '#C8CEE6', // Cloud
  300: '#9BA3C2', // Soft
  400: '#6B7394', // Muted
  500: '#4A5278', // Steel
  600: '#2A3150', // Slate
  700: '#1C2235', // Ink Mid
  800: '#141927', // Ink Light
  900: '#0B0F1A', // Ink
  950: '#050811', // Deeper than Ink for extreme surfaces
};

module.exports = {
  content: [
    './views/**/*.twig',
    './*.php',
    './src/**/*.php',
    './src/js/**/*.js',
  ],

  theme: {
    // 1200px max-width container with 24px gutters (Figma "Layout").
    container: {
      center: true,
      padding: {
        DEFAULT: '24px',
        lg: '24px',
      },
      screens: {
        sm: '100%',
        md: '100%',
        lg: '100%',
        xl: '1200px',
        '2xl': '1200px',
      },
    },

    extend: {
      colors: {
        // ---- Figma "Primary dark palette" ---------------------------
        ink: {
          DEFAULT: '#0B0F1A', // Ink
          light:   '#141927', // Ink Light
          mid:     '#1C2235', // Ink Mid
          ...inkScale,
        },
        slate: '#2A3150',
        steel: '#4A5278',
        muted: '#6B7394',
        soft:  '#9BA3C2',
        cloud: '#C8CEE6',
        fog:   '#E8ECF4',
        paper: '#F4F6FB',

        // ---- Figma "Accent Usage" -----------------------------------
        accent: {
          DEFAULT: '#00D4AA', // Accent
          bright:  '#00FFCC', // Accent Bright
          dim:     '#009B7D', // Accent Dim
          glow:    'rgba(0, 212, 170, 0.15)', // Accent Glow (15% alpha)
        },
        brand: {
          teal: '#0891B2', // Brand Teal
          navy: '#0F2140', // Brand Navy
        },
      },

      fontFamily: {
        // Syne — display/headings, hero text, stat numbers, logo.
        // Weights 700 (bold), 800 (extra-bold).
        display: ['Syne', ...defaultTheme.fontFamily.sans],

        // DM Sans — body copy, nav, labels, inputs, metadata.
        // Weights 400 / 500 / 600 / 700.
        body: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
        sans: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
      },

      // Figma "Type Scale". Keys match semantic roles, not t-shirt sizes,
      // so intent travels from design → markup → CSS.
      fontSize: {
        label: [
          '0.6875rem', // 11px
          { lineHeight: '1.4', letterSpacing: '0.08em', fontWeight: '600' },
        ],
        small: ['0.875rem', { lineHeight: '1.5' }],           // 14px
        body:  ['1rem',     { lineHeight: '1.6' }],           // 16px
        h3:    [
          '1.25rem', // 20px
          { lineHeight: '1.35', letterSpacing: '-0.005em', fontWeight: '700' },
        ],
        h2: [
          '2.75rem', // 44px
          { lineHeight: '1.05', letterSpacing: '-0.02em', fontWeight: '800' },
        ],
        h1: [
          '3.5rem', // 56px
          { lineHeight: '1.02', letterSpacing: '-0.02em', fontWeight: '800' },
        ],
      },

      // Figma "Spacing Scale". Co-exists with Tailwind's default 0.5-96 scale.
      spacing: {
        xs:  '4px',
        sm:  '8px',
        md:  '16px',
        lg:  '24px',
        xl:  '40px',
        '2xl': '64px',
        '3xl': '100px',
        '4xl': '140px',

        // Semantic section rhythm for page blocks.
        section:    '100px',
        'section-sm': '64px',
      },

      maxWidth: {
        content: '1200px', // Figma "Max width: 1200px"
      },

      boxShadow: {
        card:    '0 1px 3px rgba(11, 15, 26, 0.06), 0 1px 2px rgba(11, 15, 26, 0.04)',
        'card-lg': '0 10px 30px rgba(11, 15, 26, 0.08)',
        'glow-accent': '0 0 40px rgba(0, 212, 170, 0.35)',
        'inset-border': 'inset 0 0 0 1px rgba(11, 15, 26, 0.08)',
      },

      // Dot grid and radial accent overlay — referenced in base.twig / CTAs.
      backgroundImage: {
        'dot-grid-light':
          'radial-gradient(rgba(255,255,255,0.08) 1.5px, transparent 1.5px)',
        'dot-grid-dark':
          'radial-gradient(rgba(11,15,26,0.12) 1.5px, transparent 1.5px)',
        'accent-halo':
          'radial-gradient(circle at center, rgba(0,212,170,0.25), transparent 70%)',
      },
      backgroundSize: {
        dot: '24px 24px', // Figma "Dot grid size: 24px × 24px"
      },

      animation: {
        'fade-in':       'fadeIn 0.6s ease-out forwards',
        'slide-up':      'slideUp 0.6s ease-out forwards',
        'slide-in-left': 'slideInLeft 0.5s ease-out forwards',
      },
      keyframes: {
        fadeIn: {
          '0%':   { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%':   { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideInLeft: {
          '0%':   { opacity: '0', transform: 'translateX(-20px)' },
          '100%': { opacity: '1', transform: 'translateX(0)' },
        },
      },
    },
  },

  plugins: [
    // Mechanical notch motif — Figma "Mechanical Notch Motif".
    // Produces utility classes `.notch`, `.notch-lg`, `.notch-md`,
    // `.notch-sm`, `.notch-xs` that bevel top-left + bottom-right corners.
    plugin(function ({ addUtilities }) {
      const notch = (size) => ({
        clipPath: `polygon(${size} 0, 100% 0, 100% calc(100% - ${size}), calc(100% - ${size}) 100%, 0 100%, 0 ${size})`,
      });
      addUtilities({
        '.notch':     notch('28px'),
        '.notch-lg':  notch('28px'),
        '.notch-md':  notch('16px'),
        '.notch-sm':  notch('12px'),
        '.notch-xs':  notch('8px'),
      });
    }),
  ],
};
