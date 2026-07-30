<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-document-chart-bar">
        <x-slot name="heading">
            Executive Business Reports Center
        </x-slot>
        <x-slot name="description">
            Generate and download live operational reports in PDF (.pdf), Excel (.xlsx), or Microsoft Word (.doc) formats.
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
            @foreach($this->getReports() as $report)
                <div class="fi-section rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 flex flex-col justify-between space-y-4 shadow-sm">
                    <div>
                        <div class="flex items-center gap-2 font-bold text-sm text-gray-900 dark:text-white">
                            <span class="text-base">📄</span>
                            <span>{{ $report['title'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                            {{ $report['description'] }}
                        </p>
                    </div>

                    <!-- Format Download Buttons -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-1">
                        <span class="text-[10px] font-extrabold uppercase text-gray-400">Export:</span>

                        <div class="flex items-center gap-1.5">
                            <!-- PDF Button -->
                            <x-filament::button
                                tag="a"
                                href="/admin-report-download/{{ $report['key'] }}/pdf"
                                target="_blank"
                                size="xs"
                                color="danger"
                                icon="heroicon-m-document-text"
                            >
                                PDF
                            </x-filament::button>

                            <!-- Excel Button -->
                            <x-filament::button
                                tag="a"
                                href="/admin-report-download/{{ $report['key'] }}/excel"
                                target="_blank"
                                size="xs"
                                color="success"
                                icon="heroicon-m-table-cells"
                            >
                                Excel
                            </x-filament::button>

                            <!-- Word Button -->
                            <x-filament::button
                                tag="a"
                                href="/admin-report-download/{{ $report['key'] }}/word"
                                target="_blank"
                                size="xs"
                                color="info"
                                icon="heroicon-m-document"
                            >
                                Word
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
