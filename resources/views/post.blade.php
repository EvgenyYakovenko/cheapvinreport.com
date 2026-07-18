@php
    $blogUrl = \App\Support\LocaleRoute::route('blog');
    $homeUrl = \App\Support\LocaleRoute::route('index');
    $authorProfileUrl = \App\Support\LocaleRoute::route('author');
    $author = config('author', []);
    $authorName = $author['name'] ?? 'Author';
    $authorRole = $author['role'] ?? '';
    $authorBio = $author['bio'] ?? '';
    $authorUrl = $author['url'] ?? null;
    $authorAvatar = $author['avatar'] ?? null;
    $authorSameAs = isset($author['same_as']) && is_array($author['same_as']) ? $author['same_as'] : [];

    // Устанавливаем meta теги из перевода или дефолтных значений
    $metaTitle = $displayMetaTitle ?: $displayTitle;
    $metaDescription = $displayMetaDescription ?? Str::limit(strip_tags($displayContent), 160);
    $metaKeywords = $displayMetaKeywords ?? '';

    $thumbnailUrl = null;
    if (!empty($displayThumbnail)) {
        if (Str::startsWith($displayThumbnail, ['http://', 'https://'])) {
            $thumbnailUrl = $displayThumbnail;
        } else {
            $thumbnailUrl = url('/' . ltrim($displayThumbnail, '/'));
        }
    }

    // Микроразметка BlogPosting: description до 250 символов без HTML
    $blogPostingSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $metaTitle,
        'description' => Str::limit(strip_tags($displayContent), 250),
        'image' => $thumbnailUrl ?: null,
        'author' => array_filter([
            '@type' => 'Person',
            'name' => $authorName,
            'url' => $authorProfileUrl,
            'sameAs' => !empty($authorSameAs) ? $authorSameAs : null,
        ], function ($value) {
            return $value !== null && $value !== '';
        }),
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'CheapVINReport',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/favicon/apple-touch-icon.png'),
            ],
        ],
        'datePublished' => $post->created_at?->toIso8601String(),
        'dateModified' => ($post->updated_at ?? $post->created_at)?->toIso8601String(),
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => url()->current(),
        ],
        'url' => url()->current(),
        'inLanguage' => str_replace('_', '-', app()->getLocale()),
    ], function ($value) {
        return $value !== null;
    });
@endphp

@include('header', [
    'metaTitle' => $metaTitle,
    'metaDescription' => $metaDescription,
    'metaKeywords' => $metaKeywords,
    'seoImage' => $thumbnailUrl,
    'blogPostingSchema' => $blogPostingSchema,
])

<!-- Post Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-600">
                    <li>
                        <a href="{{ $homeUrl }}" class="hover:text-primary transition-colors duration-200">
                            {{ __('index.blog.home') }}
                        </a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <a href="{{ $blogUrl }}" class="hover:text-primary transition-colors duration-200">
                            {{ __('index.blog.title') }}
                        </a>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li class="text-gray-900 font-medium" aria-current="page">
                        {{ Str::limit($displayTitle, 50) }}
                    </li>
                </ol>
            </nav>

            <!-- Post Content -->
            <article class="bg-white rounded-lg shadow-sm p-8 md:p-12">
                <!-- Post Header -->
                <header class="mb-8 pb-6 border-b border-gray-200">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                        {{ $displayTitle }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        <time class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $post->created_at->format('d.m.Y H:i') }}
                        </time>
                        @if($post->updated_at && $post->updated_at != $post->created_at)
                            <time class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                {{ __('index.blog.updated') }}: {{ $post->updated_at->format('d.m.Y H:i') }}
                            </time>
                        @endif
                    </div>
                </header>

                <!-- Table of Contents -->
                <div id="table-of-contents" class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200 hidden">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        {{ __('index.blog.table_of_contents') }}
                    </h2>
                    <nav id="toc-list" class="space-y-2"></nav>
                </div>

                <!-- Post Content -->
                <div id="post-content" class="prose prose-lg max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-h1:text-3xl prose-h2:text-2xl prose-h3:text-xl prose-p:text-gray-700 prose-p:leading-relaxed prose-p:mb-4 prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-strong:text-gray-900 prose-strong:font-semibold prose-ul:list-disc prose-ul:pl-6 prose-ol:list-decimal prose-ol:pl-6 prose-li:mb-2 prose-blockquote:border-l-4 prose-blockquote:border-primary prose-blockquote:pl-4 prose-blockquote:italic prose-blockquote:text-gray-600">
                    {!! $displayContent !!}
                </div>

                <!-- Author -->
                <div class="mt-10 rounded-xl border border-gray-200 bg-gray-50 p-6">
                    <div class="flex items-start gap-4">
                        @if(!empty($authorAvatar))
                            <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" class="h-14 w-14 rounded-full object-cover" loading="lazy">
                        @else
                            <div class="h-14 w-14 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-lg">
                                {{ strtoupper(substr($authorName, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            @if(!empty($authorProfileUrl))
                                <a href="{{ $authorProfileUrl }}" class="text-lg font-semibold text-gray-900 hover:text-primary transition-colors">
                                    {{ $authorName }}
                                </a>
                            @else
                                <p class="text-lg font-semibold text-gray-900">{{ $authorName }}</p>
                            @endif
                            @if(!empty($authorRole))
                                <p class="text-sm text-gray-600 mt-1">{{ $authorRole }}</p>
                            @endif
                            @if(!empty($authorBio))
                                <p class="text-sm text-gray-700 mt-3">{{ $authorBio }}</p>
                            @endif
                            @if(!empty($authorUrl))
                                <p class="text-xs text-gray-500 mt-3">
                                    <a href="{{ $authorUrl }}" target="_blank" rel="noopener nofollow" class="hover:text-primary">
                                        {{ $authorUrl }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Categories (если есть) -->
                @if($post->categories && $post->categories->count() > 0)
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">
                            {{ __('index.blog.categories') }}:
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->categories as $category)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors duration-200">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Back to Blog -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ $blogUrl }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('index.blog.back_to_blog') }}
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>

@include('footer')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const postContent = document.getElementById('post-content');
    const tocContainer = document.getElementById('table-of-contents');
    const tocList = document.getElementById('toc-list');
    
    if (!postContent) return;
    
    // Находим все заголовки h2-h6 в контенте
    const headings = postContent.querySelectorAll('h2, h3, h4, h5, h6');
    
    if (headings.length === 0) {
        return; // Нет заголовков - скрываем оглавление
    }
    
    // Показываем оглавление
    tocContainer.classList.remove('hidden');
    
    let tocHTML = '';
    let previousLevel = 1;
    
    headings.forEach((heading, index) => {
        const level = parseInt(heading.tagName.charAt(1));
        const id = 'heading-' + index;
        const text = heading.textContent.trim();
        
        // Добавляем id к заголовку для якоря
        heading.id = id;
        
        // Добавляем иконку якоря к заголовку
        const anchor = document.createElement('a');
        anchor.href = '#' + id;
        anchor.className = 'toc-anchor ml-2 text-gray-400 hover:text-primary transition-colors opacity-0 group-hover:opacity-100';
        anchor.innerHTML = '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>';
        
        // Обертываем заголовок в группу для hover эффекта
        heading.classList.add('group', 'relative');
        heading.appendChild(anchor);
        
        // Определяем отступ для вложенности (в пикселях)
        const paddingLeft = (level - 2) * 16; // h2 = 0px, h3 = 16px, h4 = 32px и т.д.
        
        // Добавляем класс для стилизации в зависимости от уровня
        const levelClasses = {
            2: 'text-base font-semibold',
            3: 'text-sm font-medium',
            4: 'text-sm font-normal',
            5: 'text-xs font-normal',
            6: 'text-xs font-normal'
        };
        
        tocHTML += `
            <a href="#${id}" 
               class="toc-link block ${levelClasses[level] || 'text-sm'} text-gray-600 hover:text-primary hover:bg-gray-100 rounded px-2 py-1 transition-colors duration-200"
               style="padding-left: ${paddingLeft}px;"
               data-level="${level}">
                ${text}
            </a>
        `;
        
        previousLevel = level;
    });
    
    tocList.innerHTML = tocHTML;
    
    // Плавная прокрутка к якорям
    document.querySelectorAll('.toc-link, .toc-anchor').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const headerOffset = 100; // Отступ сверху для фиксированного хедера
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Подсвечиваем активный пункт в оглавлении
                document.querySelectorAll('.toc-link').forEach(l => {
                    l.classList.remove('text-primary', 'bg-primary-50', 'font-semibold');
                });
                this.classList.add('text-primary', 'bg-primary-50', 'font-semibold');
            }
        });
    });
    
    // Подсветка активного пункта при прокрутке
    let currentActive = null;
    const observerOptions = {
        root: null,
        rootMargin: '-100px 0px -66%',
        threshold: 0
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.id;
                const tocLink = document.querySelector(`.toc-link[href="#${id}"]`);
                
                if (tocLink && currentActive !== tocLink) {
                    // Убираем подсветку с предыдущего
                    if (currentActive) {
                        currentActive.classList.remove('text-primary', 'bg-primary-50', 'font-semibold');
                    }
                    
                    // Подсвечиваем текущий
                    tocLink.classList.add('text-primary', 'bg-primary-50', 'font-semibold');
                    currentActive = tocLink;
                }
            }
        });
    }, observerOptions);
    
    // Наблюдаем за всеми заголовками
    headings.forEach(heading => {
        observer.observe(heading);
    });
});
</script>

<style>
/* Плавная прокрутка для всех якорей */
html {
    scroll-behavior: smooth;
}

/* Стили для якорей в заголовках */
.prose h2.group:hover .toc-anchor,
.prose h3.group:hover .toc-anchor,
.prose h4.group:hover .toc-anchor,
.prose h5.group:hover .toc-anchor,
.prose h6.group:hover .toc-anchor {
    opacity: 1;
}

.prose .toc-anchor {
    text-decoration: none;
}
</style>
