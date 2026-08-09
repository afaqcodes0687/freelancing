<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobProposalMilestone extends Model
{
    use HasFactory;

    protected $fillable = ['job_proposal_id', 'title', 'description', 'price', 'revision', 'deadline'];

    public function job_proposal()
    {
        return $this->belongsTo(JobProposal::class, 'job_proposal_id', 'id');
    }
}
