<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إدارة طلاب المركز — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
  .collapsible-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
    opacity: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .collapsible-menu.open {
    max-height: 300px;
    opacity: 1;
  }
  .action-btn {
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: background 0.2s, transform 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .action-btn:hover {
    transform: scale(1.1);
  }
  .action-btn.edit {
    color: #0284c7;
  }
  .action-btn.edit:hover {
    background: rgba(2, 132, 199, 0.1);
  }
  .action-btn.delete {
    color: #dc3545;
  }
  .action-btn.delete:hover {
    background: rgba(220, 53, 69, 0.1);
  }

  /* تصميم النوافذ المنبثقة الاحترافية (Modals) */
  .custom-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .custom-modal-backdrop.show {
    display: flex;
    opacity: 1;
  }
  .custom-modal-box {
    background: #ffffff;
    width: 100%;
    max-width: 450px;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(20px);
    transition: transform 0.3s ease;
    overflow: hidden;
    text-align: right;
  }
  .custom-modal-backdrop.show .custom-modal-box {
    transform: translateY(0);
  }
  .custom-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .custom-modal-header h3 {
    margin: 0;
    font-size: 1.15rem;
    color: #1e293b;
  }
  .custom-modal-body {
    padding: 20px;
  }
  .custom-modal-footer {
    padding: 12px 20px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
  }
</style>
</head>
<body data-home="{{ url('/') }}" data-login="{{ route('login') }}">

<div class="mobile-topbar">
  <button id="menuToggle" class="btn-ghost" style="color:#fff; font-size:1.3rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong id="mobileTitle">منارة</strong>
  <span></span>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell">
  <!-- القائمة الجانبية للإدارة -->
  <aside class="sidebar" id="sidebar">
    <div style="padding: 20px;">
      <p style="margin-bottom: 5px; font-weight: bold;">{{ Auth::user()->name }}</p>
      <p style="font-size: 0.85rem; color: #d97706; margin-bottom: 15px;">الإدارة</p>
      
      <nav class="sidebar-nav" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px;">
        <a href="{{ route('management.dashboard') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 لوحة التحكم</a>
        
        <button type="button" onclick="toggleMenu('usersMenu')" class="btn btn-ghost nav-item" style="text-align: right; justify-content: space-between; display: flex; width: 100%; background: none; border: none; cursor: pointer;">
          <span>👥 إدارة المستخدمين</span>
          <span>▾</span>
        </button>

        <div id="usersMenu" class="collapsible-menu open" style="padding-right: 15px; margin-top: 4px;">
          <button type="button" onclick="toggleMenu('studentsMenu')" class="btn btn-ghost" style="text-align: right; justify-content: space-between; display: flex; width: 100%; font-size: 0.9rem; background: none; border: none; cursor: pointer; color: #cbd5e1;">
            <span>🎓 إدارة الطلاب</span>
            <span>▾</span>
          </button>
          
          <div id="studentsMenu" class="collapsible-menu open" style="padding-right: 15px; margin-top: 2px;">
            <a href="{{ route('management.students.index') }}" class="btn btn-ghost" style="text-align: right; font-size: 0.85rem; text-decoration: none; color: #38bdf8; display: block; padding: 4px 0; font-weight: bold;">
                • كل الطلاب
            </a>
            @php
                $sidebarSubjects = App\Models\Subject::all();
            @endphp
            @forelse($sidebarSubjects as $sub)
                <a href="{{ route('management.subjects.students', $sub->id) }}" class="btn btn-ghost" style="text-align: right; font-size: 0.85rem; text-decoration: none; color: #94a3b8; display: block; padding: 4px 0;">
                    • {{ $sub->name }}
                </a>
            @empty
                <span style="font-size: 0.8rem; color: #777; padding: 4px 0;">لا توجد مواد مضافة</span>
            @endforelse
          </div>

          <a href="{{ route('management.users.index') }}" class="btn btn-ghost" style="text-align: right; justify-content: start; text-decoration: none; font-size: 0.9rem; color: #cbd5e1; margin-top: 4px; display: block;">
            📚 إدارة المعلمين
          </a>
        </div>
        <a href="{{ route('management.profile') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 ملفي الشخصي</a>
      </nav>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #dc3545; border: none;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <main class="main">
    <section data-section="all-students">
      <div class="main-head">
        <div>
          <h1>إدارة طلاب المركز</h1>
          <p>عرض جميع الطلاب المسجلين في المركز وإمكانية إضافة طالب جديد وتحديد مساقه الدراسي.</p>
        </div>
        <button class="btn btn-primary" onclick="UI.openModal('addStudentModal')">+ إضافة طالب جديد</button>
      </div>

      @if(session('success'))
        <div style="background: #d1e7dd; color: #0f5132; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
      @endif

      @if($errors->any())
        <div style="background: #f8d7da; color: #842029; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      <!-- قسم البحث والفلترة المتقدمة -->
      <div class="card section-card" style="margin-bottom: 20px; padding: 15px;">
        <form method="GET" action="{{ route('management.students.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px; gap: 10px; align-items: end;">
          
          <div class="field" style="margin: 0;">
            <label style="font-size: 0.85rem; margin-bottom: 4px; display: block;">بحث بالاسم أو البريد</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث هنا..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
          </div>

          <div class="field" style="margin: 0;">
            <label style="font-size: 0.85rem; margin-bottom: 4px; display: block;">من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
          </div>

          <div class="field" style="margin: 0;">
            <label style="font-size: 0.85rem; margin-bottom: 4px; display: block;">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
          </div>

          <div style="display: flex; gap: 5px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 8px; justify-content: center;">بحث</button>
            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
              <a href="{{ route('management.students.index') }}" class="btn btn-ghost" style="padding: 8px; border: 1px solid #cbd5e1;" title="إعادة ضبط">✕</a>
            @endif
          </div>

        </form>
      </div>

      <div class="card section-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>الاسم</th>
              <th>البريد الإلكتروني</th>
              <th>المساق المسجل به</th>
              <th>تاريخ الالتحاق</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($students as $student)
            <tr>
              <td>{{ $student->name }}</td>
              <td>{{ $student->email }}</td>
              <td>
                @forelse($student->subjects as $subject)
                    <span style="background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; margin-left: 4px; display: inline-block; margin-bottom: 2px;">{{ $subject->name }}</span>
                @empty
                    <span style="color: #999; font-size: 0.85rem;">غير مسجل بمساق</span>
                @endforelse
              </td>
              <td>{{ $student->created_at->format('Y-m-d') }}</td>
              <td>
                <div style="display: flex; gap: 8px; align-items: center;">
                  <!-- زر التعديل -->
                  <button type="button" class="action-btn edit" title="تعديل بيانات الطالب"
                    onclick="openEditModal('{{ $student->id }}', '{{ $student->name }}', '{{ $student->email }}', {{ json_encode($student->subjects->pluck('id')) }})">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                  
                  <!-- زر الحذف -->
                  <button type="button" class="action-btn delete" title="حذف الطالب"
                    onclick="openDeleteModal('{{ route('management.users.destroy', $student->id) }}', '{{ $student->name }}')">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" style="text-align: center; color: #888; padding: 20px;">لا يوجد طلاب مطابقة لنتائج البحث حالياً.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
        <div style="margin-top: 15px;">
            {{ $students->links() }}
        </div>
      </div>
    </section>
  </main>
</div>

<!-- ===== نافذة إضافة طالب جديد (تم تعديلها لاختيار مواد متعددة) ===== -->
<div class="modal-backdrop" id="addStudentModal">
  <div class="modal" style="max-width: 480px;">
    <h3>تسجيل طالب جديد</h3>
    <form action="{{ route('management.users.store') }}" method="POST">
      @csrf
      <input type="hidden" name="role" value="student">
      <div class="field">
        <label>الاسم الكامل</label>
        <input type="text" name="name" required>
      </div>
      <div class="field">
        <label>البريد الإلكتروني</label>
        <input type="email" name="email" required>
      </div>
      <div class="field">
        <label>كلمة المرور</label>
        <input type="password" name="password" required>
      </div>
      <div class="field">
        <label style="display: block; margin-bottom: 6px; font-weight: 500;">اختر المساقات الدراسية (يمكن اختيار أكثر من مساق)</label>
        <div style="max-height: 160px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; background: #f8fafc;">
          @forelse($subjects as $subject)
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer; font-size: 0.9rem;">
              <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" style="width: 16px; height: 16px;">
              <span>{{ $subject->name }}</span>
            </label>
          @empty
            <span style="color: #888; font-size: 0.85rem;">لا توجد مواد متاحة</span>
          @endforelse
        </div>
      </div>
      <div class="modal-actions" style="margin-top: 15px; display: flex; gap: 10px;">
        <button type="submit" class="btn btn-primary">حفظ الطالب</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('addStudentModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<!-- ===== نافذة تأكيد الحذف الاحترافية ===== -->
<div class="custom-modal-backdrop" id="deleteModal">
  <div class="custom-modal-box" style="max-width: 400px; text-align: center;">
    <div style="padding: 25px 20px 10px 20px;">
      <div style="width: 50px; height: 50px; background: #fee2e2; color: #dc3545; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
      </div>
      <h3 style="margin-bottom: 8px; color: #1e293b;">تأكيد الحذف</h3>
      <p style="color: #64748b; font-size: 0.95rem; margin: 0;">هل أنت متأكد تماماً من رغبتك في حذف الطالب (<span id="deleteStudentName" style="font-weight: bold; color: #1e293b;"></span>)؟ لا يمكن التراجع عن هذا الإجراء.</p>
    </div>
    <div class="custom-modal-footer" style="justify-content: center; background: transparent; border: none; padding-bottom: 20px;">
      <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()" style="border: 1px solid #cbd5e1; padding: 6px 20px;">إلغاء</button>
      <form id="deleteForm" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-primary" style="background-color: #dc3545; border: none; padding: 6px 20px;">نعم، احذف</button>
      </form>
    </div>
  </div>
</div>

<!-- ===== نافذة تعديل بيانات الطالب الاحترافية ===== -->
<div class="custom-modal-backdrop" id="editStudentModal">
  <div class="custom-modal-box" style="max-width: 480px;">
    <div class="custom-modal-header">
      <h3>تعديل بيانات الطالب</h3>
      <button type="button" onclick="closeEditModal()" style="background:none; border:none; font-size: 1.2rem; cursor:pointer; color: #64748b;">✕</button>
    </div>
    <form id="editStudentForm" method="POST">
      @csrf
      @method('PUT')
      <div class="custom-modal-body">
        <div class="field" style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #334155;">الاسم الكامل</label>
          <input type="text" name="name" id="editName" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
        </div>
        <div class="field" style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #334155;">البريد الإلكتروني</label>
          <input type="email" name="email" id="editEmail" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
        </div>
        <div class="field" style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #334155;">كلمة المرور الجديدة (اختياري)</label>
          <input type="password" name="password" placeholder="اتركها فارغة إذا لم ترغب بتغييرها" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
        </div>
        <div class="field" style="margin-bottom: 5px;">
          <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #334155;">المساقات الدراسية</label>
          <div style="max-height: 160px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; background: #f8fafc;">
            @foreach($subjects as $subject)
              <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer; font-size: 0.9rem;">
                <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" class="edit-subject-checkbox" style="width: 16px; height: 16px;">
                <span>{{ $subject->name }}</span>
              </label>
            @endforeach
          </div>
        </div>
      </div>
      <div class="custom-modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeEditModal()" style="border: 1px solid #cbd5e1;">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleMenu(menuId) {
  const menu = document.getElementById(menuId);
  if (menu.classList.contains('open')) {
    menu.classList.remove('open');
  } else {
    menu.classList.add('open');
  }
}

// دوال التحكم بنافذة الحذف الاحترافية
function openDeleteModal(deleteUrl, studentName) {
  const modal = document.getElementById('deleteModal');
  const form = document.getElementById('deleteForm');
  document.getElementById('deleteStudentName').innerText = studentName;
  form.action = deleteUrl;
  modal.classList.add('show');
}

function closeDeleteModal() {
  document.getElementById('deleteModal').classList.remove('show');
}

// دوال التحكم بنافذة التعديل الاحترافية (مع تفعيل تحديد مواد الطالب الحالية مسبقاً)
function openEditModal(id, name, email, studentSubjectIds) {
  const modal = document.getElementById('editStudentModal');
  const form = document.getElementById('editStudentForm');
  
  form.action = `/management/users/${id}`;
  document.getElementById('editName').value = name;
  document.getElementById('editEmail').value = email;
  
  // ضبط مربعات الاختيار للمواد الخاصة بالطالب
  const checkboxes = document.querySelectorAll('.edit-subject-checkbox');
  checkboxes.forEach(cb => {
    cb.checked = studentSubjectIds.includes(parseInt(cb.value));
  });
  
  modal.classList.add('show');
}

function closeEditModal() {
  document.getElementById('editStudentModal').classList.remove('show');
}
</script>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
</body>
</html>