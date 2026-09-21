<?php

use App\Models\Container;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $counters = [];

        Container::with('location.unit')
            ->orderBy('id')
            ->get()
            ->each(function (Container $container) use (&$counters): void {
                $unitCode = preg_replace('/[^A-Z0-9]/', '', strtoupper($container->location?->unit?->code ?? 'UNIT'));
                $counters[$unitCode] = ($counters[$unitCode] ?? 0) + 1;

                $container->forceFill([
                    'code' => 'TPN-'.$unitCode.'-'.str_pad((string) $counters[$unitCode], 4, '0', STR_PAD_LEFT),
                ])->save();
            });
    }

    public function down(): void
    {
        //
    }
};
