// إدارة الوضع الليلي / النهاري عبر كامل المنصة
(function () {
  const STORAGE_KEY = "manara-theme";

  function applyTheme(theme) {
    document.documentElement.classList.toggle("dark", theme === "dark");
  }

  const saved = localStorage.getItem(STORAGE_KEY);
  const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
  applyTheme(saved || (prefersDark ? "dark" : "light"));

  window.ManaraTheme = {
    toggle() {
      const isDark = document.documentElement.classList.toggle("dark");
      localStorage.setItem(STORAGE_KEY, isDark ? "dark" : "light");
      return isDark;
    },
    init(buttonEl) {
      if (!buttonEl) return;
      buttonEl.addEventListener("click", () => this.toggle());
    },
  };
})();
