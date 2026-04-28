/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,ts,tsx}'],
  theme: {
    extend: {
      colors: {
        ink: {
          DEFAULT: '#0D1B2A',
          2: '#1A2E42',
          3: '#253D56',
        },
        emerald: {
          DEFAULT: '#059669',
          2: '#047857',
          3: '#D1FAE5',
          4: '#ECFDF5',
        },
        brand: {
          sapphire: '#1D4ED8',
          'sapphire-l': '#EFF6FF',
          coral: '#DC2626',
          'coral-l': '#FEF2F2',
          amber: '#D97706',
          'amber-l': '#FFFBEB',
        },
      },
      fontFamily: {
        serif: ['"Instrument Serif"', 'Georgia', 'serif'],
        sans:  ['"DM Sans"', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        xs: '6px',
        sm: '10px',
        DEFAULT: '14px',
        lg: '20px',
        xl: '28px',
      },
      boxShadow: {
        sm: '0 1px 3px rgba(13,27,42,.06), 0 1px 2px rgba(13,27,42,.04)',
        DEFAULT: '0 4px 16px rgba(13,27,42,.08), 0 1px 4px rgba(13,27,42,.04)',
        lg: '0 12px 40px rgba(13,27,42,.12), 0 4px 12px rgba(13,27,42,.06)',
      },
    },
  },
  plugins: [],
}
