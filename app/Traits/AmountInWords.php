<?php
namespace App\Traits;

trait AmountInWords {
 function displayAmountInWords($number)
    {
        if (is_numeric($number)) 
        {
            $no = (int)floor($number);
            $point = (int)round(($number - $no) * 100);
            $hundred = null;
            $digits_1 = strlen($no);
            $i = 0;
            $str = array();
            $words = array('0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety');
            $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
            while ($i < $digits_1) 
            {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += ($divider == 10) ? 1 : 2;


                if ($number) 
                {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred
                    :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
                } 
                else $str[] = null;
            }
            $str = array_reverse($str);
            $result = implode('', $str);


            $points = ($point) ?
            "" . $words[floor($point / 10) * 10] . " " . 
            $words[$point = $point % 10] : ''; 

            if($points != ''){        
            return  strtoupper($result) . strtoupper(" Rupees ") . strtoupper($points) . strtoupper(" Paise Only");
            } else {

            return  strtoupper($result) . strtoupper("Rupees Only");
            }
        }
        else
        {
            return  strtoupper("number format incorrect");
        }

    }
	function amountFormatInKL($number)
	{
		 if( $number > 1000 ) {

        $x = round($number);
        $x_number_format =  money_format("%!.0n", $number);
        $x_array = explode(',', $x_number_format);
         $x_parts = array('K', 'L','M', 'B', 'T');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];
        
        return $x_display;
		}
		return $number;
	}
 
}