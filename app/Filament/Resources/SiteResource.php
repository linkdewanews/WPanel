<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteResource\Pages;
use App\Models\PostHistory;
use App\Models\Site;
use App\Services\WordPressPublisher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nama Situs')->required()->maxLength(255),
                Forms\Components\TextInput::make('url')->label('URL Situs')->required()->url()->placeholder('https://www.domain.com')->maxLength(255),
                Forms\Components\TextInput::make('user')->label('Username')->required()->maxLength(255),
                Forms\Components\TextInput::make('pass')->label('Application Password')->password()->revealable()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Situs')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('url')->label('URL'),
                Tables\Columns\TextColumn::make('user')->label('Username')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime('d M Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('post')
                    ->label('Posting')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->form([
                        Forms\Components\Group::make()->schema([
                            Forms\Components\TextInput::make('title')->label('Judul Postingan')->required(),
                            Forms\Components\RichEditor::make('content')->label('Konten')->required(),
                        ])->columnSpan(2),
                        
                        Forms\Components\Group::make()->schema([
                            Forms\Components\FileUpload::make('featured_image')->label('Gambar Unggulan (Opsional)')->image()->disk('public'),
                            Forms\Components\CheckboxList::make('categories')
                                ->label('Kategori')
                                ->options(function (Site $record): array {
                                    $publisher = new WordPressPublisher();
                                    return $publisher->getCategories($record);
                                })
                                ->searchable(),
                            Forms\Components\Select::make('status')->label('Status')->options(['publish' => 'Publikasikan', 'draft' => 'Simpan Draft'])->default('publish')->required(),
                            Forms\Components\DateTimePicker::make('date')->label('Jadwalkan (Opsional)'),
                        ])->columnSpan(1),
                    ])
                    // Perhatikan, ->columns(3) yang error sudah dihapus dari akhir blok ->form() ini.
                    ->action(function (array $data, Site $record) {
                        $publisher = new WordPressPublisher();
                        $featuredImageId = null;

                        if (!empty($data['featured_image'])) {
                            $imagePath = storage_path('app/public/' . $data['featured_image']);
                            $imageName = basename($imagePath);
                            $featuredImageId = $publisher->uploadMedia($record, $imagePath, $imageName);
                            if (!$featuredImageId) {
                                Notification::make()->title('Gagal meng-upload gambar!')->danger()->send();
                                return;
                            }
                        }

                        $payload = [
                            'title'   => $data['title'],
                            'content' => $data['content'],
                            'status'  => $data['status'],
                            'categories' => $data['categories'] ?? [],
                        ];
                        if ($featuredImageId) {
                            $payload['featured_media'] = $featuredImageId;
                        }
                        if (!empty($data['date'])) {
                            if (strtotime($data['date']) > time()) {
                                $payload['status'] = 'future';
                            }
                            $payload['date'] = $data['date'];
                        }

                        $result = $publisher->publish($record, $payload);
                        
                        if ($result) {
                            PostHistory::create([
                                'site_id' => $record->id,
                                'wp_post_id' => $result['id'],
                                'post_title' => $data['title'],
                                'post_url' => $result['link'],
                                'status' => 'Berhasil',
                            ]);
                            Notification::make()->title('Aksi berhasil!')->body('"' . $data['title'] . '" telah diproses.')->success()->send();
                        } else {
                            PostHistory::create([
                                'site_id' => $record->id,
                                'wp_post_id' => null,
                                'post_title' => $data['title'],
                                'post_url' => '#',
                                'status' => 'Gagal',
                            ]);
                            Notification::make()->title('Gagal memposting')->body('Periksa log atau koneksi.')->danger()->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSites::route('/'),
            'create' => Pages\CreateSite::route('/create'),
            'edit' => Pages\EditSite::route('/{record}/edit'),
        ];
    }    
}