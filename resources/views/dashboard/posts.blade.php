<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Posts Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Posts</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage and track your posts</p>
                        </div>
                        <a
                            href="{{ route('dashboard.posts.create') }}"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors font-medium inline-flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Post
                        </a>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button
                                onclick="toggleFilterTab('search')"
                                id="tab-search"
                                class="filter-tab border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600 dark:text-indigo-400"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>
                            <button
                                onclick="toggleFilterTab('filters')"
                                id="tab-filters"
                                class="filter-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filters
                            </button>
                            <button
                                onclick="toggleFilterTab('sort')"
                                id="tab-sort"
                                class="filter-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                </svg>
                                Sorting
                            </button>
                        </nav>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('dashboard.posts') }}" id="filterForm" class="mb-6">
                        <!-- Search Tab -->
                        <div id="panel-search" class="filter-panel">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Quick Search
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Search by title, slug, content, ID..."
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Filters Tab -->
                        <div id="panel-filters" class="filter-panel hidden">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Status -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Status
                                        </label>
                                        <select name="status" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="">All Statuses</option>
                                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                                        </select>
                                    </div>

                                    <!-- Date Range -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Period
                                        </label>
                                        <div class="flex gap-2">
                                            <input
                                                type="date"
                                                name="date_from"
                                                value="{{ request('date_from') }}"
                                                placeholder="From"
                                                class="flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                                            >
                                            <span class="self-center text-gray-500">—</span>
                                            <input
                                                type="date"
                                                name="date_to"
                                                value="{{ request('date_to') }}"
                                                placeholder="To"
                                                class="flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sorting Tab -->
                        <div id="panel-sort" class="filter-panel hidden">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Sort By
                                        </label>
                                        <select name="sort_by" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                                            <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Title</option>
                                            <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                                            <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Updated Date</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Direction
                                        </label>
                                        <select name="sort_order" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                                            <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex justify-end gap-3">
                            <a href="{{ route('dashboard.posts') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                Reset
                            </a>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                Apply Filters
                            </button>
                        </div>
                    </form>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                            <div class="text-sm opacity-90">Total</div>
                            <div class="text-2xl font-bold">{{ $totalPosts }}</div>
                        </div>
                        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
                            <div class="text-sm opacity-90">Drafts</div>
                            <div class="text-2xl font-bold">{{ $draftPosts }}</div>
                        </div>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                            <div class="text-sm opacity-90">Published</div>
                            <div class="text-2xl font-bold">{{ $publishedPosts }}</div>
                        </div>
                        <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg p-4 text-white">
                            <div class="text-sm opacity-90">Archived</div>
                            <div class="text-2xl font-bold">{{ $archivedPosts }}</div>
                        </div>
                    </div>

                    <!-- Posts Table -->
                    @if($posts->total() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Title
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Slug
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Categories
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Created Date
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($posts as $post)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 text-xs font-semibold">
                                                            #{{ $post->id }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Str::limit($post->title, 50) }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-mono text-gray-500 dark:text-gray-400">{{ Str::limit($post->slug, 30) }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex flex-wrap gap-1">
                                                        @forelse($post->categories as $category)
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                                {{ $category->name }}
                                                            </span>
                                                        @empty
                                                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">No categories</span>
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="relative inline-block" id="status-container-{{ $post->id }}">
                                                        @if($post->status === 'published')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $post->id }}, event)">
                                                                Published
                                                            </span>
                                                        @elseif($post->status === 'draft')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $post->id }}, event)">
                                                                Draft
                                                            </span>
                                                        @else
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $post->id }}, event)">
                                                                Archived
                                                            </span>
                                                        @endif

                                                        <!-- Dropdown Menu -->
                                                        <div id="status-dropdown-{{ $post->id }}" class="hidden absolute z-50 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700" style="top: calc(100% + 0.25rem); left: 0;">
                                                            <div class="py-1">
                                                                <button onclick="updatePostStatus({{ $post->id }}, 'draft')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $post->status === 'draft' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Draft</span>
                                                                </button>
                                                                <button onclick="updatePostStatus({{ $post->id }}, 'published')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $post->status === 'published' ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Published</span>
                                                                </button>
                                                                <button onclick="updatePostStatus({{ $post->id }}, 'archived')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $post->status === 'archived' ? 'bg-gray-50 dark:bg-gray-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">Archived</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $post->created_at->format('Y-m-d H:i') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex items-center gap-2">
                                                        <a
                                                            href="{{ route('dashboard.posts.edit', $post->id) }}"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                                                        >
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Edit
                                                        </a>
                                                        <button
                                                            onclick="openPostModal({{ $post->id }})"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                                                        >
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            Details
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No posts found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try changing your search parameters or filters</p>
                        </div>
                    @endif

                    <!-- Pagination -->
                    @if($posts->hasPages())
                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Post Details Modal -->
    <div id="postModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="relative top-10 mx-auto p-0 w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-xl rounded-lg bg-white dark:bg-gray-800 mb-10">
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Post Details</h3>
                <button onclick="closePostModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex px-6" aria-label="Tabs">
                    <button
                        onclick="switchModalTab('info')"
                        id="modal-tab-info"
                        class="modal-tab border-b-2 border-indigo-500 py-4 px-4 text-sm font-medium text-indigo-600 dark:text-indigo-400"
                    >
                        Basic Information
                    </button>
                    <button
                        onclick="switchModalTab('content')"
                        id="modal-tab-content"
                        class="modal-tab border-b-2 border-transparent py-4 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        Content
                    </button>
                    <button
                        onclick="switchModalTab('translations')"
                        id="modal-tab-translations"
                        class="modal-tab border-b-2 border-transparent py-4 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        Translations
                    </button>
                </nav>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-6">
                <div id="modal-tab-content-info" class="modal-tab-content">
                    <div id="postModalContent" class="space-y-4 text-gray-900 dark:text-gray-100">
                        <!-- Content will be loaded via JavaScript -->
                    </div>
                </div>
                <div id="modal-tab-content-content" class="modal-tab-content hidden">
                    <div id="contentModalContent" class="space-y-4 text-gray-900 dark:text-gray-100">
                        <!-- Content will be loaded via JavaScript -->
                    </div>
                </div>
                <div id="modal-tab-content-translations" class="modal-tab-content hidden">
                    <div id="translationsModalContent" class="space-y-4 text-gray-900 dark:text-gray-100">
                        <!-- Content will be loaded via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle filter tabs
        function toggleFilterTab(tabName) {
            // Hide all panels
            document.querySelectorAll('.filter-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Show the appropriate panel
            document.getElementById('panel-' + tabName).classList.remove('hidden');

            // Activate the appropriate tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
        }

        // Switch modal tabs
        function switchModalTab(tabName) {
            // Hide all contents
            document.querySelectorAll('.modal-tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.modal-tab').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Show the appropriate content
            document.getElementById('modal-tab-content-' + tabName).classList.remove('hidden');

            // Activate the appropriate tab
            const activeTab = document.getElementById('modal-tab-' + tabName);
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
        }

        const postsData = {!! json_encode($posts->keyBy('id')->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'thumbnail' => $post->thumbnail,
                'meta_title' => $post->meta_title,
                'meta_description' => $post->meta_description,
                'meta_keywords' => $post->meta_keywords,
                'status' => $post->status,
                'translations' => $post->translations,
                'categories' => $post->categories->map(function($cat) {
                    return ['id' => $cat->id, 'name' => $cat->name];
                }),
                'created_at' => $post->created_at ? $post->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $post->updated_at ? $post->updated_at->format('Y-m-d H:i:s') : null,
            ];
        })) !!};

        function openPostModal(postId) {
            const post = postsData[postId];
            if (!post) return;

            const modal = document.getElementById('postModal');
            const content = document.getElementById('postModalContent');
            const contentTab = document.getElementById('contentModalContent');
            const translationsTab = document.getElementById('translationsModalContent');

            // Basic information
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Post ID</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">#${post.id}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Slug</p>
                        <p class="text-sm font-mono text-gray-900 dark:text-gray-100 break-all">${post.slug || 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 md:col-span-2">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Title</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">${post.title || 'N/A'}</p>
                    </div>
                    ${post.thumbnail ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 md:col-span-2">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Thumbnail</p>
                        <img src="${post.thumbnail}" alt="${post.title}" class="max-w-full h-auto rounded-lg">
                    </div>
                    ` : ''}
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Status</p>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full ${
                            post.status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                            post.status === 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                            'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                        }">${post.status || 'N/A'}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Categories</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            ${post.categories && post.categories.length > 0
                                ? post.categories.map(cat => `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">${cat.name}</span>`).join('')
                                : '<span class="text-xs text-gray-400 dark:text-gray-500 italic">No categories</span>'
                            }
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Created Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${post.created_at ? new Date(post.created_at).toLocaleString('en-US', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Updated Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${post.updated_at ? new Date(post.updated_at).toLocaleString('en-US', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'N/A'}</p>
                    </div>
                    ${post.meta_title ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Meta Title</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${post.meta_title}</p>
                    </div>
                    ` : ''}
                    ${post.meta_description ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Meta Description</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">${post.meta_description}</p>
                    </div>
                    ` : ''}
                    ${post.meta_keywords ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Meta Keywords</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">${post.meta_keywords}</p>
                    </div>
                    ` : ''}
                </div>
            `;

            // Content
            contentTab.innerHTML = `
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Post Content</p>
                    <div class="prose dark:prose-invert max-w-none text-gray-900 dark:text-gray-100">
                        ${post.content ? post.content.replace(/\n/g, '<br>') : '<p class="text-gray-400 italic">No content</p>'}
                    </div>
                </div>
            `;

            // Translations
            let translationsHtml = '<p class="text-gray-500 dark:text-gray-400">No translations</p>';
            if (post.translations && Object.keys(post.translations).length > 0) {
                translationsHtml = '<div class="space-y-4">';
                for (const [locale, translation] of Object.entries(post.translations)) {
                    translationsHtml += `
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3 uppercase">${locale.toUpperCase()}</h4>
                            <div class="space-y-2">
                                ${translation.title ? `<p><span class="text-xs font-medium text-gray-500 dark:text-gray-400">Title:</span> <span class="text-sm text-gray-900 dark:text-gray-100">${translation.title}</span></p>` : ''}
                                ${translation.content ? `<p><span class="text-xs font-medium text-gray-500 dark:text-gray-400">Content:</span> <span class="text-sm text-gray-900 dark:text-gray-100">${translation.content.substring(0, 200)}${translation.content.length > 200 ? '...' : ''}</span></p>` : ''}
                                ${translation.meta_title ? `<p><span class="text-xs font-medium text-gray-500 dark:text-gray-400">Meta Title:</span> <span class="text-sm text-gray-900 dark:text-gray-100">${translation.meta_title}</span></p>` : ''}
                                ${translation.meta_description ? `<p><span class="text-xs font-medium text-gray-500 dark:text-gray-400">Meta Description:</span> <span class="text-sm text-gray-900 dark:text-gray-100">${translation.meta_description}</span></p>` : ''}
                                ${translation.meta_keywords ? `<p><span class="text-xs font-medium text-gray-500 dark:text-gray-400">Meta Keywords:</span> <span class="text-sm text-gray-900 dark:text-gray-100">${translation.meta_keywords}</span></p>` : ''}
                            </div>
                        </div>
                    `;
                }
                translationsHtml += '</div>';
            }
            translationsTab.innerHTML = translationsHtml;

            // Reset tabs to first one
            switchModalTab('info');
            modal.classList.remove('hidden');
        }

        function closePostModal() {
            document.getElementById('postModal').classList.add('hidden');
        }

        // Close on click outside modal
        document.getElementById('postModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePostModal();
            }
        });

        // Open status dropdown menu
        let openDropdownId = null;

        function openStatusDropdown(postId, event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }

            // Close previously opened dropdown
            if (openDropdownId !== null && openDropdownId !== postId) {
                const prevDropdown = document.getElementById('status-dropdown-' + openDropdownId);
                if (prevDropdown) {
                    prevDropdown.classList.add('hidden');
                }
            }

            const dropdown = document.getElementById('status-dropdown-' + postId);
            if (dropdown) {
                const isHidden = dropdown.classList.contains('hidden');
                dropdown.classList.toggle('hidden');
                openDropdownId = isHidden ? postId : null;
            }
        }

        // Close dropdown on click outside
        document.addEventListener('click', function(event) {
            if (openDropdownId !== null) {
                const container = document.getElementById('status-container-' + openDropdownId);
                if (container && !container.contains(event.target)) {
                    document.getElementById('status-dropdown-' + openDropdownId).classList.add('hidden');
                    openDropdownId = null;
                }
            }
        });

        // Update post status
        function updatePostStatus(postId, newStatus) {
            const container = document.getElementById('status-container-' + postId);
            const badge = container.querySelector('.status-badge');
            const oldStatus = postsData[postId]?.status || badge.textContent.trim();

            // Close dropdown
            document.getElementById('status-dropdown-' + postId).classList.add('hidden');
            openDropdownId = null;

            // Disable badge during request
            badge.style.opacity = '0.6';
            badge.style.cursor = 'wait';
            badge.style.pointerEvents = 'none';

            // Show loading indicator
            const loadingIndicator = document.createElement('span');
            loadingIndicator.className = 'ml-2 inline-block';
            loadingIndicator.innerHTML = '<svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            container.appendChild(loadingIndicator);

            // TODO: Add route for updating post status
            // fetch(`/posts/${postId}/status`, {
            //     method: 'PATCH',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'X-CSRF-TOKEN': '{{ csrf_token() }}',
            //         'Accept': 'application/json'
            //     },
            //     body: JSON.stringify({ status: newStatus })
            // })
            // .then(response => response.json())
            // .then(data => {
            //     loadingIndicator.remove();
            //     badge.style.opacity = '1';
            //     badge.style.cursor = 'pointer';
            //     badge.style.pointerEvents = 'auto';
            //
            //     if (data.success) {
            //         if (postsData[postId]) {
            //             postsData[postId].status = newStatus;
            //         }
            //         showNotification('Post status updated successfully', 'success');
            //         setTimeout(() => {
            //             window.location.reload();
            //         }, 500);
            //     } else {
            //         showNotification(data.error || 'Error updating status', 'error');
            //     }
            // })
            // .catch(error => {
            //     loadingIndicator.remove();
            //     badge.style.opacity = '1';
            //     badge.style.cursor = 'pointer';
            //     badge.style.pointerEvents = 'auto';
            //     console.error('Error:', error);
            //     showNotification('Error updating status. Check your internet connection.', 'error');
            // });

            // Temporary notification (until route is added)
            setTimeout(() => {
                loadingIndicator.remove();
                badge.style.opacity = '1';
                badge.style.cursor = 'pointer';
                badge.style.pointerEvents = 'auto';
                showNotification('Status update function will be available after adding the route', 'error');
            }, 500);
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all transform translate-x-0 ${
                type === 'success'
                    ? 'bg-green-500 text-white'
                    : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Appearance animation
            setTimeout(() => {
                notification.classList.add('translate-x-0');
            }, 10);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }
    </script>

</x-app-layout>
