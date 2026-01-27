<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Dashboard - UBG Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        {{-- Warning Banner --}}
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="font-bold">⚠️ TESTING ENVIRONMENT ONLY</p>
                    <p class="text-sm">This page provides quick login bypass for testing. Remove before production deployment!</p>
                </div>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-8">Testing Dashboard</h1>

        {{-- Quick Actions --}}
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('test.login.superadmin') }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-6 shadow-lg transition-colors">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <div>
                        <h2 class="text-xl font-bold">Login as SuperAdmin</h2>
                        <p class="text-purple-200 text-sm">Full access to all features</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('test.login.admin') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-6 shadow-lg transition-colors">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <div>
                        <h2 class="text-xl font-bold">Login as Admin</h2>
                        <p class="text-blue-200 text-sm">University level access</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('test.logout') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg p-6 shadow-lg transition-colors">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <div>
                        <h2 class="text-xl font-bold">Logout</h2>
                        <p class="text-gray-300 text-sm">Clear current session</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- User List --}}
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-800 text-white px-6 py-4">
                <h2 class="text-xl font-bold">Available Users</h2>
                <p class="text-gray-300 text-sm">Click on any user to login as that user</p>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($user->role->value === 'superadmin') bg-purple-100 text-purple-800
                                @elseif($user->role->value === 'admin_universitas') bg-blue-100 text-blue-800
                                @elseif($user->role->value === 'admin_fakultas') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($user->unit_type && $user->unit_id)
                                {{ $user->unit_type->label() }} #{{ $user->unit_id }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('test.login.user', $user->id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                Login →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Direct Links --}}
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Admin Panel Quick Links</h2>
            <div class="grid md:grid-cols-4 gap-4">
                <a href="/admin" class="text-blue-600 hover:underline">📊 Dashboard</a>
                <a href="/admin/users" class="text-blue-600 hover:underline">👥 Users</a>
                <a href="/admin/fakultas" class="text-blue-600 hover:underline">🏛️ Fakultas</a>
                <a href="/admin/prodis" class="text-blue-600 hover:underline">📚 Prodi</a>
                <a href="/admin/articles" class="text-blue-600 hover:underline">📰 Articles</a>
                <a href="/admin/article-categories" class="text-blue-600 hover:underline">🏷️ Categories</a>
                <a href="/admin/pages" class="text-blue-600 hover:underline">📄 Pages</a>
                <a href="/admin/menus" class="text-blue-600 hover:underline">☰ Menus</a>
                <a href="/admin/settings" class="text-blue-600 hover:underline">⚙️ Settings</a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>UBG Profile Testing Dashboard | Environment: <strong>{{ app()->environment() }}</strong></p>
            <p class="mt-2 text-red-500 font-bold">🚨 Remember to remove test routes before production! 🚨</p>
        </div>
    </div>
</body>
</html>
