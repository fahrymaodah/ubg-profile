<?php

namespace App\Enums;

enum MenuType: string
{
    case LINK = 'link';
    case ARTICLE = 'article';
    case DROPDOWN = 'dropdown';
    case PAGE = 'page';
    case LOGIN = 'login';
    case BUTTON = 'button';

    public function label(): string
    {
        return match ($this) {
            self::LINK => 'Link URL',
            self::ARTICLE => 'Artikel',
            self::DROPDOWN => 'Dropdown (Sub Menu)',
            self::PAGE => 'Halaman Statis',
            self::LOGIN => 'Login',
            self::BUTTON => 'Tombol (CTA)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LINK => 'heroicon-o-link',
            self::ARTICLE => 'heroicon-o-document-text',
            self::DROPDOWN => 'heroicon-o-chevron-down',
            self::PAGE => 'heroicon-o-document',
            self::LOGIN => 'heroicon-o-arrow-right-on-rectangle',
            self::BUTTON => 'heroicon-o-cursor-arrow-rays',
        };
    }

    public function requiresUrl(): bool
    {
        return $this === self::LINK || $this === self::BUTTON;
    }

    public function requiresArticle(): bool
    {
        return $this === self::ARTICLE;
    }

    public function requiresPage(): bool
    {
        return $this === self::PAGE;
    }

    public function canHaveChildren(): bool
    {
        return $this === self::DROPDOWN;
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toSelectOptions(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}
