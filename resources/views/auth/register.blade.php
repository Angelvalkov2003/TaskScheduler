<x-layout>
    <form action="{{ route('register') }}" method="POST" class="max-w-sm mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200">
        @csrf
  
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Register for an Account</h2>
  
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
            <input 
                type="text"
                name="name"
                required
                value="{{ old('name') }}"
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                placeholder="UserName"
            >
        </div>
  
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
            <input 
                type="email"
                name="email"
                required
                value="{{ old('email') }}"
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                placeholder="email@gmail.com"
  
            >
        </div>
  
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
            <input 
                type="password"
                name="password"
                required
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                placeholder="*******"
            >
        </div>
  
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password:</label>
            <input 
                type="password"
                name="password_confirmation"
                required
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                placeholder="*******"
            >
        </div>
  
        <button type="submit" class="w-full py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-200">
            Register
        </button>
  
        @if ($errors->any())
            <ul class="mt-4 px-4 py-2 bg-red-100 border border-red-400 rounded-lg">
                @foreach ($errors->all() as $error)
                    <li class="my-2 text-red-500">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
  
    </form>
  </x-layout>