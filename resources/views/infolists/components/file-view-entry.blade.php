<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $getState();
        $showAsLink = $shouldShowAsLink();
        $asModal = $shouldShowAsModal();
        $withModalEye = $shouldShowWithModalEye();
        $contained = $isContained();
        $lazyLoad = $shouldLazyLoad();
        $showFileSize = $shouldShowFileSize();
        $showFileCount = $shouldShowFileCount();
        $loadingSkeleton = $shouldShowLoadingSkeleton();
        $downloadable = $isDownloadable();
        $height = $getPreviewHeight();
        $gridColumns = $getGridColumns();
        $titleKey = $getTitleKey();
        $pathKey = $getPathKey();
        $dateKey = $getDateKey();
        
        // Handle different input types
        if ($state instanceof \Illuminate\Support\Collection) {
            // Eloquent collection - convert to array
            $files = $state->all();
        } elseif (is_array($state)) {
            // Check if it's an array of files or a single file entry
            if (isset($state[$pathKey]) || isset($state['file_path']) || isset($state['path'])) {
                // Single file as array - wrap it
                $files = [$state];
            } else {
                // Multiple files
                $files = $state;
            }
        } elseif (is_string($state) || is_object($state)) {
            // Single file path or object
            $files = [$state];
        } else {
            $files = [];
        }
        
        $files = array_filter($files);
        $files = array_values($files);
        
        // Determine grid class
        $fileCount = count($files);
        if ($gridColumns !== null) {
            $gridClass = match($gridColumns) {
                1 => 'grid-cols-1',
                2 => 'grid-cols-2',
                3 => 'grid-cols-2 md:grid-cols-3',
                4 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
                5 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
                6 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6',
                default => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
            };
        } else {
            // Auto grid based on file count
            $gridClass = $fileCount === 1 ? 'grid-cols-1' : 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5';
        }
        
        // Helper to extract value from file (array or object)
        $getFileValue = function($file, $key, $default = null) {
            if (is_array($file)) {
                return $file[$key] ?? $default;
            } elseif (is_object($file)) {
                return $file->{$key} ?? $default;
            }
            return $default;
        };
        
        // Helper to get file size
        $getFileSize = function($path) use ($entry) {
            return $entry->getFileSize($path);
        };
        };
    @endphp

    <div {{ $getExtraAttributeBag()->class(['fi-in-file-view w-full']) }}>
        {{-- File count badge --}}
        @if($showFileCount && $fileCount > 0)
            <div class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $fileCount }} {{ $fileCount === 1 ? __('file') : __('files') }}
            </div>
        @endif
        
        {{-- Loading skeleton --}}
        @if($loadingSkeleton && empty($files))
            <div class="grid {{ $gridClass }} gap-4 w-full">
                @for($i = 0; $i < 4; $i++)
                    <div class="w-full aspect-square p-4 rounded-2xl bg-gray-100 dark:bg-gray-800 animate-pulse">
                        <div class="w-12 h-12 mx-auto mb-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mx-auto"></div>
                    </div>
                @endfor
            </div>
        @elseif($fileCount > 0)
            <div class="grid {{ $gridClass }} gap-4 w-full">
                @foreach($files as $index => $file)
                    @php
                        // Extract file data based on keys
                        if (is_string($file)) {
                            $filePath = $file;
                            $fileTitle = basename($file);
                            $fileDate = null;
                        } else {
                            $filePath = $getFileValue($file, $pathKey) ?? $getFileValue($file, 'file_path') ?? $getFileValue($file, 'path') ?? '';
                            $fileTitle = $getFileValue($file, $titleKey) ?? $getFileValue($file, 'name') ?? $getFileValue($file, 'title') ?? basename($filePath);
                            $fileDate = $dateKey ? ($getFileValue($file, $dateKey) ?? $getFileValue($file, 'created_at')) : null;
                        }
                        
                        $fileUrl = $filePath ? $getFileUrl($filePath) : null;
                        $fileType = $filePath ? $getFileType($filePath) : 'other';
                        $fileIcon = $getFileIcon($fileType);
                        $canPreview = $filePath ? $canPreviewInBrowser($filePath) : false;
                        $fileSize = ($showFileSize && $filePath) ? $getFileSize($filePath) : null;
                        $modalId = 'file-preview-modal-' . $entry->getName() . '-' . $index;
                        
                        // Format date (only if dateKey is set)
                        $displayDate = null;
                        if ($dateKey && $fileDate) {
                            try {
                                $displayDate = \Carbon\Carbon::parse($fileDate)->format('M d, Y');
                            } catch (\Exception $e) {
                                $displayDate = null;
                            }
                        }
                    @endphp
                    
                    @if($fileUrl)
                        @if($showAsLink && $canPreview)
                            {{-- Compact list view with modal --}}
                            <div 
                                x-data="{ open: false }"
                                class="group cursor-pointer"
                            >
                                <div 
                                    @class([
                                        'flex items-center gap-3 p-3 rounded-lg transition-all',
                                        'bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-gray-200 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800' => $contained,
                                        'hover:text-primary-500' => !$contained,
                                    ])
                                    
                                    x-on:click="open = true"
                                >
                                    {{-- Icon --}}
                                    @svg($fileIcon, 'w-5 h-5 text-gray-400 group-hover:text-primary-500 flex-shrink-0')
                                    
                                    {{-- Filename --}}
                                    <span class="text-sm font-medium text-gray-900 dark:text-white truncate flex-1">{{ $fileTitle }}</span>
                                    
                                    @if($displayDate || $fileSize)
                                        {{-- Date and/or Size --}}
                                        <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                                            {{ $displayDate }}{{ $displayDate && $fileSize ? ' · ' : '' }}{{ $fileSize }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Modal --}}
                                <div
                                    x-show="open"
                                    style="display: none;"
                                    x-on:keydown.escape.window="open = false"
                                    class="fixed inset-0 z-50 overflow-y-auto"
                                    aria-labelledby="modal-title-{{ $index }}"
                                    role="dialog"
                                    aria-modal="true"
                                >
                                    {{-- Backdrop --}}
                                    <div 
                                        x-show="open"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                                        x-on:click="open = false"
                                    ></div>

                                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                        <div
                                            x-show="open"
                                            x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl"
                                            x-on:click.stop
                                        >
                                            {{-- Header --}}
                                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex items-center justify-between">
                                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white truncate pr-4" id="modal-title-{{ $index }}">
                                                    {{ $fileTitle }}
                                                </h3>
                                                <button 
                                                    type="button" 
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none flex-shrink-0"
                                                    x-on:click="open = false"
                                                >
                                                    @svg('heroicon-o-x-mark', 'w-6 h-6')
                                                </button>
                                            </div>

                                            {{-- Content --}}
                                            <div class="px-4 py-5 sm:p-6">
                                                <div class="w-full rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700" style="max-height: 60vh;">
                                                    @switch($fileType)
                                                        @case('image')
                                                            <img 
                                                                src="{{ $fileUrl }}" 
                                                                alt="{{ $fileTitle }}"
                                                                class="w-full h-auto object-contain bg-gray-100 dark:bg-gray-800"
                                                                @if($lazyLoad) loading="lazy" @endif
                                                            />
                                                            @break
                                                        
                                                        @case('video')
                                                            <video 
                                                                controls
                                                                class="w-full bg-gray-900"
                                                                style="max-height: 60vh;"
                                                            >
                                                                <source src="{{ $fileUrl }}" type="video/{{ pathinfo($filePath, PATHINFO_EXTENSION) }}">
                                                                @lang('Your browser does not support the video tag.')
                                                            </video>
                                                            @break
                                                        
                                                        @case('audio')
                                                            <div class="w-full p-8 bg-gray-50 dark:bg-gray-800">
                                                                <audio controls class="w-full">
                                                                    <source src="{{ $fileUrl }}" type="audio/{{ pathinfo($filePath, PATHINFO_EXTENSION) }}">
                                                                    @lang('Your browser does not support the audio tag.')
                                                                </audio>
                                                            </div>
                                                            @break
                                                        
                                                        @case('pdf')
                                                            <iframe 
                                                                src="{{ $fileUrl }}" 
                                                                class="w-full border-0"
                                                                style="height: 60vh;"
                                                                title="{{ $fileTitle }}"
                                                            ></iframe>
                                                            @break
                                                        
                                                        @case('text')
                                                            <div class="w-full p-4 bg-gray-50 dark:bg-gray-800 overflow-auto font-mono text-sm" style="max-height: 60vh;">
                                                                @php
                                                                    $disk = \Illuminate\Support\Facades\Storage::disk($getDiskName());
                                                                    $fileContent = '';
                                                                    try {
                                                                        if ($disk->exists($filePath)) {
                                                                            $fileContent = $disk->get($filePath);
                                                                        }
                                                                    } catch (\Exception $e) {
                                                                        $fileContent = 'Unable to read file content.';
                                                                    }
                                                                @endphp
                                                                <pre class="whitespace-pre-wrap break-words">{{ $fileContent }}</pre>
                                                            </div>
                                                            @break
                                                        
                                                        @default
                                                            <div class="w-full p-8 bg-gray-50 dark:bg-gray-800 flex flex-col items-center justify-center">
                                                                @svg($fileIcon, 'w-16 h-16 text-gray-400 mb-4')
                                                                <p class="text-gray-600 dark:text-gray-400">Preview not available</p>
                                                            </div>
                                                    @endswitch
                                                </div>
                                            </div>

                                            {{-- Footer --}}
                                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                                                <a 
                                                    href="{{ $fileUrl }}" 
                                                    target="_blank"
                                                    class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto"
                                                >
                                                    @svg('heroicon-o-arrow-top-right-on-square', 'w-4 h-4 mr-1')
                                                    @lang('Open')
                                                </a>
                                                @if($downloadable)
                                                    <a 
                                                        href="{{ $fileUrl }}" 
                                                        target="_blank"
                                                        download
                                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto"
                                                    >
                                                        @svg('heroicon-o-arrow-down-tray', 'w-4 h-4 mr-1')
                                                        @lang('Download')
                                                    </a>
                                                @endif
                                                <button 
                                                    type="button" 
                                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto"
                                                    x-on:click="open = false"
                                                >
                                                    @lang('Close')
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($showAsLink)
                            {{-- Simple link for non-previewable files --}}
                            <a 
                                href="{{ $fileUrl }}"
                                target="_blank"
                                @class([
                                    'group flex items-center gap-3 rounded-lg transition-all',
                                    'p-3 bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-gray-200 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800' => $contained,
                                    'p-0 hover:text-primary-500' => !$contained,
                                ])
                                
                            >
                                {{-- Icon --}}
                                @svg($fileIcon, 'w-5 h-5 text-gray-400 group-hover:text-primary-500 flex-shrink-0')
                                
                                {{-- Filename --}}
                                <span class="text-sm font-medium text-gray-900 dark:text-white truncate flex-1">{{ $fileTitle }}</span>
                                
                                @if($displayDate || $fileSize)
                                    {{-- Date and/or Size --}}
                                    <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                                        {{ $displayDate }}{{ $displayDate && $fileSize ? ' · ' : '' }}{{ $fileSize }}
                                    </span>
                                @endif
                            </a>
                        @elseif($asModal)
                                {{-- Click opens modal --}}
                                <div 
                                    x-data="{ open: false }"
                                    class="group cursor-pointer"
                                >
                                    <div 
                                        @class([
                                            'w-full aspect-square p-4 rounded-2xl transition-all flex flex-col items-center justify-center text-center',
                                            'bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-gray-200 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800' => $contained,
                                            'hover:text-primary-500' => !$contained,
                                        ])
                                        
                                        x-on:click="open = true"
                                    >
                                        {{-- Icon at top --}}
                                        <div class="flex-1 flex items-center justify-center">
                                            @svg($fileIcon, 'w-12 h-12 text-gray-400 group-hover:text-primary-500')
                                        </div>
                                        
                                        {{-- Filename --}}
                                        <span class="text-sm font-medium text-gray-900 dark:text-white truncate w-full mt-2">{{ $fileTitle }}</span>
                                        
                                        @if($displayDate || $fileSize)
                                            {{-- Date and/or Size --}}
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $displayDate }}{{ $displayDate && $fileSize ? ' · ' : '' }}{{ $fileSize }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Modal --}}
                                <div
                                    x-show="open"
                                    style="display: none;"
                                    x-on:keydown.escape.window="open = false"
                                    class="fixed inset-0 z-50 overflow-y-auto"
                                    aria-labelledby="modal-title"
                                    role="dialog"
                                    aria-modal="true"
                                >
                                    {{-- Backdrop --}}
                                    <div 
                                        x-show="open"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                                        x-on:click="open = false"
                                    ></div>

                                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                        <div
                                            x-show="open"
                                            x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl"
                                            x-on:click.stop
                                        >
                                            {{-- Header --}}
                                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex items-center justify-between">
                                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white truncate pr-4" id="modal-title">
                                                    {{ $fileTitle }}
                                                </h3>
                                                <button 
                                                    type="button" 
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none flex-shrink-0"
                                                    x-on:click="open = false"
                                                >
                                                    @svg('heroicon-o-x-mark', 'w-6 h-6')
                                                </button>
                                            </div>

                                            {{-- Content --}}
                                            <div class="px-4 py-5 sm:p-6">
                                                <div class="w-full rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700" style="max-height: 60vh;">
                                                    @switch($fileType)
                                                        @case('image')
                                                            <img 
                                                                src="{{ $fileUrl }}" 
                                                                alt="{{ $fileTitle }}"
                                                                class="w-full h-auto object-contain bg-gray-100 dark:bg-gray-800"
                                                            />
                                                            @break
                                                        
                                                        @case('video')
                                                            <video 
                                                                controls
                                                                class="w-full bg-gray-900"
                                                                style="max-height: 60vh;"
                                                            >
                                                                <source src="{{ $fileUrl }}" type="video/{{ pathinfo($filePath, PATHINFO_EXTENSION) }}">
                                                                @lang('Your browser does not support the video tag.')
                                                            </video>
                                                            @break
                                                        
                                                        @case('audio')
                                                            <div class="w-full p-8 bg-gray-50 dark:bg-gray-800">
                                                                <audio controls class="w-full">
                                                                    <source src="{{ $fileUrl }}" type="audio/{{ pathinfo($filePath, PATHINFO_EXTENSION) }}">
                                                                    @lang('Your browser does not support the audio tag.')
                                                                </audio>
                                                            </div>
                                                            @break
                                                        
                                                        @case('pdf')
                                                            <iframe 
                                                                src="{{ $fileUrl }}" 
                                                                class="w-full border-0"
                                                                style="height: 60vh;"
                                                                title="{{ $fileTitle }}"
                                                            ></iframe>
                                                            @break
                                                        
                                                        @case('text')
                                                            <div class="w-full p-4 bg-gray-50 dark:bg-gray-800 overflow-auto font-mono text-sm" style="max-height: 60vh;">
                                                                @php
                                                                    $disk = \Illuminate\Support\Facades\Storage::disk($getDiskName());
                                                                    $fileContent = '';
                                                                    try {
                                                                        if ($disk->exists($filePath)) {
                                                                            $fileContent = $disk->get($filePath);
                                                                        }
                                                                    } catch (\Exception $e) {
                                                                        $fileContent = 'Unable to read file content.';
                                                                    }
                                                                @endphp
                                                                <pre class="whitespace-pre-wrap break-words">{{ $fileContent }}</pre>
                                                            </div>
                                                            @break
                                                        
                                                        @default
                                                            <div class="w-full p-8 bg-gray-50 dark:bg-gray-800 flex flex-col items-center justify-center">
                                                                @svg($fileIcon, 'w-16 h-16 text-gray-400 mb-4')
                                                                <p class="text-gray-600 dark:text-gray-400">Preview not available</p>
                                                            </div>
                                                    @endswitch
                                                </div>
                                            </div>

                                            {{-- Footer --}}
                                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                                                <a 
                                                    href="{{ $fileUrl }}" 
                                                    target="_blank"
                                                    class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto"
                                                >
                                                    @svg('heroicon-o-arrow-top-right-on-square', 'w-4 h-4 mr-1')
                                                    @lang('Open')
                                                </a>
                                                @if($downloadable)
                                                    <a 
                                                        href="{{ $fileUrl }}" 
                                                        target="_blank"
                                                        download
                                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto"
                                                    >
                                                        @svg('heroicon-o-arrow-down-tray', 'w-4 h-4 mr-1')
                                                        @lang('Download')
                                                    </a>
                                                @endif
                                                <button 
                                                    type="button" 
                                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto"
                                                    x-on:click="open = false"
                                                >
                                                    @lang('Close')
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Show content directly inline --}}
                            <div 
                                @if($withModalEye && $canPreview) x-data="{ open: false }" @endif
                                @class([
                                    'w-full rounded-2xl overflow-hidden',
                                    'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700' => $contained,
                                ])
                                
                            >
                                    {{-- Title header --}}
                                    <div @class([
                                        'px-4 py-3',
                                        'border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700' => $contained,
                                    ])>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2 min-w-0">
                                                @svg($fileIcon, 'w-5 h-5 text-gray-500 flex-shrink-0')
                                                <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $fileTitle }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                @if($withModalEye && $canPreview)
                                                    {{-- Eye button to open modal --}}
                                                    <button
                                                        type="button"
                                                        class="text-gray-400 hover:text-primary-500"
                                                        title="@lang('Preview')"
                                                        x-on:click="open = true"
                                                    >
                                                        @svg('heroicon-o-eye', 'w-5 h-5')
                                                    </button>
                                                @endif
                                                @if($downloadable)
                                                    <a 
                                                        href="{{ $fileUrl }}" 
                                                        target="_blank"
                                                        download
                                                        class="text-gray-400 hover:text-primary-500"
                                                        title="@lang('Download')"
                                                    >
                                                        @svg('heroicon-o-arrow-down-tray', 'w-5 h-5')
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        @if($displayDate || $fileSize)
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                                                {{ $displayDate }}{{ $displayDate && $fileSize ? ' · ' : '' }}{{ $fileSize }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Content --}}
                                    <div class="w-full">
                                        @switch($fileType)
                                            @case('image')
                                                <img 
                                                    src="{{ $fileUrl }}" 
                                                    alt="{{ $fileTitle }}"
                                                    class="w-full h-auto object-contain bg-gray-100 dark:bg-gray-800"
                                                    @if($lazyLoad) loading="lazy" @endif
                                                />
                                                @break
                                            
                                            @case('video')
                                                <video 
                                                    controls
                                                    class="w-full bg-gray-900"
                                                >
                                                    <source src="{{ $fileUrl }}" type="video/{{ pathinfo($filePath, PATHINFO_EXTENSION) }}">
                                                    @lang('Your browser does not support the video tag.')
                                                </video>
                                                @break
                                            
                                            @case('audio')
                                                <div class="w-full p-4 bg-gray-50 dark:bg-gray-800">
                                                    <audio controls class="w-full">
                                                        <source src="{{ $fileUrl }}" type="audio/{{ pathinfo($filePath, PATHINFO_EXTENSION) }}">
                                                        @lang('Your browser does not support the audio tag.')
                                                    </audio>
                                                </div>
                                                @break
                                            
                                            @case('pdf')
                                                <iframe 
                                                    src="{{ $fileUrl }}" 
                                                    class="w-full border-0"
                                                    style="height: {{ $height }}; min-height: 400px;"
                                                    title="{{ $fileTitle }}"
                                                ></iframe>
                                                @break
                                            
                                            @case('text')
                                                <div class="w-full p-4 bg-gray-50 dark:bg-gray-800 overflow-auto font-mono text-sm" style="max-height: {{ $height }};">
                                                    @php
                                                        $disk = \Illuminate\Support\Facades\Storage::disk($getDiskName());
                                                        $fileContent = '';
                                                        try {
                                                            if ($disk->exists($filePath)) {
                                                                $fileContent = $disk->get($filePath);
                                                            }
                                                        } catch (\Exception $e) {
                                                            $fileContent = 'Unable to read file content.';
                                                        }
                                                    @endphp
                                                    <pre class="whitespace-pre-wrap break-words">{{ $fileContent }}</pre>
                                                </div>
                                                @break
                                            
                                            @default
                                                <div class="w-full p-8 bg-gray-50 dark:bg-gray-800 flex flex-col items-center justify-center">
                                                    @svg($fileIcon, 'w-16 h-16 text-gray-400 mb-4')
                                                    <p class="text-gray-600 dark:text-gray-400">Preview not available</p>
                                                    <a 
                                                        href="{{ $fileUrl }}" 
                                                        target="_blank"
                                                        class="mt-4 inline-flex items-center text-primary-600 hover:text-primary-500"
                                                    >
                                                        @svg('heroicon-o-arrow-top-right-on-square', 'w-4 h-4 mr-1')
                                                        @lang('Open File')
                                                    </a>
                                                </div>
                                        @endswitch
                                    </div>

                                    {{-- Modal for eye button --}}
                                    @if($withModalEye && $canPreview)
                                        <div
                                            x-show="open"
                                            style="display: none;"
                                            x-on:keydown.escape.window="open = false"
                                            class="fixed inset-0 z-50 overflow-y-auto"
                                            aria-labelledby="modal-title-{{ $index }}"
                                            role="dialog"
                                            aria-modal="true"
                                        >
                                            {{-- Backdrop --}}
                                            <div 
                                                x-show="open"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                                                x-on:click="open = false"
                                            ></div>

                                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                                <div
                                                    x-show="open"
                                                    x-transition:enter="ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave="ease-in duration-200"
                                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl"
                                                    x-on:click.stop
                                                >
                                                    {{-- Header --}}
                                                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex items-center justify-between">
                                                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white truncate pr-4" id="modal-title-{{ $index }}">
                                                            {{ $fileTitle }}
                                                        </h3>
                                                        <button 
                                                            type="button" 
                                                            class="text-gray-400 hover:text-gray-500 focus:outline-none flex-shrink-0"
                                                            x-on:click="open = false"
                                                        >
                                                            @svg('heroicon-o-x-mark', 'w-6 h-6')
                                                        </button>
                                                    </div>

                                                    {{-- Content --}}
                                                    <div class="px-4 py-5 sm:p-6">
                                                        <div class="w-full rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700" style="max-height: 60vh;">
                                                            @switch($fileType)
                                                                @case('image')
                                                                    <img 
                                                                        src="{{ $fileUrl }}" 
                                                                        alt="{{ $fileTitle }}"
                                                                        class="w-full h-auto object-contain bg-gray-100 dark:bg-gray-800"
                                                                        @if($lazyLoad) loading="lazy" @endif
                                                                    />
                                                                    @break
                                                                
                                                                @case('video')
                                                                    <video 
                                                                        controls
                                                                        class="w-full bg-gray-900"
                                                                        style="max-height: 60vh;"
                                                                    >
                                                                        <source src="{{ $fileUrl }}" type="video/{{ pathinfo($filePath, PATHINFO_EXTENSION) }}">
                                                                        @lang('Your browser does not support the video tag.')
                                                                    </video>
                                                                    @break
                                                                
                                                                @case('audio')
                                                                    <div class="w-full p-8 bg-gray-50 dark:bg-gray-800">
                                                                        <audio controls class="w-full">
                                                                            <source src="{{ $fileUrl }}" type="audio/{{ pathinfo($filePath, PATHINFO_EXTENSION) }}">
                                                                            @lang('Your browser does not support the audio tag.')
                                                                        </audio>
                                                                    </div>
                                                                    @break
                                                                
                                                                @case('pdf')
                                                                    <iframe 
                                                                        src="{{ $fileUrl }}" 
                                                                        class="w-full border-0"
                                                                        style="height: 60vh;"
                                                                        title="{{ $fileTitle }}"
                                                                    ></iframe>
                                                                    @break
                                                                
                                                                @case('text')
                                                                    <div class="w-full p-4 bg-gray-50 dark:bg-gray-800 overflow-auto font-mono text-sm" style="max-height: 60vh;">
                                                                        <pre class="whitespace-pre-wrap break-words">{{ $fileContent ?? '' }}</pre>
                                                                    </div>
                                                                    @break
                                                                
                                                                @default
                                                                    <div class="w-full p-8 bg-gray-50 dark:bg-gray-800 flex flex-col items-center justify-center">
                                                                        @svg($fileIcon, 'w-16 h-16 text-gray-400 mb-4')
                                                                        <p class="text-gray-600 dark:text-gray-400">Preview not available</p>
                                                                    </div>
                                                            @endswitch
                                                        </div>
                                                    </div>

                                                    {{-- Footer --}}
                                                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                                                        <a 
                                                            href="{{ $fileUrl }}" 
                                                            target="_blank"
                                                            class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto"
                                                        >
                                                            @svg('heroicon-o-arrow-top-right-on-square', 'w-4 h-4 mr-1')
                                                            @lang('Open')
                                                        </a>
                                                        @if($downloadable)
                                                            <a 
                                                                href="{{ $fileUrl }}" 
                                                                target="_blank"
                                                                download
                                                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto"
                                                            >
                                                                @svg('heroicon-o-arrow-down-tray', 'w-4 h-4 mr-1')
                                                                @lang('Download')
                                                            </a>
                                                        @endif
                                                        <button 
                                                            type="button" 
                                                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto"
                                                            x-on:click="open = false"
                                                        >
                                                            @lang('Close')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                    @else
                        <div @class([
                            'w-full aspect-square p-4 rounded-2xl flex flex-col items-center justify-center text-center',
                            'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700' => $contained,
                        ])>
                            @svg('heroicon-o-document', 'w-12 h-12 text-danger-500 mb-2')
                            <span class="text-sm text-danger-600 truncate w-full">{{ $fileTitle }}</span>
                            <span class="text-xs text-gray-500 mt-1">@lang('File not found')</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @elseif(!$loadingSkeleton)
            <div class="fi-in-file-placeholder text-gray-500 dark:text-gray-400 text-sm">
                {{ $getPlaceholder() ?? __('No file') }}
            </div>
        @endif
    </div>
</x-dynamic-component>
