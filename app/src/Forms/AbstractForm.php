<?php
declare(strict_types=1);

namespace App\Forms;

use App\Forms\Enums\Rule;

abstract class AbstractForm
{
    public array $errors;

    public function __serialize(): array
    {
        return [
            'model' => $this->getFormModel(),
            'errors' => $this->errors,
            '__class__' => static::class,
        ];
    }

    public function __unserialize(array $data): void
    {
        if (isset($data['model']) && is_array($data['model'])) {
            $this->load($data['model']);
        }
        
        $this->errors = $data['errors'] ?? [];
    }

    abstract public function rules(): array;

    public function getFormModel(): array
    {
        $props = get_object_vars($this);

        $modelData = array_filter($props, static fn($key) => $key !== 'errors', ARRAY_FILTER_USE_KEY);

        return $modelData;
    }

    public function load($data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $key => $rules) {
            $value = $this->{$key};

            foreach ($rules as $rule) {
                $ruleName = $rule;
                $param = null;

                if (is_array($rule)) {
                    $ruleName = $rule[0];
                    $param = $rule[$ruleName->value] ?? null;
                }

                if ($ruleName === Rule::Required && ($value === null || $value === '')) {
                    $this->addErrorByRule($key, Rule::Required);
                    // Skip remaining checks for this field if it's required and missing
                    continue 2;
                }

                if ($value === null || $value === '') {
                    continue; 
                }

                if ($ruleName === Rule::Email && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorByRule($key, Rule::Email);
                }

                if ($ruleName === Rule::Match) {
                    if ($param && property_exists($this, $param) && !hash_equals($this->{$param}, (string)$value)) {
                        $this->addErrorByRule($key, Rule::Match, $param);
                    }
                }

                if ($ruleName === Rule::Min) {
                    if ($param !== null && strlen((string)$value) < $param) {
                        $this->addErrorByRule($key, Rule::Min, $param);
                    }
                }

                if ($ruleName === Rule::Max) {
                    if ($param !== null && strlen((string)$value) > $param) {
                        $this->addErrorByRule($key, Rule::Max, $param);
                    }
                }

                if ($ruleName === Rule::ALowercase && !preg_match('/[a-z]/', (string)$value)) {
                    $this->addErrorByRule($key, Rule::ALowercase);
                }

                if ($ruleName === Rule::AUppercase && !preg_match('/[A-Z]/', (string)$value)) {
                    $this->addErrorByRule($key, Rule::AUppercase);
                }

                if ($ruleName === Rule::ANumber && !preg_match('/[0-9]/', (string)$value)) {
                    $this->addErrorByRule($key, Rule::ANumber);
                }

                if ($ruleName === Rule::ASpecial && !preg_match('/[^A-Za-z0-9]/', (string)$value)) {
                    $this->addErrorByRule($key, Rule::ASpecial);
                }
            }
        }

        return empty($this->errors);
    }

    protected function errorMessages(): array
    {
        return [
            Rule::Required->value => "This field is required.",
            Rule::Email->value => "This field must be valid email address",
            Rule::Match->value => "This field must be the same as {" . Rule::Match->value . "}.",
            Rule::Min->value => "This field must be at least {" . Rule::Min->value . "} characters long.",
            Rule::Max->value => "This field must be maximum {" . Rule::Max->value . "} characters long.",
            Rule::ALowercase->value => "Password must contain at least one lowercase letter.",
            Rule::AUppercase->value => "Password must contain at least one uppercase letter.",
            Rule::ANumber->value => "Password must contain at least one number.",
            Rule::ASpecial->value => "Password must contain at least one special character."
        ];
    }

    protected function errorMessage(Rule $rule): string
    {
        return $this->errorMessages()[$rule->value];
    }

    protected function fillErrorMsgWithParam(Rule $rule, string $param, $msg): string
    {
        return str_replace("{{$rule->value}}", $param, $msg);
    }

    public function addErrorByRule(string $key, Rule $rule, mixed $param = null): void
    {
        $message = $this->errorMessage($rule);

        if ($param) {
            $message = $this->fillErrorMsgWithParam($rule, (string)$param, $message);
        }

        $this->addError($key, $message);
    }

    public function addError(string $key, string $msg): void
    {
        $this->errors[$key][] = $msg;
    }

}
