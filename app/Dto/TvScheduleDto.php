<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\WeekEnum;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class TvScheduleDto
{
    public function __construct(
        public DateTime $dateTime,
        public WeekEnum $day,
        public string $time,
        public ?string $showTitle = null,
    ){}

    /**
     * @throws \DateMalformedStringException
     */
    public static function fromRequest(Request $request): self
    {
        $inputDate = $request->get('air_time');
        $title     = $request->get('show_title') ?: null;

        return self::create($inputDate, $title);
    }

    public static function create(?string $inputDate = null, ?string $title = null): self
    {
        $dateTime    = new DateTime($inputDate ?? 'now');
        $currentDay  = WeekEnum::tryFrom((int) $dateTime->format('N'));
        $currentTime = $dateTime->format('H:i:s');

        return new self($dateTime, $currentDay, $currentTime, $title);
    }

    /**
     * @throws Exception
     */
    public function validate(): void
    {
        $nowFormatted      = (new DateTime())->format('Y-m-d H:i');
        $selectedFormatted = $this->dateTime->format('Y-m-d H:i');

        if ($selectedFormatted < $nowFormatted) {
            throw new Exception("The selected date and time cannot be in the past.");
        }
    }
}
