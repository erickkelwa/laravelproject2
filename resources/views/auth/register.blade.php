<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center p-6">
        <div class="max-w-4xl w-full bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-white border-opacity-30">
            
            <!-- Left Side: Branding/Visual -->
            <div class="w-full md:w-1/2 p-8 md:p-12 text-white flex flex-col justify-center relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute top-0 left-0 w-full h-full bg-indigo-600 bg-opacity-40 z-0"></div>
                <div class="absolute -top-24 -left-24 w-48 h-48 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-pink-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center mb-8">
                        <svg class="w-10 h-10 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="text-2xl font-bold tracking-wider">EduManage</span>
                    </div>
                    <h2 class="text-4xl font-extrabold mb-4 leading-tight">Start your journey today.</h2>
                    <p class="text-lg mb-8 text-indigo-100">Create an account to manage your students, courses, and schedules all in one place with our modern platform.</p>
                    <div class="hidden md:flex space-x-2">
                       <div class="w-12 h-1 bg-white bg-opacity-80 rounded"></div>
                       <div class="w-4 h-1 bg-white bg-opacity-40 rounded"></div>
                       <div class="w-4 h-1 bg-white bg-opacity-40 rounded"></div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="w-full md:w-1/2 p-8 md:p-12 bg-white relative z-10">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">Create an Account</h3>
                    <p class="text-sm text-gray-500 mt-2">Sign up to get started</p>
                </div>

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4 bg-red-50 text-red-500 p-3 rounded-lg text-sm" :errors="$errors" />

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-200"
                            placeholder="John Doe">
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-200"
                            placeholder="you@example.com">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-200"
                            placeholder="••••••••">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm shadow-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-200"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                            Register
                        </button>
                    </div>

                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                                Log in here
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
