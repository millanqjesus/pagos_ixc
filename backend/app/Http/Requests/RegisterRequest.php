<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:100'],
            'cpf_cnpj'  => ['required', 'string', 'max:20', 'unique:users,cpf_cnpj'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:6'],
            'type'      => ['required', 'in:comun,lojista'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'O campo nome é obrigatório.',
            'name.string'       => 'O nome deve ser uma cadeia de caracteres.',
            'name.max'          => 'O nome não pode ter mais que 100 caracteres.',

            'cpf_cnpj.required' => 'O campo CPF/CNPJ é obrigatório.',
            'cpf_cnpj.string'   => 'O CPF/CNPJ deve ser uma cadeia de caracteres.',
            'cpf_cnpj.max'      => 'O CPF/CNPJ não pode ter mais que 20 caracteres.',
            'cpf_cnpj.unique'   => 'Este CPF/CNPJ já está cadastrado.',

            'email.required'    => 'O campo e-mail é obrigatório.',
            'email.email'       => 'Informe um e-mail válido.',
            'email.unique'      => 'Este e-mail já está cadastrado.',

            'password.required' => 'O campo senha é obrigatório.',
            'password.string'   => 'A senha deve ser uma cadeia de caracteres.',
            'password.min'      => 'A senha deve ter no mínimo 6 caracteres.',

            'type.required'     => 'O campo tipo é obrigatório.',
            'type.in'           => 'O tipo deve ser "comun" ou "lojista".',
        ];
    }


    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
      throw new HttpResponseException(response()->json([
          'errors' => $validator->errors(),
      ], 422));
    }
}
