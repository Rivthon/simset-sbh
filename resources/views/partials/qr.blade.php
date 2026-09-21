<div class="{{ $class ?? '' }}">
    {!! \App\Support\QrCode::svg($data, $size ?? 180) !!}
</div>

