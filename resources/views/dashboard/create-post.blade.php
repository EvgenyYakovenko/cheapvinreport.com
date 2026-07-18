<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Create Post
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('dashboard.posts.store') }}" id="postForm">
                @csrf

                <!-- Language tabs -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex -mb-px" aria-label="Tabs">
                            @foreach($locales as $localeKey => $localeData)
                                <button
                                    type="button"
                                    onclick="switchLanguageTab('{{ $localeKey }}')"
                                    id="tab-{{ $localeKey }}"
                                    class="language-tab {{ $loop->first ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }} whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                                >
                                    {{ $localeData['name'] }}
                                    @if($localeKey === $defaultLocale)
                                        <span class="ml-2 text-xs text-gray-400">(Main)</span>
                                    @endif
                                </button>
                            @endforeach
                            <div class="ml-auto flex items-center px-4">
                                <button
                                    type="button"
                                    onclick="openJsonEditor()"
                                    class="px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 border border-indigo-300 dark:border-indigo-700 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-900/20"
                                >
                                    JSON Language Editor
                                </button>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="flex gap-6">
                    <!-- Main column (left) -->
                    <div class="flex-1 space-y-6">
                        @foreach($locales as $localeKey => $localeData)
                            <div id="lang-panel-{{ $localeKey }}" class="language-panel {{ $loop->first ? '' : 'hidden' }}">
                                <!-- Post Title -->
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                                    <div class="p-6">
                                        <label for="title_{{ $localeKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Post Title
                                        </label>
                                        <input
                                            type="text"
                                            id="title_{{ $localeKey }}"
                                            name="title_{{ $localeKey }}"
                                            value="{{ old("title_{$localeKey}") }}"
                                            class="title-input block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-lg font-semibold"
                                            placeholder="Enter post title"
                                            data-locale="{{ $localeKey }}"
                                        >
                                        @error("title_{$localeKey}")
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Post Content -->
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                                    <div class="p-6">
                                        <label for="content_{{ $localeKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Content
                                        </label>
                                        <textarea
                                            id="content_{{ $localeKey }}"
                                            name="content_{{ $localeKey }}"
                                            rows="20"
                                            class="content-input block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 font-mono text-sm"
                                            placeholder="Enter post content (HTML supported)"
                                            data-locale="{{ $localeKey }}"
                                        >{{ old("content_{$localeKey}") }}</textarea>
                                        @error("content_{$localeKey}")
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            You can use HTML tags for formatting
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Slug (Permalink) - shared for all languages -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Permalink (Slug)
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ url('/blog') }}/</span>
                                    <input
                                        type="text"
                                        id="slug"
                                        name="slug"
                                        value="{{ old('slug') }}"
                                        required
                                        readonly
                                        class="flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 font-mono text-sm bg-gray-50 dark:bg-gray-900 cursor-not-allowed"
                                        placeholder="post-slug"
                                    >
                                    <button
                                        type="button"
                                        id="edit-slug-btn"
                                        onclick="enableSlugEditing()"
                                        class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700"
                                    >
                                        Edit
                                    </button>
                                </div>
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar column (right) -->
                    <div class="w-80 space-y-6">
                        <!-- Publishing -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Publishing</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Status
                                    </label>
                                    <select
                                        id="status"
                                        name="status"
                                        required
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                                    >
                                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <button
                                        type="submit"
                                        class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors font-medium"
                                    >
                                        Create Post
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Metaboxes for each language -->
                        @foreach($locales as $localeKey => $localeData)
                            <div id="meta-panel-{{ $localeKey }}" class="meta-panel {{ $loop->first ? '' : 'hidden' }}">
                                <!-- Post Thumbnail -->
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Post Thumbnail ({{ $localeData['name'] }})</h3>
                                    </div>
                                    <div class="p-6">
                                        <label for="thumbnail_{{ $localeKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Image URL
                                        </label>
                                        <input
                                            type="url"
                                            id="thumbnail_{{ $localeKey }}"
                                            name="thumbnail_{{ $localeKey }}"
                                            value="{{ old("thumbnail_{$localeKey}") }}"
                                            class="thumbnail-input block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-sm"
                                            placeholder="https://example.com/image.jpg"
                                            data-locale="{{ $localeKey }}"
                                        >
                                        <div id="thumbnail-preview-{{ $localeKey }}" class="mt-4 hidden">
                                            <img id="thumbnail-img-{{ $localeKey }}" src="" alt="Preview" class="w-full h-auto rounded-lg border border-gray-200 dark:border-gray-700">
                                        </div>
                                        <button
                                            type="button"
                                            onclick="removeThumbnail('{{ $localeKey }}')"
                                            id="remove-thumbnail-btn-{{ $localeKey }}"
                                            class="mt-2 hidden text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                        >
                                            Remove thumbnail
                                        </button>
                                    </div>
                                </div>

                                <!-- SEO Settings -->
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">SEO Settings ({{ $localeData['name'] }})</h3>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label for="meta_title_{{ $localeKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Meta Title
                                            </label>
                                            <input
                                                type="text"
                                                id="meta_title_{{ $localeKey }}"
                                                name="meta_title_{{ $localeKey }}"
                                                value="{{ old("meta_title_{$localeKey}") }}"
                                                maxlength="255"
                                                class="meta-title-input block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-sm"
                                                placeholder="Title for search engines"
                                                data-locale="{{ $localeKey }}"
                                            >
                                        </div>
                                        <div>
                                            <label for="meta_description_{{ $localeKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Meta Description
                                            </label>
                                            <textarea
                                                id="meta_description_{{ $localeKey }}"
                                                name="meta_description_{{ $localeKey }}"
                                                rows="3"
                                                class="meta-description-input block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-sm"
                                                placeholder="Description for search engines"
                                                data-locale="{{ $localeKey }}"
                                            >{{ old("meta_description_{$localeKey}") }}</textarea>
                                        </div>
                                        <div>
                                            <label for="meta_keywords_{{ $localeKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Meta Keywords
                                            </label>
                                            <input
                                                type="text"
                                                id="meta_keywords_{{ $localeKey }}"
                                                name="meta_keywords_{{ $localeKey }}"
                                                value="{{ old("meta_keywords_{$localeKey}") }}"
                                                class="meta-keywords-input block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-sm"
                                                placeholder="Keywords separated by commas"
                                                data-locale="{{ $localeKey }}"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Hidden field for JSON translations -->
                <input type="hidden" id="translations_json" name="translations_json" value="">
            </form>
        </div>
    </div>

    <!-- JSON Editor Modal -->
    <div id="jsonEditorModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-0 w-11/12 md:w-3/4 lg:w-2/3 shadow-xl rounded-lg bg-white dark:bg-gray-800 mb-10">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">JSON Translations Editor</h3>
                <button onclick="closeJsonEditor()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Copy the JSON below, send it to AI for translation, then paste the result and click "Apply"
                </p>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Current translations (for copying):
                    </label>
                    <textarea
                        id="currentTranslationsJson"
                        rows="10"
                        readonly
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm bg-gray-50 dark:bg-gray-900 font-mono text-sm text-white"
                    ></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Paste updated JSON (with translations):
                    </label>
                    <textarea
                        id="newTranslationsJson"
                        rows="10"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 font-mono text-sm"
                        placeholder='{"en": {"title": "...", "content": "..."}, "ru": {...}, "es": {...}}'
                    ></textarea>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeJsonEditor()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        onclick="applyJsonTranslations()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const defaultLocale = '{{ $defaultLocale }}';
        const locales = @json(array_keys($locales));
        let slugAutoGenerate = true;
        let currentLanguage = defaultLocale;

        // Switch language tabs
        function switchLanguageTab(locale) {
            // Hide all panels
            document.querySelectorAll('.language-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            document.querySelectorAll('.meta-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.language-tab').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Show required panels
            document.getElementById('lang-panel-' + locale).classList.remove('hidden');
            document.getElementById('meta-panel-' + locale).classList.remove('hidden');

            // Activate required tab
            const activeTab = document.getElementById('tab-' + locale);
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');

            currentLanguage = locale;
        }

        // Transliteration from Cyrillic to Latin
        function transliterate(text) {
            const translitMap = {
                'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
                'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
                'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
                'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
                'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
                'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo',
                'Ж': 'Zh', 'З': 'Z', 'И': 'I', 'Й': 'Y', 'К': 'K', 'Л': 'L', 'М': 'M',
                'Н': 'N', 'О': 'O', 'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U',
                'Ф': 'F', 'Х': 'H', 'Ц': 'Ts', 'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sch',
                'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu', 'Я': 'Ya'
            };

            return text.split('').map(function(char) {
                return translitMap[char] || char;
            }).join('');
        }

        // Auto-generate slug from current language title
        function generateSlug() {
            if (!slugAutoGenerate) return;

            const currentTitleInput = document.getElementById(`title_${currentLanguage}`);
            const slugField = document.getElementById('slug');
            let sourceTitle = currentTitleInput?.value?.trim() || '';

            if (!sourceTitle) {
                const defaultTitleInput = document.getElementById(`title_${defaultLocale}`);
                sourceTitle = defaultTitleInput?.value?.trim() || '';
            }

            if (!sourceTitle) {
                for (const locale of locales) {
                    const input = document.getElementById(`title_${locale}`);
                    if (input && input.value.trim()) {
                        sourceTitle = input.value.trim();
                        break;
                    }
                }
            }

            if (sourceTitle && slugField && slugField.readOnly) {
                let slug = transliterate(sourceTitle)
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');

                slugField.value = slug;
            }
        }

        // Enable manual slug editing
        function enableSlugEditing() {
            const slugField = document.getElementById('slug');
            const editBtn = document.getElementById('edit-slug-btn');

            slugField.readOnly = false;
            slugField.classList.remove('bg-gray-50', 'dark:bg-gray-900', 'cursor-not-allowed');
            slugField.classList.add('bg-white', 'dark:bg-gray-700');
            editBtn.style.display = 'none';
            slugAutoGenerate = false;
            slugField.focus();
        }

        // Auto-generate slug when entering any language title
        // Use event delegation for reliability
        document.addEventListener('input', function(e) {
            if (e.target && e.target.id && e.target.id.startsWith('title_')) {
                generateSlug();
            }
        });

        // Also add direct handler (in case delegation doesn't work)
        setTimeout(function() {
            locales.forEach(locale => {
                const titleInput = document.getElementById(`title_${locale}`);
                if (!titleInput) return;
                titleInput.addEventListener('input', function() {
                    generateSlug();
                });

                titleInput.addEventListener('blur', function() {
                    const slugField = document.getElementById('slug');
                    if (slugField && !slugField.value) {
                        generateSlug();
                    }
                });
            });
        }, 100);

        // Thumbnail preview for each language
        locales.forEach(locale => {
            const thumbnailInput = document.getElementById(`thumbnail_${locale}`);
            if (thumbnailInput) {
                thumbnailInput.addEventListener('input', function() {
                    const url = this.value;
                    const preview = document.getElementById(`thumbnail-preview-${locale}`);
                    const img = document.getElementById(`thumbnail-img-${locale}`);
                    const removeBtn = document.getElementById(`remove-thumbnail-btn-${locale}`);

                    if (url) {
                        img.src = url;
                        img.onerror = function() {
                            preview.classList.add('hidden');
                            removeBtn.classList.add('hidden');
                        };
                        img.onload = function() {
                            preview.classList.remove('hidden');
                            removeBtn.classList.remove('hidden');
                        };
                    } else {
                        preview.classList.add('hidden');
                        removeBtn.classList.add('hidden');
                    }
                });
            }
        });

        function removeThumbnail(locale) {
            document.getElementById(`thumbnail_${locale}`).value = '';
            document.getElementById(`thumbnail-preview-${locale}`).classList.add('hidden');
            document.getElementById(`remove-thumbnail-btn-${locale}`).classList.add('hidden');
        }

        // JSON editor
        function openJsonEditor() {
            // Collect current translations
            const translations = {};
            locales.forEach(locale => {
                translations[locale] = {
                    title: document.getElementById(`title_${locale}`)?.value || '',
                    content: document.getElementById(`content_${locale}`)?.value || '',
                    meta_title: document.getElementById(`meta_title_${locale}`)?.value || '',
                    meta_description: document.getElementById(`meta_description_${locale}`)?.value || '',
                    meta_keywords: document.getElementById(`meta_keywords_${locale}`)?.value || '',
                    thumbnail: document.getElementById(`thumbnail_${locale}`)?.value || ''
                };
            });

            // Show current translations
            document.getElementById('currentTranslationsJson').value = JSON.stringify(translations, null, 2);
            document.getElementById('newTranslationsJson').value = '';
            document.getElementById('jsonEditorModal').classList.remove('hidden');
        }

        function closeJsonEditor() {
            document.getElementById('jsonEditorModal').classList.add('hidden');
        }

        function applyJsonTranslations() {
            const jsonText = document.getElementById('newTranslationsJson').value.trim();

            if (!jsonText) {
                alert('Please paste JSON with translations');
                return;
            }

            try {
                const translations = JSON.parse(jsonText);

                // Apply translations to form fields
                locales.forEach(locale => {
                    if (translations[locale]) {
                        const data = translations[locale];

                        if (data.title !== undefined) {
                            const titleInput = document.getElementById(`title_${locale}`);
                            if (titleInput) titleInput.value = data.title;
                        }
                        if (data.content !== undefined) {
                            const contentInput = document.getElementById(`content_${locale}`);
                            if (contentInput) contentInput.value = data.content;
                        }
                        if (data.meta_title !== undefined) {
                            const metaTitleInput = document.getElementById(`meta_title_${locale}`);
                            if (metaTitleInput) metaTitleInput.value = data.meta_title;
                        }
                        if (data.meta_description !== undefined) {
                            const metaDescInput = document.getElementById(`meta_description_${locale}`);
                            if (metaDescInput) metaDescInput.value = data.meta_description;
                        }
                        if (data.meta_keywords !== undefined) {
                            const metaKeywordsInput = document.getElementById(`meta_keywords_${locale}`);
                            if (metaKeywordsInput) metaKeywordsInput.value = data.meta_keywords;
                        }
                        if (data.thumbnail !== undefined) {
                            const thumbnailInput = document.getElementById(`thumbnail_${locale}`);
                            if (thumbnailInput) {
                                thumbnailInput.value = data.thumbnail;
                                // Update preview
                                if (data.thumbnail) {
                                    const preview = document.getElementById(`thumbnail-preview-${locale}`);
                                    const img = document.getElementById(`thumbnail-img-${locale}`);
                                    const removeBtn = document.getElementById(`remove-thumbnail-btn-${locale}`);
                                    if (preview && img) {
                                        img.src = data.thumbnail;
                                        preview.classList.remove('hidden');
                                        removeBtn.classList.remove('hidden');
                                    }
                                }
                            }
                        }
                    }
                });

                // Save JSON to hidden field
                document.getElementById('translations_json').value = jsonText;

                closeJsonEditor();
                alert('Translations successfully applied!');
            } catch (e) {
                alert('JSON parsing error: ' + e.message);
            }
        }

        // Form validation
        document.getElementById('postForm').addEventListener('submit', function(e) {
            const slug = document.getElementById('slug').value.trim();
            let hasContent = false;

            locales.forEach(locale => {
                const titleValue = document.getElementById(`title_${locale}`)?.value?.trim() || '';
                const contentValue = document.getElementById(`content_${locale}`)?.value?.trim() || '';
                if (titleValue && contentValue) {
                    hasContent = true;
                }
            });

            if (!hasContent || !slug) {
                e.preventDefault();
                alert('Please fill title and content for at least one language and provide a slug');
                return false;
            }

            if (slug && !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug)) {
                e.preventDefault();
                alert('Slug can only contain lowercase Latin letters, numbers and hyphens');
                return false;
            }
        });

        // Close modal by clicking outside of it
        document.getElementById('jsonEditorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeJsonEditor();
            }
        });
    </script>
</x-app-layout>
