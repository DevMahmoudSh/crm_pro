<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Tajawal', sans-serif; }
</style>

<div class="min-h-screen bg-gray-50 flex items-center justify-center p-6" dir="rtl">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-gray-100">
        
        <h2 class="text-3xl font-bold text-gray-800 mb-2 text-center">تسجيل الدخول</h2>
        <p class="text-gray-500 text-center mb-8">مرحباً بك مجدداً، يرجى إدخال بياناتك</p>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 mr-1">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-transparent outline-none transition-all text-right"
                    placeholder="example@mail.com">
            </div>
            
            <div>
                <div class="flex justify-between mb-2 px-1">
                    <label class="text-sm font-medium text-gray-700">كلمة المرور</label>
                </div>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-transparent outline-none transition-all text-right"
                    placeholder="••••••••">
            </div>
            <div class="flex justify-between mb-2 px-1">
                <label class="text-sm font-medium text-gray-700"></label>
                    <a href="#" class="text-xs text-indigo-600 hover:underline">نسيت كلمة المرور؟</a>
                </div>

            <!-- @if ($errors->any())
                <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded-md">
                    <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                </div>
            @endif -->

            <button type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-200 transition-all duration-300 transform hover:-translate-y-1">
                دخول
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            ليس لديك حساب؟ <a href="#" class="text-indigo-600 font-bold hover:underline">إنشاء حساب جديد</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'خطأ في الدخول',
            text: '{{ $errors->first() }}',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#4f46e5', // Matches your Indigo-600
            padding: '2rem',
            background: '#ffffff',
            borderRadius: '20px',
            // RTL Settings
            direction: 'rtl',
            customClass: {
                title: 'text-xl font-bold text-gray-800',
                popup: 'rounded-3xl shadow-2xl'
            }
        });
    @endif
</script>