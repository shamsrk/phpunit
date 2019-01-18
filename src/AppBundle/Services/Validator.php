<?php
namespace AppBundle\Services;

use AppBundle\Constants\KeyConstants as Key;
use Symfony\Component\HttpFoundation\Session\Session;
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
     * @return Validator
     * @throws \Exception
     */
    public static function validate(array $data, array $rules, array $messages = null)
    {
        // Create session instance
        $session = new Session();

        // Create translator and load message file resource to get the messages based on the locale
        $translator = new Translator($session->get(Key::LOCALE));
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource(
            'yaml',
            '../translations/validations.' . $session->get(Key::LOCALE) . '.yml',
            $session->get(Key::LOCALE)
        );

        // Create validator object if it does not exist.
        $validator = self::getInstance();

        $errors = [];

        // Check all the rules and validate the corresponding data
        foreach ($rules as $k => $rule) {
            $rs = explode('|', $rule);

            foreach ($rs as $r) {
                if (!empty($r) && !isset($validator::$errors[$k])) {
                    switch ($r) {
                        // required field validation
                        case Key::REQUIRED:
                            if (!(isset($data[$k]) && !empty($data[$k]))) {
                                $validator::$hasError = true;
                                $validator::$errors[$k] = (isset($messages[$k . Key::REQUIRED])) ?
                                    $messages[$k . Key::REQUIRED] :
                                    $translator->trans(Key::REQUIRED, [Key::TRANS_KEY => $k]);
                            }
                            break;

                        // string data type validation
                        case Key::STRING:
                            if (!is_string($data[$k])) {
                                $validator::$hasError = true;
                                $validator::$errors[$k] = (isset($messages[$k . Key::STRING])) ?
                                    $messages[$k . Key::STRING] : $translator->trans(Key::STRING, [Key::TRANS_KEY => $k]);
                            }
                            break;

                        // numeric data type validation
                        case Key::NUMERIC:
                            if (!is_numeric($data[$k])) {
                                $validator::$hasError = true;
                                $validator::$errors[$k] = (isset($messages[$k . Key::NUMERIC])) ?
                                    $messages[$k . Key::NUMERIC] : $translator->trans(Key::NUMERIC, [Key::TRANS_KEY => $k]);
                            }
                            break;

                        // maximum length validation
                        case ((strpos($r, Key::MAX) !== false) ? $r : false):
                            $sr = explode(':', $r);
                            $ml = (int)end($sr);

                            if ($ml < 0) {
                                throw new \Exception($translator->trans(Key::INCORRECT, [Key::TRANS_KEY => Key::MAX]));
                            }

                            if (strlen($data[$k]) > $ml) {
                                $validator::$hasError = true;
                                $validator::$errors[$k] = (isset($messages[$k . Key::MAX])) ?
                                    $messages[$k . Key::MAX] : $translator->trans(Key::MAX, [Key::TRANS_KEY => $ml]);
                            }
                            break;

                        // minimum length validation
                        case ((strpos($r, Key::MIN) !== false) ? $r : false):
                            $sr = explode(':', $r);
                            $ml = (int)end($sr);

                            if ($ml < 0) {
                                throw new \Exception($translator->trans(Key::INCORRECT, [Key::TRANS_KEY => Key::MIN]));
                            }

                            if (strlen($data[$k]) < $ml) {
                                $validator::$hasError = true;
                                $validator::$errors[$k] = (isset($messages[$k . Key::MIN])) ?
                                    $messages[$k . Key::MIN] : $translator->trans(Key::MIN, [Key::TRANS_KEY => $ml]);
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
                                $validator::$errors[$k] = (isset($messages[$k . Key::DIGITS])) ?
                                    $messages[$k . Key::DIGITS] : 
                                    $translator->trans('numeric_equal', [Key::TRANS_KEY => $ml]);
                            }
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
}