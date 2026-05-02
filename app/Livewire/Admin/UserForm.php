<?php

namespace App\Livewire\Admin;

use App\Mail\UserInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class UserForm extends Component
{
    public ?User $user = null;
    public bool $isCreate = true;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = '';
    public bool $is_active = true;

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists) {
            $this->user = $user;
            $this->isCreate = false;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->is_active = $user->is_active;
            $this->role = $user->roles->first()?->name ?? '';
        } else {
            $this->isCreate = true;
            $this->role = 'editor';
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user?->id),
            ],
            'password' => [$this->isCreate ? 'required' : 'nullable', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:super-admin,editor,viewer'],
            'is_active' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isCreate) {
            $this->user = User::create($data);
            $this->user->syncRoles([$this->role]);

            $this->dispatch('notify', type: 'success', message: 'User berhasil dibuat.');
            $this->redirectRoute('admin.users.edit', ['user' => $this->user], navigate: true);
        } else {
            $this->user->update($data);
            $this->user->syncRoles([$this->role]);

            $this->dispatch('notify', type: 'success', message: 'User berhasil diperbarui.');
        }
    }

    public function sendInvitation(): void
    {
        if (! $this->user) {
            return;
        }

        Mail::to($this->user->email)->queue(new UserInvitation($this->user));
        $this->dispatch('notify', type: 'success', message: "Undangan telah dikirim ke {$this->user->email}.");
    }

    public function render()
    {
        return view('livewire.admin.user-form', [
            'roles' => Role::all(),
        ]);
    }
}
