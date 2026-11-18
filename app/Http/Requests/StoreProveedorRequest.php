<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ruc' => ['required', 'digits:11', 'unique:proveedores,ruc'],
            'nombre' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'digits_between:7,15'],
            'correo' => ['nullable', 'email', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.digits' => 'El RUC debe tener exactamente 11 dígitos numéricos.',
            'ruc.unique' => 'Ya existe un proveedor registrado con ese RUC.',
            'nombre.required' => 'La razón social o nombre es obligatorio.',
            'nombre.max' => 'El nombre admite como máximo 100 caracteres.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 15 dígitos.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
            'correo.max' => 'El correo admite como máximo 100 caracteres.',
        ];
    }
}
