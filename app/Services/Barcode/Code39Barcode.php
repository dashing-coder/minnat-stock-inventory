<?php

namespace App\Services\Barcode;

use InvalidArgumentException;

class Code39Barcode
{
    private const PATTERNS = [
        '0'=>'nnnwwnwnn','1'=>'wnnwnnnnw','2'=>'nnwwnnnnw','3'=>'wnwwnnnnn','4'=>'nnnwwnnnw','5'=>'wnnwwnnnn','6'=>'nnwwwnnnn','7'=>'nnnwnnwnw','8'=>'wnnwnnwnn','9'=>'nnwwnnwnn',
        'A'=>'wnnnnwnnw','B'=>'nnwnnwnnw','C'=>'wnwnnwnnn','D'=>'nnnnwwnnw','E'=>'wnnnwwnnn','F'=>'nnwnwwnnn','G'=>'nnnnnwwnw','H'=>'wnnnnwwnn','I'=>'nnwnnwwnn','J'=>'nnnnwwwnn',
        'K'=>'wnnnnnnww','L'=>'nnwnnnnww','M'=>'wnwnnnnwn','N'=>'nnnnwnnww','O'=>'wnnnwnnwn','P'=>'nnwnwnnwn','Q'=>'nnnnnnwww','R'=>'wnnnnnwwn','S'=>'nnwnnnwwn','T'=>'nnnnwnwwn',
        'U'=>'wwnnnnnnw','V'=>'nwwnnnnnw','W'=>'wwwnnnnnn','X'=>'nwnnwnnnw','Y'=>'wwnnwnnnn','Z'=>'nwwnwnnnn','-'=>'nwnnnnwnw','.'=>'wwnnnnwnn',' '=>'nwwnnnwnn',
        '$'=>'nwnwnwnnn','/'=>'nwnwnnnwn','+'=>'nwnnnwnwn','%'=>'nnnwnwnwn','*'=>'nwnnwnwnn',
    ];

    /** Code 39 supports a restricted alphabet. Never silently change printed values. */
    public function normalize(string $value): string
    {
        $value = strtoupper(trim($value));
        if ($value === '' || strlen($value) > 64) {
            throw new InvalidArgumentException('Code 39 value must contain 1 to 64 characters.');
        }
        foreach (str_split($value) as $character) {
            if (! isset(self::PATTERNS[$character]) || $character === '*') {
                throw new InvalidArgumentException('Unsupported character in Code 39 value. Assign a compatible product barcode or SKU.');
            }
        }
        return $value;
    }

    /** Conservative minimum label width for a 0.20 mm narrow module plus 4 mm padding. */
    public function minimumLabelWidthMm(string $value): int
    {
        $encoded = '*' . $this->normalize($value) . '*';
        $units = 28 + 2 * (strlen($encoded) - 1); // quiet zones + character gaps
        foreach (str_split($encoded) as $character) {
            foreach (str_split(self::PATTERNS[$character]) as $element) {
                $units += $element === 'w' ? 5 : 2;
            }
        }
        return (int) ceil($units * 0.1 + 4); // 2-unit narrow module at 0.20mm
    }

    public function svg(string $value, int $height = 62): string
    {
        $value = $this->normalize($value);
        $encoded = '*'.$value.'*';
        $narrow = 2;
        $wide = 5;
        $gap = 2;
        $quiet = 14;
        $x = $quiet;
        $rects = [];

        foreach (str_split($encoded) as $char) {
            $pattern = self::PATTERNS[$char] ?? self::PATTERNS['-'];
            foreach (str_split($pattern) as $i => $widthCode) {
                $width = $widthCode === 'w' ? $wide : $narrow;
                if ($i % 2 === 0) {
                    $rects[] = '<rect x="'.$x.'" y="0" width="'.$width.'" height="'.$height.'" />';
                }
                $x += $width;
            }
            $x += $gap;
        }
        $totalWidth = $x + $quiet - $gap;
        $label = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$totalWidth.' '.($height + 18).'" role="img" aria-label="Barcode '.$label.'" preserveAspectRatio="xMidYMid meet"><g fill="#000">'.implode('', $rects).'</g><text x="'.($totalWidth/2).'" y="'.($height+14).'" text-anchor="middle" font-family="monospace" font-size="12" fill="#000">'.$label.'</text></svg>';
    }
}
