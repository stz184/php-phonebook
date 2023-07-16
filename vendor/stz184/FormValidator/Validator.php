<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 2.6.2015 г.
 * Time: 13:20 ч.
 */

namespace stz184\FormValidator;


class Validator {
    protected $errors           = [];
    protected $userData         = [];
    protected $validationRules  = [];
    protected $userValidators   = [];

    /**
     * Initialize the validator with user submitted data, most often $_POST
     * @param array $userData
     */
    function __construct(array $userData) {
        $this->userData = $userData;
    }

    /**
     * @param string $field
     * @param string $label
     * @param array $rules validation rules separated by |
     */
    public function addRule($field, $label, array $rules)
    {
        $this->validationRules[$field] = [
            'field' => $field,
            'label' => $label,
            'rules' => $rules
        ];
    }

    public function registerValidator($name, $callable)
    {
        if (is_callable($callable)) {
            $this->userValidators[$name] = $callable;
        }
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function getValue($field)
    {
        return array_key_exists($field, $this->userData) ? $this->userData[$field] : null;
    }

    /**
     * @param $field
     * @return mixed
     */
    public function getLabel($field)
    {
        return $this->validationRules[$field]['label'];
    }

    /**
     * @param $field
     * @param $message
     */
    public function setError($field, $message)
    {
        $this->errors[$field] = $message;
    }

    /**
     * @param $field
     * @return bool
     */
    private function required($field)
    {
        $value = $this->getValue($field);
        $value = is_string($value) ? trim($value) : $value;

        if (is_scalar($value) && !empty($value)) {
            return true;
        }

        if (is_array($value) && count($value)) {
            return true;
        }

        $this->setError($field, sprintf('Field %s is required', $this->getLabel($field)));
        return false;
    }

    /**
     * @param $field
     * @param $length
     * @return bool
     */
    private function minlength($field, $length)
    {
        $length = intval($length);
        if (!$this->getValue($field) || mb_strlen($this->getValue($field), 'UTF-8') >= $length) {
            return true;
        } else {
            $this->setError($field, sprintf('The required min length for field %s is %d', $this->getLabel($field), $length));
            return false;
        }
    }

    /**
     * @param $field
     * @param $length
     * @return bool
     */
    private function maxlength($field, $length)
    {
        if (!$this->getValue($field) || mb_strlen($this->getValue($field), 'UTF-8') <= $length) {
            return true;
        } else {
            $this->setError($field, sprintf('The required max length for field %s is %d', $this->getLabel($field), $length));
            return false;
        }
    }

    /**
     * @param string $field
     * @return bool
     */
    private function email($field) {
        if (!$this->getValue($field) || filter_var(trim($this->getValue($field)), FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            $this->setError($field, 'Invalid email address');
            return false;
        }
    }

    /**
     * @param $field
     * @return bool
     */
    private function phone($field)
    {
        $pattern =  '/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|'.
                    '2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|'.
                    '4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/';
       if (!$this->getValue($field) || preg_match($pattern, $this->getValue($field))) {
           return true;
       } else {
           $this->setError($field, 'Invalid phone number. Use the international format with leading + sign');
           return false;
       }
    }

    /**
     * @param $field
     * @return bool
     */
    private function skype($field)
    {
        $pattern = '/^[a-zA-Z][a-zA-Z0-9\._]{5,31}$/';
        if (!$this->getValue($field) || preg_match($pattern, $this->getValue($field))) {
            return true;
        } else {
            $this->setError($field, 'Invalid phone number');
            return false;
        }
    }

    /**
     * @param $field
     * @return bool
     */
    private function url($field) {
        $start_url  = "(http(s)?\:\/\/)?"; // start url
        $dots       = "([\w_-]{2,}\.)+"; // one or more parts containing a '.' at the end
        $last_part  = "([\w_-]{2,6})"; // last part doesn't contain a dot
        $user       = "((\/)(\~)[\w_-]+)?((\/)[\w_-]+)*"; // maybe subdirectories - possibly with user ~
        $end        = "((\/)|(\/)[\w_-]+\.[\w]{2,})?"; // maybe a slash at the end or slash+file+extension
        $qstring1   = "((\?[\w_-]+\=([^\#]+)){0,1}"; // querystring - first argument (?a=b)
        $qstring2   = "(\&[\w_-]+\=([^\#]+))*)?"; // querystring - following arguments (&c=d)
        $bkmrk      = "(#[\w_-]+)?"; // bookmark

        $pattern = "/^".$start_url.$dots.$last_part.$user.$end.$qstring1.$qstring2.$bkmrk."$/i";
        if (!$this->getValue($field) || preg_match($pattern, $this->getValue($field))) {
            return true;
        } else {
            $this->setError($field, 'Invalid URL address');
            return false;
        }
    }

    /**
     * @param string $field
     * @return bool
     */
    private function number($field)
    {
        if (!$this->getValue($field) || is_numeric($this->getValue($field))) {
            return true;
        } else {
            $this->setError($field, sprintf('The field %s should contains only numeric values', $this->getLabel($field)));
            return false;
        }
    }

    /**
     * @param string $field1
     * @param string $field2
     * @return bool
     */
    private function matches($field1, $field2)
    {
        if ($this->getValue($field1) && $this->getValue($field2) && $this->getValue($field1) == $this->getValue($field2)) {
            return true;
        } else {
            $this->setError($field1, sprintf('The fields %s and %s should match.', $this->getLabel($field1), $this->getLabel($field2)));
            return false;
        }
    }

    /**
     * Validate the user data against validation rules
     * @return bool
     */
    function validate()
    {
        foreach ($this->validationRules as $field => $validationRule) {
            foreach ($validationRule['rules'] as $key => $value) {

                $functionName       = is_string($key) ? $key : $value;
                $functionArguments  = [$field];

                if (is_string($key)) {
                    if (is_array($value)) {
                        $functionArguments = array_merge($functionArguments, $value);
                    } else {
                        $functionArguments[] = $value;
                    }
                }

                if (method_exists($this, $functionName)) {
                    if (!call_user_func_array(array($this, $functionName), $functionArguments)) {
                        break;
                    }
                } elseif (function_exists($functionName)) {
                    if (!call_user_func_array($functionName, $functionArguments)) {
                        break;
                    }
                } elseif (isset($this->userValidators[$functionName])) {
                    array_unshift($functionArguments, $this);
                    if (!call_user_func_array($this->userValidators[$functionName], $functionArguments)) {
                        break;
                    }
                }
            }
        }

        return $this->isValid();
    }

    /**
     * Check if the validation passed
     * @return bool
     */
    public function isValid()
    {
        return count($this->errors) == 0;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $field
     * @return string
     */
    public function getFieldError($field)
    {
        return array_key_exists($field, $this->errors) ? $this->errors[$field] : "";
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasErrors($field)
    {
        return array_key_exists($field, $this->errors);
    }
}