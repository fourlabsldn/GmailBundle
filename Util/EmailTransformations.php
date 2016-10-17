<?php

namespace FL\GmailBundle\Util;

abstract class EmailTransformations
{
    final private function __construct() {}

    /**
     * Will convert even somewhat broken strings to an array of emails. E.g.:
     * email@example.com Miles <miles@example.com>, Mila <mila@example.com, Charles charles@example.com,,,,, <Mick> mick@example.com
     *
     * @param string $string
     * @return string[]
     */
    final public static function getMultipleEmailsFromString(string $string = null)
    {
        $emails = [];
        if (is_string($string) && !empty($string)) {
            $possibleEmails = preg_split("/(,|<|>|,|\\s)/", $string);
            foreach($possibleEmails as $possibleEmail){
                if (filter_var($possibleEmail, FILTER_VALIDATE_EMAIL)) {
                    $emails[$possibleEmail] = $possibleEmail;
                }
            }
        }
        return $emails;
    }
}