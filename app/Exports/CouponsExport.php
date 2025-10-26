<?php
namespace App\Exports;

use App\Models\Coupon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CouponsExport implements FromCollection, WithHeadings
{
    protected $start;
    protected $end;
    protected $createdAt;
    protected $companyId;
    protected $batch;


    public function __construct($start, $end, $createdAt = null, $companyId, $batch = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->createdAt = $createdAt;
        $this->companyId = $companyId;
        $this->batch = $batch;
    }

    public function collection()
    {
        $query = Coupon::with('company')
            ->when($this->start, function ($q) {
                $q->whereDate('start_date', '>=', $this->start);
            })
            ->when($this->end, function ($q) {
                $q->whereDate('end_date', '<=', $this->end);
            })
            ->when($this->createdAt, function ($q) {
                $time24 = date("H:i:s", strtotime($this->createdAt));
                $q->whereTime('created_at', $time24);
            })
            ->when($this->companyId, function ($q) {
                $q->where('company_id', $this->companyId);
            })
            ->when($this->batch && $this->companyId, function ($q) {
                $q->where('batch', $this->batch);
            });


        return $query->get()->map(function ($coupon, $index) {
            return [
                'id' => $index + 1,
                'company_name' => $coupon->company ? $coupon->company->name : null,
                'batch' => $coupon->batch,
                'code' => $coupon->code,
                'value' => $coupon->value,
                'type' => $coupon->type,
                'discount_type' => $coupon->discount_type,
                'total_games' => $coupon->total_games,
                'active' => $coupon->active,
                'usage_limit' => $coupon->usage_limit,
                'usage_per_user' => $coupon->usage_per_user,
                'start_date' => $coupon->start_date,
                'end_date' => $coupon->end_date,
                'user_id' => $coupon->user_id,
                'created_at' => $coupon->created_at ? $coupon->created_at->diffForHumans() : null,
                'updated_at' => $coupon->updated_at ? $coupon->updated_at->diffForHumans() : null,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company Name',
            'Batch',
            'Code',
            'Value',
            'Type',
            'Discount Type',
            'Total Games',
            'Active',
            'Usage Limit',
            'Usage Per User',
            'Start Date',
            'End Date',
            'User ID',
            'Created At',
            'Updated At',
        ];
    }
}
