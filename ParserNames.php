<?php

function getPartsFromFullname(string $fullName)
{
    $partsName = explode(' ', $fullName);
    return [
        'surname' => $partsName[0],
        'name' => $partsName[1],
        'patronomyc' => $partsName[2]
    ];
}

function getFullnameFromParts($name, $surname, $patronomyc)
{
    return "{$surname} {$name} {$patronomyc}";
}

function getShortName(string $fullName)
{
    $partsName = getPartsFromFullname($fullName);
    $surName = $partsName['surname'];
    $name = $partsName['name'];
    $shotName = mb_substr($name, 0, 1);
    return "{$surName} $shotName.";
}

function getGenderFromName(string $fullName)
{
    $femaleSexSign = ['surname' => ['ва'], 'name' => ['а'], 'patronomyc' => ['вна']];
    $maleSexSign = ['surname' => ['в'], 'name' => ['й', 'н'], 'patronomyc' => ['ич']];
    $sex = 0;

    $partsName = getPartsFromFullname($fullName);

    foreach ($partsName as $key => $value)
    {
        foreach ($femaleSexSign[$key] as $sing)
        {
            if (str_ends_with($value, $sing))
                $sex -= 1;
        }

        foreach ($maleSexSign[$key] as $sing)
        {
            if (str_ends_with($value, $sing))
                $sex += 1;
        }
    }

    return ($sex === 0 ? 0 : ($sex > 0 ? 1 : -1));
}

function getGenderDescription(array $personsArray)
{
    $resultTemplate = "Гендерный состав аудитории:\n---------------------------\nМужчины - {male}%\nЖенщины - {female}%\nНе удалось определить - {undef}%";
    $resultGender = ['female' => 0, 'male' => 0, 'undef' => 0];
    $resultGenderPercent = ['female' => 0, 'male' => 0, 'undef' => 0];
    $totalCount = count($personsArray);

    foreach ($personsArray as $person)
    {
        $gender = getGenderFromName($person['fullname']);
        if ($gender > 0)
            $resultGender['male'] += 1;
        elseif ($gender < 0)
            $resultGender['female'] += 1;
        else
            $resultGender['undef'] += 1;
    }

    foreach ($resultGender as $gender => $value)
    {
        $resultGenderPercent[$gender] = round(($value / $totalCount * 100), 1);
    }

    return str_replace(
        ['{male}', '{female}', '{undef}'],
        [$resultGenderPercent['male'], $resultGenderPercent['female'], $resultGenderPercent['undef']],
        $resultTemplate
    );
}

function normalizePersonName(string $string)
{
    $string = mb_strtolower($string);
    $let = mb_strtoupper(mb_substr($string, 0, 1));
    return $let . mb_substr($string, 1);
}

function getPairFullNameByGender(int $gender, array $personsArray)
{
    $rand =  rand(0, (count($personsArray) - 1));
    if ($randPersonName = $personsArray[$rand]['fullname'])
    {
        $randomGender = getGenderFromName($randPersonName);
        if ($gender === $randomGender || $randomGender === 0)
            return getPairFullNameByGender($gender, $personsArray);

        return $rand;
    }

    return getPairFullNameByGender($gender, $personsArray);
}

function randomFloat($min = 0, $max = 1)
{
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}

function getPerfectPartner(string $surname, string $name, string $patronomyc, array $personsArray)
{
    $fullName = getFullnameFromParts(normalizePersonName($surname), normalizePersonName($name), normalizePersonName($patronomyc));
    $gender = getGenderFromName($fullName);

    $perfPartnerIndex = getPairFullNameByGender($gender, $personsArray);
    $perfPartner = $personsArray[$perfPartnerIndex];

    return getShortName($fullName) ." + ". getShortName($perfPartner['fullname']) ." =\n♡ Идеально на ". round(randomFloat(50, 100), 2) ."% ♡";
}
