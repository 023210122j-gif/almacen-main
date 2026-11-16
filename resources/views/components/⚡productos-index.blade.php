<?php

use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Producto;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url]
    public $buscar = '';

    #[Url]
    public $categoria_id = '';

    #[Url]
    public $almacen_id = '';

    #[Url]
    public $stock_bajo = false;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'categoria_id', 'almacen_id', 'stock_bajo'])) {
            $this->resetPage();
        }
    }

    public function with(): array
    {
        $consulta = Producto::query()
            ->select('productos.*')
            ->with(['cantidad.almacen', 'categoria']);

        if ($this->buscar) {
            $consulta->where(function ($q) {
                $q->where('productos.nombre', 'like', '%' . $this->buscar . '%')
                  ->orWhere('productos.codigo', 'like', '%' . $this->buscar . '%');
            });
        }

        if ($this->categoria_id) {
            $consulta->where('productos.categoria_id', $this->categoria_id);
        }

        if ($this->almacen_id || $this->stock_bajo) {
            $consulta->join('stock__almacen', 'productos.id', '=', 'stock__almacen.producto_id');

            if ($this->almacen_id) {
                $consulta->where('stock__almacen.almacen_id', $this->almacen_id);
            }

            if ($this->stock_bajo) {
                $consulta->where('stock__almacen.stock', '<=', 5);
            }

            $consulta->distinct();
        }

        return [
            'productos'  => $consulta->orderBy('productos.nombre')->paginate(10),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'almacenes'  => Almacen::orderBy('nombre')->get(),
        ];
    }
};
?>

<div class="p-4 text-gray-900">
    {{-- Barra de búsqueda y filtros --}}
    <div class="flex flex-wrap gap-3 mb-4 items-center">
        <input 
            type="text" 
            wire:model.live.debounce.400ms="buscar" 
            placeholder="Buscar por nombre o código"
            class="border border-gray-400 bg-white rounded px-3 py-2 text-sm text-gray-800"
        >

        <select wire:model.live="categoria_id" class="border border-gray-400 bg-white rounded px-3 py-2 text-sm text-gray-800">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
            @endforeach
        </select>

        <select wire:model.live="almacen_id" class="border border-gray-400 bg-white rounded px-3 py-2 text-sm text-gray-800">
            <option value="">Todos los almacenes</option>
            @foreach ($almacenes as $almacen)
                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
            @endforeach
        </select>

        <label class="flex items-center gap-2 text-sm font-medium text-gray-900 cursor-pointer bg-white/80 px-3 py-2 rounded border border-gray-400">
            <input type="checkbox" wire:model.live="stock_bajo" class="rounded">
            Solo stock bajo (&le; 5)
        </label>
    </div>

    {{-- Tabla de productos --}}
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-200 border-b border-gray-300 text-gray-800 text-sm">
                    <th class="p-3">Código</th>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Categoría</th>
                    <th class="p-3">Precio</th>
                    <th class="p-3">Almacén</th>
                    <th class="p-3">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 text-sm">
                        <td class="p-3">{{ $producto->codigo ?? 'S/C' }}</td>
                        <td class="p-3 font-medium">{{ $producto->nombre }}</td>
                        <td class="p-3">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</td>
                        <td class="p-3">S/ {{ number_format($producto->precio, 2) }}</td>
                        <td class="p-3">{{ $producto->cantidad->first()?->almacen?->nombre ?? 'Sin almacén' }}</td>
                        <td class="p-3">{{ $producto->cantidad->first()?->stock ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">Sin datos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
