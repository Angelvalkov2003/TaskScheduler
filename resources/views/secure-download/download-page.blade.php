<x-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">File Ready for Download</h2>
                    
                    <p class="mb-4">Your file is ready to download. Click the button below to start the download.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('secure-download.download', $link->slug) }}" 
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Download File
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>