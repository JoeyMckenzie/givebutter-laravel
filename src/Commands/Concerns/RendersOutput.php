<?php

declare(strict_types=1);

namespace Givebutter\Laravel\Commands\Concerns;

use Givebutter\Laravel\Enums\BadgeColor;

use function Termwind\render;

trait RendersOutput
{
    public function infoColumn(string $content): void
    {
        $this->column(BadgeColor::INFO, $content);
    }

    public function errorColumn(string $content): void
    {
        $this->column(BadgeColor::ERROR, $content);
    }

    public function warnColumn(string $content): void
    {
        $this->column(BadgeColor::WARN, $content);
    }

    public function infoBadge(string $content): void
    {
        $this->textWithBadge(BadgeColor::INFO, $content);
    }

    public function errorBadge(string $content): void
    {
        $this->textWithBadge(BadgeColor::ERROR, $content);
    }

    public function warnBadge(string $content): void
    {
        $this->textWithBadge(BadgeColor::WARN, $content);
    }

    public function lineBreak(): void
    {
        render('<div></div>');
    }

    public function errorMessage(string $header, string $reason): void
    {
        render(
            <<<HTML
                <div class="flex mx-2 max-w-150">
                    <span>
                        {$header}
                    </span>
                    <span class="flex-1 content-repeat-[.] text-gray ml-1"></span>
                        <span class="ml-1 text-gray">
                           $reason
                        </span>
                </div>
            HTML
        );
    }

    private function column(BadgeColor $level, string $content): void
    {
        $parsed = htmlspecialchars($content);
        $bgBadgeColor = $this->getBadgeColor($level);

        render(
            <<<HTML
                <div class="my-1">
                    <span class="ml-2 px-1 bg-$bgBadgeColor-500 font-bold">$level->value</span>
                    <span class="ml-1">
                        $parsed
                    </span>
                </div>
            HTML
        );
    }

    private function getBadgeColor(BadgeColor $type): string
    {
        return match ($type) {
            BadgeColor::INFO => 'blue',
            BadgeColor::ERROR => 'red',
            BadgeColor::WARN => 'orange',
        };
    }

    private function textWithBadge(BadgeColor $type, string $content): void
    {
        $bgBadgeColor = $this->getBadgeColor($type);
        $parsed = htmlspecialchars($content);

        render(
            <<<HTML
                <div class="my-1">
                    <span class="ml-2 px-1 bg-$bgBadgeColor-500 font-bold">$type->value</span>
                    <span class="ml-1">
                       $parsed
                    </span>
                </div>
            HTML
        );
    }
}
