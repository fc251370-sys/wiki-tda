<?php 

// Indica que este modelo pertenece al espacio de nombres App\Models
namespace App\Models; 

// Importa el modelo base de Eloquent
use Illuminate\Database\Eloquent\Model; 

// Permite definir relaciones de tipo "pertenece a"
use Illuminate\Database\Eloquent\Relations\BelongsTo; 


// Modelo ArticleTag
// Representa la tabla intermedia que conecta artículos con etiquetas
class ArticleTag extends Model 
{ 
    // Indica que este modelo utiliza la tabla "article_tag"
    protected $table = 'article_tag'; 

    // La tabla no tiene las columnas created_at y updated_at
    public $timestamps = false; 

    // Define una clave primaria compuesta:
    // article_id + tag_id
    protected $primaryKey = ['article_id', 'tag_id']; 

    // Indica que la clave primaria no es autoincremental
    public $incrementing = false; 

    // Campos que pueden ser asignados de manera masiva
    protected $fillable = [ 
        'article_id', 
        'tag_id', 
    ]; 


    // Relación con el modelo WikiArticle
    // Cada registro de article_tag pertenece a un artículo
    public function article(): BelongsTo 
    { 
        return $this->belongsTo(
            WikiArticle::class, 
            'article_id'
        ); 
    } 


    // Relación con el modelo WikiTag
    // Cada registro de article_tag pertenece a una etiqueta
    public function tag(): BelongsTo 
    { 
        return $this->belongsTo(
            WikiTag::class, 
            'tag_id'
        ); 
    } 
}