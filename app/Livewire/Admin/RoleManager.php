<?php

namespace App\Livewire\Admin;

use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class RoleManager extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public ?int $confirmingDelete = null;

    public ?string $deleteError = null;

    // Permission editor state
    public ?int $editingRoleId = null;

    public ?string $editingRoleName = null;

    public array $selectedPermissions = [];

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\-_\s]+$/',
                Rule::unique('roles', 'name')->ignore($this->editingId),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
            'name.regex' => 'Nama role hanya boleh mengandung huruf, angka, spasi, tanda hubung, dan underscore.',
        ];
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'role-modal');
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', 'role-modal');
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->confirmingDelete = null;
        $this->deleteError = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        Role::updateOrCreate(
            ['id' => $this->editingId],
            ['name' => $this->name, 'guard_name' => 'web']
        );

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: $this->editingId ? 'Role berhasil diperbarui.' : 'Role berhasil ditambahkan.');
    }

    public function edit(int $id): void
    {
        $role = Role::findOrFail($id);
        $this->editingId = $role->id;
        $this->name = $role->name;
        $this->dispatch('open-modal', 'role-modal');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDelete = $id;
        $this->deleteError = null;
    }

    public function delete(): void
    {
        if (! $this->confirmingDelete) {
            return;
        }

        $role = Role::findOrFail($this->confirmingDelete);

        if ($role->name === 'super-admin') {
            $this->deleteError = 'Role Super Admin tidak bisa dihapus.';

            return;
        }

        $role->delete();
        $this->confirmingDelete = null;
        $this->deleteError = null;
        $this->dispatch('notify', type: 'success', message: 'Role berhasil dihapus.');
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = null;
        $this->deleteError = null;
    }

    // Permission editing (pre-existing, converted to ID-based)
    public function editRolePermissions(int $roleId): void
    {
        $this->editingRoleId = $roleId;
        $role = Role::findOrFail($roleId);
        $this->editingRoleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    public function cancelEditPermissions(): void
    {
        $this->editingRoleId = null;
        $this->editingRoleName = null;
        $this->selectedPermissions = [];
    }

    public function savePermissions(): void
    {
        if (! $this->editingRoleId) {
            return;
        }

        $role = Role::findOrFail($this->editingRoleId);
        $role->syncPermissions($this->selectedPermissions);

        $this->dispatch('notify', type: 'success', message: "Permission untuk role {$role->name} berhasil diperbarui.");
        $this->cancelEditPermissions();
    }

    protected function getPermissionGroups(): array
    {
        return [
            'Dashboard' => ['dashboard-access'],
            'Berita' => ['posts-list', 'posts-create', 'posts-edit', 'posts-delete'],
            'Kategori' => ['categories-list', 'categories-create', 'categories-edit', 'categories-delete'],
            'Tags' => ['tags-list', 'tags-create', 'tags-edit', 'tags-delete'],
            'Users' => ['users-list', 'users-create', 'users-edit', 'users-delete'],
            'Roles' => ['roles-list', 'roles-create', 'roles-edit', 'roles-delete'],
            'Pengaturan' => ['settings-list', 'settings-edit'],
            'Audit & Log' => ['audit-logs-list', 'system-logs-list'],
            'System Tools' => ['system-email-tester', 'system-queue-monitor', 'schedule-tasks-list', 'schedule-tasks-execute'],
        ];
    }

    protected function getActionLabels(): array
    {
        return [
            'list' => 'Lihat',
            'create' => 'Tambah',
            'edit' => 'Edit',
            'delete' => 'Hapus',
            'export' => 'Export',
            'access' => 'Akses',
            'execute' => 'Jalankan',
        ];
    }

    public function render(): View
    {
        $roles = Role::withCount('users')->orderByRaw("CASE WHEN name = 'super-admin' THEN 0 ELSE 1 END, name")->get();
        $allPermissions = Permission::pluck('name')->toArray();

        return view('livewire.admin.role-manager', [
            'roles' => $roles,
            'permissionGroups' => $this->getPermissionGroups(),
            'actionLabels' => $this->getActionLabels(),
            'allPermissions' => $allPermissions,
        ]);
    }
}
