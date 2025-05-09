<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class Maestro extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'apellidos',
        'direccion',
        'curp',
        'telefono',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function imparte()
    {
        return $this->hasMany(Imparte::class)->with(['grupo', 'materia']);
    }

    public function gruposImpartidos($generacion)
    {
        return $this->imparte->filter(function ($imparte) use ($generacion) {
            return $imparte->grupo->generacion == $generacion;
        });
    }

    /**
     * Obtiene los grupos únicos que imparte el maestro, filtrados por año
     * 
     * @param int|null $año El año para filtrar los grupos por generación, por defecto el año actual
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerGruposImpartidos($año = null)
    {
        $año = $año ?? date('Y');

        // Obtener grupos únicos que imparte el maestro
        $imparte = $this->imparte()
            ->with(['grupo', 'materia'])
            ->whereHas('grupo', function ($query) use ($año) {
                $query->where('generacion', '<=', $año);
            })
            ->get();

        // Agrupar por grupo para el dropdown
        $gruposUnicos = $imparte->pluck('grupo')->unique('id')->values();

        return $gruposUnicos->map(function ($grupo) {
            return [
                'id' => $grupo->id,
                'nombre' => $grupo->grado . '°' . $grupo->letra . ' (Gen. ' . $grupo->generacion . ')'
            ];
        });
    }

    /**
     * Obtiene las materias que el maestro imparte en un grupo específico
     * 
     * @param int $grupoId El ID del grupo
     * @return array
     */
    public function obtenerMateriasImpartidasEnGrupo($grupoId)
    {
        if (!$grupoId) {
            return [];
        }

        $imparte = $this->imparte()
            ->with(['materia'])
            ->where('grupo_id', $grupoId)
            ->get();

        return $imparte->map(function ($relacion) {
            return [
                'id' => $relacion->materia_id,
                'imparte_id' => $relacion->id,
                'nombre' => $relacion->materia->nombre . ' (' . $relacion->materia->clave . ')'
            ];
        })->toArray();
    }
}
