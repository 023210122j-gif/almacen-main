<?php

use App\Models\Proveedor;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?Proveedor $proveedor = null;
    public $ruc = '';
    public $nombre = '';
    public $telefono = '';
    public $correo = '';

    protected function rules(): array
    {
        return [
            'ruc' => [
                'required',
                'digits:11',
                Rule::unique('proveedores', 'ruc')->ignore($this->proveedor?->id),
            ],
            'nombre' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'digits_between:7,15'],
            'correo' => ['nullable', 'email', 'max:100'],
        ];
    }

    protected function messages(): array
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

    public function updated($propiedad)
    {
        if ($propiedad === 'ruc') {
            $this->ruc = trim((string) $this->ruc);
        }
        $this->validateOnly($propiedad);
    }

    public function guardar()
    {
        $datos = $this->validate();

        if ($this->proveedor) {
            $this->proveedor->update($datos);
        } else {
            Proveedor::create($datos);
        }

        $this->cancelar();
        session()->flash('ok', 'Proveedor guardado correctamente.');
        $this->dispatch('proveedor-guardado');
    }

    #[On('editar-proveedor')]
    public function editar($id)
    {
        $this->proveedor = Proveedor::findOrFail($id);
        $this->fill($this->proveedor->only('ruc', 'nombre', 'telefono', 'correo'));
        $this->resetValidation();
    }

    public function cancelar()
    {
        $this->reset();
        $this->resetValidation();
    }
};
?>

<div>
    @if (session('ok'))
        <p class="text-green-700 font-medium mb-2">{{ session('ok') }}</p>
    @endif

    <form wire:submit="guardar" class="space-y-4">
        <h2 class="text-lg font-bold">{{ $proveedor ? 'Editar proveedor' : 'Nuevo proveedor' }}</h2>

        <div>
            <label for="ruc" class="block text-sm font-medium">RUC</label>
            <input id="ruc" type="text" wire:model.blur="ruc" class="w-full border rounded px-3 py-1">
            @error('ruc')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nombre" class="block text-sm font-medium">Nombre / Razón Social</label>
            <input id="nombre" type="text" wire:model.blur="nombre" class="w-full border rounded px-3 py-1">
            @error('nombre')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefono" class="block text-sm font-medium">Teléfono</label>
            <input id="telefono" type="text" wire:model.blur="telefono" class="w-full border rounded px-3 py-1">
            @error('telefono')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="correo" class="block text-sm font-medium">Correo electrónico</label>
            <input id="correo" type="email" wire:model.blur="correo" class="w-full border rounded px-3 py-1">
            @error('correo')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
            @if ($proveedor)
                <button type="button" wire:click="cancelar" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            @endif
        </div>
    </form>
</div>
