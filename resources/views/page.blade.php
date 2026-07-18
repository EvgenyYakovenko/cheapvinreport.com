@php
    // Устанавливаем meta теги из перевода или дефолтных значений
    $metaTitle = $displayMetaTitle ?: $displayTitle;
    $metaDescription = $displayMetaDescription ?? Str::limit(strip_tags($displayContent), 160);
    $metaKeywords = $displayMetaKeywords ?? '';
@endphp

@include('header', ['metaTitle' => $metaTitle, 'metaDescription' => $metaDescription, 'metaKeywords' => $metaKeywords])

<!-- Page Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Page Content -->
            <article class="bg-white rounded-lg shadow-sm p-8 md:p-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-8 leading-tight">
                    {{ $displayTitle }}
                </h1>

                <!-- Page Content -->
                <div class="prose prose-lg max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-h1:text-3xl prose-h2:text-2xl prose-h3:text-xl prose-p:text-gray-700 prose-p:leading-relaxed prose-p:mb-4 prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-strong:text-gray-900 prose-strong:font-semibold prose-ul:list-disc prose-ul:pl-6 prose-ol:list-decimal prose-ol:pl-6 prose-li:mb-2 prose-blockquote:border-l-4 prose-blockquote:border-primary prose-blockquote:pl-4 prose-blockquote:italic prose-blockquote:text-gray-600">
                    {!! $displayContent !!}
                </div>
            </article>
        </div>
    </div>
</section>

@include('footer')
