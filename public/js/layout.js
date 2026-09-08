// ==========================================================================
// منارة — بناء هيكل لوحة التحكم (الشريط الجانبي) بحسب الدور
// ==========================================================================

const NAV_CONFIG = {
  admin: [
    { key: "overview", label: "الرئيسية", icon: "🏠" },
    { key: "students", label: "الطلاب", icon: "🎓" },
    { key: "teachers", label: "المعلمون", icon: "📚" },
    { key: "management", label: "فريق الإدارة", icon: "🧭" },
    { key: "announcements", label: "الإعلانات", icon: "📣" },
  ],
  management: [
    { key: "overview", label: "الرئيسية", icon: "🏠" },
    { key: "students", label: "الطلاب", icon: "🎓" },
    { key: "teachers", label: "المعلمون", icon: "📚" },
    { key: "announcements", label: "الإعلانات", icon: "📣" },
  ],
  teacher: [
    { key: "overview", label: "الرئيسية", icon: "🏠" },
    { key: "lectures", label: "محاضراتي", icon: "🎥" },
    { key: "exams", label: "اختباراتي", icon: "📝" },
    { key: "students", label: "طلابي", icon: "🎓" },
  ],
  student: [
    { key: "overview", label: "الرئيسية", icon: "🏠" },
    { key: "lectures", label: "المحاضرات", icon: "🎥" },
    { key: "exams", label: "الاختبارات", icon: "📝" },
    { key: "announcements", label: "الإعلانات", icon: "📣" },
  ],
};

function initials(name) {
  return (name || "؟").trim().split(" ").slice(0, 2).map((w) => w[0]).join("");
}

function buildLayout(role, activeKey) {
  const user = UI.guard([role]);
  if (!user) return null;

  const items = NAV_CONFIG[role];
  const sidebar = document.getElementById("sidebar");

  sidebar.innerHTML = `
    <div>
      <div class="sidebar-brand"><span class="beacon-dot"></span> منارة</div>
      <div class="sidebar-role" style="margin-top:0.8rem;">${Manara.roleLabel(role)}</div>
    </div>
    <div class="sidebar-user card" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); padding:0.9rem; border-radius: var(--radius-m); display:flex; align-items:center; gap:0.7rem;">
      <div class="avatar" style="background:rgba(200,150,58,0.18); color:#E0AE54;">${UI.escapeHTML(initials(user.name))}</div>
      <div>
        <strong style="display:block; color:#fff; font-size:0.95rem;">${UI.escapeHTML(user.name)}</strong>
        <span style="font-size:0.78rem;">${Manara.roleLabel(role)}</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      ${items.map((it) => `
        <a href="#${it.key}" data-key="${it.key}" class="${it.key === activeKey ? "active" : ""}">
          <span class="icon">${it.icon}</span> ${it.label}
        </a>`).join("")}
    </nav>
    <div class="sidebar-foot">
      <a href="${UI.homePath()}" style="display:flex; align-items:center; gap:0.7rem; padding:0.7rem 0.9rem; color:#CBD3CD; font-weight:600; font-size:0.92rem;">
        <span class="icon">🌐</span> الموقع العام
      </a>
      <button id="logoutBtn"><span class="icon">🚪</span> تسجيل الخروج</button>
      <button class="theme-toggle" id="themeToggle" style="align-self:flex-start;" aria-label="تبديل الوضع الليلي"><span class="knob">🌙</span></button>
    </div>
  `;

  document.getElementById("logoutBtn").addEventListener("click", () => UI.logout());
  ManaraTheme.init(document.getElementById("themeToggle"));

  const mobileTitle = document.getElementById("mobileTitle");
  if (mobileTitle) mobileTitle.textContent = "منارة — " + Manara.roleLabel(role);

  const menuBtn = document.getElementById("menuToggle");
  if (menuBtn) menuBtn.addEventListener("click", () => document.body.classList.toggle("sidebar-open"));
  const scrim = document.getElementById("sidebarScrim");
  if (scrim) scrim.addEventListener("click", () => document.body.classList.remove("sidebar-open"));

  return user;
}

function initSections(defaultKey) {
  const sections = document.querySelectorAll("[data-section]");
  const navLinks = document.querySelectorAll(".sidebar-nav a[data-key]");

  function show(key) {
    let found = false;
    sections.forEach((s) => {
      const match = s.dataset.section === key;
      s.hidden = !match;
      if (match) found = true;
    });
    if (!found && sections.length) sections[0].hidden = false;
    navLinks.forEach((a) => a.classList.toggle("active", a.dataset.key === key));
    document.body.classList.remove("sidebar-open");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  const initial = (location.hash || "#" + defaultKey).slice(1);
  show(initial);

  window.addEventListener("hashchange", () => {
    show((location.hash || "#" + defaultKey).slice(1));
  });
}

window.buildLayout = buildLayout;
window.initSections = initSections;
