<?php

namespace App\Filament\Pages;

use App\Models\Abonado;
use App\Models\Actividad;
use App\Models\Ciudad;
use App\Models\Material;
use App\Models\Ordene;
use App\Models\Persona;
use App\Models\serializado;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use App\Models\Precio;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Facades\DB;


class Ingreso extends Page /*implements HasForms*/
{
    use InteractsWithForms;

    public Ordene $orden;

    public $mostrarModalConfirmacion = false;
    public $mensajeAdvertencia = '';
    public $fecha;
    public $acta;
    public $ticket;
    public $manga;
    public $observaciones;
    public $abonado_id;

    public $abonadoTemp = null;
    public $precio_id;
    public $actividad_id;
    public $ciudad_id;
    public $persona_id;
    public $user_id;

    public $material = [];


    public $orden1;

   /* public static function canAccess(Authorizable $user): bool
    {
        return $user->can(); // Asegúrate de que el permiso

    }*/

    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-m-plus';

    protected static string $view = 'filament.pages.ingreso';

    protected static ?string $navigationGroup = 'Resgistro';


    public function mount(?int $ordenId = null): void
    {
        if ($ordenId) {
            $this->orden1 = $ordenId;
            $this->orden = Ordene::with('materiales')->findOrFail($ordenId); // Cargar la orden
            $this->form->fill($this->orden->toArray()); // Llenar el formulario

            $this->material = $this->orden->materiales->map(function ($material) {

                return [
                    'material_id' => $material->id,
                    'cantidad' => $material->pivot->cantidad,
                    'serializado_id' => $material->pivot->serializado_id,
                    'estado_id' => optional($material->serializados->where('id', $material->pivot->serializado_id)->first())->estado,
                ];
            })->toArray();

        }

    }

    public function getUser()
    {


        dd(Ordene::find($this->orden1));

    }

    public static function getSlug(): string
    {
        return 'admin/ingreso/{ordenId?}'; // Parámetro opcional
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('DATOS ABONADO')
                ->schema([
                    Forms\Components\Select::make('abonado_id')
                        ->relationship('abonado', 'plan')
                        ->label('Plan')
                        ->required()
                        ->placeholder('Ingrese el plan')
                        ->searchable(['plan', 'nombre'])
                        ->createOptionForm([
                            Forms\Components\TextInput::make('nombre')
                                ->maxLength(255)
                                ->default(null),
                            Forms\Components\TextInput::make('plan')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->validationMessages([
                                    'unique' => 'El abonado ya existe'
                                ])
                                ->default(null),
                            Forms\Components\TextInput::make('codigo')
                                ->maxLength(255)
                                ->default(null),
                        ])
                        ->afterStateUpdated(function ($state, callable $set) {
                            //dd($state);
                            $abonado = Abonado::find($state);
                            if ($abonado) {
                                $this->abonadoTemp = $abonado;
                                $set('nombre_abonado', $abonado->nombre);
                                // $set('codigo_abonado', $abonado->codigo);

                            } else {
                                // Limpiar valores si no hay abonado seleccionado
                                $set('nombre_abonado', null);
                                $set('codigo_abonado', null);

                            }

                        })->reactive(),

                    Forms\Components\Placeholder::make('nombre_abonado_placeholder')
                        ->label('Abonado')
                        ->reactive()
                        ->content(fn() => $this->abonadoTemp ? $this->abonadoTemp->nombre : ''),

                    Forms\Components\Placeholder::make('codigo_abonado_placeholder')
                        ->label('Código')
                        ->content(fn() => $this->abonadoTemp ? $this->abonadoTemp->codigo : ''),


                ])->columns(4)
                ->model(Ordene::class),
            Forms\Components\Section::make('DATOS ACTA')
                ->schema([
                    Forms\Components\DatePicker::make('fecha')
                        ->required(),

                    Forms\Components\TextInput::make('acta')
                        ->required(),

                    Forms\Components\TextInput::make('ticket')
                        ->required()
                        ->unique(
                            table: 'ordenes',
                            column: 'ticket',
                            ignorable: function () {
                                if ($this->orden1) {

                                    return Ordene::find($this->orden1);
                                }
                                return null;
                            }
                        ),

                    Forms\Components\TextInput::make('manga')
                        ->required(),


                    Forms\Components\Select::make('precio_id')
                        //->relationship('precio', 'precio')
                        ->label('Precio')
                        ->options(Precio::where('activo', 1)->pluck('precio', 'id')->toArray())
                        ->searchable()
                        ->placeholder('Ingrese el precio')
                        ->required()
                        ->optionsLimit(5),

                    Forms\Components\Select::make('actividad_id')
                        ->label('Actividad')
                        ->options(Actividad::pluck('tipo_actividad', 'id')->toArray())
                        ->searchable()
                        ->placeholder('Ingrese el actividad')
                        ->required()
                        ->optionsLimit(5),

                    forms\Components\Select::make('ciudad_id')
                        ->label('Ciudad')
                        ->options(Ciudad::pluck('nombre', 'id')->toArray())
                        ->searchable()
                        ->placeholder('Ingrese la ciudad')
                        ->required()
                        ->optionsLimit(5),

                    Forms\Components\Select::make('persona_id')
                        ->label('Persona')
                        ->options(Persona::pluck('nombre', 'id')->toArray())
                        ->searchable()
                        ->placeholder('Ingrese el persona')
                        ->required()
                        ->optionsLimit(5),

                    Forms\Components\Textarea::make('observaciones')->required(),
                ])->columns(3)
                ->model(Ordene::class),

            Forms\Components\Section::make('MATERIAL')
                ->schema([

                    Forms\Components\Repeater::make('material')
                        ->schema([
                            Forms\Components\Select::make('material_id')
                                ->label('Material')
                                ->required()
                                ->options(Material::pluck('nombre', 'id')->toArray())
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('serializado_id', null);
                                    $set('estado_id', null);
                                }),
                            Forms\Components\TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->required(),

                            Forms\Components\Select::make('serializado_id')
                                ->relationship('serializados', 'serie')
                                ->label('Serie')
                                ->required()
                                ->options(function (callable $get, $state) {
                                    $materialId = $get('material_id');
                                    return $materialId
                                        ? Serializado::where('material_id', $materialId)->pluck('serie', 'id')
                                            ->where('estado', 'Disponible')
                                            ->toArray()
                                        : [];
                                })
                                ->unique('material_ordenes', 'serializado_id')
                                ->searchable()
                                ->hidden(function (callable $get) {
                                    $materialId = $get('material_id');
                                    $material = Material::find($materialId);
                                    return $material && $material->tipo == 'generico';
                                })
                                ->reactive()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('serie')
                                        ->label('Serie')
                                        ->required()
                                        ->unique('serializados', 'serie'),
                                    // ->validationRules(['unique:serializados,serie'])
                                    Forms\Components\DatePicker::make('fecha'),
                                    Forms\Components\Select::make('estado')
                                        ->options([
                                            'Disponible' => 'Disponible',
                                            'Vendido' => 'Vendido',
                                            'Instalado' => 'Instalado',
                                            'Dañado' => 'Dañado',
                                            'Devuelto' => 'Devuelto',
                                            'Retirado' => 'Retirado',
                                        ])
                                        ->required(),
                                    Forms\Components\Select::make('material_id')
                                        ->relationship('material', 'nombre')
                                        ->options(Material::where('tipo', 'serializado')->pluck('nombre', 'id')->toArray())
                                        ->searchable()
                                        ->required(),
                                ]),

                            Forms\Components\Select::make('estado_id')
                                ->label('Estado')
                                ->required()
                                ->options([ // Opciones de estado
                                    'Disponible' => 'Disponible',
                                    'Vendido' => 'Vendido',
                                    'Instalado' => 'Instalado',
                                    'Dañado' => 'Dañado',
                                    'Devuelto' => 'Devuelto',
                                    'Retirado' => 'Retirado',
                                ])
                                ->hidden(function (callable $get) {
                                    // Ocultar este campo si el material seleccionado no es serializado
                                    $materialId = $get('material_id');
                                    $material = Material::find($materialId);
                                    return $material && $material->tipo === 'generico';
                                })
                                ->reactive(),

                        ])->columns(4)
                        ->model(Material::class),
                ])


        ];

    }


    public function guardar(): void
    {

        DB::beginTransaction();

        try {
            $data = $this->form->getState();
            //dd($data);
            unset($data ['material']);
            $data['user_id'] = Auth()->id();
            //dd($data);

            if ($this->orden1 != null) {

                $this->orden->update($data);
                $orden = $this->orden;

            } else {

                $orden = Ordene::create($data);


            }

            $orden->materiales()->detach();

            if (!empty($this->material)) {
                foreach ($this->material as $fila) {

                    $orden->materiales()
                        ->attach($fila['material_id'],
                            ['cantidad' => $fila['cantidad'], 'serializado_id' => $fila['serializado_id']]);


                    if (!empty($fila['serializado_id'])) {

                        Serializado::where('id', $fila['serializado_id'])
                            ->update([
                                'estado' => $fila['estado_id'],
                                'fecha' => $data['fecha']
                            ]);
                    }
                }
            }

            DB::commit();


            $this->form->fill(['fecha' => '',
                'acta' => '',
                'ticket' => '',
                'manga' => '',
                'observaciones' => '',
                'abonado_id' => null,
                'precio_id' => null,
                'actividad_id' => null,
                'ciudad_id' => null,
                'persona_id' => null,]);
            $this->material = [];

            $this->abonadoTemp = null;

            Notification::make()
                ->title($this->orden1 ? 'Orden actualizada' : 'Orden guardada')
                ->body($this->orden1 ? 'Se actualizó correctamente.' : 'Se guardó correctamente.')
                ->success()
                ->send();

            $fechaOrden = Carbon::parse($data['fecha']);
            $fechaLimite = $fechaOrden->copy()->subDays(30);
            $ordenExistente = Ordene::where('abonado_id', $data['abonado_id'])
                ->where('fecha', '>=', $fechaLimite)
                ->where('fecha', '<', $fechaOrden)
                ->exists();
            if ($ordenExistente) {

                Notification::make()
                    ->title('Advertencia')
                    ->body('Este abonado tiene una orden registrada en los últimos 30 días.')
                    ->danger()
                    ->persistent()
                    ->color('danger')
                    ->send();
            }


        } catch (\Exception $e) {
            DB::rollBack();

            //dd($e);

            Notification::make()
                ->title('Error')
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->danger()
                ->send();
        }


    }


    public function getHeaderActions(): array
    {

        return [
            Action::make('guardar')
                ->label('Guardar')
                ->action(function () {
                    $this->guardar();
                }),


            Action::make('User')
                ->label('getUser')
                ->action(fn() => $this->getUser())
                ->color('danger'),

        ];
    }


}
