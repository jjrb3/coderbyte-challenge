<?php


/**
 * @param string $str
 * @return bool
 */
function WildcardCharacters(string $str): bool
{
    if (!strpos($str, ' ') || substr_count($str, ' ') > 1) {
        return false;
    }

    $characters  = explode(' ', trim($str));
    $results     = str_split(strtolower($characters[1]));
    $charList    = str_split($characters[0]);
    $countString = 0;

    for ($key = 0; $key < count($charList); $key++) {

        switch ($charList[$key])
        {
            case '+':

                if (!preg_match("/^[a-z]/", $results[$countString])) {
                    return false;
                }

                $countString++;
                break;

            case '*':

                if ($key + 1 < count($charList) && $charList[$key + 1] === '{') {

                    $quantity = getQuantityBySpecialCharacter($charList, $key + 1);

                    if (!$quantity || !verifySpecialCharacter($results, $countString, (int)$quantity['value'])) {
                        return false;
                    }

                    $key = $quantity['position'];
                    $countString += $quantity['value'];
                }

                break;

            default:
                return false;
        }

    }

    return $countString === count($results);
}

/**
 * @param array $results
 * @param int $position
 * @param int $quantity
 * @return bool
 */
function verifySpecialCharacter(array $results, int $position, int $quantity): bool
{
    if ($position - 1 + $quantity < count($results)) {

        for ($i = $position; $i < $position + $quantity; $i++) {

            if ($results[$position] !== $results[$i]) {
                return false;
            }
        }

        return true;
    }

    return false;
}

/**
 * @param array $characters
 * @param int $position
 * @return array|null
 */
function getQuantityBySpecialCharacter(array $characters, int $position): ?array
{
    $value = '';

    for ($i = $position + 1; $i < count($characters); $i++) {

        if (!preg_match("/^[0-9]/", $characters[$i]) && !$value) {
            return null;
        }
        elseif ($characters[$i] === '}') {
            return [
                'position' => $i,
                'value' => (int)$value
            ];
        }

        $value .= $characters[$i];
    }

    return null;
}

// keep this function call here
echo WildcardCharacters('*{5}+++ gheeeeejkl') ? 'true' : ' false';
