tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        display: ['"Bricolage Grotesque"', "sans-serif"],
        sans: ['"DM Sans"', "sans-serif"],
        mono: ['"Space Mono"', "monospace"]
      },
      colors: {
        paper: "#f3ece0",
        ink: "#1a1210",
        umm: {
          50: "#fff1f2", 100: "#ffe0e3", 200: "#ffc7cd", 300: "#ff9ba5",
          400: "#f2616f", 500: "#e03447", 600: "#c8102e", 700: "#a50d26",
          800: "#850a1e", 900: "#6b0818", 950: "#3d030d"
        },
        gold: { 300: "#fcd34d", 500: "#f5b301", 600: "#d99a00" }
      }
    }
  }
};