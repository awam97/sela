<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table      = 'invoice';
    protected $primaryKey = 'invoice_id';

    protected $returnType     = 'array';
    protected $allowedFields = ['student_id', 'title', 'description', 'amount', 'amount_paid', 'due', 'creation_timestamp', 'payment_timestamp', 'payment_method', 'payment_details', 'status', 'year', 'school'];

    protected $useTimestamps = false;
}
