<div>
    <!-- Toast Notification -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition
         class="fixed top-5 right-5 z-50 px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium"
         :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
         style="display: none;">
        <span x-text="message"></span>
    </div>

    <h1 class="text-xl font-bold text-gray-800 mb-6">Manajemen Roles</h1>

    <!-- Roles Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @foreach($roles as $role)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800 capitalize">{{ $role->name }}</h3>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                        @if($role->name === 'super-admin') bg-purple-100 text-purple-700
                        @elseif($role->name === 'editor') bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $role->users_count }} user
                    </span>
                </div>
                <p class="text-sm text-gray-500 mb-4">{{ $role->permissions->count() }} permission</p>
                @can('roles-edit')
                <button wire:click="editRole('{{ $role->name }}')"
                        class="w-full px-4 py-2 bg-[#1A6FAA] text-white text-sm font-medium rounded-lg hover:bg-[#155a8a] transition-colors">
                    Edit Permission
                </button>
                @endcan
            </div>
        @endforeach
    </div>

    <!-- Permission Editor -->
    @if($editingRole)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Edit Permission: <span class="capitalize">{{ $editingRole }}</span></h2>
                <button wire:click="cancelEdit" class="text-sm text-gray-500 hover:text-gray-700">Batal</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">Modul</th>
                            @foreach(['list', 'create', 'edit', 'delete', 'export'] as $action)
                                <th class="px-4 py-3 text-center">{{ $actionLabels[$action] ?? ucfirst($action) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($permissionGroups as $module => $permissions)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-700">{{ $module }}</td>
                                @foreach(['list', 'create', 'edit', 'delete', 'export'] as $action)
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $permName = collect($permissions)->first(fn($p) => str_ends_with($p, '-' . $action));
                                        @endphp
                                        @if($permName)
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" wire:model="selectedPermissions" value="{{ $permName }}"
                                                       class="w-4 h-4 text-[#1A6FAA] border-gray-300 rounded focus:ring-[#1A6FAA]">
                                            </label>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- System Tools -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">System Tools</h3>
                <div class="flex flex-wrap gap-4">
                    @foreach($systemToolPermissions as $permName => $permLabel)
                        <label class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $permName }}"
                                   class="w-4 h-4 text-[#1A6FAA] border-gray-300 rounded focus:ring-[#1A6FAA]">
                            <span class="text-sm text-gray-700">{{ $permLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button wire:click="cancelEdit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="savePermissions" class="px-4 py-2 bg-[#1A6FAA] text-white text-sm font-medium rounded-lg hover:bg-[#155a8a] transition-colors">Simpan Permission</button>
            </div>
        </div>
    @endif
</div>
