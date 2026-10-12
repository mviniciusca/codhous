<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Datas do Meu Workspace no fuso de exibição, em rótulos curtos ("Hoje", "Amanhã", "Quinta", "20 out").
 */
class WorkspaceDates
{
    protected const WEEKDAYS = [1 => 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];

    public static function timezone(): string
    {
        return config('app.display_timezone', config('app.timezone'));
    }

    public static function today(): CarbonImmutable
    {
        return CarbonImmutable::now(static::timezone())->startOfDay()->locale('pt_BR');
    }

    /** Converte uma data guardada sem fuso (coluna date) para o dia correspondente no fuso de exibição */
    public static function day(CarbonInterface|string $date): CarbonImmutable
    {
        $value = $date instanceof CarbonInterface ? $date->format('Y-m-d') : $date;

        return CarbonImmutable::createFromFormat('!Y-m-d', $value, static::timezone())->locale('pt_BR');
    }

    /** Dia relativo: Hoje, Amanhã, Ontem, nome do dia na mesma semana ou "20 out" */
    public static function relativeDay(CarbonInterface $date, ?CarbonInterface $today = null, bool $weekdays = true): string
    {
        $today ??= static::today();
        $date = static::day($date);
        $diff = (int) $today->diffInDays($date, false);

        return match (true) {
            $diff === 0 => 'Hoje',
            $diff === 1 => 'Amanhã',
            $diff === -1 => 'Ontem',
            $weekdays && abs($diff) < 7 => static::WEEKDAYS[$date->dayOfWeekIso],
            $date->year === $today->year => $date->translatedFormat('j M'),
            default => $date->translatedFormat('j M Y'),
        };
    }

    /** Momento (timestamp em UTC) no fuso de exibição: "Hoje, 09:15" */
    public static function moment(?CarbonInterface $moment, ?CarbonInterface $today = null): string
    {
        if (! $moment) {
            return '';
        }

        $local = CarbonImmutable::instance($moment)->setTimezone(static::timezone());

        return static::relativeDay($local, $today).', '.$local->format('H:i');
    }
}
