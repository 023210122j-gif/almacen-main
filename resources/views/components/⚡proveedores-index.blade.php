<?php

use App\Models\Proveedor;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $buscar = '';

    #[On('proveedor-guardado')]
    public function refrescar()
    {
    }

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'proveedores' => Proveedor::where('nombre', 'like', "%{$this->buscar}%")
                ->orWhere('ruc', 'like', "%{$this->buscar}%")
                ->orderByDesc('id')
                ->paginate(10),
        ];
    }
};
?>

<div class="space-y-4">
    <div>
        <input type="text" wire:model.live="buscar" placeholder="Buscar por RUC o nombre..." class="border rounded px-3 py-1">
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b bg-gray-100">
                <th class="p-2">RUC</th>
                <th class="p-2">Nombre / Razón Social</th>
                <th class="p-2">Teléfono</th>
                <th class="p-2">Correo</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proveedores as $proveedor)
                <tr class="border-b">
                    <td class="p-2">{{ $proveedor->ruc }}</td>
                    <td class="p-2">{{ $proveedor->nombre }}</td>
                    <td class="p-2">{{ $proveedor->telefono ?? 'S/T' }}</td>
                    <td class="p-2">{{ $proveedor->correo ?? 'S/C' }}</td>
                    <td class="p-2">
                        <button wire:click="$dispatch('editar-proveedor', { id: {{ $proveedor->id }} })" class="text-blue-600 hover:underline">
                            Editar
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">No hay proveedores registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $proveedores->links() }}
    </div>
</div>
