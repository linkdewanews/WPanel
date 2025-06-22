<?php

namespace App\Filament\Pages;

use App\Models\Site;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\PostHistory;
use App\Services\WordPressPublisher;
use Filament\Notifications\Notification;

class BulkPost extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static string $view = 'filament.pages.bulk-post';
    protected static ?string $navigationGroup = 'Manajemen Situs';
    protected static ?int $navigationSort = 2; // Untuk mengatur urutan menu

    // Properti publik untuk menampung data dari form
    public ?array $data = [];

    // Method ini berjalan saat halaman pertama kali dimuat
    public function mount(): void
    {
        // Inisialisasi form dengan nilai default (kosong)
        $this->form->fill();
    }

    // Method untuk mendefinisikan semua field form
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Judul Postingan')
                    ->required(),
                RichEditor::make('content')
                    ->label('Konten')
                    ->required(),
                // DAFTAR CENTANG UNTUK MEMILIH SITUS
                CheckboxList::make('site_ids')
                    ->label('Pilih Situs Tujuan')
                    ->options(Site::all()->pluck('name', 'id')) // Ambil data dari tabel 'sites'
                    ->columns(2) // Tampilkan dalam 2 kolom agar tidak terlalu panjang
                    ->required(),
                // Opsi status & jadwal yang sama seperti sebelumnya
                Select::make('status')
                    ->label('Status Publikasi')
                    ->options([
                        'publish' => 'Langsung Publikasikan',
                        'draft'   => 'Simpan sebagai Draft',
                    ])
                    ->default('publish')
                    ->required(),
                DateTimePicker::make('date')
                    ->label('Jadwalkan Publikasi (Opsional)'),
            ])
            ->statePath('data'); // Menghubungkan form ini dengan properti $data di atas
    }

    // Method untuk aksi posting. Akan kita isi di langkah berikutnya.
    public function publishBulk(): void
    {
        // 1. Ambil semua data yang sudah divalidasi dari form
        $formData = $this->form->getState();

        // 2. Siapkan service dan counter untuk laporan akhir
        $publisher = new WordPressPublisher();
        $successCount = 0;
        $failureCount = 0;

        // 3. Persiapkan data/payload postingan yang akan sama untuk semua situs
        $payload = [
            'title'   => $formData['title'],
            'content' => $formData['content'],
            'status'  => $formData['status'],
        ];

        // 4. Tambahkan logika untuk penjadwalan, sama seperti sebelumnya
        if (!empty($formData['date'])) {
            if (strtotime($formData['date']) > time()) {
                $payload['status'] = 'future';
            }
            $payload['date'] = $formData['date'];
        }

        // 5. Loop melalui setiap ID situs yang telah Anda centang di form
        foreach ($formData['site_ids'] as $siteId) {
            $site = Site::find($siteId);
            if ($site) {
                // Panggil service untuk mempublikasikan ke situs saat ini
                $result = $publisher->publish($site, $payload);

                // Cek hasilnya dan catat ke history
                if ($result) {
                    $successCount++;
                    PostHistory::create([
                        'site_id' => $site->id,
                        'wp_post_id' => $result['id'],
                        'post_title' => $formData['title'],
                        'post_url' => $result['link'],
                        'status' => 'Berhasil',
                    ]);
                } else {
                    $failureCount++;
                    PostHistory::create([
                        'site_id' => $site->id,
                        'wp_post_id' => null, // Tidak ada ID karena gagal
                        'post_title' => $formData['title'],
                        'post_url' => '#',
                        'status' => 'Gagal',
                    ]);
                }
            }
        }

        // 6. Berikan notifikasi rangkuman kepada pengguna
        if ($successCount > 0) {
            Notification::make()
                ->title('Proses Selesai')
                ->body("Berhasil memposting ke {$successCount} situs.")
                ->success()->send();
        }
        if ($failureCount > 0) {
            Notification::make()
                ->title('Beberapa Gagal')
                ->body("Gagal memposting ke {$failureCount} situs. Periksa halaman 'Post Histories' untuk detail.")
                ->danger()->send();
        }

        // 7. Kosongkan kembali form agar siap untuk postingan berikutnya
        $this->form->fill();
    }
}