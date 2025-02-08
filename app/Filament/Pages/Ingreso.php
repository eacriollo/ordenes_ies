<?php

namespace App\Filament\Pages;

use App\Models\Abonado;
use App\Models\Actividad;
use App\Models\Ciudad;
use App\Models\Material;
use App\Models\Ordene;
use App\Models\Persona;
use App\Models\serializado;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Fieldset;
use App\Models\Precio;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\alert;

class Ingreso extends Page /*implements HasForms*/
{
    use InteractsWithForms;

    public Ordene $orden;

    public $fecha;
    public $acta;
    public $ticket;
    public $manga;
    public $observaciones;
    public $abonado_id;
    public $precio_id;
    public $actividad_id;
    public $ciudad_id;
    public $persona_id;
    public $user_id;

    public $material = [];


    public ?Ordene $orden1 = null;


    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.ingreso';


    public function mount(?int $ordenId = null): void
    {
        if ($ordenId) {
            $this->orden = Ordene::with('materiales.serializado')->findOrFail($ordenId); // Cargar la orden
            $this->form->fill($this->orden->toArray()); // Llenar el formulario

            $this->material = $this->orden->materiales->map(function ($material) {
                return [
                    'material_id' => $material->id,
                    'cantidad' => $material->pivot->cantidad,
                    'serializado_id' => $material->serializado->serie ?? null,
                    'estado_id' => $material->serializado->estado ?? null,
                ];
            })->toArray();
        }
        //  $this->orden1 = $orden;

        /*      $this->form->fill([
                  'fecha' => '',
                  'acta' => '',
                  'ticket' => '',
                  'manga' => '',
                  'observaciones' => '',
                  'abonado_id' => null,
                  'precio_id' => null,
                  'actividad_id' => null,
                  'ciudad_id' => null,
                  'persona_id' => null,

              ]);*/

    }

    public function getUser()
    {
        /* $tamano = count($this->material);

         dd($tamano);*/
        //$data = $this->form->getState();

        dd($this->orden);
        /*
                foreach ($this->material as $fila) {
                   // dump($fila['serializado_id']);
                    if ($fila['serializado_id'] != null) {
                        dump($fila['serializado_id']);
                    }else{
                        dump('no hay serializado');
                    }
                }*/
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
                        ->label('Abonado')
                        ->required()
                        ->default($this->orden1?->abonado_id ?? null)
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
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $abonado = Abonado::find($state);

                            if ($abonado) {
                                $set('nombre_abonado', $abonado->nombre);
                                $set('codigo_abonado', $abonado->codigo);
                                $set('plan_abonado', $abonado->plan);
                            } else {
                                $set('nombre', null);
                                $set('codigo', null);
                                $set('plan', null);
                            }
                        }),

                    Forms\Components\Placeholder::make('nombre_abonado')
                        ->label('Abonado')
                        ->content(fn($get) => $get('nombre_abonado') ?? ''),
                    Forms\Components\Placeholder::make('codigo_abonado')
                        ->label('codigo')
                        ->content(fn($get) => $get('codigo_abonado') ?? ''),
                    forms\Components\Placeholder::make('plan_abonado')
                        ->label('plan')
                        ->content(fn($get) => $get('plan_abonado') ?? ''),


                ])->columns(4)
                ->model(Ordene::class),
            Forms\Components\Section::make('DATOS ACTA')
                ->schema([
                    Forms\Components\DatePicker::make('fecha')
                        ->required()
                        ->default($this->orden1?->fecha ?? null),
                    Forms\Components\TextInput::make('acta')
                        ->required()
                        ->default($this->orden1?->acta ?? null),
                    Forms\Components\TextInput::make('ticket')
                        ->unique()
                        ->required(),
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
                                ->label('Serie')
                                ->required()
                                ->options(function (callable $get) {
                                    $materialId = $get('material_id');
                                    return $materialId
                                        ? serializado::where('material_id', $materialId)->pluck('serie', 'id')->toArray()
                                        : [];
                                })
                                ->searchable()
                                ->hidden(function (callable $get) {
                                    $materialId = $get('material_id');
                                    $material = Material::find($materialId);
                                    return $material && $material->tipo == 'generico';
                                })
                                ->reactive(),
                            Forms\Components\Select::make('estado_id')
                                ->label('Estado')
                                ->required()
                                ->options(function (callable $get) {
                                    $materialId = $get('material_id');
                                    // Filtrar estados solo para el material seleccionado
                                    return $materialId
                                        ? ['Disponible' => 'Disponible',
                                            'Vendido' => 'Vendido',
                                            'Instalado' => 'Instalado',
                                            'Dañado' => 'Dañado',
                                            'Devuelto' => 'Devuelto',
                                            'Retirado' => 'Retirado',]
                                        : [];
                                })
                                ->searchable()
                                ->hidden(function (callable $get) {
                                    // Ocultar este campo si el material seleccionado no es serializado
                                    $materialId = $get('material_id');
                                    $material = Material::find($materialId);
                                    return $material && $material->tipo === 'generico';
                                })
                                ->reactive(),

                        ])->columns(4),
                ])
            //  ->model(Ordene::class)
            // ->columns(3)

        ];
        // ->model($this->orden);
    }

    /*    public function form(Form $form): Form
        {
            return $form
                ->schema([

                    Forms\Components\Section::make('DATOS ABONADO')
                        ->schema([
                            Forms\Components\Select::make('abonado_id')
                                ->relationship('abonado', 'plan')
                                ->label('Abonado')
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
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $abonado = Abonado::find($state);

                                    if ($abonado) {
                                        $set('nombre_abonado', $abonado->nombre);
                                        $set('codigo_abonado', $abonado->codigo);
                                        $set('plan_abonado', $abonado->plan);
                                    } else {
                                        $set('nombre', null);
                                        $set('codigo', null);
                                        $set('plan', null);
                                    }
                                }),

                            Forms\Components\Placeholder::make('nombre_abonado')
                                ->label('Abonado')
                                ->content(fn($get) => $get('nombre_abonado') ?? ''),
                            Forms\Components\Placeholder::make('codigo_abonado')
                                ->label('codigo')
                                ->content(fn($get) => $get('codigo_abonado') ?? ''),
                            forms\Components\Placeholder::make('plan_abonado')
                                ->label('plan')
                                ->content(fn($get) => $get('plan_abonado') ?? ''),


                        ])->columns(4),
                    Forms\Components\Section::make('DATOS ACTA')
                        ->schema([
                            Forms\Components\DatePicker::make('fecha')
                                ->required(),
                            Forms\Components\TextInput::make('acta')
                                ->required(),
                            Forms\Components\TextInput::make('ticket')
                                ->unique()
                                ->required(),
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
                        ])->columns(3),

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
                                        ->label('Serie')
                                        ->required()
                                        ->options(function (callable $get) {
                                            $materialId = $get('material_id');
                                            return $materialId
                                                ? serializado::where('material_id', $materialId)->pluck('serie', 'id')->toArray()
                                                : [];
                                        })
                                        ->searchable()
                                        ->hidden(function (callable $get) {
                                            $materialId = $get('material_id');
                                            $material = Material::find($materialId);
                                            return $material && $material->tipo == 'generico';
                                        })
                                        ->reactive(),
                                    Forms\Components\Select::make('estado_id')
                                        ->label('Estado')
                                        ->required()
                                        ->options(function (callable $get) {
                                            $materialId = $get('material_id');
                                            // Filtrar estados solo para el material seleccionado
                                            return $materialId
                                                ? ['Disponible' => 'Disponible',
                                                    'Vendido' => 'Vendido',
                                                    'Instalado' => 'Instalado',
                                                    'Dañado' => 'Dañado',
                                                    'Devuelto' => 'Devuelto',
                                                    'Retirado' => 'Retirado',]
                                                : [];
                                        })
                                        ->searchable()
                                        ->hidden(function (callable $get) {
                                            // Ocultar este campo si el material seleccionado no es serializado
                                            $materialId = $get('material_id');
                                            $material = Material::find($materialId);
                                            return $material && $material->tipo === 'generico';
                                        })
                                        ->reactive(),

                                ])->columns(4),
                        ])


                ])
                //->statePath('data')
                ->model(Ordene::class)
                ->columns(3);
        }*/

    public function guardar(): void
    {

        DB::beginTransaction();

        try {
            $data = $this->form->getState();
            unset($data ['material']);
            $data['user_id'] = Auth()->id();

            if ($this->orden1) {
                $val = 'actualizado';
                $this->orden1->update($data);
                $orden = $this->orden1;

            } else {

                $val = 'creado';
                $orden = Ordene::create($data);
                $this->orden1 = $orden;

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
            dd($val);
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e);
        }


        /*
                Ordene::create($data);

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

        */
        /*
        if($this->material['serializado_id'] != null){


        }*/
    }


    public function getHeaderActions(): array
    {

        return [
            Action::make('guardar')
                ->label('GUARDAR')
                ->action(function () {
                    $this->guardar();
                    Notification::make('Se guardo correctamente')
                        ->success()
                        ->body('Se guardo correctamente')
                        ->color('success')
                        ->send();

                }),

            Action::make('User')
                ->label('getUser')
                ->action(fn() => $this->getUser())
                ->color('danger'),

        ];
    }


}
