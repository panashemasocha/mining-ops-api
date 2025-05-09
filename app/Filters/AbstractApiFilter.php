<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class AbstractApiFilter
{
    /**
     * Safe parameters that can be filtered.
     * Format: ['paramName' => ['operator1', 'operator2', ...]]
     */
    protected $safeParams = [];

    /**
     * Map query parameters to database columns.
     * Format: ['queryParam' => 'db_column']
     */
    protected $columnMap = [];

    /**
     * Map operators to their SQL equivalents.
     * Format: ['eq' => '=', 'lt' => '<', ...]
     */
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'ne' => '!='
    ];

    /**
     * Get the SQL operator for a given operator code.
     *
     * @param string $operator
     * @return string
     */
    protected function mapOperator(string $operator): string
    {
        return $this->operatorMap[$operator] ?? '=';
    }

    /**
     * Transform the request query parameters into filter conditions.
     *
     * @param Request $request
     * @return array
     */
    public function transform(Request $request): array
    {
        $filterQuery = [];

        foreach ($this->safeParams as $param => $operators) {
            $query = $request->query($param);

            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $filterQuery[] = [$column, $this->mapOperator($operator), $query[$operator]];
                }
            }
        }

        return $filterQuery;
    }
}