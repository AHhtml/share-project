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
  .action-btn.view {
    color: #0284c7;
  }
  .action-btn.view:hover {
    background: rgba(2, 132, 199, 0.1);
  }
  .action-btn.edit {
    color: #d97706;
  }
  .action-btn.edit:hover {
    background: rgba(217, 119, 6, 0.1);
  }
  .action-btn.delete {
    color: #dc3545;
  }
  .action-btn.delete:hover {
    background: rgba(220, 53, 69, 0.1);
  }
</style>
</head>
<body>

<div class="app-shell">
  <!-- القائمة الجانبية الموحدة -->
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
            <a href="{{ route('management.students.index') }}" class="btn btn-ghost" style="text-align: right; font-size: 0.85rem; text-decoration: none; color: #94a3b8; display: block; padding: 4px 0;">
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

          <a href="{{ route('management.users.index') }}" class="btn btn-ghost" style="text-align: right; justify-content: start; text-decoration: none; font-size: 0.9rem; color: #38bdf8; margin-top: 4px; display: block; font-weight: bold;">
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
      <div style="display: flex; gap: 10px; align-items: center;">
        <button class="btn btn-primary" onclick="UI.openModal('addUserModal')">+ إضافة معلم جديد</button>
      </div>
    </div>

    <!-- شريط البحث والفلترة المتقدمة -->
    <div class="card section-card" style="margin-bottom: 20px; padding: 15px;">
      <form method="GET" action="{{ route('management.users.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px; gap: 10px; align-items: end;">
        
        <div class="field" style="margin: 0;">
          <label style="font-size: 0.85rem; margin-bottom: 4px; display: block;">بحث بالاسم أو البريد</label>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث هنا..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff;">
        </div>

        <div class="field" style="margin: 0;">
          <label style="font-size: 0.85rem; margin-bottom: 4px; display: block;">المساق الدراسي</label>
          <select name="subject_id" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff;">
            <option value="">كل المساقات</option>
            @foreach(App\Models\Subject::all() as $subject)
              <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="field" style="margin: 0;">
          <label style="font-size: 0.85rem; margin-bottom: 4px; display: block;">من تاريخ</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff;">
        </div>

        <div class="field" style="margin: 0;">
          <label style="font-size: 0.85rem; margin-bottom: 4px; display: block;">إلى تاريخ</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff;">
        </div>

        <div style="display: flex; gap: 5px;">
          <button type="submit" class="btn btn-primary" style="width: 100%; padding: 8px; justify-content: center;">بحث</button>
          @if(request()->anyFilled(['search', 'subject_id', 'date_from', 'date_to']))
            <a href="{{ route('management.users.index') }}" class="btn btn-ghost" style="padding: 8px; border: 1px solid #cbd5e1;" title="إعادة ضبط">✕</a>
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
            <th>رقم الهاتف</th>
            <th>المساق (المادة)</th>
            <th>الدور (الصلاحية)</th>
            <th>تاريخ التسجيل</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          @php
              // إذا كان المتحكم يرسل $users مصفاة مسبقاً، أو إذا أردت دعم الفلترة البسيطة هنا:
              $teachers = $users->where('role', 'teacher');
              if(request('search')) {
                  $q = request('search');
                  $teachers = $teachers->filter(function($u) use ($q) {
                      return str_contains(strtolower($u->name), strtolower($q)) || str_contains(strtolower($u->email), strtolower($q));
                  });
              }
              if(request('subject_id')) {
                  $teachers = $teachers->where('subject_id', request('subject_id'));
              }
          @endphp

          @forelse($teachers as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->phone ?? 'غير متوفر' }}</td>
              <td>
                <span class="badge" style="background: #e2e8f0; color: #334155; padding: 3px 8px; border-radius: 4px; font-weight: 500;">
                  {{ $user->subject->name ?? 'غير محدد' }}
                </span>
              </td>
              <td>
                <span class="badge" style="background: #0284c7; color: #fff; padding: 3px 8px; border-radius: 4px;">معلم</span>
              </td>
              <td>{{ $user->created_at->format('Y-m-d') }}</td>
              <td>
                <div style="display: flex; gap: 6px; align-items: center;">
                  <!-- زر العرض -->
                  <a href="{{ route('management.teachers.content', $user->id) }}" class="action-btn view" title="عرض المحتوى">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  </a>

                  <!-- زر التعديل -->
                  <button type="button" class="action-btn edit" title="تعديل"
                    onclick="openEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone ?? '' }}', '{{ $user->subject_id }}')">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>

                  <!-- زر الحذف -->
                  @if($user->id !== auth()->id())
                    <button type="button" class="action-btn delete" title="حذف"
                      onclick="openDeleteModal('{{ route('management.users.destroy', $user->id) }}', '{{ $user->name }}')">
                      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  @else
                    <span style="color: #888; font-size: 0.8rem; padding: 0 4px;">حسابك</span>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; color: #888; padding: 20px;">لا يوجد معلمون مطابقة لنتائج البحث حالياً.</td>
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
        <label>رقم الهاتف</label>
        <input type="text" name="phone" class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;" placeholder="أدخل رقم الهاتف">
      </div>
      <div class="field" style="margin-top: 10px;">
        <label>المساق (المادة)</label>
        <select name="subject_id" class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; background: #fff;">
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
        <label>رقم الهاتف</label>
        <input type="text" name="phone" id="edit_phone" class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
      </div>
      <div class="field" style="margin-top: 10px;">
        <label>المساق (المادة)</label>
        <select name="subject_id" id="edit_subject_id" required class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; background: #fff;">
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

<!-- نافذة تأكيد الحذف الاحترافية -->
<div class="modal-backdrop" id="deleteUserModal">
  <div class="modal" style="text-align: center; max-width: 400px;">
    <div style="width: 50px; height: 50px; background: #fee2e2; color: #dc3545; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
      <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
    </div>
    <h3 style="margin-bottom: 8px; color: #1e293b;">تأكيد الحذف</h3>
    <p id="deleteModalText" style="color: #64748b; font-size: 0.95rem; margin-bottom: 20px;">هل أنت متأكد من رغبتك في حذف هذا المعلم؟</p>
    <form id="deleteUserForm" method="POST">
      @csrf
      @method('DELETE')
      <div class="modal-actions" style="display: flex; gap: 10px; justify-content: center;">
        <button type="submit" class="btn" style="background-color: #dc3545; color: #fff; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer;">نعم، قم بالحذف</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('deleteUserModal')" style="padding: 8px 20px; border: 1px solid #cbd5e1;">إلغاء</button>
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

  function openEditModal(id, name, email, phone, subjectId) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_subject_id').value = subjectId ? subjectId : '';
    
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