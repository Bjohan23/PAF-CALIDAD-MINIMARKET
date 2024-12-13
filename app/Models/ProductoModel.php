<?php
namespace App\Models;

use CodeIgniter\Model;

// Modelo de Producto actualizado
class ProductoModel extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    protected $allowedFields = [
        'id_categoria',
        'nombre',
        'slug',
        'descripcion',
        'precio',
        'precio_oferta',
        'stock',
        'codigo_barras',
        'imagen_url',
        'imagenes_adicionales',
        'meta_descripcion',
        'meta_keywords',
        'destacado',
        'is_active'
    ];
    // Método para generar slug único
    public function generateSlug($nombre)
    {
        $slug = url_title($nombre, '-', true);
        $count = 0;
        $originalSlug = $slug;
        
        while ($this->where('slug', $slug)->first()) {
            $count++;
            $slug = $originalSlug . '-' . $count;
        }
        
        return $slug;
    }
}