<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class RoleManager extends Component
{
    public ?string $editingRole = null;

    public array $selectedPermissions = [];

    protected function getPermissionGroups(): array
    {
        return [
            'Berita' => ['posts-list', 'posts-create', 'posts-edit', 'posts-delete'],
            'Catatan' => ['notes-list', 'notes-create', 'notes-edit', 'notes-delete'],
            'Laporan' => ['reports-list', 'reports-create', 'reports-edit', 'reports-delete'],
            'Galeri' => ['gallery-list', 'gallery-create', 'gallery-edit', 'gallery-delete'],
            'Aspirasi' => ['aspirations-list', 'aspirations-edit', 'aspirations-delete', 'aspirations-export'],
            'Profil' => ['profile-list', 'profile-edit'],
            'Visi & Misi' => ['missions-list', 'missions-create', 'missions-edit', 'missions-delete'],
            'Program' => ['programs-list', 'programs-create', 'programs-edit', 'programs-delete'],
            'Kategori' => ['categories-list', 'categories-create', 'categories-edit', 'categories-delete'],
            'Tags' => ['tags-list', 'tags-create', 'tags-edit', 'tags-delete'],
            'Users' => ['users-list', 'users-create', 'users-edit', 'users-delete'],
            'Roles' => ['roles-list', 'roles-create', 'roles-edit', 'roles-delete'],
            'Pengaturan' => ['settings-list', 'settings-edit'],
            'Audit & Log' => ['audit-logs-list', 'system-logs-list'],
        ];
    }

    protected function getSystemToolPermissions(): array
    {
        return [
            'system-email-tester' => 'Email Tester',
            'system-queue-monitor' => 'Queue Monitor',
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
        ];
    }

    public function editRole(string $roleName): void
    {
        $this->editingRole = $roleName;
        $role = Role::findByName($roleName);
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    public function cancelEdit(): void
    {
        $this->editingRole = null;
        $this->selectedPermissions = [];
    }

    public function savePermissions(): void
    {
        if (! $this->editingRole) {
            return;
        }

        $role = Role::findByName($this->editingRole);
        $role->syncPermissions($this->selectedPermissions);

        $this->dispatch('notify', type: 'success', message: "Permission untuk role {$this->editingRole} berhasil diperbarui.");
        $this->cancelEdit();
    }

    public function render()
    {
        $roles = Role::withCount('users')->get();
        $allPermissions = Permission::all()->pluck('name')->toArray();

        return view('livewire.admin.role-manager', [
            'roles' => $roles,
            'permissionGroups' => $this->getPermissionGroups(),
            'actionLabels' => $this->getActionLabels(),
            'allPermissions' => $allPermissions,
            'systemToolPermissions' => $this->getSystemToolPermissions(),
        ]);
    }
}
