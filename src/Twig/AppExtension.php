<?php declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate', [$this, 'truncateText']),
        ];
    }

    public function truncateText(string $text, int $limit = 10, string $trailingReplacement = ''): string
    {
        if (strlen($text) <= $limit) return $text;
        $text = substr($text, 0, $limit);
        return "$text$trailingReplacement";
    }
}