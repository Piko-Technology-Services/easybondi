<?php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class IdentityDocument extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'type',
        'document_number',
        'issued_country',
        'issued_date',
        'expiry_date',
        'document_file',
        'verified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
