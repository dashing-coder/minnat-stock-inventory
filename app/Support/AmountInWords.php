<?php

namespace App\Support;

final class AmountInWords
{
    public static function taka(float|int|string $amount, string $locale = 'en'): string
    {
        $amount = round((float) $amount, 2);
        $taka = (int) floor(abs($amount));
        $paisa = (int) round((abs($amount) - $taka) * 100);

        if ($locale === 'bn') {
            $words = self::bnNumber($taka) . ' টাকা';
            if ($paisa > 0) {
                $words .= ' ' . self::bnNumber($paisa) . ' পয়সা';
            }
            return ($amount < 0 ? 'ঋণাত্মক ' : '') . $words . ' মাত্র';
        }

        $words = self::enNumber($taka) . ' Taka';
        if ($paisa > 0) {
            $words .= ' and ' . self::enNumber($paisa) . ' Paisa';
        }
        return ($amount < 0 ? 'Minus ' : '') . $words . ' Only';
    }

    private static function enNumber(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $underThousand = function (int $n) use (&$ones, &$tens): string {
            $parts = [];
            if ($n >= 100) {
                $parts[] = $ones[intdiv($n, 100)] . ' Hundred';
                $n %= 100;
            }
            if ($n >= 20) {
                $parts[] = $tens[intdiv($n, 10)] . ($n % 10 ? ' ' . $ones[$n % 10] : '');
            } elseif ($n > 0) {
                $parts[] = $ones[$n];
            }
            return implode(' ', $parts);
        };

        $parts = [];
        foreach ([[10000000, 'Crore'], [100000, 'Lakh'], [1000, 'Thousand']] as [$value, $label]) {
            if ($number >= $value) {
                $chunk = intdiv($number, $value);
                $parts[] = self::enNumber($chunk) . ' ' . $label;
                $number %= $value;
            }
        }
        if ($number > 0) {
            $parts[] = $underThousand($number);
        }
        return implode(' ', $parts);
    }

    private static function bnNumber(int $number): string
    {
        if ($number === 0) {
            return 'শূন্য';
        }

        $words = [
            0=>'শূন্য',1=>'এক',2=>'দুই',3=>'তিন',4=>'চার',5=>'পাঁচ',6=>'ছয়',7=>'সাত',8=>'আট',9=>'নয়',10=>'দশ',
            11=>'এগারো',12=>'বারো',13=>'তেরো',14=>'চৌদ্দ',15=>'পনেরো',16=>'ষোলো',17=>'সতেরো',18=>'আঠারো',19=>'উনিশ',20=>'বিশ',
            21=>'একুশ',22=>'বাইশ',23=>'তেইশ',24=>'চব্বিশ',25=>'পঁচিশ',26=>'ছাব্বিশ',27=>'সাতাশ',28=>'আটাশ',29=>'ঊনত্রিশ',30=>'ত্রিশ',
            31=>'একত্রিশ',32=>'বত্রিশ',33=>'তেত্রিশ',34=>'চৌত্রিশ',35=>'পঁয়ত্রিশ',36=>'ছত্রিশ',37=>'সাঁইত্রিশ',38=>'আটত্রিশ',39=>'ঊনচল্লিশ',40=>'চল্লিশ',
            41=>'একচল্লিশ',42=>'বিয়াল্লিশ',43=>'তেতাল্লিশ',44=>'চুয়াল্লিশ',45=>'পঁয়তাল্লিশ',46=>'ছেচল্লিশ',47=>'সাতচল্লিশ',48=>'আটচল্লিশ',49=>'ঊনপঞ্চাশ',50=>'পঞ্চাশ',
            51=>'একান্ন',52=>'বায়ান্ন',53=>'তিপ্পান্ন',54=>'চুয়ান্ন',55=>'পঞ্চান্ন',56=>'ছাপ্পান্ন',57=>'সাতান্ন',58=>'আটান্ন',59=>'ঊনষাট',60=>'ষাট',
            61=>'একষট্টি',62=>'বাষট্টি',63=>'তেষট্টি',64=>'চৌষট্টি',65=>'পঁয়ষট্টি',66=>'ছেষট্টি',67=>'সাতষট্টি',68=>'আটষট্টি',69=>'ঊনসত্তর',70=>'সত্তর',
            71=>'একাত্তর',72=>'বাহাত্তর',73=>'তিয়াত্তর',74=>'চুয়াত্তর',75=>'পঁচাত্তর',76=>'ছিয়াত্তর',77=>'সাতাত্তর',78=>'আটাত্তর',79=>'ঊনআশি',80=>'আশি',
            81=>'একাশি',82=>'বিরাশি',83=>'তিরাশি',84=>'চুরাশি',85=>'পঁচাশি',86=>'ছিয়াশি',87=>'সাতাশি',88=>'আটাশি',89=>'ঊননব্বই',90=>'নব্বই',
            91=>'একানব্বই',92=>'বিরানব্বই',93=>'তিরানব্বই',94=>'চুরানব্বই',95=>'পঁচানব্বই',96=>'ছিয়ানব্বই',97=>'সাতানব্বই',98=>'আটানব্বই',99=>'নিরানব্বই',
        ];

        $parts = [];
        foreach ([[10000000, 'কোটি'], [100000, 'লক্ষ'], [1000, 'হাজার'], [100, 'শত']] as [$value, $label]) {
            if ($number >= $value) {
                $chunk = intdiv($number, $value);
                $parts[] = ($chunk < 100 ? $words[$chunk] : self::bnNumber($chunk)) . ' ' . $label;
                $number %= $value;
            }
        }
        if ($number > 0) {
            $parts[] = $words[$number];
        }
        return implode(' ', $parts);
    }
}
