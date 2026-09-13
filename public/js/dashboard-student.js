
// (function () {
//   const user = buildLayout("student", "overview");
//   if (!user) return;
//   initSections("overview");

//   const student = Manara.currentStudentRecord();
//   document.getElementById("trackHead").textContent = "مسارك الحالي: " + student.track;

//   function myLectures() { return Manara.lecturesForTrack(student.track); }
//   function myExams() { return Manara.examsForTrack(student.track); }

//   function fmtDate(d) {
//     return new Date(d).toLocaleDateString("ar-EG", { day: "numeric", month: "long", year: "numeric" });
//   }

//   function renderStats() {
//     const lectures = myLectures();
//     const exams = myExams();
//     const done = exams.filter((e) => Manara.submissionFor(e.id, student.id)).length;
//     document.getElementById("statLectures").textContent = lectures.length;
//     document.getElementById("statExams").textContent = exams.length - done;
//     document.getElementById("statDone").textContent = done;
//   }

//   function renderOverviewLectures() {
//     const box = document.getElementById("overviewLectures");
//     const list = myLectures().slice(0, 3);
//     box.innerHTML = list.length ? list.map((l) => `
//       <div style="display:flex; justify-content:space-between; padding:0.9rem 0; border-bottom:1px solid var(--paper-line); gap:1rem;">
//         <div><strong>${UI.escapeHTML(l.title)}</strong><div style="color:var(--ink-soft); font-size:0.85rem;">${fmtDate(l.date)}</div></div>
//         <span class="pill">${UI.escapeHTML(l.duration || "—")}</span>
//       </div>`).join("") : `<div class="empty-state"><div class="glyph">🎥</div><p>لم يُنشر أي محتوى بعد.</p></div>`;
//   }

//   // ---------- المحاضرات ----------
//   function renderLectures() {
//     const list = myLectures();
//     document.getElementById("lecturesEmpty").hidden = list.length !== 0;
//     const box = document.getElementById("lecturesList");
//     box.innerHTML = list.map((l) => `
//       <div class="card lecture-card">
//         <div style="display:flex; gap:1rem; align-items:center;">
//           <div class="icon-box">🎥</div>
//           <div>
//             <strong>${UI.escapeHTML(l.title)}</strong>
//             <div class="meta">${fmtDate(l.date)} · ${UI.escapeHTML(l.duration || "—")} · ${UI.escapeHTML(Manara.teacherName(Manara.courseById(l.courseId).teacherId))}</div>
//           </div>
//         </div>
//         <button class="btn btn-outline btn-sm" data-view="${l.id}">فتح المحاضرة</button>
//       </div>`).join("");

//     box.querySelectorAll("[data-view]").forEach((b) => b.addEventListener("click", () => {
//       const l = Manara.db.lectures.find((x) => x.id === b.dataset.view);
//       document.getElementById("lvTitle").textContent = l.title;
//       document.getElementById("lvMeta").textContent = `${fmtDate(l.date)} · ${l.duration || "—"}`;
//       document.getElementById("lvNotes").textContent = l.notes || "لا يوجد وصف إضافي لهذه المحاضرة.";
//       UI.openModal("lectureViewModal");
//     }));
//   }

//   // ---------- الاختبارات ----------
//   function renderExams() {
//     const list = myExams();
//     document.getElementById("examsEmpty").hidden = list.length !== 0;
//     const box = document.getElementById("examsList");
//     box.innerHTML = list.map((ex) => {
//       const sub = Manara.submissionFor(ex.id, student.id);
//       return `
//       <div class="card exam-card">
//         <div style="display:flex; gap:1rem; align-items:center;">
//           <div class="icon-box">📝</div>
//           <div>
//             <strong>${UI.escapeHTML(ex.title)}</strong>
//             <div class="meta">${fmtDate(ex.date)} · ${UI.escapeHTML(ex.duration || "—")} · العلامة الكاملة ${ex.totalMarks}</div>
//           </div>
//         </div>
//         ${sub
//           ? `<span class="pill good">علامتك: ${sub.score} / ${ex.totalMarks}</span>`
//           : `<button class="btn btn-primary btn-sm" data-take="${ex.id}">تسجيل العلامة</button>`}
//       </div>`;
//     }).join("");

//     box.querySelectorAll("[data-take]").forEach((b) => b.addEventListener("click", () => {
//       const ex = Manara.db.exams.find((x) => x.id === b.dataset.take);
//       document.getElementById("etExamId").value = ex.id;
//       document.getElementById("etTitle").textContent = ex.title;
//       document.getElementById("etMeta").textContent = `${fmtDate(ex.date)} · العلامة الكاملة ${ex.totalMarks}`;
//       document.getElementById("etScore").max = ex.totalMarks;
//       document.getElementById("etScore").value = "";
//       UI.openModal("examTakeModal");
//     }));
//   }

//   document.getElementById("examTakeForm").addEventListener("submit", (e) => {
//     e.preventDefault();
//     const examId = document.getElementById("etExamId").value;
//     const score = Number(document.getElementById("etScore").value);
//     Manara.setSubmission(examId, student.id, score);
//     UI.closeModal("examTakeModal");
//     renderExams(); renderStats();
//     UI.toast("تم حفظ علامتك بنجاح.");
//   });

//   // ---------- الإعلانات ----------
//   function renderAnnouncements() {
//     const box = document.getElementById("annList");
//     const list = Manara.db.announcements;
//     box.innerHTML = list.length ? list.map((a) => `
//       <div style="padding:1rem 0; border-bottom:1px solid var(--paper-line);">
//         <div style="display:flex; justify-content:space-between; gap:1rem;">
//           <strong>${UI.escapeHTML(a.title)}</strong>
//           <span style="font-size:0.78rem; color:var(--ink-soft); white-space:nowrap;">${a.date}</span>
//         </div>
//         <p style="color:var(--ink-soft); margin-top:0.4rem;">${UI.escapeHTML(a.body)}</p>
//         <span class="pill">${UI.escapeHTML(a.author)}</span>
//       </div>`).join("") : `<div class="empty-state"><div class="glyph">📣</div><p>لا توجد إعلانات حالياً.</p></div>`;
//   }

//   renderStats();
//   renderOverviewLectures();
//   renderLectures();
//   renderExams();
//   renderAnnouncements();
// })();








/**
 * لوحة تحكم الطالب — منصة منارة
 * تم تنظيف هذا الملف وتهيئة بيئة العمل للتوافق مع لارافل والتحكم بالواجهات
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log("تم تحميل ملف لوحة الطالب بنجاح.");

    // التحكم بالقائمة الجانبية (Sidebar) للشاشات الصغيرة
    const toggleMenuBtn = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarScrim = document.getElementById('sidebarScrim');

    if (toggleMenuBtn && sidebar) {
        toggleMenuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (sidebarScrim) {
                sidebarScrim.classList.toggle('active');
            }
        });
    }

    if (sidebarScrim && sidebar) {
        sidebarScrim.addEventListener('click', function () {
            sidebar.classList.remove('open');
            sidebarScrim.classList.remove('active');
        });
    }
});
