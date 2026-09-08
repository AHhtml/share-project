// ==========================================================================
// منارة — طبقة البيانات الوهمية (Frontend فقط، تُخزَّن في localStorage)
// ==========================================================================

const DB_KEY = "manara-db-v2";

const SEED = {
  currentUser: null, // يُضبط بعد تسجيل الدخول

  users: {
    admin: [
      { id: "adm-1", name: "د. سامر خليل", email: "samer.admin@manara.edu", password: "123456", phone: "0599111222", joined: "2023-01-10" },
    ],
    management: [
      { id: "mgt-1", name: "ريما عودة", email: "rima.mgmt@manara.edu", password: "123456", phone: "0599222333", dept: "شؤون الطلبة", joined: "2023-03-02" },
      { id: "mgt-2", name: "خالد أبو شرخ", email: "khaled.mgmt@manara.edu", password: "123456", phone: "0599222444", dept: "الجودة الأكاديمية", joined: "2023-06-19" },
    ],
    teachers: [
      { id: "tch-1", name: "أ. ليان النجار", email: "layan.t@manara.edu", password: "123456", phone: "0599333111", subject: "تطوير الويب", joined: "2022-09-01" },
      { id: "tch-2", name: "أ. عمر ياسين", email: "omar.t@manara.edu", password: "123456", phone: "0599333222", subject: "قواعد البيانات", joined: "2022-11-15" },
      { id: "tch-3", name: "أ. هبة قاسم", email: "heba.t@manara.edu", password: "123456", phone: "0599333444", subject: "تصميم واجهات", joined: "2023-02-20" },
    ],
    students: [
      { id: "stu-1", name: "أحمد مراد", email: "ahmad.s@manara.edu", password: "123456", phone: "0599444111", track: "تطوير الويب", joined: "2023-09-01" },
      { id: "stu-2", name: "سارة الحاج", email: "sara.s@manara.edu", password: "123456", phone: "0599444222", track: "تطوير الويب", joined: "2023-09-01" },
      { id: "stu-3", name: "يزن دويكات", email: "yazan.s@manara.edu", password: "123456", phone: "0599444333", track: "قواعد البيانات", joined: "2023-09-15" },
      { id: "stu-4", name: "نور شاهين", email: "nour.s@manara.edu", password: "123456", phone: "0599444555", track: "تصميم واجهات", joined: "2024-01-08" },
    ],
  },

  courses: [
    { id: "crs-1", title: "تطوير الويب الحديث", teacherId: "tch-1", track: "تطوير الويب" },
    { id: "crs-2", title: "أساسيات قواعد البيانات", teacherId: "tch-2", track: "قواعد البيانات" },
    { id: "crs-3", title: "تصميم تجربة المستخدم", teacherId: "tch-3", track: "تصميم واجهات" },
  ],

  lectures: [
    { id: "lec-1", courseId: "crs-1", title: "مقدمة في HTML و CSS", date: "2026-08-20", duration: "90 دقيقة", notes: "بناء أول صفحة ويب واستخدام Flexbox." },
    { id: "lec-2", courseId: "crs-1", title: "JavaScript غير المتزامن", date: "2026-08-27", duration: "100 دقيقة", notes: "Promises و async/await مع أمثلة عملية." },
    { id: "lec-3", courseId: "crs-2", title: "تصميم قواعد بيانات علائقية", date: "2026-08-22", duration: "80 دقيقة", notes: "التطبيع حتى الصورة الثالثة." },
    { id: "lec-4", courseId: "crs-3", title: "مبادئ تجربة المستخدم", date: "2026-08-25", duration: "70 دقيقة", notes: "أبحاث المستخدم وبناء الشخصيات." },
  ],

  exams: [
    { id: "exm-1", courseId: "crs-1", title: "اختبار منتصف الفصل — الويب", date: "2026-09-10", duration: "60 دقيقة", totalMarks: 20 },
    { id: "exm-2", courseId: "crs-2", title: "اختبار قصير — SQL", date: "2026-09-05", duration: "30 دقيقة", totalMarks: 10 },
  ],

  submissions: [
    { id: "sub-1", examId: "exm-2", studentId: "stu-3", score: 8, submittedAt: "2026-09-05" },
  ],

  attendance: [
    { id: "att-1", lectureId: "lec-1", studentId: "stu-1", status: "حاضر" },
    { id: "att-2", lectureId: "lec-1", studentId: "stu-2", status: "حاضر" },
  ],

  announcements: [
    { id: "ann-1", author: "الإدارة", title: "تعديل جدول الاختبارات", body: "تم تأجيل اختبار قواعد البيانات إلى الأسبوع القادم.", date: "2026-08-30" },
  ],
};

function loadDB() {
  const raw = localStorage.getItem(DB_KEY);
  if (!raw) {
    localStorage.setItem(DB_KEY, JSON.stringify(SEED));
    return structuredClone(SEED);
  }
  try {
    return JSON.parse(raw);
  } catch (e) {
    localStorage.setItem(DB_KEY, JSON.stringify(SEED));
    return structuredClone(SEED);
  }
}

function saveDB(db) {
  localStorage.setItem(DB_KEY, JSON.stringify(db));
}

function uid(prefix) {
  return prefix + "-" + Math.random().toString(36).slice(2, 9);
}

window.Manara = {
  db: loadDB(),

  persist() { saveDB(this.db); },

  resetDemoData() {
    localStorage.removeItem(DB_KEY);
    this.db = loadDB();
  },

  // ---- المصادقة (تجريبية بدون خادم) ----
  login(role, name) {
    this.db.currentUser = { role, name: name || this.roleLabel(role) };
    this.persist();
  },
  logout() {
    this.db.currentUser = null;
    this.persist();
  },
  currentUser() {
    return this.db.currentUser;
  },
  roleLabel(role) {
    return { admin: "مدير المركز", management: "الإدارة", teacher: "معلم", student: "طالب" }[role] || role;
  },

  // ---- تحويل بين مفتاح الدور ونوع القائمة في قاعدة البيانات ----
  TYPE_BY_ROLE: { admin: "admin", management: "management", teacher: "teachers", student: "students" },
  ROLE_BY_TYPE: { admin: "admin", management: "management", teachers: "teacher", students: "student" },

  typeForRole(role) { return this.TYPE_BY_ROLE[role]; },

  // ---- البحث عن حساب عبر البريد الإلكتروني (لتسجيل الدخول والتحقق من التكرار) ----
  findAccountByEmail(email) {
    const target = (email || "").trim().toLowerCase();
    if (!target) return null;
    for (const type of Object.keys(this.ROLE_BY_TYPE)) {
      const rec = this.db.users[type].find((u) => (u.email || "").trim().toLowerCase() === target);
      if (rec) return { type, role: this.ROLE_BY_TYPE[type], record: rec };
    }
    return null;
  },

  createAccount(role, record) {
    const type = this.typeForRole(role);
    return this.add(type, record);
  },

  // ---- مستخدمو النظام (طلاب / معلمون / إدارة) ----
  list(type) { return this.db.users[type] || []; },

  add(type, record) {
    record.id = uid(type.slice(0, 3));
    record.joined = new Date().toISOString().slice(0, 10);
    this.db.users[type].push(record);
    this.persist();
    return record;
  },

  update(type, id, patch) {
    const arr = this.db.users[type];
    const idx = arr.findIndex((r) => r.id === id);
    if (idx > -1) { arr[idx] = { ...arr[idx], ...patch }; this.persist(); }
    return arr[idx];
  },

  remove(type, id) {
    this.db.users[type] = this.db.users[type].filter((r) => r.id !== id);
    this.persist();
  },

  // ---- المحاضرات ----
  lecturesByCourse(courseId) { return this.db.lectures.filter((l) => l.courseId === courseId); },
  lecturesByTeacher(teacherId) {
    const courseIds = this.db.courses.filter((c) => c.teacherId === teacherId).map((c) => c.id);
    return this.db.lectures.filter((l) => courseIds.includes(l.courseId));
  },
  lecturesForTrack(track) {
    const courseIds = this.db.courses.filter((c) => c.track === track).map((c) => c.id);
    return this.db.lectures.filter((l) => courseIds.includes(l.courseId));
  },
  addLecture(rec) { rec.id = uid("lec"); this.db.lectures.unshift(rec); this.persist(); return rec; },
  updateLecture(id, patch) {
    const idx = this.db.lectures.findIndex((l) => l.id === id);
    if (idx > -1) { this.db.lectures[idx] = { ...this.db.lectures[idx], ...patch }; this.persist(); }
  },
  removeLecture(id) { this.db.lectures = this.db.lectures.filter((l) => l.id !== id); this.persist(); },

  // ---- الاختبارات ----
  examsByTeacher(teacherId) {
    const courseIds = this.db.courses.filter((c) => c.teacherId === teacherId).map((c) => c.id);
    return this.db.exams.filter((e) => courseIds.includes(e.courseId));
  },
  examsForTrack(track) {
    const courseIds = this.db.courses.filter((c) => c.track === track).map((c) => c.id);
    return this.db.exams.filter((e) => courseIds.includes(e.courseId));
  },
  addExam(rec) { rec.id = uid("exm"); this.db.exams.unshift(rec); this.persist(); return rec; },
  updateExam(id, patch) {
    const idx = this.db.exams.findIndex((e) => e.id === id);
    if (idx > -1) { this.db.exams[idx] = { ...this.db.exams[idx], ...patch }; this.persist(); }
  },
  removeExam(id) { this.db.exams = this.db.exams.filter((e) => e.id !== id); this.persist(); },

  // ---- المقررات ----
  courseById(id) { return this.db.courses.find((c) => c.id === id); },
  coursesByTeacher(teacherId) { return this.db.courses.filter((c) => c.teacherId === teacherId); },
  teacherName(teacherId) {
    const t = this.db.users.teachers.find((t) => t.id === teacherId);
    return t ? t.name : "—";
  },

  // ---- علامات الاختبارات ----
  submissionFor(examId, studentId) {
    return this.db.submissions.find((s) => s.examId === examId && s.studentId === studentId);
  },
  setSubmission(examId, studentId, score) {
    const existing = this.submissionFor(examId, studentId);
    if (existing) { existing.score = score; }
    else { this.db.submissions.push({ id: uid("sub"), examId, studentId, score, submittedAt: new Date().toISOString().slice(0, 10) }); }
    this.persist();
  },

  // ---- ربط المستخدم الحالي بسجله التجريبي ----
  currentTeacherRecord() {
    const u = this.currentUser();
    const list = this.db.users.teachers;
    return (u && list.find((t) => t.name === u.name)) || list[0];
  },
  currentStudentRecord() {
    const u = this.currentUser();
    const list = this.db.users.students;
    return (u && list.find((s) => s.name === u.name)) || list[0];
  },

  // ---- الإعلانات ----
  addAnnouncement(rec) { rec.id = uid("ann"); rec.date = new Date().toISOString().slice(0, 10); this.db.announcements.unshift(rec); this.persist(); return rec; },
};
