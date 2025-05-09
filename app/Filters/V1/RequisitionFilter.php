<?php
namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class RequisitionFilter extends ApiFilter{
    
    protected $safeparams = [
        'status' => ['eq'],
        'accountId' => ['eq'],
    ];
    
    protected $columnMap = [
        'status' => 'status',
        'accountId' => 'account_id'
    ];
    
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '≤',
        'gt' => '>',
        'gte' => '≥',
        'ne' => '!='
    ];

    protected function operatorMap(string $operator): string
    {
         return $this->operatorMap[$operator] ?? '=';
    }


    public function transform(Request $request){
        $eloQuery = [];

        foreach($this->safeparams as $parm =>$operators){
            $query = $request->query($parm);

            if(!isset($query)){
                continue;
            }

            $column = $this->columnMap[$parm] ?? $parm;

            foreach($operators as $operator){
                if(isset($query[$operator])){
                    $eloQuery[] = [$column, $this->operatorMap[$operator],$query[$operator]];
                }
            }
        }
        return $eloQuery;
    }


}