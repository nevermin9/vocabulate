<?php
declare(strict_types=1);

namespace App\Forms;

use App\Forms\Enums\Rule;

class ResetPasswordForm extends AbstractForm
{
    public readonly string $password;
    public readonly string $confirmPassword;

    public function rules(): array
    {
        return [
            'password' => [[Rule::Min, Rule::Min->value => 8 ], Rule::AUppercase, Rule::ALowercase, Rule::ANumber, Rule::ASpecial],
            'confirmPassword' => [[Rule::Match, Rule::Match->value => 'password']]
        ];
    }

    public function getPasswordMessages(): array
    {
        $passwordMessages = [];
        $errorMessages = $this->errorMessages();
        $passwordRules = $this->rules()['password'];

        foreach ($passwordRules as $rule) {
            if (is_array($rule)) {
                $ruleValue = $rule[0]->value;
                $param = $rule[$ruleValue];
                $msg = $this->fillErrorMsgWithParam($rule[0], (string)$param, $errorMessages[$ruleValue]);
            } else {
                $msg = $errorMessages[$rule->value];
            }

            $passwordMessages[] = $msg;
        }

        return $passwordMessages;
    }
}
