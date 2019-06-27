<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
//    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'is_active', 'is_delete', 'id'
    ];

    private static $searchable = ['transaction_type', 'transaction_amount', 'transction_date'];
    private static $orderBy = ['transaction_type', 'transaction_amount', 'transction_date'];
    /**
     * Scope a query to only include active records.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('is_active', 1)
            ->where('is_delete', 0);
    }

    /**
     * Get the account record associated with the user
     */
    public function account_details(){
        return $this->hasOne(Accounts::class, 'id', 'account_id')
            ->with('account_type_details');
    }

    /**
     * Get the branch record associated with the user
     */
    public function branch_details(){
        return $this->hasOne(Branches::class, 'id', 'branch_id')
            ->with('address_details');
    }

    /**
     * Scope a query to only include conditional records.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTransactionDate($query, $value) {
        if(!array_key_exists('from', $value)) {
            return false;
        }
        if(!array_key_exists('from', $value)) {
            $value['to'] = strtotime("now");
        }
        return $query->where(function($query) use($value) {
            $start = date("Y-m-d",strtotime($value['from']));
            $end = date("Y-m-d",strtotime($value['to']));
            return $query->whereBetween('created_at',[$start,$end]);
        });
    }

    /**
     * Get the all transaction data for the respective users
     * @param $customer_id
     * @param array $requestInput
     * @return mixed
     */
    public static function customerTransaction($customer_id, $requestInput=[], $orderBy=[]){
         $returnDatas = self::active()
            ->where('customer_id', $customer_id)
            ->with(['account_details', 'branch_details']);

         if(array_key_exists('dateFilter', $requestInput)){
             $returnDatas = $returnDatas->transactionDate($requestInput['dateFilter']);
             unset($requestInput['dateFilter']);
         }

         foreach ($requestInput as $key => $input){
             if(in_array($key, self::$searchable) && !is_array($input)){
                 $returnDatas = $returnDatas->where($key, $input);
             }

             if(in_array($key, self::$searchable) && is_array($input)){
                 $condition = array_key_exists('condition', $input)? $input['condition']:'=';
                 $returnDatas = $returnDatas->where($key, $condition, $input['value']);
             }
         }

        foreach ($orderBy as $item) {
            if(in_array($item['type'], self::$orderBy)){
                $returnDatas = $returnDatas->orderBy($item['type'], $item['order']);
            }
         }
        return  $returnDatas->paginate(env('PAGINATE'));
    }
}
