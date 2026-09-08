<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إدارة المعلمين — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
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
  .sidebar-nav button span:last-child,
  .sidebar-nav span:last-of-type {
    font-size: 14px !important;
    width: auto !important;
    height: auto !important;
    line-height: 1 !important;
    transform: none !important;
    display: inline-block !important;
  }
  .btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #f1f5f9;
    color: var(--text-main);
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    border: 1px solid var(--border-color);
  }
  .btn-back:hover {
    background: #e2e8f0;
    color: var(--primary-color);
  }
</style>
</head>
<body>

<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <div style="padding: 20px;">
      <p style="margin-bottom: 5px; font-weight: bold;">{{ Auth::user()->name }}</p>
      <p style="font-size: 0.85rem; color: #d97706; margin-bottom: 15px;">الإدارة</p>
      
      <nav class="sidebar-nav" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px;">
        <a href="{{ route('management.dashboard') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 لوحة التحكم</a>
        
        <button type="button" onclick="toggleMenu('usersMenu')" class="btn btn-ghost nav-item active" style="text-align: right; justify-content: space-between; display: flex; width: 100%; background: none; border: none; cursor: pointer;">
          <span>👥 إدارة المستخدمين</span>
          <span>▾</span>
        </button>

        <div id="usersMenu" class="collapsible-menu" style="padding-right: 15px; margin-top: 4px;">
          <button type="button" onclick="toggleMenu('studentsMenu')" class="btn btn-ghost" style="text-align: right; justify-content: space-between; display: flex; width: 100%; font-size: 0.9rem; background: none; border: none; cursor: pointer; color: #cbd5e1;">
            <span>🎓 إدارة الطلاب</span>
            <span>▾</span>
          </button>
          
          <div id="studentsMenu" class="collapsible-menu" style="padding-right: 15px; margin-top: 2px;">
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
      </nav>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #dc3545; border: none;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <main class="main">
    @if(session('success'))
      <div style="padding: 12px 16px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="padding: 12px 16px; background-color: #f8d7da; color: #721c24; border-radius: 8px; margin-bottom: 20px;">
        {{ session('error') }}
      </div>
    @endif

    <div class="main-head">
      <div>
        <h1>إدارة المعلمين</h1>
        <p>عرض وتعديل وحذف حسابات المعلمين وتخصصاتهم في النظام.</p>
      </div>
       <a href="{{ route('management.dashboard') }}" class="btn-back">
            <span>←</span> عودة
        </a>
      <button class="btn btn-primary" onclick="UI.openModal('addUserModal')">+ إضافة معلم جديد</button>
    </div>

    <div class="card section-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>المساق (المادة)</th>
            <th>الدور (الصلاحية)</th>
            <th>تاريخ التسجيل</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users->where('role', 'teacher') as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>
                <span class="badge" style="background: #e2e8f0; color: #334155; padding: 3px 8px; border-radius: 4px; font-weight: 500;">
                  {{ $user->subject->name ?? 'غير محدد' }}
                </span>
              </td>
              <td>
                <span class="badge" style="background: #0d6efd; color: #fff; padding: 3px 8px; border-radius: 4px;">معلم</span>
              </td>
              <td>{{ $user->created_at->format('Y-m-d') }}</td>
              <td style="display: flex; gap: 8px; align-items: center;">
                <a href="{{ route('management.teachers.content', $user->id) }}" class="btn btn-sm" style="background-color: #0d6efd; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; text-decoration: none; cursor: pointer;">
                  عرض
                </a>

                <button type="button" class="btn btn-sm" style="background-color: #ffc107; color: #000; border: none; padding: 4px 10px; border-radius: 4px; cursor: pointer;"
                  onclick="openEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->subject_id }}')">
                  تعديل
                </button>

                @if($user->id !== auth()->id())
                  <button type="button" class="btn btn-sm" style="background-color: #dc3545; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; cursor: pointer;"
                    onclick="openDeleteModal('{{ route('management.users.destroy', $user->id) }}', '{{ $user->name }}')">
                    حذف
                  </button>
                @else
                  <span style="color: #888; font-size: 0.85rem;">حسابك</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; color: #888;">لا يوجد معلمون مسجلون حالياً.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div style="margin-top: 20px;">
        {{ $users->links() }}
      </div>
    </div>
  </main>
</div>

<!-- نافذة إضافة معلم جديد -->
<div class="modal-backdrop" id="addUserModal">
  <div class="modal">
    <h3>إضافة معلم جديد</h3>
    <form action="{{ route('management.users.store') }}" method="POST">
      @csrf
      <input type="hidden" name="role" value="teacher">
      <div class="field">
        <label>الاسم الكامل</label>
        <input type="text" name="name" required class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
      </div>
      <div class="field" style="margin-top: 10px;">
        <label>البريد الإلكتروني</label>
        <input type="email" name="email" required class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
      </div>
      <div class="field" style="margin-top: 10px;">
        <label>المساق (المادة)</label>
        <select name="subject_id" class="form-control">
            <option value="">اختر المساق</option>
            @foreach(App\Models\Subject::all() as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
            @endforeach
        </select>
      </div>
      <div class="field" style="margin-top: 10px;">
        <label>كلمة المرور</label>
        <input type="password" name="password" required class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
      </div>
      <div class="modal-actions" style="margin-top: 15px; display: flex; gap: 10px;">
        <button type="submit" class="btn btn-primary">حفظ المعلم</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('addUserModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<!-- نافذة تعديل المعلم -->
<div class="modal-backdrop" id="editUserModal">
  <div class="modal">
    <h3>تعديل بيانات المعلم</h3>
    <form id="editUserForm" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" name="role" value="teacher">
      <div class="field">
        <label>الاسم الكامل</label>
        <input type="text" name="name" id="edit_name" required class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
      </div>
      <div class="field" style="margin-top: 10px;">
        <label>البريد الإلكتروني</label>
        <input type="email" name="email" id="edit_email" required class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
      </div>
      <div class="field" style="margin-top: 10px;">
        <label>المساق (المادة)</label>
        <select name="subject_id" id="edit_subject_id" required class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
          <option value="">اختر المساق</option>
          @foreach(App\Models\Subject::all() as $subject)
            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field" style="margin-top: 10px;">
        <label>كلمة المرور الجديدة (اختياري)</label>
        <input type="password" name="password" placeholder="اتركها فارغة إذا لم ترد تغييرها" class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
      </div>
      <div class="modal-actions" style="margin-top: 15px; display: flex; gap: 10px;">
        <button type="submit" class="btn btn-primary">تحديث البيانات</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('editUserModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<!-- نافذة تأكيد الحذف -->
<div class="modal-backdrop" id="deleteUserModal">
  <div class="modal" style="text-align: center; max-width: 400px;">
    <div style="font-size: 3rem; margin-bottom: 10px;">⚠️</div>
    <h3 style="margin-bottom: 10px; color: #dc3545;">تأكيد الحذف</h3>
    <p id="deleteModalText" style="color: #64748b; margin-bottom: 20px; font-size: 0.95rem;">هل أنت متأكد من رغبتك في حذف هذا المعلم؟</p>
    <form id="deleteUserForm" method="POST">
      @csrf
      @method('DELETE')
      <div class="modal-actions" style="display: flex; gap: 10px; justify-content: center;">
        <button type="submit" class="btn" style="background-color: #dc3545; color: #fff; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer;">نعم، قم بالحذف</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('deleteUserModal')" style="padding: 8px 20px;">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function toggleMenu(menuId) {
    const menu = document.getElementById(menuId);
    if (menu.classList.contains('open')) {
      menu.classList.remove('open');
    } else {
      menu.classList.add('open');
    }
  }

  function openEditModal(id, name, email, subjectId) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_subject_id').value = subjectId ? subjectId : '';
    
    // ربط الفورم بالمسار الديناميكي الصحيح متضمنًا الـ ID
    document.getElementById('editUserForm').action = "/management/users/" + id;
    
    UI.openModal('editUserModal');
  }

  function openDeleteModal(actionUrl, userName) {
    document.getElementById('deleteUserForm').action = actionUrl;
    document.getElementById('deleteModalText').innerHTML = `هل أنت متأكد من رغبتك في حذف المعلم <b style="color: #0f172a;">"${userName}"</b>؟ لا يمكن التراجع عن هذا الإجراء.`;
    UI.openModal('deleteUserModal');
  }
</script>
</body>
</html>