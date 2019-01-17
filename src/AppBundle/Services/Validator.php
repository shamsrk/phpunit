<?php
/**
 * Created by PhpStorm.
 * User: shams
 * Date: 20/12/18
 * Time: 1:47 PM
 */

namespace AppBundle\Services;
use AppBundle\Constants\KeyConstants as Key;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;


class Validator
{
    protected static $errors = [];

    protected static $hasError = false;

    private static $instance;

    public static function getInstance()
    {
        // Create it if it doesn't exist.
        if (!self::$instance) {
            self::$instance = new Validator();
        }
        return self::$instance;
    }

    public static function validate(array $data, array $rules, array $messages = null)
    {

        $session = new Session();
        $translator = new Translator($session->get(Key::LOCALE));
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource(
            'yaml',
            '../translations/validations.' . $session->get(Key::LOCALE) . '.yml',
            $session->get(Key::LOCALE)
        );

        // Create it if it doesn't exist.
        $validator = self::getInstance();

        $errors = [];

        foreach ($rules as $k => $rule) {
            $rs = explode('|', $rule);

            foreach ($rs as $r) {
                if (!empty($r) && !isset($validator::$errors[$k])) {
                    switch ($r) {
                        case Key::REQUIRED:
                            if (!(isset($data[$k]) && !empty($data[$k]))) {
                                $validator::$hasError = true;
                                $validator::$errors[$k] = (isset($messages[$k . Key::REQUIRED])) ?
                                    $messages[$k . Key::REQUIRED] :
                                    $translator->trans(Key::REQUIRED, [Key::TRANS_KEY => $k]);
                            }
                            break;

                        case Key::STRING:
                            if (!is_string($data[$k])) {
                                $validator::$hasError = true;
                                $validator::$errors[$k] = (isset($messages[$k . Key::STRING])) ?
                                    $messages[$k . Key::STRING] : $translator->trans(Key::STRING, [Key::TRANS_KEY => $k]);
                            }
                            break;

                        case Key::NUMERIC:
                            if (!is_numeric($data[$k])) {
                                $validator::$hasError = true;
                                $validator::$errors[$k] = (isset($messages[$k . Key::NUMERIC])) ?
                                    $messages[$k . Key::NUMERIC] : $translator->trans(Key::NUMERIC, [Key::TRANS_KEY => $k]);
                            }
                            break;

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

    public function fails()
    {
        return self::$hasError;
    }

    public function validationErrors()
    {
        return self::$errors;
    }


}