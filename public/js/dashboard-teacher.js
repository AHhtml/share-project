// ==========================================================================
// منارة — لوحة المعلم
// ==========================================================================

(function () {
  const user = buildLayout("teacher", "overview");
  if (!user) return;
  initSections("overview");

  const teacher = Manara.currentTeacherRecord();
  const course = Manara.coursesByTeacher(teacher.id)[0] || Manara.db.courses[0];
  document.getElementById("courseNameHead").textContent = "مساقك الحالي: " + course.title;

  function myLectures() { return Manara.lecturesByTeacher(teacher.id); }
  function myExams() { return Manara.examsByTeacher(teacher.id); }
  function myStudents() { return Manara.list("students").filter((s) => s.track === course.track); }

  function fmtDate(d) {
    return new Date(d).toLocaleDateString("ar-EG", { day: "numeric", month: "long", year: "numeric" });
  }

  function renderStats() {
    document.getElementById("statLectures").textContent = myLectures().length;
    document.getElementById("statExams").textContent = myExams().length;
    document.getElementById("statStudents").textContent = myStudents().length;
  }

  function renderOverviewLectures() {
    const box = document.getElementById("overviewLectures");
    const list = myLectures().slice(0, 3);
    box.innerHTML = list.length ? list.map((l) => `
      <div style="display:flex; justify-content:space-between; padding:0.9rem 0; border-bottom:1px solid var(--paper-line); gap:1rem;">
        <div><strong>${UI.escapeHTML(l.title)}</strong><div style="color:var(--ink-soft); font-size:0.85rem;">${fmtDate(l.date)}</div></div>
        <span class="pill">${UI.escapeHTML(l.duration || "—")}</span>
      </div>`).join("") : `<div class="empty-state"><div class="glyph">🎥</div><p>لم تنشر أي محاضرة بعد.</p></div>`;
  }

  // ---------- المحاضرات ----------
  function renderLectures() {
    const list = myLectures();
    document.getElementById("lecturesEmpty").hidden = list.length !== 0;
    const box = document.getElementById("lecturesList");
    box.innerHTML = list.map((l) => `
      <div class="card lecture-card">
        <div style="display:flex; gap:1rem; align-items:center;">
          <div class="icon-box">🎥</div>
          <div>
            <strong>${UI.escapeHTML(l.title)}</strong>
            <div class="meta">${fmtDate(l.date)} · ${UI.escapeHTML(l.duration || "—")}</div>
            ${l.notes ? `<div class="meta" style="margin-top:0.3rem; max-width:52ch;">${UI.escapeHTML(l.notes)}</div>` : ""}
          </div>
        </div>
        <div class="row-actions">
          <button class="btn btn-outline btn-sm" data-edit="${l.id}">تعديل</button>
          <button class="btn btn-danger btn-sm" data-del="${l.id}">حذف</button>
        </div>
      </div>`).join("");

    box.querySelectorAll("[data-edit]").forEach((b) => b.addEventListener("click", () => openLectureModal(b.dataset.edit)));
    box.querySelectorAll("[data-del]").forEach((b) => b.addEventListener("click", () => {
      UI.confirmAction("سيتم حذف هذه المحاضرة نهائياً ولن تظهر للطلاب. هل تريد المتابعة؟", () => {
        Manara.removeLecture(b.dataset.del);
        renderLectures(); renderStats(); renderOverviewLectures();
        UI.toast("تم حذف المحاضرة.");
      });
    }));
  }

  function openLectureModal(id) {
    const form = document.getElementById("lectureForm");
    form.reset();
    if (id) {
      const l = Manara.db.lectures.find((x) => x.id === id);
      document.getElementById("lectureModalTitle").textContent = "تعديل المحاضرة";
      document.getElementById("lectureId").value = l.id;
      document.getElementById("lectureTitle").value = l.title;
      document.getElementById("lectureDate").value = l.date;
      document.getElementById("lectureDuration").value = l.duration || "";
      document.getElementById("lectureNotes").value = l.notes || "";
    } else {
      document.getElementById("lectureModalTitle").textContent = "محاضرة جديدة";
      document.getElementById("lectureId").value = "";
    }
    UI.openModal("lectureModal");
  }

  document.getElementById("addLectureBtn").addEventListener("click", () => openLectureModal(null));
  document.getElementById("lectureForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const id = document.getElementById("lectureId").value;
    const payload = {
      courseId: course.id,
      title: document.getElementById("lectureTitle").value.trim(),
      date: document.getElementById("lectureDate").value,
      duration: document.getElementById("lectureDuration").value.trim(),
      notes: document.getElementById("lectureNotes").value.trim(),
    };
    if (id) { Manara.updateLecture(id, payload); UI.toast("تم تحديث المحاضرة."); }
    else { Manara.addLecture(payload); UI.toast("تم نشر المحاضرة، وستظهر لطلابك فوراً."); }
    UI.closeModal("lectureModal");
    renderLectures(); renderStats(); renderOverviewLectures();
  });

  // ---------- الاختبارات ----------
  function renderExams() {
    const list = myExams();
    document.getElementById("examsEmpty").hidden = list.length !== 0;
    const box = document.getElementById("examsList");
    box.innerHTML = list.map((ex) => `
      <div class="card exam-card">
        <div style="display:flex; gap:1rem; align-items:center;">
          <div class="icon-box">📝</div>
          <div>
            <strong>${UI.escapeHTML(ex.title)}</strong>
            <div class="meta">${fmtDate(ex.date)} · ${UI.escapeHTML(ex.duration || "—")} · العلامة الكاملة ${ex.totalMarks}</div>
          </div>
        </div>
        <div class="row-actions">
          <button class="btn btn-outline btn-sm" data-edit="${ex.id}">تعديل</button>
          <button class="btn btn-danger btn-sm" data-del="${ex.id}">حذف</button>
        </div>
      </div>`).join("");

    box.querySelectorAll("[data-edit]").forEach((b) => b.addEventListener("click", () => openExamModal(b.dataset.edit)));
    box.querySelectorAll("[data-del]").forEach((b) => b.addEventListener("click", () => {
      UI.confirmAction("سيتم حذف هذا الاختبار نهائياً. هل تريد المتابعة؟", () => {
        Manara.removeExam(b.dataset.del);
        renderExams(); renderStats();
        UI.toast("تم حذف الاختبار.");
      });
    }));
  }

  function openExamModal(id) {
    const form = document.getElementById("examForm");
    form.reset();
    if (id) {
      const ex = Manara.db.exams.find((x) => x.id === id);
      document.getElementById("examModalTitle").textContent = "تعديل الاختبار";
      document.getElementById("examId").value = ex.id;
      document.getElementById("examTitle").value = ex.title;
      document.getElementById("examDate").value = ex.date;
      document.getElementById("examDuration").value = ex.duration || "";
      document.getElementById("examMarks").value = ex.totalMarks;
    } else {
      document.getElementById("examModalTitle").textContent = "اختبار جديد";
      document.getElementById("examId").value = "";
    }
    UI.openModal("examModal");
  }

  document.getElementById("addExamBtn").addEventListener("click", () => openExamModal(null));
  document.getElementById("examForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const id = document.getElementById("examId").value;
    const payload = {
      courseId: course.id,
      title: document.getElementById("examTitle").value.trim(),
      date: document.getElementById("examDate").value,
      duration: document.getElementById("examDuration").value.trim(),
      totalMarks: Number(document.getElementById("examMarks").value) || 10,
    };
    if (id) { Manara.updateExam(id, payload); UI.toast("تم تحديث الاختبار."); }
    else { Manara.addExam(payload); UI.toast("تمت جدولة الاختبار، وسيصل إشعار لطلابك."); }
    UI.closeModal("examModal");
    renderExams(); renderStats();
  });

  // ---------- الطلاب (عرض فقط) ----------
  function renderStudents() {
    const rows = myStudents();
    document.getElementById("studentsEmpty").hidden = rows.length !== 0;
    document.getElementById("studentsTable").innerHTML = rows.map((s) => `
      <tr>
        <td><div class="avatar-name"><div class="avatar">${UI.escapeHTML(s.name.slice(0,1))}</div>${UI.escapeHTML(s.name)}</div></td>
        <td>${UI.escapeHTML(s.email)}</td>
        <td><span class="pill">${UI.escapeHTML(s.track)}</span></td>
        <td>${s.joined}</td>
      </tr>`).join("");
  }

  renderStats();
  renderOverviewLectures();
  renderLectures();
  renderExams();
  renderStudents();
})();




document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
    };

    // ==========================================
    // 1. إدارة المحاضرات (Lessons)
    // ==========================================
    async function loadLessons() {
        try {
            const response = await fetch('/teacher/lessons');
            const lessons = await response.json();
            const container = document.getElementById('lessonsList'); // أو العنصر المسؤول عن عرض المحاضرات

            if (!container) return;
            container.innerHTML = '';

            lessons.forEach(lesson => {
                const item = document.createElement('div');
                item.className = 'card mb-2';
                item.innerHTML = `
                    <div style="display:flex; justify-between; align-items:center; padding: 10px;">
                        <div>
                            <h4>${lesson.title}</h4>
                            <p>${lesson.description || ''}</p>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="deleteLesson(${lesson.id})">حذف</button>
                    </div>
                `;
                container.appendChild(item);
            });
        } catch (error) {
            console.error('خطأ في تحميل المحاضرات:', error);
        }
    }

    // إضافة محاضرة جديدة
    const lessonForm = document.getElementById('addLessonForm');
    if (lessonForm) {
        lessonForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = document.getElementById('lessonTitle').value;
            const description = document.getElementById('lessonDesc').value;

            try {
                const response = await fetch('/teacher/lessons', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({ title, description })
                });
                const result = await response.json();

                if (response.ok) {
                    alert(result.message);
                    lessonForm.reset();
                    loadLessons();
                }
            } catch (error) {
                console.error('خطأ في إضافة المحاضرة:', error);
            }
        });
    }

    window.deleteLesson = async function(id) {
        if (!confirm('هل أنت تأكد من حذف المحاضرة؟')) return;
        try {
            const response = await fetch(`/teacher/lessons/${id}`, {
                method: 'DELETE',
                headers: headers
            });
            const result = await response.json();
            if (response.ok) {
                alert(result.message);
                loadLessons();
            }
        } catch (error) {
            console.error('خطأ في حذف المحاضرة:', error);
        }
    };

    // تحميل البيانات
    loadLessons();
});





document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- جلب المحاضرات والاختبارات عند التحميل ---
    loadLectures();
    loadExams();

    // --- إدارة المحاضرات ---
    const lectureForm = document.getElementById('lectureForm');
    if (lectureForm) {
        lectureForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                id: document.getElementById('lectureId').value,
                title: document.getElementById('lectureTitle').value,
                date: document.getElementById('lectureDate').value,
                duration: document.getElementById('lectureDuration').value,
                description: document.getElementById('lectureNotes').value,
            };

            const response = await fetch('/teacher/lessons', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const res = await response.json();
            if (res.success) {
                UI.closeModal('lectureModal');
                lectureForm.reset();
                document.getElementById('lectureId').value = '';
                loadLectures();
            } else {
                alert(res.message || 'حدث خطأ أثناء الحفظ');
            }
        });
    }

    // --- إدارة الاختبارات ---
    const examForm = document.getElementById('examForm');
    if (examForm) {
        examForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                id: document.getElementById('examId').value,
                title: document.getElementById('examTitle').value,
                due_date: document.getElementById('examDate').value,
                duration: document.getElementById('examDuration').value,
                marks: document.getElementById('examMarks').value,
            };

            const response = await fetch('/teacher/assignments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const res = await response.json();
            if (res.success) {
                UI.closeModal('examModal');
                examForm.reset();
                document.getElementById('examId').value = '';
                loadExams();
            } else {
                alert(res.message || 'حدث خطأ أثناء الحفظ');
            }
        });
    }
});

// دالة جلب وعرض المحاضرات
async function loadLectures() {
    const container = document.getElementById('lecturesList');
    const emptyState = document.getElementById('lecturesEmpty');
    if (!container) return;

    const res = await fetch('/teacher/lessons');
    const data = await res.json();

    if (data.success && data.lessons.length > 0) {
        emptyState.hidden = true;
        container.innerHTML = data.lessons.map(item => `
            <div class="card" style="padding: 15px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0 0 5px;">${item.title}</h3>
                    <p style="margin: 0; font-size: 0.85rem; color: #666;">التاريخ: ${item.date} | المدة: ${item.duration || 'غير محدودة'}</p>
                    <p style="margin: 5px 0 0; font-size: 0.9rem;">${item.description || ''}</p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-sm btn-outline" onclick='editLecture(${JSON.stringify(item)})'>تعديل</button>
                    <button class="btn btn-sm" style="background:#dc3545; color:#fff; border:none;" onclick="deleteLecture(${item.id})">حذف</button>
                </div>
            </div>
        `).join('');
    } else {
        container.innerHTML = '';
        emptyState.hidden = false;
    }
}

// دالة جلب وعرض الاختبارات
async function loadExams() {
    const container = document.getElementById('examsList');
    const emptyState = document.getElementById('examsEmpty');
    if (!container) return;

    const res = await fetch('/teacher/assignments');
    const data = await res.json();

    if (data.success && data.assignments.length > 0) {
        emptyState.hidden = true;
        container.innerHTML = data.assignments.map(item => `
            <div class="card" style="padding: 15px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0 0 5px;">${item.title}</h3>
                    <p style="margin: 0; font-size: 0.85rem; color: #666;">موعد التسليم: ${item.due_date} | العلامة: ${item.marks}</p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-sm btn-outline" onclick='editExam(${JSON.stringify(item)})'>تعديل</button>
                    <button class="btn btn-sm" style="background:#dc3545; color:#fff; border:none;" onclick="deleteExam(${item.id})">حذف</button>
                </div>
            </div>
        `).join('');
    } else {
        container.innerHTML = '';
        emptyState.hidden = false;
    }
}

// التعديل والحذف للمحاضرات
function editLecture(item) {
    document.getElementById('lectureId').value = item.id;
    document.getElementById('lectureTitle').value = item.title;
    document.getElementById('lectureDate').value = item.date;
    document.getElementById('lectureDuration').value = item.duration || '';
    document.getElementById('lectureNotes').value = item.description || '';
    document.getElementById('lectureModalTitle').innerText = 'تعديل المحاضرة';
    UI.openModal('lectureModal');
}

async function deleteLecture(id) {
    if (!confirm('هل أنت تأكد من حذف هذه المحاضرة؟')) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    await fetch(`/teacher/lessons/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });
    loadLectures();
}

// التعديل والحذف للاختبارات
function editExam(item) {
    document.getElementById('examId').value = item.id;
    document.getElementById('examTitle').value = item.title;
    document.getElementById('examDate').value = item.due_date;
    document.getElementById('examDuration').value = item.duration || '';
    document.getElementById('examMarks').value = item.marks;
    document.getElementById('examModalTitle').innerText = 'تعديل الاختبار';
    UI.openModal('examModal');
}

async function deleteExam(id) {
    if (!confirm('هل أنت تأكد من حذف هذا الاختبار؟')) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    await fetch(`/teacher/assignments/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });
    loadExams();
}
