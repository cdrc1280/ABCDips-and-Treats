<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-document-chart-bar">
        <x-slot name="heading">
            Executive Business Reports Center
        </x-slot>
        <x-slot name="description">
            Generate and download live operational reports in PDF (.pdf), Excel (.xlsx), or Microsoft Word (.doc) formats. Optionally filter by date range.
        </x-slot>

        {{-- Date Range Filter --}}
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Start Date</label>
                    <input
                        type="date"
                        id="report-start-date"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500"
                    />
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">End Date</label>
                    <input
                        type="date"
                        id="report-end-date"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500"
                    />
                </div>
                <div>
                    <button
                        type="button"
                        id="report-clear-dates"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        Clear Dates
                    </button>
                </div>
            </div>
            <p id="report-date-error" class="hidden mt-2 text-xs text-red-600 dark:text-red-400 font-semibold">
                ⚠️ End date must be greater than or equal to start date.
            </p>
        </div>

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
                                :href="'/admin-report-download/' . $report['key'] . '/pdf'"
                                target="_blank"
                                size="xs"
                                color="danger"
                                icon="heroicon-m-document-text"
                                class="report-export-btn"
                                :data-key="$report['key']"
                                data-format="pdf"
                                onclick="handleReportDownload(event, this)"
                            >
                                PDF
                            </x-filament::button>

                            <!-- Excel Button -->
                            <x-filament::button
                                tag="a"
                                :href="'/admin-report-download/' . $report['key'] . '/excel'"
                                target="_blank"
                                size="xs"
                                color="success"
                                icon="heroicon-m-table-cells"
                                class="report-export-btn"
                                :data-key="$report['key']"
                                data-format="excel"
                                onclick="handleReportDownload(event, this)"
                            >
                                Excel
                            </x-filament::button>

                            <!-- Word Button -->
                            <x-filament::button
                                tag="a"
                                :href="'/admin-report-download/' . $report['key'] . '/word'"
                                target="_blank"
                                size="xs"
                                color="info"
                                icon="heroicon-m-document"
                                class="report-export-btn"
                                :data-key="$report['key']"
                                data-format="word"
                                onclick="handleReportDownload(event, this)"
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

<script>
function handleReportDownload(event, el) {
    event.preventDefault();
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    const errorEl = document.getElementById('report-date-error');

    // Validate date range
    if (startDate && endDate && endDate < startDate) {
        errorEl.classList.remove('hidden');
        return;
    }
    errorEl.classList.add('hidden');

    const key = el.dataset.key;
    const format = el.dataset.format;
    let url = `/admin-report-download/${key}/${format}`;
    const params = new URLSearchParams();
    if (startDate) params.set('start_date', startDate);
    if (endDate) params.set('end_date', endDate);
    if (params.toString()) url += '?' + params.toString();
    window.open(url, '_blank');
}

document.addEventListener('DOMContentLoaded', () => {
    const clearBtn = document.getElementById('report-clear-dates');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            document.getElementById('report-start-date').value = '';
            document.getElementById('report-end-date').value = '';
            document.getElementById('report-date-error').classList.add('hidden');
        });
    }
});
</script>
