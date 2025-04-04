<?php 
namespace App\Filters;

use illuminate\Http\Request;

class  ApiFilter{
    protected $safeparams = 
    [
      /*  'name' => ['eq'],
        'type' => ['eq'],
        'email' => ['eq'],
        'address' => ['eq'],
        'city' => ['eq'],
        'state' => ['eq'],
        'postalCode' => ['eq','gt','lt']
        */
    ];

    protected $columnMap =
    [
       // 'postalCode' => 'postal_code'
    ];

    protected $operatorMap = 
    [
      /*
        'eq' => '=',
        'lt' => '<',
        'lte' => '≤',
        'gt' => '>',
        'gte' => '≥',
        */
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