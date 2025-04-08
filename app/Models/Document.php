<?php

namespace App\Models;

use App\Enums\{
    DocumentStatus,
    DocumentType
};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'file_path', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getStatusLabelAttribute(): string
    {
        try {
            return DocumentStatus::from($this->status)->label();
        } catch (\ValueError) {
            return 'Desconhecido';
        }
    }

    public function getTypeLabelAttribute(): string
    {
        try {
            return DocumentType::from($this->type)->label();
        } catch (\ValueError) {
            return 'Desconhecido';
        }
    }

    protected $appends = ['status_label', 'type_label'];

}
