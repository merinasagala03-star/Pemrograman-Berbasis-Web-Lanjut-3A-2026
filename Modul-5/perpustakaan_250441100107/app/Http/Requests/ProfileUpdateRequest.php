<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Hanya field di bawah ini yang diproses. id, role, dan anggota_id
     * dari request diabaikan (data yang diubah selalu milik user yang login).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $anggotaId = $this->user()->anggota_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
                Rule::unique('anggotas', 'email')->ignore($anggotaId),
            ],
            'no_telepon' => ['required', 'string', 'max:20'],
        ];
    }
}
