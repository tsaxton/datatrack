<?php
function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
{
    $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
    $enc = preg_replace_callback(
        '/"(.*?)"/s',
        function ($field) {
            return urlencode(utf8_encode($field[1]));
        },
        $enc
    );
    $lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);
    return array_map(
        function ($line) use ($delimiter, $trim_fields) {
            $fields = $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);
            return array_map(
                function ($field) {
                    return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
                },
                $fields
            );
        },
        $lines
    );
}

$str = "Year,AIRCRAFT FLOWN- HOURS (NO), AIRCRAFT FLOWN- KMS. (000), PASSENGERS CARRIED (NO),   PASSENGERS KMS.PERFORMED (MILLION),Availabe Seat KMs,Paasenger Load Factor,Cargo Carried in Tons
2005-06,475352,252668,25204988,23709,35077,67.6 ,299.2
2006-07,648408,347912,35792747,33519,48702,68.8 ,321.3
2007-08,805934,439377.5,44384302,41718,60590,68.9 ,368.1
2008-09,808442,426099,39467072,37704,59160,63.7 ,340.76
2009-10,820991,412594,45337263,43959,61091,72.0 ,395.76
2010-11,892630,438559,53842538,52707,68216,77.3 ,476.81
2011-12,988125,500395,60843134,59097,78653,75.1 ,452.46";

var_dump(parse_csv($str));

echo strtotime('2010-11'), "\n";
echo strtotime('2010-2011'), "\n";
echo strtotime('01/2010'), "\n";
echo strtotime('January 2010'), "\n";
echo strtotime('2010'), "\n";
echo strtotime('January'), "\n";
