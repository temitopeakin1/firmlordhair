<?php
declare(strict_types=1);

function piece_media(array $product, ?int $frame = null): string
{
    $photos = product_photos((string) $product['slug']);
    if ($frame === null) {
        $frame = $photos ? (int) array_key_first($photos) : 0;
    }
    if (isset($photos[$frame])) {
        $src = asset('images/products/' . $photos[$frame]);
        return '<img class="piece" src="' . e($src) . '" alt="' . e($product['name']) . '">';
    }
    if ($photos) {
        return '';
    }
    return piece_art($product, $frame);
}

function piece_frames(array $product): array
{
    $photos = product_photos((string) $product['slug']);
    if ($photos) {
        return array_keys($photos);
    }
    return [0, 1, 2];
}

function piece_art(array $product, int $frame = 0): string
{
    static $serial = 0;
    $serial++;
    $id = 'g' . $serial . substr(md5($product['slug'] . '|' . $frame), 0, 6);
    $wash = $product['wash'];
    $hair = $product['hair'];
    $label = e($product['name']);
    $angle = $frame === 0 ? '0' : ($frame === 1 ? '1' : '0.2');
    $texture = strtolower(trim((string) $product['texture']));
    $inner = match ($texture) {
        'straight' => art_straight($hair),
        'body wave' => art_wave($hair, 10),
        'loose wave' => art_wave($hair, 18),
        'deep wave' => art_wave($hair, 7),
        'curly' => art_curly($hair),
        'kinky' => art_kinky($hair),
        default => art_object($product['slug'], $hair),
    };
    if ($frame === 2) {
        $inner = '<g transform="translate(18 0)">' . $inner . '</g>';
    }

    return <<<SVG
<svg class="piece" viewBox="50 20 300 490" role="img" aria-label="{$label}">
  <defs>
    <linearGradient id="{$id}" x1="{$angle}" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$wash}"/>
      <stop offset="72%" stop-color="#ffffff"/>
    </linearGradient>
  </defs>
  <rect width="400" height="520" fill="url(#{$id})"/>
  <circle cx="320" cy="78" r="46" fill="#FF1493" opacity=".12"/>
  {$inner}
</svg>
SVG;
}

function art_straight(string $hair): string
{
    return <<<SVG
<ellipse cx="200" cy="486" rx="92" ry="14" fill="#1a1a1a" opacity=".08"/>
<path d="M118 300c8-62 28-96 82-96s74 34 82 96l28 150H92z" fill="#f3d2c6"/>
<path d="M156 268c0-62 18-104 44-104s44 42 44 104c0 18-8 28-44 28s-44-10-44-28z" fill="#f7ddd2"/>
<path d="M78 250c8-120 48-196 122-196 86 0 132 70 124 176-18-78-58-112-104-104-52 8-92 58-104 132-10-8-22-6-38-8z" fill="{$hair}"/>
<path d="M168 150c-8 70-6 150 4 250" fill="none" stroke="#fff" stroke-width="14" stroke-linecap="round" opacity=".28"/>
<path d="M214 92c28 18 40 64 34 120" fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" opacity=".35"/>
<circle cx="214" cy="246" r="3.5" fill="#1a1a1a" opacity=".55"/>
<path d="M206 262c8 6 16 6 22-2" fill="none" stroke="#c98484" stroke-width="2" stroke-linecap="round"/>
SVG;
}

function art_wave(string $hair, int $amp): string
{
    $a = 200 - $amp;
    $b = 200 + $amp;
    return <<<SVG
<ellipse cx="200" cy="486" rx="100" ry="14" fill="#1a1a1a" opacity=".08"/>
<path d="M124 310c10-58 30-90 76-90s66 32 76 90l22 140H102z" fill="#f3d2c6"/>
<path d="M160 286c2-54 16-90 40-90s38 36 40 90c0 16-10 24-40 24s-40-8-40-24z" fill="#f7ddd2"/>
<path d="M70 280c20-20 28-70 48-110 18-36 40-70 82-78 54-10 96 20 112 78 8 28 6 52-4 78-16-46-48-70-86-62-34 8-52 36-62 78-20-10-48-8-70 8-8 4-16 6-20 8z" fill="{$hair}"/>
<path d="M150 160 C{$a} 210 {$b} 250 {$a} 310 C{$b} 360 {$a} 400 168 470" fill="none" stroke="#fff" stroke-width="12" stroke-linecap="round" opacity=".3"/>
<path d="M230 120 C{$b} 170 {$a} 220 {$b} 280 C{$a} 340 {$b} 390 236 460" fill="none" stroke="{$hair}" stroke-width="16" stroke-linecap="round" opacity=".35"/>
<circle cx="208" cy="252" r="3.5" fill="#1a1a1a" opacity=".55"/>
SVG;
}

function art_curly(string $hair): string
{
    $coils = '';
    $spots = [[150, 150], [210, 130], [250, 190], [120, 210], [180, 230], [250, 270], [140, 300], [210, 330], [170, 400], [240, 420]];
    foreach ($spots as $i => $spot) {
        $r = 18 + ($i % 3) * 6;
        $coils .= '<circle cx="' . $spot[0] . '" cy="' . $spot[1] . '" r="' . $r . '" fill="' . $hair . '"/>';
    }
    return <<<SVG
<ellipse cx="200" cy="486" rx="96" ry="14" fill="#1a1a1a" opacity=".08"/>
<path d="M132 330c6-40 24-70 68-70s62 30 68 70l16 120h-168z" fill="#f3d2c6"/>
<path d="M166 300c0-40 14-68 34-68s34 28 34 68c0 12-8 18-34 18s-34-6-34-18z" fill="#f7ddd2"/>
{$coils}
<circle cx="200" cy="292" r="3" fill="#1a1a1a" opacity=".5"/>
SVG;
}

function art_kinky(string $hair): string
{
    $dots = '';
    for ($y = 80; $y <= 460; $y += 22) {
        for ($x = 90; $x <= 310; $x += 22) {
            $wave = (int) (sin($y / 18) * 10);
            $cx = $x + $wave;
            if ($cx < 150 && $y > 300 && $y < 420) {
                continue;
            }
            $dots .= '<circle cx="' . $cx . '" cy="' . $y . '" r="9" fill="' . $hair . '"/>';
        }
    }
    return <<<SVG
<ellipse cx="200" cy="486" rx="96" ry="14" fill="#1a1a1a" opacity=".08"/>
<path d="M140 340c4-36 20-60 60-60s56 24 60 60l12 110H128z" fill="#f3d2c6"/>
<path d="M170 312c0-34 12-56 30-56s30 22 30 56c0 10-6 16-30 16s-30-6-30-16z" fill="#f7ddd2"/>
{$dots}
SVG;
}

function art_object(string $slug, string $hair): string
{
    return match ($slug) {
        'silk-grip-band' => <<<SVG
<ellipse cx="200" cy="300" rx="120" ry="46" fill="none" stroke="{$hair}" stroke-width="22"/>
<ellipse cx="200" cy="300" rx="120" ry="46" fill="none" stroke="#fff" stroke-width="6"/>
<path d="M120 250c40-30 120-30 160 0" fill="none" stroke="#FF1493" stroke-width="4" opacity=".7"/>
SVG,
        'marble-wig-stand' => <<<SVG
<ellipse cx="200" cy="430" rx="70" ry="16" fill="#d9d0cb"/>
<rect x="186" y="210" width="28" height="210" rx="8" fill="#cfc6c1"/>
<ellipse cx="200" cy="200" rx="78" ry="92" fill="#f6e7df"/>
<path d="M150 150c20-50 90-50 110 10" fill="none" stroke="{$hair}" stroke-width="10" stroke-linecap="round"/>
SVG,
        'edge-ritual-set' => <<<SVG
<rect x="150" y="120" width="18" height="250" rx="8" fill="{$hair}"/>
<rect x="140" y="96" width="38" height="36" rx="8" fill="#E7B7A4"/>
<path d="M230 160c40 10 60 40 50 90-20 10-50-10-70-40" fill="none" stroke="#FF1493" stroke-width="16" stroke-linecap="round"/>
<path d="M250 280c30 20 40 50 10 80" fill="none" stroke="#fff" stroke-width="10" stroke-linecap="round"/>
SVG,
        default => <<<SVG
<rect x="168" y="120" width="64" height="250" rx="28" fill="#fff" stroke="{$hair}" stroke-width="8"/>
<rect x="186" y="150" width="28" height="70" rx="10" fill="{$hair}" opacity=".25"/>
<path d="M186 250h28M186 280h28" stroke="{$hair}" stroke-width="4" opacity=".4"/>
<circle cx="200" cy="390" r="10" fill="{$hair}"/>
SVG,
    };
}
