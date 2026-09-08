// ==========================================================================
// منارة — أدوات واجهة مشتركة
// ==========================================================================

const UI = {
  toast(message, type = "success") {
    let stack = document.querySelector(".toast-stack");
    if (!stack) {
      stack = document.createElement("div");
      stack.className = "toast-stack";
      document.body.appendChild(stack);
    }
    const el = document.createElement("div");
    el.className = "toast" + (type === "error" ? " error" : "");
    el.innerHTML = `<span>${type === "error" ? "⚠️" : "✓"}</span><span>${message}</span>`;
    stack.appendChild(el);
    setTimeout(() => {
      el.style.opacity = "0";
      el.style.transform = "translateY(8px)";
      el.style.transition = "all 220ms ease";
      setTimeout(() => el.remove(), 240);
    }, 2600);
  },

  openModal(id) {
    document.getElementById(id).classList.add("open");
  },
  closeModal(id) {
    document.getElementById(id).classList.remove("open");
  },

  confirmAction(message, onConfirm) {
    const wrap = document.createElement("div");
    wrap.className = "modal-backdrop";
    wrap.innerHTML = `
      <div class="modal" style="max-width:380px;">
        <h3>تأكيد الإجراء</h3>
        <p style="color:var(--ink-soft); margin-bottom: 1.4rem;">${message}</p>
        <div class="modal-actions">
          <button class="btn btn-danger" id="__confirm-yes">تأكيد الحذف</button>
          <button class="btn btn-ghost" id="__confirm-no">إلغاء</button>
        </div>
      </div>`;
    document.body.appendChild(wrap);
    requestAnimationFrame(() => wrap.classList.add("open"));
    const remove = () => { wrap.classList.remove("open"); setTimeout(() => wrap.remove(), 300); };
    wrap.querySelector("#__confirm-yes").addEventListener("click", () => { onConfirm(); remove(); });
    wrap.querySelector("#__confirm-no").addEventListener("click", remove);
    wrap.addEventListener("click", (e) => { if (e.target === wrap) remove(); });
  },

  guard(allowedRoles) {
    const user = window.Manara.currentUser();
    if (!user || !allowedRoles.includes(user.role)) {
      window.location.href = this.loginPath();
      return null;
    }
    return user;
  },

  rootPath() {
    return document.body.dataset.root || "";
  },
  loginPath() {
    return document.body.dataset.login || "login.html";
  },
  homePath() {
    return document.body.dataset.home || "index.html";
  },

  logout() {
    window.Manara.logout();
    window.location.href = this.homePath();
  },

  // تفعيل حركة الظهور التدريجي عند التمرير
  observeReveal() {
    const els = document.querySelectorAll(".reveal");
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("in");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    els.forEach((el) => io.observe(el));
  },

  escapeHTML(str) {
    return String(str ?? "").replace(/[&<>"']/g, (m) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;",
    }[m]));
  },
};

window.UI = UI;
document.addEventListener("DOMContentLoaded", () => UI.observeReveal());
