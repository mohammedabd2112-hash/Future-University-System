import React, { useState, useEffect } from 'react';
import { 
  LogIn, Lock, Mail, AlertCircle, GraduationCap, Users, 
  LayoutDashboard, LogOut, Search, ShieldCheck, 
  UserPlus, Trash2, Pencil, X, School, UserCheck, Newspaper, ArrowUpRight, 
  PlusCircle, FolderPlus, Calendar, UserCog, BookOpen, RefreshCw, ArrowRight,
  ArrowLeft, Sparkles
} from 'lucide-react';

interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'student';
}

interface Student {
  id: number;
  name: string;
  email: string;
  role: string;
}

interface College {
  id: number;
  name: string;
  description: string;
}

interface Teacher {
  id: number;
  name: string;
  email: string;
  phone: string;
  department: string;
  specialization: string;
}

interface NewsItem {
  id: number;
  title: string;
  content: string;
  date?: string;
}

interface DashboardStats {
  students: number;
  teachers: number;
  colleges: number;
  news: number;
}

const SidebarToggleIcon = ({ size = 20, className = "" }: { size?: number; className?: string }) => (
  <svg 
    width={size} 
    height={size} 
    viewBox="0 0 24 24" 
    fill="none" 
    stroke="currentColor" 
    strokeWidth="2" 
    strokeLinecap="round" 
    strokeLinejoin="round" 
    className={className}
  >
    <rect width="18" height="18" x="3" y="3" rx="3" />
    <path d="M15 3v18" />
  </svg>
);

export default function App() {
  const [currentUser, setCurrentUser] = useState<User | null>(null);
  const [checkingSession, setCheckingSession] = useState(true);
  const [showLoginView, setShowLoginView] = useState(false);
  const [activeTab, setActiveTab] = useState<'overview' | 'students' | 'colleges' | 'teachers' | 'news' | 'users'>('overview');
  
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

  const [stats, setStats] = useState<DashboardStats>({ students: 0, teachers: 0, colleges: 0, news: 0 });
  const [recentStudents, setRecentStudents] = useState<Student[]>([]);
  const [recentNews, setRecentNews] = useState<NewsItem[]>([]);

  const [students, setStudents] = useState<Student[]>([]);
  const [colleges, setColleges] = useState<College[]>([]);
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [newsList, setNewsList] = useState<NewsItem[]>([]);
  const [systemUsers, setSystemUsers] = useState<User[]>([]);

  const [searchQuery, setSearchQuery] = useState('');
  const [showSearchDropdown, setShowSearchDropdown] = useState(false);

  const [loadingStudents, setLoadingStudents] = useState(false);
  const [loadingColleges, setLoadingColleges] = useState(false);
  const [loadingTeachers, setLoadingTeachers] = useState(false);
  const [loadingNews, setLoadingNews] = useState(false);

  const [showAddUserModal, setShowAddUserModal] = useState(false);
  const [newUserName, setNewUserName] = useState('');
  const [newUserEmail, setNewUserEmail] = useState('');
  const [newUserPassword, setNewUserPassword] = useState('123456');
  const [newUserRole, setNewUserRole] = useState('student');

  const [editingUser, setEditingUser] = useState<User | null>(null);
  const [editUserName, setEditUserName] = useState('');
  const [editUserEmail, setEditUserEmail] = useState('');
  const [editUserPassword, setEditUserPassword] = useState('');
  const [editUserRole, setEditUserRole] = useState('student');

  const [showAddStudentModal, setShowAddStudentModal] = useState(false);
  const [newStudentName, setNewStudentName] = useState('');
  const [newStudentEmail, setNewStudentEmail] = useState('');
  const [newStudentPassword, setNewStudentPassword] = useState('123456');

  const [editingStudent, setEditingStudent] = useState<Student | null>(null);
  const [editStudentName, setEditStudentName] = useState('');
  const [editStudentEmail, setEditStudentEmail] = useState('');
  const [editStudentPassword, setEditStudentPassword] = useState('');

  const [showAddCollegeModal, setShowAddCollegeModal] = useState(false);
  const [newCollegeName, setNewCollegeName] = useState('');
  const [newCollegeDesc, setNewCollegeDesc] = useState('');

  const [editingCollege, setEditingCollege] = useState<College | null>(null);
  const [editCollegeName, setEditCollegeName] = useState('');
  const [editCollegeDesc, setEditCollegeDesc] = useState('');

  const [showAddTeacherModal, setShowAddTeacherModal] = useState(false);
  const [newTeacherName, setNewTeacherName] = useState('');
  const [newTeacherEmail, setNewTeacherEmail] = useState('');
  const [newTeacherPhone, setNewTeacherPhone] = useState('');
  const [newTeacherDept, setNewTeacherDept] = useState('');
  const [newTeacherSpec, setNewTeacherSpec] = useState('');

  const [editingTeacher, setEditingTeacher] = useState<Teacher | null>(null);
  const [editTeacherName, setEditTeacherName] = useState('');
  const [editTeacherEmail, setEditTeacherEmail] = useState('');
  const [editTeacherPhone, setEditTeacherPhone] = useState('');
  const [editTeacherDept, setEditTeacherDept] = useState('');
  const [editTeacherSpec, setEditTeacherSpec] = useState('');

  const [showAddNewsModal, setShowAddNewsModal] = useState(false);
  const [newNewsTitle, setNewNewsTitle] = useState('');
  const [newNewsContent, setNewNewsContent] = useState('');

  const [editingNews, setEditingNews] = useState<NewsItem | null>(null);
  const [editNewsTitle, setEditNewsTitle] = useState('');
  const [editNewsContent, setEditNewsContent] = useState('');

  const [modalLoading, setModalLoading] = useState(false);
  const [modalError, setModalError] = useState<string | null>(null);

  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    loadColleges();
    loadNews();

    fetch('http://localhost/uws/api/session.php', { credentials: 'include' })
      .then(res => res.ok ? res.json() : null)
      .then(data => {
        if (data?.authenticated && data.user) {
          setCurrentUser(data.user);
          if (data.user.role === 'admin') loadAllAdminData();
          else loadStudentViewData();
        }
      })
      .catch(() => {})
      .finally(() => setCheckingSession(false));
  }, []);

  const loadAllAdminData = () => {
    loadDashboardData();
    loadStudents();
    loadColleges();
    loadTeachers();
    loadNews();
    loadSystemUsers();
  };

  const loadStudentViewData = () => {
    loadNews();
    loadColleges();
  };

  const loadDashboardData = async () => {
    try {
      const res = await fetch('http://localhost/uws/api/dashboard.php', { credentials: 'include' });
      const data = await res.json();
      if (data.success) {
        setStats(data.stats);
        setRecentStudents(data.recent_students || []);
        setRecentNews(data.recent_news || []);
      }
    } catch {}
  };

  const loadStudents = async () => {
    setLoadingStudents(true);
    try {
      const res = await fetch('http://localhost/uws/api/students.php', { credentials: 'include' });
      const data = await res.json();
      if (data.success) setStudents(data.students);
    } finally {
      setLoadingStudents(false);
    }
  };

  const loadColleges = async () => {
    setLoadingColleges(true);
    try {
      const res = await fetch('http://localhost/uws/api/colleges.php', { credentials: 'include' });
      const data = await res.json();
      if (data.success) setColleges(data.colleges);
    } finally {
      setLoadingColleges(false);
    }
  };

  const loadTeachers = async () => {
    setLoadingTeachers(true);
    try {
      const res = await fetch('http://localhost/uws/api/teachers.php', { credentials: 'include' });
      const data = await res.json();
      if (data.success) setTeachers(data.teachers);
    } finally {
      setLoadingTeachers(false);
    }
  };

  const loadNews = async () => {
    setLoadingNews(true);
    try {
      const res = await fetch('http://localhost/uws/api/news.php', { credentials: 'include' });
      const data = await res.json();
      if (data.success) setNewsList(data.news);
    } finally {
      setLoadingNews(false);
    }
  };

  const loadSystemUsers = async () => {
    try {
      const res = await fetch('http://localhost/uws/api/users.php', { credentials: 'include' });
      const data = await res.json();
      if (data.success) setSystemUsers(data.users);
    } catch {}
  };

  const handleAddUser = async (e: React.FormEvent) => {
    e.preventDefault();
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/users.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ name: newUserName, email: newUserEmail, password: newUserPassword, role: newUserRole })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setShowAddUserModal(false);
        setNewUserName('');
        setNewUserEmail('');
        setNewUserPassword('123456');
        loadSystemUsers();
        loadStudents();
        loadDashboardData();
      } else {
        setModalError(data.message || 'حدث خطأ');
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleUpdateUser = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingUser) return;
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/users.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id: editingUser.id, name: editUserName, email: editUserEmail, password: editUserPassword, role: editUserRole })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setEditingUser(null);
        loadSystemUsers();
        loadStudents();
        loadDashboardData();
      } else {
        setModalError(data.message || 'فشل التعديل');
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleDeleteUser = async (id: number) => {
    if (!window.confirm('هل تريد حذف هذا المستخدم؟')) return;
    try {
      const res = await fetch(`http://localhost/uws/api/users.php?id=${id}`, { method: 'DELETE', credentials: 'include' });
      const data = await res.json();
      if (data.success) {
        setSystemUsers(prev => prev.filter(u => u.id !== id));
        loadStudents();
        loadDashboardData();
      } else {
        alert(data.message || 'فشل الحذف');
      }
    } catch {}
  };

  const handleAddNews = async (e: React.FormEvent) => {
    e.preventDefault();
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/news.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ title: newNewsTitle, content: newNewsContent })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setShowAddNewsModal(false);
        setNewNewsTitle('');
        setNewNewsContent('');
        loadNews();
        loadDashboardData();
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleUpdateNews = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingNews) return;
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/news.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id: editingNews.id, title: editNewsTitle, content: editNewsContent })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setEditingNews(null);
        loadNews();
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleDeleteNews = async (id: number) => {
    if (!window.confirm('حذف الخبر؟')) return;
    try {
      const res = await fetch(`http://localhost/uws/api/news.php?id=${id}`, { method: 'DELETE', credentials: 'include' });
      const data = await res.json();
      if (data.success) {
        setNewsList(prev => prev.filter(n => n.id !== id));
        loadDashboardData();
      }
    } catch {}
  };

  const handleAddTeacher = async (e: React.FormEvent) => {
    e.preventDefault();
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/teachers.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ name: newTeacherName, email: newTeacherEmail, phone: newTeacherPhone, department: newTeacherDept, specialization: newTeacherSpec })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setShowAddTeacherModal(false);
        setNewTeacherName('');
        setNewTeacherEmail('');
        setNewTeacherPhone('');
        setNewTeacherDept('');
        setNewTeacherSpec('');
        loadTeachers();
        loadDashboardData();
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleUpdateTeacher = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingTeacher) return;
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/teachers.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id: editingTeacher.id, name: editTeacherName, email: editTeacherEmail, phone: editTeacherPhone, department: editTeacherDept, specialization: editTeacherSpec })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setEditingTeacher(null);
        loadTeachers();
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleDeleteTeacher = async (id: number) => {
    if (!window.confirm('حذف المدرس؟')) return;
    try {
      const res = await fetch(`http://localhost/uws/api/teachers.php?id=${id}`, { method: 'DELETE', credentials: 'include' });
      const data = await res.json();
      if (data.success) {
        setTeachers(prev => prev.filter(t => t.id !== id));
        loadDashboardData();
      }
    } catch {}
  };

  const handleAddCollege = async (e: React.FormEvent) => {
    e.preventDefault();
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/colleges.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ name: newCollegeName, description: newCollegeDesc })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setShowAddCollegeModal(false);
        setNewCollegeName('');
        setNewCollegeDesc('');
        loadColleges();
        loadDashboardData();
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleUpdateCollege = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingCollege) return;
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/colleges.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id: editingCollege.id, name: editCollegeName, description: editCollegeDesc })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setEditingCollege(null);
        loadColleges();
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleDeleteCollege = async (id: number) => {
    if (!window.confirm('حذف الكلية؟')) return;
    try {
      const res = await fetch(`http://localhost/uws/api/colleges.php?id=${id}`, { method: 'DELETE', credentials: 'include' });
      const data = await res.json();
      if (data.success) {
        setColleges(prev => prev.filter(c => c.id !== id));
        loadDashboardData();
      }
    } catch {}
  };

  const handleAddStudent = async (e: React.FormEvent) => {
    e.preventDefault();
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/students.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ name: newStudentName, email: newStudentEmail, password: newStudentPassword })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setShowAddStudentModal(false);
        setNewStudentName('');
        setNewStudentEmail('');
        setNewStudentPassword('123456');
        loadStudents();
        loadSystemUsers();
        loadDashboardData();
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleUpdateStudent = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingStudent) return;
    setModalError(null);
    setModalLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/students.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id: editingStudent.id, name: editStudentName, email: editStudentEmail, password: editStudentPassword })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setEditingStudent(null);
        loadStudents();
        loadSystemUsers();
      }
    } finally {
      setModalLoading(false);
    }
  };

  const handleDeleteStudent = async (id: number) => {
    if (!window.confirm('حذف الطالب؟')) return;
    try {
      const res = await fetch(`http://localhost/uws/api/students.php?id=${id}`, { method: 'DELETE', credentials: 'include' });
      const data = await res.json();
      if (data.success) {
        setStudents(prev => prev.filter(s => s.id !== id));
        loadSystemUsers();
        loadDashboardData();
      }
    } catch {}
  };

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setLoading(true);
    try {
      const res = await fetch('http://localhost/uws/api/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ email, password }),
      });
      const data = await res.json();
      if (res.ok && data.success && data.user) {
        setCurrentUser(data.user);
        if (data.user.role === 'admin') loadAllAdminData();
        else loadStudentViewData();
      } else {
        setError(data.message || 'بيانات الدخول غير صحيحة');
      }
    } catch {
      setError('تعذر الاتصال بالخادم');
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = async () => {
    try {
      await fetch('http://localhost/uws/api/logout.php', { credentials: 'include' });
    } finally {
      setCurrentUser(null);
      setShowLoginView(false);
    }
  };

  if (checkingSession) {
    return (
      <div className="min-h-screen bg-[#151320] flex items-center justify-center text-white font-sans">
        <div className="w-10 h-10 border-4 border-[#8453FC] border-t-transparent rounded-full animate-spin" />
      </div>
    );
  }

  const q = searchQuery.toLowerCase().trim();
  const filteredStudents = students.filter(s => s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q));
  const filteredColleges = colleges.filter(c => c.name.toLowerCase().includes(q) || (c.description && c.description.toLowerCase().includes(q)));
  const filteredTeachers = teachers.filter(t => t.name.toLowerCase().includes(t.name.toLowerCase()) && (t.name.toLowerCase().includes(q) || t.email.toLowerCase().includes(q) || (t.department && t.department.toLowerCase().includes(q))));
  const filteredNews = newsList.filter(n => n.title.toLowerCase().includes(q) || n.content.toLowerCase().includes(q));
  const filteredUsers = systemUsers.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));

  const totalResultsCount = (q === '') ? 0 : (
    filteredStudents.length + filteredColleges.length + filteredTeachers.length + filteredNews.length + filteredUsers.length
  );

  return (
    <>
      <style>{`
        @keyframes floatSlow {
          0%, 100% { transform: translateY(0px) rotate(0deg); }
          50% { transform: translateY(-10px) rotate(0.8deg); }
        }
        @keyframes floatReverse {
          0%, 100% { transform: translateY(0px) rotate(0deg); }
          50% { transform: translateY(10px) rotate(-0.8deg); }
        }
        @keyframes ambientGlow {
          0%, 100% { opacity: 0.45; transform: scale(1); }
          50% { opacity: 0.75; transform: scale(1.1); }
        }
        .animate-float-slow {
          animation: floatSlow 7s ease-in-out infinite;
        }
        .animate-float-reverse {
          animation: floatReverse 8s ease-in-out infinite;
        }
        .animate-ambient-glow {
          animation: ambientGlow 9s ease-in-out infinite;
        }
        .card-3d {
          transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.35s ease, border-color 0.35s ease;
          transform: perspective(1000px) translateZ(0);
        }
        .card-3d:hover {
          transform: perspective(1000px) translateY(-6px) scale(1.012) translateZ(8px);
          box-shadow: 0 20px 35px -8px rgba(79, 38, 233, 0.16), 0 0 1px 1px rgba(132, 83, 252, 0.2);
          border-color: rgba(132, 83, 252, 0.45) !important;
        }
      `}</style>

      {/* 1. الواجهة الترحيبية العامة */}
      {!currentUser && !showLoginView && (
        <div className="min-h-screen bg-[#F7F5FC] font-sans text-slate-800 relative overflow-hidden" dir="rtl">
          <div className="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-gradient-to-tr from-[#8453FC]/25 to-[#4F26E9]/25 blur-3xl pointer-events-none animate-ambient-glow" />
          <div className="absolute top-1/2 -left-40 w-96 h-96 rounded-full bg-gradient-to-br from-[#8453FC]/20 to-[#ECE8F6] blur-3xl pointer-events-none animate-float-reverse" />

          <header className="bg-white/80 backdrop-blur-xl border-b border-[#ECE8F6] sticky top-0 z-30 shadow-sm">
            <div className="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
              <div className="flex items-center gap-3.5 card-3d">
                <div className="p-3 bg-gradient-to-tr from-[#4F26E9] via-[#6334F1] to-[#8453FC] text-white rounded-2xl shadow-lg shadow-[#4F26E9]/30">
                  <GraduationCap size={28} />
                </div>
                <div>
                  <h1 className="text-xl font-extrabold text-[#151320] tracking-tight">جامعة المستقبل</h1>
                  <p className="text-xs text-[#8453FC] font-semibold">نظام إدارة الجامعة الأكاديمي (UWS)</p>
                </div>
              </div>

              <button 
                onClick={() => setShowLoginView(true)}
                className="px-6 py-2.5 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white font-semibold rounded-xl text-sm transition-all duration-300 flex items-center gap-2 shadow-lg shadow-[#4F26E9]/30 hover:shadow-[#4F26E9]/50 hover:-translate-y-0.5 active:scale-95"
              >
                <LogIn size={18} />
                <span>تسجيل الدخول للنظام</span>
              </button>
            </div>
          </header>

          <section className="relative pt-20 pb-16">
            <div className="max-w-5xl mx-auto px-6 text-center space-y-6 relative z-10">
              <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/90 border border-[#ECE8F6] text-[#4F26E9] text-xs font-bold shadow-md shadow-[#4F26E9]/5 card-3d">
                <Sparkles size={14} className="text-[#8453FC]" /> المنظومة الأكاديمية الذكية
              </div>

              <h2 className="text-4xl md:text-5xl font-black text-[#151320] tracking-tight leading-tight">
                أهلاً بكم في المنظومة الأكاديمية <br />
                <span className="bg-gradient-to-r from-[#4F26E9] via-[#6B3CF4] to-[#8453FC] bg-clip-text text-transparent">
                  لجامعة المستقبل
                </span>
              </h2>

              <p className="text-slate-600 text-base md:text-lg max-w-2xl mx-auto leading-relaxed font-normal">
                منصة جامعية متكاملة لإدارة الكليات، هيئة التدريس، شؤون الطلاب، ومتابعة التعاميم الأكاديمية بنمط تفاعلي حديث.
              </p>

              <div className="pt-4 flex flex-wrap items-center justify-center gap-4">
                <button 
                  onClick={() => setShowLoginView(true)}
                  className="px-8 py-3.5 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white font-bold rounded-2xl transition-all duration-300 shadow-xl shadow-[#4F26E9]/35 hover:shadow-[#4F26E9]/55 hover:-translate-y-1 active:scale-95 flex items-center gap-2.5 text-base"
                >
                  <span>الدخول إلى النظام الأكاديمي</span>
                  <ArrowLeft size={18} />
                </button>
              </div>
            </div>
          </section>

          <section className="py-16 max-w-7xl mx-auto px-6 relative z-10">
            <div className="flex items-center justify-between mb-8">
              <div>
                <h3 className="text-2xl font-bold text-[#151320]">كليات وأقسام الجامعة</h3>
                <p className="text-sm text-slate-500 mt-1">الكليات العلمية والبرامج الأكاديمية المتاحة</p>
              </div>
              <span className="text-xs font-bold px-3 py-1.5 bg-white text-[#4F26E9] rounded-xl border border-[#ECE8F6] shadow-sm">
                {colleges.length} كليات معتمدة
              </span>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {colleges.map((col) => (
                <div key={col.id} className="bg-white/90 backdrop-blur-md p-6 rounded-3xl border border-[#ECE8F6] shadow-sm card-3d flex flex-col justify-between">
                  <div>
                    <div className="w-12 h-12 rounded-2xl bg-[#F7F5FC] text-[#4F26E9] flex items-center justify-center mb-4 font-bold border border-[#ECE8F6]">
                      <School size={24} />
                    </div>
                    <h4 className="font-bold text-[#151320] text-base mb-2">{col.name}</h4>
                    <p className="text-xs text-slate-500 line-clamp-3 leading-relaxed">{col.description || 'برامج أكاديمية متميزة ونخبة من الأكاديميين.'}</p>
                  </div>
                  <div className="pt-4 mt-4 border-t border-[#ECE8F6] flex items-center justify-between text-xs text-slate-400 font-mono">
                    <span>كلية #{col.id}</span>
                  </div>
                </div>
              ))}
            </div>
          </section>

          <section className="py-16 bg-white/70 backdrop-blur-md border-t border-[#ECE8F6] relative z-10">
            <div className="max-w-7xl mx-auto px-6">
              <div className="flex items-center justify-between mb-8">
                <div>
                  <h3 className="text-2xl font-bold text-[#151320]">آخر الإعلانات والأنشطة</h3>
                  <p className="text-sm text-slate-500 mt-1">التعاميم الرسمية وأخبار الحرم الجامعي</p>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {newsList.map((n) => (
                  <div key={n.id} className="bg-[#F7F5FC] p-6 rounded-3xl border border-[#ECE8F6] card-3d flex flex-col justify-between">
                    <div>
                      <div className="flex items-center justify-between mb-3">
                        <span className="text-xs font-bold px-2.5 py-1 bg-white text-[#4F26E9] rounded-lg border border-[#ECE8F6] flex items-center gap-1.5 shadow-sm">
                          <Calendar size={13} className="text-[#8453FC]" /> {n.date || 'اليوم'}
                        </span>
                        <span className="text-xs text-slate-400 font-mono">#{n.id}</span>
                      </div>
                      <h4 className="font-bold text-[#151320] text-base mb-2">{n.title}</h4>
                      <p className="text-xs text-slate-600 leading-relaxed line-clamp-4">{n.content}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </section>

          <footer className="border-t border-[#ECE8F6] bg-[#ECE8F6]/40 py-8 text-center text-xs text-slate-500">
            <p>جميع الحقوق محفوظة © جامعة المستقبل - نظام إدارة الجامعة الأكاديمي (UWS)</p>
          </footer>
        </div>
      )}

      {/* 2. شاشة تسجيل الدخول */}
      {!currentUser && showLoginView && (
        <div className="min-h-screen bg-[#151320] flex items-center justify-center p-4 font-sans relative overflow-hidden" dir="rtl">
          <div className="absolute top-10 right-10 w-96 h-96 rounded-full bg-[#4F26E9]/25 blur-3xl pointer-events-none animate-ambient-glow" />
          <div className="absolute bottom-10 left-10 w-96 h-96 rounded-full bg-[#8453FC]/20 blur-3xl pointer-events-none animate-float-slow" />

          <div className="bg-[#1D192B]/90 border border-[#8453FC]/25 p-8 rounded-3xl shadow-2xl max-w-md w-full backdrop-blur-xl relative z-10 card-3d">
            <button 
              onClick={() => setShowLoginView(false)}
              className="mb-6 inline-flex items-center gap-2 text-xs text-slate-400 hover:text-white transition"
            >
              <ArrowRight size={14} />
              <span>العودة للواجهة الرئيسية</span>
            </button>

            <div className="text-center mb-8">
              <div className="inline-flex p-3.5 bg-[#4F26E9]/20 text-[#8453FC] border border-[#8453FC]/30 rounded-2xl mb-3 shadow-lg shadow-[#4F26E9]/20 animate-float-slow">
                <GraduationCap size={40} />
              </div>
              <h1 className="text-2xl font-bold text-white tracking-wide">تسجيل دخول النظام الأكاديمي</h1>
              <p className="text-slate-400 text-sm mt-1">سجّل دخولك للوصول إلى لوحة التحكم أو بوابة الطالب</p>
            </div>

            {error && (
              <div className="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl flex items-center gap-3 text-rose-400 text-sm">
                <AlertCircle size={18} className="shrink-0" />
                <span>{error}</span>
              </div>
            )}

            <form onSubmit={handleLogin} className="space-y-5">
              <div>
                <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">البريد الإلكتروني</label>
                <div className="relative">
                  <Mail className="absolute right-3.5 top-3 text-slate-400" size={18} />
                  <input 
                    type="email" 
                    required 
                    value={email} 
                    onChange={(e) => setEmail(e.target.value)} 
                    placeholder="admin@gmail.com أو test@gmail.com" 
                    className="w-full bg-[#151320]/80 border border-[#8453FC]/30 rounded-xl px-4 py-2.5 pr-11 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#8453FC] transition" 
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">كلمة المرور</label>
                <div className="relative">
                  <Lock className="absolute right-3.5 top-3 text-slate-400" size={18} />
                  <input 
                    type="password" 
                    required 
                    value={password} 
                    onChange={(e) => setPassword(e.target.value)} 
                    placeholder="••••••••" 
                    className="w-full bg-[#151320]/80 border border-[#8453FC]/30 rounded-xl px-4 py-2.5 pr-11 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#8453FC] transition" 
                  />
                </div>
              </div>

              <button 
                type="submit" 
                disabled={loading} 
                className="w-full py-3 px-4 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white font-semibold rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-[#4F26E9]/40 transition duration-300 disabled:opacity-50 text-sm mt-2 hover:-translate-y-0.5 active:scale-95"
              >
                {loading ? <span className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" /> : <> <LogIn size={18} /> <span>دخول النظام</span> </>}
              </button>
            </form>
          </div>
        </div>
      )}

      {/* 3. واجهة الطالب */}
      {currentUser && currentUser.role === 'student' && (
        <div className="min-h-screen bg-[#F7F5FC] font-sans" dir="rtl">
          <header className="bg-white/90 backdrop-blur-md border-b border-[#ECE8F6] h-16 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-[#4F26E9] text-white rounded-xl shadow-md shadow-[#4F26E9]/30">
                <GraduationCap size={22} />
              </div>
              <div>
                <h2 className="font-bold text-[#151320] text-base leading-tight">بوابة الطالب - جامعة المستقبل</h2>
                <span className="text-xs text-slate-400">النظام الأكاديمي للطلاب</span>
              </div>
            </div>

            <div className="flex items-center gap-4">
              <div className="flex items-center gap-3 pl-4 border-l border-[#ECE8F6]">
                <div className="text-left">
                  <div className="text-sm font-bold text-[#151320]">{currentUser.name}</div>
                  <div className="text-xs text-[#8453FC] font-semibold">حساب طالب نشط</div>
                </div>
                <div className="w-9 h-9 rounded-xl bg-[#F7F5FC] border border-[#ECE8F6] text-[#4F26E9] font-bold flex items-center justify-center text-sm shadow-sm">
                  {currentUser.name.charAt(0).toUpperCase()}
                </div>
              </div>

              <button 
                onClick={handleLogout}
                className="flex items-center gap-2 px-3.5 py-2 text-rose-500 hover:bg-rose-50 rounded-xl transition text-xs font-semibold"
              >
                <LogOut size={16} />
                <span>خروج</span>
              </button>
            </div>
          </header>

          <main className="p-8 max-w-6xl mx-auto space-y-6">
            <div className="bg-gradient-to-r from-[#4F26E9] via-[#6334F1] to-[#8453FC] rounded-3xl p-8 text-white shadow-xl shadow-[#4F26E9]/20 flex flex-col md:flex-row md:items-center justify-between gap-6 card-3d">
              <div>
                <span className="px-3 py-1 bg-white/20 rounded-full text-xs font-medium">الفصل الدراسي الأول 2026</span>
                <h1 className="text-3xl font-bold mt-2">مرحباً بك، {currentUser.name}</h1>
                <p className="text-violet-100 text-sm mt-1">الرقم الأكاديمي: #{currentUser.id} | البريد الجامعي: {currentUser.email}</p>
              </div>
              <div className="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 flex items-center gap-4">
                <BookOpen size={32} className="text-violet-200" />
                <div>
                  <p className="text-xs text-violet-200">الحالة الأكاديمية</p>
                  <p className="text-base font-bold">منتظم ومسجل</p>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div className="md:col-span-2 bg-white rounded-3xl p-6 border border-[#ECE8F6] shadow-sm space-y-4 card-3d">
                <div className="flex items-center gap-2 border-b border-[#ECE8F6] pb-3">
                  <Newspaper size={20} className="text-[#8453FC]" />
                  <h3 className="font-bold text-[#151320] text-lg">التعاميم والأخبار الأكاديمية</h3>
                </div>
                {newsList.length === 0 ? (
                  <p className="text-slate-400 text-sm py-4">لا توجد إعلانات جديدة حالياً.</p>
                ) : (
                  <div className="space-y-3">
                    {newsList.map((nw) => (
                      <div key={nw.id} className="p-4 bg-[#F7F5FC] rounded-2xl border border-[#ECE8F6]">
                        <div className="flex items-center justify-between mb-1">
                          <h4 className="font-bold text-[#151320] text-sm">{nw.title}</h4>
                          <span className="text-[11px] text-slate-400 flex items-center gap-1"><Calendar size={12} /> {nw.date || 'اليوم'}</span>
                        </div>
                        <p className="text-xs text-slate-600 leading-relaxed mt-1">{nw.content}</p>
                      </div>
                    ))}
                  </div>
                )}
              </div>

              <div className="bg-white rounded-3xl p-6 border border-[#ECE8F6] shadow-sm space-y-4 card-3d">
                <div className="flex items-center gap-2 border-b border-[#ECE8F6] pb-3">
                  <School size={20} className="text-[#4F26E9]" />
                  <h3 className="font-bold text-[#151320] text-lg">كليات الجامعة</h3>
                </div>
                <div className="space-y-3">
                  {colleges.map((c) => (
                    <div key={c.id} className="p-3 bg-[#F7F5FC] rounded-xl border border-[#ECE8F6]">
                      <h4 className="font-semibold text-[#151320] text-xs">{c.name}</h4>
                      <p className="text-[11px] text-slate-500 line-clamp-2 mt-0.5">{c.description}</p>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </main>
        </div>
      )}

      {/* 4. واجهة المسؤول */}
      {currentUser && currentUser.role === 'admin' && (
        <div className="min-h-screen bg-[#F7F5FC] flex font-sans text-slate-800" dir="rtl">
          <aside className={`bg-[#151320] text-slate-300 flex-col justify-between p-4 hidden md:flex border-l border-[#2B273F] transition-all duration-300 ease-in-out relative ${
            sidebarCollapsed ? 'w-20' : 'w-64'
          }`}>
            <div className="space-y-6">
              <div className="flex items-center justify-between px-1">
                {!sidebarCollapsed ? (
                  <>
                    <div className="flex items-center gap-3 overflow-hidden">
                      <div className="p-2.5 bg-gradient-to-tr from-[#4F26E9] to-[#8453FC] text-white rounded-xl shadow-lg shadow-[#4F26E9]/35 shrink-0">
                        <GraduationCap size={22} />
                      </div>
                      <div className="truncate">
                        <h2 className="font-bold text-white text-sm truncate">جامعة المستقبل</h2>
                        <span className="text-[11px] text-[#8453FC] truncate block font-medium">بوابة UWS المتكاملة</span>
                      </div>
                    </div>

                    <button 
                      onClick={() => setSidebarCollapsed(true)}
                      className="p-2 text-slate-400 hover:text-white hover:bg-[#231F35] rounded-xl transition cursor-pointer shrink-0"
                      title="إغلاق الشريط الجانبي"
                    >
                      <SidebarToggleIcon size={20} />
                    </button>
                  </>
                ) : (
                  <button 
                    onClick={() => setSidebarCollapsed(false)}
                    className="mx-auto p-2.5 bg-gradient-to-tr from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white rounded-xl shadow-lg shadow-[#4F26E9]/40 transition cursor-pointer flex items-center justify-center group relative"
                    title="فتح الشريط الجانبي"
                  >
                    <GraduationCap size={22} className="block group-hover:hidden transition" />
                    <SidebarToggleIcon size={22} className="hidden group-hover:block transition" />
                  </button>
                )}
              </div>

              <nav className="space-y-1.5">
                <button 
                  onClick={() => setActiveTab('overview')} 
                  title={sidebarCollapsed ? "لوحة التحكم" : undefined}
                  className={`w-full flex items-center rounded-xl font-medium text-sm transition-all duration-200 ${
                    sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-2.5'
                  } ${activeTab === 'overview' ? 'bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white shadow-lg shadow-[#4F26E9]/30' : 'hover:bg-[#231F35] text-slate-400 hover:text-white'}`}
                >
                  <LayoutDashboard size={18} className="shrink-0" />
                  {!sidebarCollapsed && <span className="truncate">لوحة التحكم</span>}
                </button>

                <button 
                  onClick={() => setActiveTab('students')} 
                  title={sidebarCollapsed ? "إدارة الطلاب" : undefined}
                  className={`w-full flex items-center rounded-xl font-medium text-sm transition-all duration-200 ${
                    sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-2.5'
                  } ${activeTab === 'students' ? 'bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white shadow-lg shadow-[#4F26E9]/30' : 'hover:bg-[#231F35] text-slate-400 hover:text-white'}`}
                >
                  <Users size={18} className="shrink-0" />
                  {!sidebarCollapsed && <span className="truncate">إدارة الطلاب</span>}
                </button>

                <button 
                  onClick={() => setActiveTab('colleges')} 
                  title={sidebarCollapsed ? "إدارة الكليات" : undefined}
                  className={`w-full flex items-center rounded-xl font-medium text-sm transition-all duration-200 ${
                    sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-2.5'
                  } ${activeTab === 'colleges' ? 'bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white shadow-lg shadow-[#4F26E9]/30' : 'hover:bg-[#231F35] text-slate-400 hover:text-white'}`}
                >
                  <School size={18} className="shrink-0" />
                  {!sidebarCollapsed && <span className="truncate">إدارة الكليات</span>}
                </button>

                <button 
                  onClick={() => setActiveTab('teachers')} 
                  title={sidebarCollapsed ? "هيئة التدريس" : undefined}
                  className={`w-full flex items-center rounded-xl font-medium text-sm transition-all duration-200 ${
                    sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-2.5'
                  } ${activeTab === 'teachers' ? 'bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white shadow-lg shadow-[#4F26E9]/30' : 'hover:bg-[#231F35] text-slate-400 hover:text-white'}`}
                >
                  <UserCheck size={18} className="shrink-0" />
                  {!sidebarCollapsed && <span className="truncate">هيئة التدريس</span>}
                </button>

                <button 
                  onClick={() => setActiveTab('news')} 
                  title={sidebarCollapsed ? "أخبار وإعلانات" : undefined}
                  className={`w-full flex items-center rounded-xl font-medium text-sm transition-all duration-200 ${
                    sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-2.5'
                  } ${activeTab === 'news' ? 'bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white shadow-lg shadow-[#4F26E9]/30' : 'hover:bg-[#231F35] text-slate-400 hover:text-white'}`}
                >
                  <Newspaper size={18} className="shrink-0" />
                  {!sidebarCollapsed && <span className="truncate">أخبار وإعلانات</span>}
                </button>

                <button 
                  onClick={() => setActiveTab('users')} 
                  title={sidebarCollapsed ? "إدارة المستخدمين" : undefined}
                  className={`w-full flex items-center rounded-xl font-medium text-sm transition-all duration-200 ${
                    sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-2.5'
                  } ${activeTab === 'users' ? 'bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white shadow-lg shadow-[#4F26E9]/30' : 'hover:bg-[#231F35] text-slate-400 hover:text-white'}`}
                >
                  <UserCog size={18} className="shrink-0" />
                  {!sidebarCollapsed && <span className="truncate">إدارة المستخدمين</span>}
                </button>
              </nav>
            </div>

            <div className="border-t border-[#2B273F] pt-4">
              <button 
                onClick={handleLogout} 
                title={sidebarCollapsed ? "تسجيل الخروج" : undefined}
                className={`w-full flex items-center rounded-xl text-rose-400 hover:bg-rose-500/10 transition text-sm font-medium ${
                  sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3 py-2.5'
                }`}
              >
                <LogOut size={18} className="shrink-0" />
                {!sidebarCollapsed && <span className="truncate">تسجيل الخروج</span>}
              </button>
            </div>
          </aside>

          <div className="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header className="bg-white/90 backdrop-blur-md border-b border-[#ECE8F6] h-16 flex items-center justify-between px-8 sticky top-0 z-30 shadow-sm">
              <div className="flex items-center gap-3">
                <div className="relative w-80 md:w-96">
                  <div className="flex items-center gap-3 bg-[#F7F5FC] px-3.5 py-2 rounded-xl text-sm text-slate-500 border border-[#ECE8F6] focus-within:border-[#8453FC] focus-within:bg-white transition shadow-sm">
                    <Search size={16} className="text-[#8453FC]" />
                    <input 
                      type="text" 
                      placeholder="ابحث عن طالب، كلية، مدرس، أو خبر..." 
                      value={searchQuery}
                      onFocus={() => setShowSearchDropdown(true)}
                      onChange={(e) => {
                        setSearchQuery(e.target.value);
                        setShowSearchDropdown(true);
                      }}
                      className="bg-transparent border-none outline-none w-full text-slate-700 placeholder-slate-400 text-sm" 
                    />
                    {searchQuery && (
                      <button onClick={() => { setSearchQuery(''); setShowSearchDropdown(false); }} className="text-slate-400 hover:text-slate-600">
                        <X size={16} />
                      </button>
                    )}
                  </div>

                  {showSearchDropdown && searchQuery.trim() !== '' && (
                    <div className="absolute top-12 right-0 w-full bg-white rounded-2xl shadow-2xl border border-[#ECE8F6] p-4 z-50 max-h-96 overflow-y-auto space-y-4 card-3d">
                      <div className="flex items-center justify-between text-xs text-slate-400 border-b border-[#ECE8F6] pb-2">
                        <span>نتائج البحث ({totalResultsCount})</span>
                        <button onClick={() => setShowSearchDropdown(false)} className="text-[#4F26E9] hover:underline font-semibold">إغلاق</button>
                      </div>

                      {totalResultsCount === 0 ? (
                        <p className="text-xs text-center text-slate-400 py-4">لم يتم العثور على أي نتائج مطابقة</p>
                      ) : (
                        <>
                          {filteredStudents.length > 0 && (
                            <div>
                              <p className="text-[11px] font-bold text-[#8453FC] mb-1.5 flex items-center gap-1"><Users size={12} /> الطلاب ({filteredStudents.length})</p>
                              <div className="space-y-1">
                                {filteredStudents.slice(0, 3).map(s => (
                                  <div key={s.id} onClick={() => { setActiveTab('students'); setShowSearchDropdown(false); }} className="p-2 hover:bg-[#F7F5FC] rounded-xl cursor-pointer flex justify-between items-center text-xs">
                                    <span className="font-semibold text-slate-700">{s.name}</span>
                                    <span className="text-slate-400 font-mono">#{s.id}</span>
                                  </div>
                                ))}
                              </div>
                            </div>
                          )}

                          {filteredColleges.length > 0 && (
                            <div>
                              <p className="text-[11px] font-bold text-[#8453FC] mb-1.5 flex items-center gap-1"><School size={12} /> الكليات ({filteredColleges.length})</p>
                              <div className="space-y-1">
                                {filteredColleges.slice(0, 3).map(c => (
                                  <div key={c.id} onClick={() => { setActiveTab('colleges'); setShowSearchDropdown(false); }} className="p-2 hover:bg-[#F7F5FC] rounded-xl cursor-pointer flex justify-between items-center text-xs">
                                    <span className="font-semibold text-slate-700">{c.name}</span>
                                    <span className="text-slate-400 font-mono">#{c.id}</span>
                                  </div>
                                ))}
                              </div>
                            </div>
                          )}

                          {filteredTeachers.length > 0 && (
                            <div>
                              <p className="text-[11px] font-bold text-[#8453FC] mb-1.5 flex items-center gap-1"><UserCheck size={12} /> هيئة التدريس ({filteredTeachers.length})</p>
                              <div className="space-y-1">
                                {filteredTeachers.slice(0, 3).map(t => (
                                  <div key={t.id} onClick={() => { setActiveTab('teachers'); setShowSearchDropdown(false); }} className="p-2 hover:bg-[#F7F5FC] rounded-xl cursor-pointer flex justify-between items-center text-xs">
                                    <span className="font-semibold text-slate-700">{t.name}</span>
                                    <span className="text-slate-400">{t.department || 'عام'}</span>
                                  </div>
                                ))}
                              </div>
                            </div>
                          )}

                          {filteredNews.length > 0 && (
                            <div>
                              <p className="text-[11px] font-bold text-[#8453FC] mb-1.5 flex items-center gap-1"><Newspaper size={12} /> الأخبار ({filteredNews.length})</p>
                              <div className="space-y-1">
                                {filteredNews.slice(0, 3).map(n => (
                                  <div key={n.id} onClick={() => { setActiveTab('news'); setShowSearchDropdown(false); }} className="p-2 hover:bg-[#F7F5FC] rounded-xl cursor-pointer flex justify-between items-center text-xs">
                                    <span className="font-semibold text-slate-700 truncate">{n.title}</span>
                                  </div>
                                ))}
                              </div>
                            </div>
                          )}
                        </>
                      )}
                    </div>
                  )}
                </div>
              </div>

              <div className="flex items-center gap-4">
                <button 
                  onClick={loadAllAdminData} 
                  className="p-2 text-slate-500 hover:text-[#4F26E9] hover:bg-[#F7F5FC] rounded-xl transition cursor-pointer hover:rotate-180 duration-500" 
                  title="تحديث البيانات (مزامنة)"
                >
                  <RefreshCw size={19} />
                </button>
                
                <div className="flex items-center gap-3 border-r pr-4 border-[#ECE8F6]">
                  <div className="text-left">
                    <div className="text-sm font-bold text-[#151320] leading-tight">{currentUser?.name}</div>
                    <div className="text-xs text-[#8453FC] font-semibold flex items-center gap-1">
                      <ShieldCheck size={12} /> {currentUser?.role}
                    </div>
                  </div>
                  <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#4F26E9] to-[#8453FC] text-white font-bold flex items-center justify-center text-sm shadow-md shadow-[#4F26E9]/20">
                    {currentUser?.name.charAt(0).toUpperCase()}
                  </div>
                </div>
              </div>
            </header>

            {/* لوحة التحكم الرئيسية */}
            {activeTab === 'overview' && (
              <main className="p-8 space-y-8 max-w-7xl w-full mx-auto">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                  <div>
                    <h1 className="text-2xl font-bold text-[#151320]">مرحباً بك، {currentUser?.name} 👋</h1>
                    <p className="text-slate-500 text-sm mt-1">نظرة عامة وإحصائيات شاملة للنظام الجامعي (UWS)</p>
                  </div>
                  <button 
                    onClick={() => setShowAddStudentModal(true)} 
                    className="px-4 py-2.5 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg shadow-[#4F26E9]/25 hover:shadow-[#4F26E9]/45 hover:-translate-y-0.5"
                  >
                    <PlusCircle size={16} />
                    <span>إضافة طالب سريع</span>
                  </button>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                  <div onClick={() => setActiveTab('students')} className="bg-white p-5 rounded-2xl border border-[#ECE8F6] shadow-sm card-3d cursor-pointer flex items-center justify-between group">
                    <div>
                      <span className="text-xs font-semibold text-slate-400">إجمالي الطلاب</span>
                      <p className="text-2xl font-black text-[#151320] mt-1">{stats.students}</p>
                      <span className="text-xs text-[#4F26E9] font-semibold flex items-center gap-1 mt-1 group-hover:underline">عرض القائمة <ArrowUpRight size={13} /></span>
                    </div>
                    <div className="p-3 bg-[#F7F5FC] text-[#4F26E9] rounded-2xl group-hover:bg-[#4F26E9] group-hover:text-white transition-all duration-300 shadow-sm">
                      <Users size={22} />
                    </div>
                  </div>

                  <div onClick={() => setActiveTab('colleges')} className="bg-white p-5 rounded-2xl border border-[#ECE8F6] shadow-sm card-3d cursor-pointer flex items-center justify-between group">
                    <div>
                      <span className="text-xs font-semibold text-slate-400">الكليات المعتمدة</span>
                      <p className="text-2xl font-black text-[#151320] mt-1">{colleges.length}</p>
                      <span className="text-xs text-[#8453FC] font-semibold flex items-center gap-1 mt-1 group-hover:underline">إدارة الكليات <ArrowUpRight size={13} /></span>
                    </div>
                    <div className="p-3 bg-[#F7F5FC] text-[#8453FC] rounded-2xl group-hover:bg-[#8453FC] group-hover:text-white transition-all duration-300 shadow-sm">
                      <School size={22} />
                    </div>
                  </div>

                  <div onClick={() => setActiveTab('teachers')} className="bg-white p-5 rounded-2xl border border-[#ECE8F6] shadow-sm card-3d cursor-pointer flex items-center justify-between group">
                    <div>
                      <span className="text-xs font-semibold text-slate-400">هيئة التدريس</span>
                      <p className="text-2xl font-black text-[#151320] mt-1">{teachers.length}</p>
                      <span className="text-xs text-[#6334F1] font-semibold flex items-center gap-1 mt-1 group-hover:underline">إدارة المدرسين <ArrowUpRight size={13} /></span>
                    </div>
                    <div className="p-3 bg-[#F7F5FC] text-[#6334F1] rounded-2xl group-hover:bg-[#6334F1] group-hover:text-white transition-all duration-300 shadow-sm">
                      <UserCheck size={22} />
                    </div>
                  </div>

                  <div onClick={() => setActiveTab('news')} className="bg-white p-5 rounded-2xl border border-[#ECE8F6] shadow-sm card-3d cursor-pointer flex items-center justify-between group">
                    <div>
                      <span className="text-xs font-semibold text-slate-400">الأخبار والتعاميم</span>
                      <p className="text-2xl font-black text-[#151320] mt-1">{newsList.length}</p>
                      <span className="text-xs text-[#8453FC] font-semibold flex items-center gap-1 mt-1 group-hover:underline">إدارة الأخبار <ArrowUpRight size={13} /></span>
                    </div>
                    <div className="p-3 bg-[#F7F5FC] text-[#8453FC] rounded-2xl group-hover:bg-[#8453FC] group-hover:text-white transition-all duration-300 shadow-sm">
                      <Newspaper size={22} />
                    </div>
                  </div>

                  <div onClick={() => setActiveTab('users')} className="bg-white p-5 rounded-2xl border border-[#ECE8F6] shadow-sm card-3d cursor-pointer flex items-center justify-between group">
                    <div>
                      <span className="text-xs font-semibold text-slate-400">كل الحسابات النشطة</span>
                      <p className="text-2xl font-black text-[#151320] mt-1">{systemUsers.length}</p>
                      <span className="text-xs text-[#4F26E9] font-semibold flex items-center gap-1 mt-1 group-hover:underline">المستخدمين <ArrowUpRight size={13} /></span>
                    </div>
                    <div className="p-3 bg-[#F7F5FC] text-[#4F26E9] rounded-2xl group-hover:bg-[#4F26E9] group-hover:text-white transition-all duration-300 shadow-sm">
                      <UserCog size={22} />
                    </div>
                  </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                  <div className="lg:col-span-2 bg-white rounded-3xl border border-[#ECE8F6] p-6 shadow-sm card-3d">
                    <div className="flex items-center justify-between mb-5">
                      <div>
                        <h3 className="font-bold text-[#151320] text-base">الطلاب المنضمون حديثاً</h3>
                        <p className="text-xs text-slate-400 mt-0.5">آخر السجلات المضافة في قاعدة البيانات</p>
                      </div>
                      <button onClick={() => setActiveTab('students')} className="text-xs text-[#4F26E9] hover:text-[#8453FC] font-bold flex items-center gap-1">
                        إدارة كافة الطلاب <ArrowUpRight size={14} />
                      </button>
                    </div>

                    <div className="divide-y divide-[#ECE8F6]">
                      {recentStudents.map((st) => (
                        <div key={st.id} className="py-3.5 flex items-center justify-between">
                          <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-[#F7F5FC] border border-[#ECE8F6] text-[#4F26E9] font-bold flex items-center justify-center text-sm">
                              {st.name.charAt(0)}
                            </div>
                            <div>
                              <p className="text-sm font-semibold text-[#151320]">{st.name}</p>
                              <p className="text-xs text-slate-400">{st.email}</p>
                            </div>
                          </div>
                          <span className="font-mono text-xs px-2.5 py-1 bg-[#F7F5FC] border border-[#ECE8F6] text-[#4F26E9] rounded-lg">#{st.id}</span>
                        </div>
                      ))}
                    </div>
                  </div>

                  <div className="space-y-6">
                    <div className="bg-white rounded-3xl border border-[#ECE8F6] p-6 shadow-sm card-3d">
                      <div className="flex items-center justify-between mb-4">
                        <h3 className="font-bold text-[#151320] text-base">إعلانات الجامعة</h3>
                        <span className="text-xs px-2.5 py-1 rounded-lg bg-[#F7F5FC] text-[#4F26E9] font-bold border border-[#ECE8F6]">تحديثات حية</span>
                      </div>
                      <div className="space-y-3">
                        {recentNews.length === 0 ? (
                          <p className="text-xs text-slate-400">لا توجد إعلانات منشورة حالياً.</p>
                        ) : (
                          recentNews.map((nw) => (
                            <div key={nw.id} className="p-3 bg-[#F7F5FC] rounded-2xl border border-[#ECE8F6]">
                              <p className="text-xs font-semibold text-[#151320]">{nw.title}</p>
                              <span className="text-[10px] text-[#8453FC] font-semibold mt-1 block">إعلان #{nw.id}</span>
                            </div>
                          ))
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </main>
            )}

            {/* تبويب إدارة المستخدمين */}
            {activeTab === 'users' && (
              <main className="p-8 space-y-6 max-w-7xl w-full mx-auto">
                <div className="flex items-center justify-between">
                  <div>
                    <h1 className="text-2xl font-bold text-[#151320]">إدارة المستخدمين والحسابات</h1>
                    <p className="text-slate-500 text-sm mt-1">عرض وتحكم في جميع الحسابات ورتبهم (مسؤولين وطلاب)</p>
                  </div>
                  <button 
                    onClick={() => setShowAddUserModal(true)} 
                    className="px-4 py-2.5 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg shadow-[#4F26E9]/25 hover:shadow-[#4F26E9]/45 hover:-translate-y-0.5"
                  >
                    <UserPlus size={18} />
                    <span>إضافة حساب جديد</span>
                  </button>
                </div>

                <div className="bg-white rounded-3xl border border-[#ECE8F6] shadow-sm overflow-hidden card-3d">
                  <table className="w-full text-right border-collapse text-sm">
                    <thead>
                      <tr className="bg-[#F7F5FC] border-b border-[#ECE8F6] text-slate-600 font-bold">
                        <th className="py-4 px-6">المعرّف</th>
                        <th className="py-4 px-6">الاسم الكامل</th>
                        <th className="py-4 px-6">البريد الإلكتروني</th>
                        <th className="py-4 px-6">نوع الحساب (الدور)</th>
                        <th className="py-4 px-6 text-center">الإجراءات</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-[#ECE8F6] text-slate-700">
                      {filteredUsers.map((u) => (
                        <tr key={u.id} className="hover:bg-[#F7F5FC]/60 transition">
                          <td className="py-4 px-6 font-mono text-slate-500">#{u.id}</td>
                          <td className="py-4 px-6 font-semibold text-[#151320]">{u.name}</td>
                          <td className="py-4 px-6 text-slate-500">{u.email}</td>
                          <td className="py-4 px-6">
                            <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border ${
                              u.role === 'admin' 
                                ? 'bg-rose-50 text-rose-700 border-rose-200/60' 
                                : 'bg-[#F7F5FC] text-[#4F26E9] border-[#ECE8F6]'
                            }`}>
                              {u.role === 'admin' ? 'مسؤول (Admin)' : 'طالب (Student)'}
                            </span>
                          </td>
                          <td className="py-4 px-6 text-center">
                            <div className="flex items-center justify-center gap-1.5">
                              <button 
                                onClick={() => {
                                  setEditingUser(u);
                                  setEditUserName(u.name);
                                  setEditUserEmail(u.email);
                                  setEditUserPassword('');
                                  setEditUserRole(u.role);
                                }} 
                                className="p-2 text-[#4F26E9] hover:bg-[#F7F5FC] rounded-lg transition" 
                                title="تعديل"
                              >
                                <Pencil size={16} />
                              </button>
                              {u.id !== currentUser?.id && (
                                <button onClick={() => handleDeleteUser(u.id)} className="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="حذف">
                                  <Trash2 size={16} />
                                </button>
                              )}
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </main>
            )}

            {/* تبويب إدارة الطلاب */}
            {activeTab === 'students' && (
              <main className="p-8 space-y-6 max-w-7xl w-full mx-auto">
                <div className="flex items-center justify-between">
                  <div>
                    <h1 className="text-2xl font-bold text-[#151320]">قائمة وإدارة الطلاب</h1>
                    <p className="text-slate-500 text-sm mt-1">عرض وإدارة الحسابات برتبة طالب فقط</p>
                  </div>
                  <button 
                    onClick={() => setShowAddStudentModal(true)} 
                    className="px-4 py-2.5 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg shadow-[#4F26E9]/25 hover:shadow-[#4F26E9]/45 hover:-translate-y-0.5"
                  >
                    <UserPlus size={18} />
                    <span>إضافة طالب جديد</span>
                  </button>
                </div>

                <div className="bg-white rounded-3xl border border-[#ECE8F6] shadow-sm overflow-hidden card-3d">
                  {loadingStudents ? (
                    <div className="p-12 text-center text-slate-400">
                      <div className="w-8 h-8 border-4 border-[#8453FC] border-t-transparent rounded-full animate-spin mx-auto mb-3" />
                      جاري تحميل سجلات الطلاب...
                    </div>
                  ) : (
                    <table className="w-full text-right border-collapse text-sm">
                      <thead>
                        <tr className="bg-[#F7F5FC] border-b border-[#ECE8F6] text-slate-600 font-bold">
                          <th className="py-4 px-6">المعرّف</th>
                          <th className="py-4 px-6">الاسم الكامل</th>
                          <th className="py-4 px-6">البريد الجامعي</th>
                          <th className="py-4 px-6">الرتبة</th>
                          <th className="py-4 px-6 text-center">الإجراءات</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-[#ECE8F6] text-slate-700">
                        {filteredStudents.map((student) => (
                          <tr key={student.id} className="hover:bg-[#F7F5FC]/60 transition">
                            <td className="py-4 px-6 font-mono text-slate-500">#{student.id}</td>
                            <td className="py-4 px-6 font-semibold text-[#151320]">{student.name}</td>
                            <td className="py-4 px-6 text-slate-500">{student.email}</td>
                            <td className="py-4 px-6"><span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#F7F5FC] text-[#4F26E9] border border-[#ECE8F6]">طالب</span></td>
                            <td className="py-4 px-6 text-center">
                              <div className="flex items-center justify-center gap-1.5">
                                <button onClick={() => { setEditingStudent(student); setEditStudentName(student.name); setEditStudentEmail(student.email); setEditStudentPassword(''); }} className="p-2 text-[#4F26E9] hover:bg-[#F7F5FC] rounded-lg transition"><Pencil size={16} /></button>
                                <button onClick={() => handleDeleteStudent(student.id)} className="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition"><Trash2 size={16} /></button>
                              </div>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  )}
                </div>
              </main>
            )}

            {/* تبويب الكليات */}
            {activeTab === 'colleges' && (
              <main className="p-8 space-y-6 max-w-7xl w-full mx-auto">
                <div className="flex items-center justify-between">
                  <div>
                    <h1 className="text-2xl font-bold text-[#151320]">إدارة الكليات والأقسام</h1>
                    <p className="text-slate-500 text-sm mt-1">عرض وإدارة كليات جامعة المستقبل</p>
                  </div>
                  <button 
                    onClick={() => setShowAddCollegeModal(true)} 
                    className="px-4 py-2.5 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg shadow-[#4F26E9]/25 hover:shadow-[#4F26E9]/45 hover:-translate-y-0.5"
                  >
                    <FolderPlus size={18} />
                    <span>إضافة كلية جديدة</span>
                  </button>
                </div>

                {loadingColleges ? (
                  <div className="p-12 text-center text-slate-400">
                    <div className="w-8 h-8 border-4 border-[#8453FC] border-t-transparent rounded-full animate-spin mx-auto mb-3" />
                    جاري تحميل الكليات...
                  </div>
                ) : (
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {filteredColleges.map((college) => (
                      <div key={college.id} className="bg-white rounded-3xl border border-[#ECE8F6] p-6 shadow-sm card-3d flex flex-col justify-between">
                        <div>
                          <div className="flex items-start justify-between">
                            <div className="p-3 bg-[#F7F5FC] text-[#4F26E9] rounded-2xl mb-4 border border-[#ECE8F6]"><School size={22} /></div>
                            <span className="font-mono text-xs px-2.5 py-1 bg-[#F7F5FC] border border-[#ECE8F6] text-[#4F26E9] rounded-lg">#{college.id}</span>
                          </div>
                          <h3 className="font-bold text-[#151320] text-lg mb-2">{college.name}</h3>
                          <p className="text-xs text-slate-500 line-clamp-3 leading-relaxed">{college.description || 'لا يوجد وصف مضاف.'}</p>
                        </div>
                        <div className="flex items-center justify-end gap-2 pt-5 mt-5 border-t border-[#ECE8F6]">
                          <button onClick={() => { setEditingCollege(college); setEditCollegeName(college.name); setEditCollegeDesc(college.description || ''); }} className="p-2 text-[#4F26E9] hover:bg-[#F7F5FC] rounded-lg transition"><Pencil size={16} /></button>
                          <button onClick={() => handleDeleteCollege(college.id)} className="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition"><Trash2 size={16} /></button>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </main>
            )}

            {/* تبويب هيئة التدريس */}
            {activeTab === 'teachers' && (
              <main className="p-8 space-y-6 max-w-7xl w-full mx-auto">
                <div className="flex items-center justify-between">
                  <div>
                    <h1 className="text-2xl font-bold text-[#151320]">هيئة التدريس</h1>
                    <p className="text-slate-500 text-sm mt-1">إدارة الكادر الأكاديمي، الأقسام، والتخصصات</p>
                  </div>
                  <button 
                    onClick={() => setShowAddTeacherModal(true)} 
                    className="px-4 py-2.5 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg shadow-[#4F26E9]/25 hover:shadow-[#4F26E9]/45 hover:-translate-y-0.5"
                  >
                    <UserPlus size={18} />
                    <span>إضافة عضو جديد</span>
                  </button>
                </div>

                <div className="bg-white rounded-3xl border border-[#ECE8F6] shadow-sm overflow-hidden card-3d">
                  {loadingTeachers ? (
                    <div className="p-12 text-center text-slate-400">
                      <div className="w-8 h-8 border-4 border-[#8453FC] border-t-transparent rounded-full animate-spin mx-auto mb-3" />
                      جاري تحميل سجلات هيئة التدريس...
                    </div>
                  ) : (
                    <table className="w-full text-right border-collapse text-sm">
                      <thead>
                        <tr className="bg-[#F7F5FC] border-b border-[#ECE8F6] text-slate-600 font-bold">
                          <th className="py-4 px-6">المعرّف</th>
                          <th className="py-4 px-6">اسم الأكاديمي</th>
                          <th className="py-4 px-6">البريد</th>
                          <th className="py-4 px-6">الهاتف</th>
                          <th className="py-4 px-6">القسم</th>
                          <th className="py-4 px-6">التخصص</th>
                          <th className="py-4 px-6 text-center">الإجراءات</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-[#ECE8F6] text-slate-700">
                        {filteredTeachers.map((t) => (
                          <tr key={t.id} className="hover:bg-[#F7F5FC]/60 transition">
                            <td className="py-4 px-6 font-mono text-slate-500">#{t.id}</td>
                            <td className="py-4 px-6 font-semibold text-[#151320]">{t.name}</td>
                            <td className="py-4 px-6 text-slate-500">{t.email}</td>
                            <td className="py-4 px-6 text-slate-500 font-mono text-xs">{t.phone || '-'}</td>
                            <td className="py-4 px-6"><span className="px-3 py-1 rounded-full text-xs font-semibold bg-[#F7F5FC] text-[#4F26E9] border border-[#ECE8F6]">{t.department || 'عام'}</span></td>
                            <td className="py-4 px-6 text-slate-600 text-xs">{t.specialization || '-'}</td>
                            <td className="py-4 px-6 text-center">
                              <div className="flex items-center justify-center gap-1.5">
                                <button onClick={() => { setEditingTeacher(t); setEditTeacherName(t.name); setEditTeacherEmail(t.email); setEditTeacherPhone(t.phone || ''); setEditTeacherDept(t.department || ''); setEditTeacherSpec(t.specialization || ''); }} className="p-2 text-[#4F26E9] hover:bg-[#F7F5FC] rounded-lg transition"><Pencil size={16} /></button>
                                <button onClick={() => handleDeleteTeacher(t.id)} className="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition"><Trash2 size={16} /></button>
                              </div>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  )}
                </div>
              </main>
            )}

            {/* تبويب الأخبار */}
            {activeTab === 'news' && (
              <main className="p-8 space-y-6 max-w-7xl w-full mx-auto">
                <div className="flex items-center justify-between">
                  <div>
                    <h1 className="text-2xl font-bold text-[#151320]">أخبار وإعلانات الجامعة</h1>
                    <p className="text-slate-500 text-sm mt-1">نشر وإدارة التعاميم الأكاديمية</p>
                  </div>
                  <button 
                    onClick={() => setShowAddNewsModal(true)} 
                    className="px-4 py-2.5 bg-gradient-to-r from-[#4F26E9] to-[#8453FC] hover:from-[#431ED6] hover:to-[#7642F8] text-white rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg shadow-[#4F26E9]/25 hover:shadow-[#4F26E9]/45 hover:-translate-y-0.5"
                  >
                    <PlusCircle size={18} />
                    <span>نشر خبر جديد</span>
                  </button>
                </div>

                {loadingNews ? (
                  <div className="p-12 text-center text-slate-400">
                    <div className="w-8 h-8 border-4 border-[#8453FC] border-t-transparent rounded-full animate-spin mx-auto mb-3" />
                    جاري تحميل الأخبار...
                  </div>
                ) : (
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {filteredNews.map((news) => (
                      <div key={news.id} className="bg-white rounded-3xl border border-[#ECE8F6] p-6 shadow-sm card-3d flex flex-col justify-between">
                        <div>
                          <div className="flex items-center justify-between mb-3">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-[#F7F5FC] text-[#4F26E9] border border-[#ECE8F6] shadow-sm">
                              <Calendar size={13} className="text-[#8453FC]" /> {news.date || 'اليوم'}
                            </span>
                            <span className="font-mono text-xs text-slate-400">#{news.id}</span>
                          </div>
                          <h3 className="font-bold text-[#151320] text-lg mb-2">{news.title}</h3>
                          <p className="text-sm text-slate-600 whitespace-pre-line leading-relaxed">{news.content}</p>
                        </div>
                        <div className="flex items-center justify-end gap-2 pt-4 mt-4 border-t border-[#ECE8F6]">
                          <button onClick={() => { setEditingNews(news); setEditNewsTitle(news.title); setEditNewsContent(news.content); }} className="p-2 text-[#4F26E9] hover:bg-[#F7F5FC] rounded-lg transition"><Pencil size={16} /></button>
                          <button onClick={() => handleDeleteNews(news.id)} className="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition"><Trash2 size={16} /></button>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </main>
            )}
          </div>

          {/* نوافذ المستخدمين */}
          {showAddUserModal && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">إضافة حساب مستخدم جديد</h3>
                  <button onClick={() => setShowAddUserModal(false)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                {modalError && <div className="mt-4 p-3 bg-red-50 text-red-600 rounded-xl text-xs">{modalError}</div>}
                <form onSubmit={handleAddUser} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الاسم</label><input type="text" required value={newUserName} onChange={(e) => setNewUserName(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">البريد الإلكتروني</label><input type="email" required value={newUserEmail} onChange={(e) => setNewUserEmail(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">كلمة المرور</label><input type="text" value={newUserPassword} onChange={(e) => setNewUserPassword(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm font-mono" /></div>
                  <div>
                    <label className="block text-xs font-semibold text-slate-600 mb-1.5">نوع الحساب (الدور)</label>
                    <select value={newUserRole} onChange={(e) => setNewUserRole(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm">
                      <option value="student">طالب (Student)</option>
                      <option value="admin">مسؤول (Admin)</option>
                    </select>
                  </div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setShowAddUserModal(false)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">إنشاء الحساب</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {editingUser && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">تعديل بيانات الحساب</h3>
                  <button onClick={() => setEditingUser(null)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleUpdateUser} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الاسم</label><input type="text" required value={editUserName} onChange={(e) => setEditUserName(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">البريد الإلكتروني</label><input type="email" required value={editUserEmail} onChange={(e) => setEditUserEmail(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">كلمة مرور جديدة (اختياري)</label><input type="text" placeholder="••••••••" value={editUserPassword} onChange={(e) => setEditUserPassword(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm font-mono" /></div>
                  <div>
                    <label className="block text-xs font-semibold text-slate-600 mb-1.5">الدور في النظام</label>
                    <select value={editUserRole} onChange={(e) => setEditUserRole(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm">
                      <option value="student">طالب (Student)</option>
                      <option value="admin">مسؤول (Admin)</option>
                    </select>
                  </div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setEditingUser(null)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">حفظ التعديلات</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {/* نوافذ الأخبار */}
          {showAddNewsModal && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">نشر إعلان أو خبر</h3>
                  <button onClick={() => setShowAddNewsModal(false)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleAddNews} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">العنوان</label><input type="text" required value={newNewsTitle} onChange={(e) => setNewNewsTitle(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">المحتوى</label><textarea rows={5} required value={newNewsContent} onChange={(e) => setNewNewsContent(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setShowAddNewsModal(false)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">نشر</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {editingNews && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">تعديل الإعلان</h3>
                  <button onClick={() => setEditingNews(null)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleUpdateNews} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">العنوان</label><input type="text" required value={editNewsTitle} onChange={(e) => setEditNewsTitle(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">المحتوى</label><textarea rows={5} required value={editNewsContent} onChange={(e) => setEditNewsContent(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setEditingNews(null)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">حفظ</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {/* نوافذ المدرسين */}
          {showAddTeacherModal && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">إضافة مدرس</h3>
                  <button onClick={() => setShowAddTeacherModal(false)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleAddTeacher} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الاسم</label><input type="text" required value={newTeacherName} onChange={(e) => setNewTeacherName(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">البريد</label><input type="email" required value={newTeacherEmail} onChange={(e) => setNewTeacherEmail(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div className="grid grid-cols-2 gap-3">
                    <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الهاتف</label><input type="text" value={newTeacherPhone} onChange={(e) => setNewTeacherPhone(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm font-mono" /></div>
                    <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">القسم</label><input type="text" value={newTeacherDept} onChange={(e) => setNewTeacherDept(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  </div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">التخصص</label><input type="text" value={newTeacherSpec} onChange={(e) => setNewTeacherSpec(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setShowAddTeacherModal(false)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">حفظ</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {editingTeacher && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">تعديل المدرس</h3>
                  <button onClick={() => setEditingTeacher(null)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleUpdateTeacher} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الاسم</label><input type="text" required value={editTeacherName} onChange={(e) => setEditTeacherName(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">البريد</label><input type="email" required value={editTeacherEmail} onChange={(e) => setEditTeacherEmail(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div className="grid grid-cols-2 gap-3">
                    <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الهاتف</label><input type="text" value={editTeacherPhone} onChange={(e) => setNewTeacherPhone(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm font-mono" /></div>
                    <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">القسم</label><input type="text" value={editTeacherDept} onChange={(e) => setNewTeacherDept(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  </div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">التخصص</label><input type="text" value={editTeacherSpec} onChange={(e) => setEditTeacherSpec(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setEditingTeacher(null)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">حفظ</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {/* نوافذ الطلاب */}
          {showAddStudentModal && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">إضافة طالب جديد</h3>
                  <button onClick={() => setShowAddStudentModal(false)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleAddStudent} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الاسم</label><input type="text" required value={newStudentName} onChange={(e) => setNewStudentName(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">البريد</label><input type="email" required value={newStudentEmail} onChange={(e) => setNewStudentEmail(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">كلمة المرور</label><input type="text" value={newStudentPassword} onChange={(e) => setNewStudentPassword(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm font-mono" /></div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setShowAddStudentModal(false)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">حفظ</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {editingStudent && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">تعديل بيانات الطالب</h3>
                  <button onClick={() => setEditingStudent(null)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleUpdateStudent} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الاسم</label><input type="text" required value={editStudentName} onChange={(e) => setEditStudentName(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">البريد</label><input type="email" required value={editStudentEmail} onChange={(e) => setEditStudentEmail(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">كلمة مرور جديدة (اختياري)</label><input type="text" placeholder="••••••••" value={editStudentPassword} onChange={(e) => setEditStudentPassword(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm font-mono" /></div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setEditingStudent(null)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">حفظ</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {/* نوافذ الكليات */}
          {showAddCollegeModal && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">إضافة كلية</h3>
                  <button onClick={() => setShowAddCollegeModal(false)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleAddCollege} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">اسم الكلية</label><input type="text" required value={newCollegeName} onChange={(e) => setNewCollegeName(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الوصف</label><textarea rows={3} value={newCollegeDesc} onChange={(e) => setNewCollegeDesc(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setShowAddCollegeModal(false)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">إضافة</button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {editingCollege && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-[#151320]/60 backdrop-blur-sm p-4">
              <div className="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#ECE8F6] card-3d">
                <div className="flex items-center justify-between pb-4 border-b border-[#ECE8F6]">
                  <h3 className="font-bold text-[#151320] text-lg">تعديل الكلية</h3>
                  <button onClick={() => setEditingCollege(null)} className="text-slate-400 hover:text-slate-600"><X size={20} /></button>
                </div>
                <form onSubmit={handleUpdateCollege} className="space-y-4 mt-5">
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">اسم الكلية</label><input type="text" required value={editCollegeName} onChange={(e) => setEditCollegeName(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div><label className="block text-xs font-semibold text-slate-600 mb-1.5">الوصف</label><textarea rows={3} value={editCollegeDesc} onChange={(e) => setEditCollegeDesc(e.target.value)} className="w-full bg-[#F7F5FC] border border-[#ECE8F6] rounded-xl px-4 py-2.5 text-sm" /></div>
                  <div className="flex gap-3 pt-3">
                    <button type="button" onClick={() => setEditingCollege(null)} className="flex-1 py-2.5 rounded-xl border border-[#ECE8F6] text-sm">إلغاء</button>
                    <button type="submit" disabled={modalLoading} className="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#4F26E9] to-[#8453FC] text-white text-sm font-semibold shadow-md shadow-[#4F26E9]/25">حفظ</button>
                  </div>
                </form>
              </div>
            </div>
          )}
        </div>
      )}
    </>
  );
}
