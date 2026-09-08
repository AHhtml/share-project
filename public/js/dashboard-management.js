// ==========================================================================
// منارة — لوحة الإدارة
// ==========================================================================

(function () {
  const user = buildLayout("management", "overview");
  if (!user) return;
  initSections("overview");

  function renderStats() {
    document.getElementById("statStudents").textContent = Manara.list("students").length;
    document.getElementById("statTeachers").textContent = Manara.list("teachers").length;
    document.getElementById("statLectures").textContent = Manara.db.lectures.length;
    document.getElementById("statExams").textContent = Manara.db.exams.length;
  }

  function renderOverviewAnnouncements() {
    const box = document.getElementById("overviewAnnouncements");
    const list = Manara.db.announcements.slice(0, 3);
    box.innerHTML = list.length ? list.map((a) => `
      <div style="padding:0.9rem 0; border-bottom:1px solid var(--paper-line);">
        <strong>${UI.escapeHTML(a.title)}</strong>
        <p style="color:var(--ink-soft); font-size:0.88rem; margin-top:0.3rem;">${UI.escapeHTML(a.body)}</p>
        <span style="font-size:0.76rem; color:var(--ink-soft);">${a.date} — ${UI.escapeHTML(a.author)}</span>
      </div>`).join("") : `<div class="empty-state"><div class="glyph">📣</div><p>لا توجد إعلانات بعد.</p></div>`;
  }

  // ---------- الطلاب ----------
  function renderStudents() {
    const rows = Manara.list("students");
    const tbody = document.getElementById("studentsTable");
    document.getElementById("studentsEmpty").hidden = rows.length !== 0;
    tbody.innerHTML = rows.map((s) => `
      <tr>
        <td><div class="avatar-name"><div class="avatar">${UI.escapeHTML(s.name.slice(0,1))}</div>${UI.escapeHTML(s.name)}</div></td>
        <td>${UI.escapeHTML(s.email)}</td>
        <td><span class="pill">${UI.escapeHTML(s.track || "—")}</span></td>
        <td>${s.joined}</td>
        <td class="row-actions">
          <button class="btn btn-outline btn-sm" data-edit="${s.id}">تعديل</button>
          <button class="btn btn-danger btn-sm" data-del="${s.id}">حذف</button>
        </td>
      </tr>`).join("");

    tbody.querySelectorAll("[data-edit]").forEach((b) => b.addEventListener("click", () => openStudentModal(b.dataset.edit)));
    tbody.querySelectorAll("[data-del]").forEach((b) => b.addEventListener("click", () => {
      UI.confirmAction("سيتم حذف بيانات هذا الطالب نهائياً. هل تريد المتابعة؟", () => {
        Manara.remove("students", b.dataset.del);
        renderStudents(); renderStats();
        UI.toast("تم حذف الطالب.");
      });
    }));
  }

  function openStudentModal(id) {
    const form = document.getElementById("studentForm");
    form.reset();
    if (id) {
      const s = Manara.list("students").find((x) => x.id === id);
      document.getElementById("studentModalTitle").textContent = "تعديل بيانات طالب";
      document.getElementById("studentId").value = s.id;
      document.getElementById("studentName").value = s.name;
      document.getElementById("studentEmail").value = s.email;
      document.getElementById("studentPassword").value = s.password || "";
      document.getElementById("studentPhone").value = s.phone || "";
      document.getElementById("studentTrack").value = s.track || "تطوير الويب";
    } else {
      document.getElementById("studentModalTitle").textContent = "إضافة طالب";
      document.getElementById("studentId").value = "";
    }
    UI.openModal("studentModal");
  }

  document.getElementById("addStudentBtn").addEventListener("click", () => openStudentModal(null));
  document.getElementById("studentForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const id = document.getElementById("studentId").value;
    const payload = {
      name: document.getElementById("studentName").value.trim(),
      email: document.getElementById("studentEmail").value.trim(),
      password: document.getElementById("studentPassword").value.trim(),
      phone: document.getElementById("studentPhone").value.trim(),
      track: document.getElementById("studentTrack").value,
    };
    if (id) { Manara.update("students", id, payload); UI.toast("تم تحديث بيانات الطالب."); }
    else { Manara.add("students", payload); UI.toast("تمت إضافة الطالب بنجاح."); }
    UI.closeModal("studentModal");
    renderStudents(); renderStats();
  });

  // ---------- المعلمون ----------
  function renderTeachers() {
    const rows = Manara.list("teachers");
    const tbody = document.getElementById("teachersTable");
    document.getElementById("teachersEmpty").hidden = rows.length !== 0;
    tbody.innerHTML = rows.map((t) => `
      <tr>
        <td><div class="avatar-name"><div class="avatar">${UI.escapeHTML(t.name.slice(0,1))}</div>${UI.escapeHTML(t.name)}</div></td>
        <td>${UI.escapeHTML(t.email)}</td>
        <td><span class="pill">${UI.escapeHTML(t.subject || "—")}</span></td>
        <td>${t.joined}</td>
        <td class="row-actions">
          <button class="btn btn-outline btn-sm" data-edit="${t.id}">تعديل</button>
          <button class="btn btn-danger btn-sm" data-del="${t.id}">حذف</button>
        </td>
      </tr>`).join("");

    tbody.querySelectorAll("[data-edit]").forEach((b) => b.addEventListener("click", () => openTeacherModal(b.dataset.edit)));
    tbody.querySelectorAll("[data-del]").forEach((b) => b.addEventListener("click", () => {
      UI.confirmAction("سيتم حذف بيانات هذا المعلم نهائياً. هل تريد المتابعة؟", () => {
        Manara.remove("teachers", b.dataset.del);
        renderTeachers(); renderStats();
        UI.toast("تم حذف المعلم.");
      });
    }));
  }

  function openTeacherModal(id) {
    const form = document.getElementById("teacherForm");
    form.reset();
    if (id) {
      const t = Manara.list("teachers").find((x) => x.id === id);
      document.getElementById("teacherModalTitle").textContent = "تعديل بيانات معلم";
      document.getElementById("teacherId").value = t.id;
      document.getElementById("teacherName").value = t.name;
      document.getElementById("teacherEmail").value = t.email;
      document.getElementById("teacherPassword").value = t.password || "";
      document.getElementById("teacherPhone").value = t.phone || "";
      document.getElementById("teacherSubject").value = t.subject || "";
    } else {
      document.getElementById("teacherModalTitle").textContent = "إضافة معلم";
      document.getElementById("teacherId").value = "";
    }
    UI.openModal("teacherModal");
  }

  document.getElementById("addTeacherBtn").addEventListener("click", () => openTeacherModal(null));
  document.getElementById("teacherForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const id = document.getElementById("teacherId").value;
    const payload = {
      name: document.getElementById("teacherName").value.trim(),
      email: document.getElementById("teacherEmail").value.trim(),
      password: document.getElementById("teacherPassword").value.trim(),
      phone: document.getElementById("teacherPhone").value.trim(),
      subject: document.getElementById("teacherSubject").value.trim(),
    };
    if (id) { Manara.update("teachers", id, payload); UI.toast("تم تحديث بيانات المعلم."); }
    else { Manara.add("teachers", payload); UI.toast("تمت إضافة المعلم بنجاح."); }
    UI.closeModal("teacherModal");
    renderTeachers(); renderStats();
  });

  // ---------- الإعلانات ----------
  function renderAnnouncements() {
    const box = document.getElementById("annList");
    const list = Manara.db.announcements;
    box.innerHTML = list.length ? list.map((a) => `
      <div style="padding:1rem 0; border-bottom:1px solid var(--paper-line);">
        <div style="display:flex; justify-content:space-between; gap:1rem;">
          <strong>${UI.escapeHTML(a.title)}</strong>
          <span style="font-size:0.78rem; color:var(--ink-soft); white-space:nowrap;">${a.date}</span>
        </div>
        <p style="color:var(--ink-soft); margin-top:0.4rem;">${UI.escapeHTML(a.body)}</p>
        <span class="pill">${UI.escapeHTML(a.author)}</span>
      </div>`).join("") : `<div class="empty-state"><div class="glyph">📣</div><p>لم يتم نشر أي إعلان بعد.</p></div>`;
  }

  document.getElementById("addAnnBtn").addEventListener("click", () => UI.openModal("annModal"));
  document.getElementById("annForm").addEventListener("submit", (e) => {
    e.preventDefault();
    Manara.addAnnouncement({
      author: "الإدارة",
      title: document.getElementById("annTitle").value.trim(),
      body: document.getElementById("annBody").value.trim(),
    });
    e.target.reset();
    UI.closeModal("annModal");
    renderAnnouncements(); renderOverviewAnnouncements();
    UI.toast("تم نشر الإعلان.");
  });

  // ---------- التشغيل الأولي ----------
  renderStats();
  renderOverviewAnnouncements();
  renderStudents();
  renderTeachers();
  renderAnnouncements();
})();
