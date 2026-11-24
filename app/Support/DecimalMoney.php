<?php
namespace App\Support;

final class DecimalMoney
{
    public static function round(string|int|float $value, int $scale=2): string
    {
        $value=(string)$value;
        if(str_starts_with($value,'-')) throw new \InvalidArgumentException('Money values cannot be negative here.');
        [$whole,$fraction]=array_pad(explode('.', $value, 2),2,'');
        $fraction=str_pad($fraction,$scale+1,'0');
        $digits=ltrim($whole.substr($fraction,0,$scale),'0');
        if($digits==='') $digits='0';
        if((int)$fraction[$scale]>=5){
            for($i=strlen($digits)-1;$i>=0;$i--){ if($digits[$i]<'9'){$digits[$i]=chr(ord($digits[$i])+1);break;} $digits[$i]='0'; if($i===0)$digits='1'.$digits; }
        }
        if($scale===0)return $digits;
        $digits=str_pad($digits,$scale+1,'0',STR_PAD_LEFT);
        return substr($digits,0,-$scale).'.'.substr($digits,-$scale);
    }

    public static function multiply(string|int|float $left,string|int|float $right,int $scale=2): string
    {
        return self::round(bcmul((string)$left,(string)$right,$scale+8),$scale);
    }

    public static function percent(string|int|float $amount,string|int|float $rate,int $divisor=100): string
    {
        return self::round(bcdiv(bcmul((string)$amount,(string)$rate,10),(string)$divisor,8),2);
    }

    public static function add(string|int|float $left,string|int|float $right,int $scale=2): string { return bcadd((string)$left,(string)$right,$scale); }
}
