<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class KanbanBoard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationGroup = 'Perbaikan';
    protected static ?string $navigationLabel = 'Kanban Board';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.kanban-board';

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdminLab() ?? false;
    }
}