<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportEvidence extends Model
{
    protected $table = 'report_evidences';

    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'evidence_path',
        'evidence_type',
        'description',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
