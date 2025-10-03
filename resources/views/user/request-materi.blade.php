@extends('layouts.user')

@section('title', 'Request Materi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Daftar Request Materi</h1>
        <p class="text-sm text-gray-500 mt-1">Berikut adalah daftar permintaan materi yang telah Anda buat.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($requests->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada request materi</h3>
            <p class="text-gray-500">Anda belum membuat request materi apapun.</p>
        </div>
    @else
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($requests as $request)
                    <li>
                        <a href="{{ route('user.request-materi.show', $request) }}" class="block hover:bg-gray-50">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-blue-600 truncate">{{ $request->judul_materi }}</p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ 
                                                $request->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                ($request->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                ($request->status === 'approved' ? 'bg-purple-100 text-purple-800' : 
                                                ($request->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'))) 
                                            }} capitalize">
                                            {{ str_replace('_', ' ', $request->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-calendar-alt mr-1.5 flex-shrink-0 text-gray-400"></i>
                                            {{ $request->created_at->format('d M Y') }}
                                        </p>
                                        @if($request->material)
                                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                <i class="fas fa-check-circle mr-1.5 flex-shrink-0 text-green-400"></i>
                                                Sudah diproses
                                            </p>
                                        @endif
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <i class="fas fa-{{ $request->prioritas === 'tinggi' ? 'arrow-up text-red-500' : ($request->prioritas === 'rendah' ? 'arrow-down text-green-500' : 'minus text-yellow-500') }} mr-1.5 flex-shrink-0"></i>
                                        {{ ucfirst($request->prioritas) }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        
        <div class="mt-4">
            {{-- {{ $requests->links() }} --}}
        </div>
    @endif
</div>
@endsection