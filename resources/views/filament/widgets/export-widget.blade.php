<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Export Data LPPM
        </x-slot>

        <x-slot name="description">
            Download laporan data dalam format PDF untuk berbagai keperluan pelaporan dan analisis.
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <x-heroicon-o-document-text class="h-8 w-8 mr-3" />
                    <div>
                        <h3 class="text-lg font-semibold">Laporan Proposal</h3>
                        <p class="text-sm opacity-90">Export data proposal penelitian</p>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $this->getAction('export_proposals_pdf') }}
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <x-heroicon-o-clipboard-document-list class="h-8 w-8 mr-3" />
                    <div>
                        <h3 class="text-lg font-semibold">Laporan Kontrak</h3>
                        <p class="text-sm opacity-90">Export data kontrak penelitian</p>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $this->getAction('export_contracts_pdf') }}
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <x-heroicon-o-presentation-chart-line class="h-8 w-8 mr-3" />
                    <div>
                        <h3 class="text-lg font-semibold">Laporan Seminar</h3>
                        <p class="text-sm opacity-90">Export data seminar penelitian</p>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $this->getAction('export_seminars_pdf') }}
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <x-heroicon-o-banknotes class="h-8 w-8 mr-3" />
                    <div>
                        <h3 class="text-lg font-semibold">Laporan Publikasi</h3>
                        <p class="text-sm opacity-90">Export data pendanaan publikasi</p>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $this->getAction('export_publications_pdf') }}
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white md:col-span-2">
                <div class="flex items-center">
                    <x-heroicon-o-document-arrow-down class="h-8 w-8 mr-3" />
                    <div>
                        <h3 class="text-lg font-semibold">Laporan Komprehensif</h3>
                        <p class="text-sm opacity-90">Export laporan lengkap semua data LPPM</p>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $this->getAction('export_comprehensive_report') }}
                </div>
            </div>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center text-gray-600">
                <x-heroicon-o-information-circle class="h-5 w-5 mr-2" />
                <span class="text-sm">
                    Laporan akan didownload dalam format PDF. Pastikan browser Anda mengizinkan download file.
                </span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
