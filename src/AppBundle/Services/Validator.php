<?php

namespace AppBundle\Services;

use AppBundle\Constants\KeyConstants as Key;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Validator class to validate the request data
 */
class Validator
{
    /*
     * Validation errors
     *
     * @var array
     */
    protected static $errors = [];

    /*
     * error flag
     *
     * @var boolean
     */
    protected static $hasError = false;

    /*
     * Validator instance
     *
     * @var Validator
     */
    private static $instance;

    /**
     * Create it if it doesn't exist.
     *
     * @return Validator
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Validator();
        }
        return self::$instance;
    }

    /**
     * Validate the request data with different criteria
     *
     * @param array $data
     * @param array $rules
     * @param array|null $messages
     * @param  string $locale
     * @return Validator
     * @throws \Exception
     */
    public static function validate(array $data, array $rules, array $messages = null, $locale = 'en')
    {
        $translator = new Translator($locale);
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource(
            'yaml',
            __DIR__ . '/../../../translations/validations.' . $locale . '.yml',
            $locale
        );

        // Create validator object if it does not exist.
        $validator = self::getInstance();

        $errors = [];

        // Check all the rules and validate the corresponding data
        foreach ($rules as $k => $rule) {
            $rs = explode('|', $rule);

            // required field validation, if required validation failed then skip further validation for the field
            if (in_array(Key::REQUIRED, $rs) && !(isset($data[$k]) && !empty($data[$k]))) {
                $validator::$hasError = true;
                $validator::$errors[$k][] = (isset($messages[$k . Key::REQUIRED])) ?
                    $messages[$k . Key::REQUIRED] :
                    $translator->trans(Key::REQUIRED, [Key::TRANS_KEY => $k]);
                continue;
            }

            foreach ($rs as $r) {
                if (!empty($r)) {
                    switch ($r) {
                        // string data type validation
                        case Key::STRING:
                            if (!is_string($data[$k])) {
                                $validator::$hasError = true;
                                $validator::$errors[$k][] = (isset($messages[$k . Key::STRING])) ?
                                    $messages[$k . Key::STRING] :
                                    $translator->trans(Key::STRING, [Key::TRANS_KEY => $k]);
                            }
                            break;

                        // numeric data type validation
                        case Key::NUMERIC:
                            if (!is_numeric($data[$k])) {
                                $validator::$hasError = true;
                                $validator::$errors[$k][] = (isset($messages[$k . Key::NUMERIC])) ?
                                    $messages[$k . Key::NUMERIC] :
                                    $translator->trans(Key::NUMERIC, [Key::TRANS_KEY => $k]);
                            }
                            break;

                        // maximum length validation
                        case ((strpos($r, Key::MAX) !== false) ? $r : false):
                            $sr = explode(':', $r);
                            $ml = (int)end($sr);

                            if ($ml < 0) {
                                throw new \Exception(
                                    $translator->trans(Key::INCORRECT, [Key::TRANS_KEY => Key::MAX])
                                );
                            }

                            if (strlen($data[$k]) > $ml) {
                                $validator::$hasError = true;
                                $validator::$errors[$k][] = (isset($messages[$k . Key::MAX])) ?
                                    $messages[$k . Key::MAX] :
                                    $translator->trans(Key::MAX, [Key::TRANS_KEY => $ml]);
                            }
                            break;

                        // minimum length validation
                        case ((strpos($r, Key::MIN) !== false) ? $r : false):
                            $sr = explode(':', $r);
                            $ml = (int)end($sr);

                            if ($ml < 0) {
                                throw new \Exception(
                                    $translator->trans(Key::INCORRECT, [Key::TRANS_KEY => Key::MIN])
                                );
                            }

                            if (strlen($data[$k]) < $ml) {
                                $validator::$hasError = true;
                                $validator::$errors[$k][] = (isset($messages[$k . Key::MIN])) ?
                                    $messages[$k . Key::MIN] :
                                    $translator->trans(Key::MIN, [Key::TRANS_KEY => $ml]);
                            }
                            break;

                        // digits data type validation
                        case ((strpos($r, Key::DIGITS) !== false) ? $r : false):
                            $sr = explode(':', $r);
                            $ml = (int)end($sr);

                            if ($ml < 0) {
                                throw new \Exception($translator->trans(
                                    Key::INCORRECT,
                                    [Key::TRANS_KEY => Key::DIGITS . ' length'])
                                );
                            }

                            if (!is_numeric($data[$k]) || strlen((int)$data[$k]) !== $ml) {
                                $validator::$hasError = true;
                                $validator::$errors[$k][] = (isset($messages[$k . Key::DIGITS])) ?
                                    $messages[$k . Key::DIGITS] :
                                    $translator->trans('numeric_equal', [Key::TRANS_KEY => $ml]);
                            }
                            break;

                        // email validation
                        case Key::EMAIL:
                            if (!filter_var($data[$k], FILTER_VALIDATE_EMAIL)) {
                                $validator::$hasError = true;
                                $validator::$errors[$k][] = (isset($messages[$k . Key::EMAIL])) ?
                                    $messages[$k . Key::EMAIL] :
                                    $translator->trans(Key::EMAIL, [Key::TRANS_KEY => $k]);
                            }
                            break;

                        default:
                            break;

                    }
                }


            }
        }

        return $validator;
    }

    /**
     * check the validation status
     *
     * @return bool
     */
    public function fails()
    {
        return self::$hasError;
    }

    /**
     * Get the validation errors
     *
     * @return array
     */
    public function validationErrors()
    {
        return self::$errors;
    }

    /**
     * Destruct the Validator to original stage
     *
     * @return void
     */
    public function __destruct()
    {
        self::$instance = null;
        self::$hasError = false;
        self::$errors = [];
    }
}