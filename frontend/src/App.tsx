import React, { useState } from 'react';
import { LogIn, Lock, Mail, AlertCircle, CheckCircle2, GraduationCap } from 'lucide-react';

interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'student';
}

interface LoginResponse {
  success: boolean;
  message: string;
  user?: User;
}

export default function App() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [currentUser, setCurrentUser] = useState<User | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    try {
      const response = await fetch('http://localhost/uws/api/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ email, password }),
      });

      const data: LoginResponse = await response.json();

      if (response.ok && data.success && data.user) {
        setCurrentUser(data.user);
      } else {
        setError(data.message || 'فشل تسجيل الدخول');
      }
    } catch {
      setError('تعذر الاتصال بالخادم، تأكد من تشغيل Apache في XAMPP');
    } finally {
      setLoading(false);
    }
  };

  if (currentUser) {
    return (
      <div className="min-h-screen bg-slate-100 flex items-center justify-center p-4 font-sans" dir="rtl">
        <div className="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center border border-slate-200">
          <div className="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckCircle2 size={32} />
          </div>
          <h2 className="text-2xl font-bold text-slate-800 mb-2">مرحباً بك، {currentUser.name}!</h2>
          <p className="text-slate-600 mb-6">تم ربط الواجهة بنظام UWS بنجاح</p>
          
          <div className="bg-slate-50 p-4 rounded-xl text-right space-y-2 mb-6 border border-slate-100 text-sm">
            <p className="text-slate-700"><strong>البريد:</strong> {currentUser.email}</p>
            <p className="text-slate-700"><strong>نوع الحساب:</strong> <span className="bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full font-semibold">{currentUser.role}</span></p>
            <p className="text-slate-700"><strong>رقم المعرّف (ID):</strong> {currentUser.id}</p>
          </div>

          <button
            onClick={() => setCurrentUser(null)}
            className="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl transition duration-200 font-medium"
          >
            تسجيل الخروج
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-slate-900 flex items-center justify-center p-4 font-sans" dir="rtl">
      <div className="bg-slate-800/90 border border-slate-700 p-8 rounded-3xl shadow-2xl max-w-md w-full backdrop-blur-md">
        <div className="text-center mb-8">
          <div className="inline-flex p-3 bg-blue-600/20 text-blue-400 rounded-2xl mb-3">
            <GraduationCap size={40} />
          </div>
          <h1 className="text-2xl font-bold text-white tracking-wide">نظام جامعة المستقبل (UWS)</h1>
          <p className="text-slate-400 text-sm mt-1">سجّل دخولك للوصول إلى البوابة الأكاديمية</p>
        </div>

        {error && (
          <div className="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl flex items-center gap-3 text-red-400 text-sm">
            <AlertCircle size={18} className="shrink-0" />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
              البريد الإلكتروني
            </label>
            <div className="relative">
              <Mail className="absolute right-3.5 top-3 text-slate-400" size={18} />
              <input
                type="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="admin@gmail.com"
                className="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 pr-11 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm"
              />
            </div>
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
              كلمة المرور
            </label>
            <div className="relative">
              <Lock className="absolute right-3.5 top-3 text-slate-400" size={18} />
              <input
                type="password"
                required
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
                className="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-2.5 pr-11 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm"
              />
            </div>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-semibold rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-blue-600/30 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm mt-2"
          >
            {loading ? (
              <span className="inline-block w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
            ) : (
              <>
                <LogIn size={18} />
                <span>دخول النظام</span>
              </>
            )}
          </button>
        </form>
      </div>
    </div>
  );
}
