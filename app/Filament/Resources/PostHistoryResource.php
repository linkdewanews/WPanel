<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostHistoryResource\Pages;
use App\Models\PostHistory;
use App\Services\WordPressPublisher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostHistoryResource extends Resource
{
    protected static ?string $model = PostHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Laporan';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form tidak digunakan untuk membuat history baru secara manual
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y, H:i')->sortable(),
                Tables\Columns\TextColumn::make('post_title')->label('Judul Postingan')->searchable()->limit(40)->wrap(),
                Tables\Columns\TextColumn::make('site.name')->label('Situs')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'Berhasil' => 'heroicon-o-check-circle',
                        'Gagal' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Berhasil' => 'success',
                        'Gagal' => 'danger',
                    }),
            ])
            ->actions([
                // === AKSI EDIT BARU DIMULAI DI SINI ===
                Tables\Actions\Action::make('edit_post')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    // Hanya tampilkan tombol edit jika postingan berhasil dibuat & punya ID
                    ->visible(fn (PostHistory $record): bool => $record->status === 'Berhasil' && !empty($record->wp_post_id))
                    // Form untuk popup edit
                    ->form([
                        Forms\Components\TextInput::make('title')->label('Judul Postingan')->required(),
                        Forms\Components\RichEditor::make('content')->label('Konten')->required(),
                        // --- TAMBAHKAN FIELD BARU DI SINI ---
                        Forms\Components\CheckboxList::make('categories')
                            ->label('Kategori')
                            ->options(function (PostHistory $record): array {
                                // Ambil kategori dari situs yang bersangkutan
                                $publisher = new WordPressPublisher();
                                return $publisher->getCategories($record->site);
                            })
                            ->searchable(),
                    ])
                    // Logika untuk mengisi form dengan data dari WordPress
                    ->mountUsing(function (Forms\ComponentContainer $form, PostHistory $record) {
                        $publisher = new WordPressPublisher();
                        $postData = $publisher->getPost($record->site, $record->wp_post_id);
                    
                        if ($postData) {
                            $form->fill([
                                'title' => $postData['title']['rendered'],
                                'content' => $postData['content']['rendered'],
                                'categories' => $postData['categories'], // Isi kategori yang sudah ada
                            ]);
                        } else { /* ... kode sama ... */ }
                    })
                    // Logika untuk menyimpan perubahan
                    ->action(function (array $data, PostHistory $record) {
                        $publisher = new WordPressPublisher();
                        // Sertakan kategori dalam data yang dikirim untuk update
                        $result = $publisher->updatePost($record->site, $record->wp_post_id, $data); 
                    
                        if ($result) { /* ... kode sama ... */ } 
                        else { /* ... kode sama ... */ }
                    }),

                Tables\Actions\Action::make('view_post')
                    ->label('Lihat')
                    ->url(fn (PostHistory $record): string => $record->post_url)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->openUrlInNewTab()
                    ->visible(fn (PostHistory $record): bool => $record->status === 'Berhasil'),
            ])
            ->defaultSort('created_at', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostHistories::route('/'),
        ];
    }    
}