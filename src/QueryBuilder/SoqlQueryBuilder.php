<?php

namespace myoutdeskllc\SalesforcePhp\QueryBuilder;

use myoutdeskllc\SalesforcePhp\Constants\SoqlDates;
use myoutdeskllc\SalesforcePhp\Exceptions\InvalidQueryException;

class SoqlQueryBuilder
{
    /** @var string[] */
    private array $fields = [];
    private string $object = '';
    /** @var array<array{0: string, 1: string|null, 2: string|int|null, 3: string}> */
    private array $where = [];
    private int $limit = 0;
    private int $offset = 0;
    /** @var string[] */
    private array $orders = [];
    /** @var int[] */
    private array $groupedConditionalStart = [];
    /** @var int[] */
    private array $groupedConditionalEnd = [];

    /**
     * @param string[] $fields
     */
    public function select(array $fields): self
    {
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }

    public function addSelect(string $field): self
    {
        $this->fields[] = $field;
        return $this;
    }

    public function from(string $object): self
    {
        $this->object = $object;
        return $this;
    }

    public function startWhere(): self
    {
        if(empty($this->where)) {
            $this->groupedConditionalStart[] = 0;
        } else {
            $this->groupedConditionalStart[] = array_key_last($this->where) + 1;
        }
        return $this;
    }

    public function endWhere(): self
    {
        $this->groupedConditionalEnd[] = array_key_last($this->where);
        return $this;
    }

    public function where(string $column, string $operator, mixed $value, string $boolean = 'AND'): self
    {
        $this->where[] = [$column, $operator, $this->prepareWhereValue($value), $boolean];
        return $this;
    }

    public function whereDate(string $column, string $operator, mixed $value, string $boolean = 'AND'): self
    {
        $this->where[] = [$column, $operator, $this->prepareWhereValue( $value, "date"), $boolean];
        return $this;
    }

    public function orWhereDate(string $column, string $operator, mixed $value): self
    {
        return $this->whereDate($column, $operator,  $value, 'OR');
    }

    public function orWhere(string $column, string $operator, mixed $value): self
    {
        return $this->where($column, $operator,  $value, 'OR');
    }

    /**
     * @param array $conditions
     * @param string $boolean
     * @return SoqlQueryBuilder
     */
    public function whereColumn(array $conditions, string $boolean = 'AND'): self
    {
        foreach ($conditions as $condition) {
            $this->where($condition[0], $condition[1], $condition[2], $boolean);
        }
        return $this;
    }

    /**
     * @param string $column
     * @param string[] $restrictions
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereIn(string $column, array $restrictions, string $boolean = 'AND', bool $not = false): self
    {
        foreach ($restrictions as &$restriction) {
            $restriction = $this->prepareWhereValue($restriction);
        }
        unset ($restriction);

        $operator = !$not ? "IN" : "NOT IN";

        $this->where[] = [$column, $operator, '(' . implode(', ', $restrictions) . ')', $boolean];
        return $this;
    }

    /**
     * @param string $column
     * @param string[] $restrictions
     * @return $this
     */
    public function whereNotIn(string $column, array $restrictions): self
    {
        $this->whereIn($column, $restrictions, "AND", true);
        return $this;
    }

    /**
     * @param string $column
     * @param string[] $restrictions
     * @return $this
     */
    public function orWhereIn(string $column, array $restrictions): self
    {
        $this->whereIn($column, $restrictions, 'OR');
        return $this;
    }

    /**
     * @param string $column
     * @param string[] $restrictions
     * @return $this
     */
    public function orWhereNotIn(string $column, array $restrictions): self
    {
        $this->whereIn($column, $restrictions, 'OR', true);
        return $this;
    }

    public function whereFunction(string $column, string $function, mixed $value, string $boolean = 'AND'): self
    {
        if (is_array($value)) {
            foreach ($value as &$item) {
                $item = $this->prepareWhereValue($item);
            }
            unset($item);
            $value = implode(', ', $value);
        } else {
            $value = $this->prepareWhereValue($value);
        }

        $this->where[] = [$column, null, $function . '(' . $value . ')', $boolean];
        return $this;
    }

    /**
     * @param int[] $expressionLocations
     * @param int $index
     * @return int
     */
    private function getGroupExpressionsAtIndex(array $expressionLocations, int $index) : int
    {
        if(empty($expressionLocations)) {
            return 0;
        }
        return count(array_filter($expressionLocations, static function ($expressionLocation) use ($index) {
            return $expressionLocation === $index;
        }));
    }

    /**
     * Escapes a string value for safe use in SOQL queries.
     * Prevents SOQL injection by escaping special characters.
     *
     * @param string $value The string to escape
     * @return string The escaped string
     */
    private function escapeSoqlString(string $value): string
    {
        // Escape backslashes first (must be done before other escapes that use backslash)
        $value = str_replace('\\', '\\\\', $value);

        // Escape single quotes (doubled for SOQL standard)
        $value = str_replace("'", "''", $value);

        // Escape control characters
        $value = str_replace("\n", '\\n', $value);
        $value = str_replace("\r", '\\r', $value);
        $value = str_replace("\t", '\\t', $value);

        return $value;
    }

    private function prepareWhereValue(mixed $value, ?string $forceType = null): mixed
    {
        if ($forceType === "date") {
            return $value;
        }

        if (is_string($value)) {
            $value = "'" . $this->escapeSoqlString($value) . "'";
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif ($value === null) {
            $value = "null";
        }

        return $value;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = $column . ' ' . $direction;

        return $this;
    }

    public function orderByDesc(string $column): self
    {
        return $this->orderBy($column, 'DESC');
    }

    protected function buildFields(): string
    {
        return implode(', ', array_unique($this->fields));
    }

    protected function validateQuery(): void
    {
        if (empty($this->object)) {
            throw new InvalidQueryException('Query must contain sObject name');
        }

        if (empty($this->fields)) {
            throw new InvalidQueryException('Query must contain fields for select');
        }

        if (count($this->groupedConditionalStart) !== count($this->groupedConditionalEnd)) {
            throw new InvalidQueryException('Unmatched parenthesis for grouped expressions. Make sure to call startWhere() and endWhere().');
        }
    }

    protected function buildWhere(): string
    {
        $conditions = [];

        foreach ($this->where as $i => $condition) {
            [$column, $operator, $value, $boolean] = $condition;

            $column = str_repeat('(', $this->getGroupExpressionsAtIndex($this->groupedConditionalStart, $i)) . $column;
            $value .= str_repeat(')', $this->getGroupExpressionsAtIndex($this->groupedConditionalEnd, $i));

            $expression = implode(' ', array_filter([$column, $operator, $value], static fn($part) => $part !== null));

            if ($i !== 0) {
                $expression = $boolean . ' ' . $expression;
            }

            $conditions[] = $expression;
        }

        return implode(' ', $conditions);
    }

    public function toSoql(): string
    {
        $this->validateQuery();

        $soql = 'SELECT ' . $this->buildFields();
        $soql .= ' FROM ' . $this->object;

        if (!empty($this->where)) {
            $soql .= ' WHERE ' . $this->buildWhere();
        }

        if (!empty($this->orders)) {
            $soql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limit > 0) {
            $soql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset > 0) {
            $soql .= ' OFFSET ' . $this->offset;
        }

        return $soql;
    }

    public function whereYesterday(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', '=', SoqlDates::YESTERDAY);
    }

    public function orWhereYesterday(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::YESTERDAY, 'OR');
    }

    public function whereToday(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::TODAY);
    }

    public function orWhereToday(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::TODAY, 'OR');
    }

    public function whereTomorrow(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::TOMORROW);
    }

    public function orWhereTomorrow(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::TOMORROW, 'OR');
    }

    public function whereLastWeek(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_WEEK);
    }

    public function orWhereLastWeek(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_WEEK, 'OR');
    }

    public function whereThisWeek(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_WEEK);
    }

    public function orWhereThisWeek(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_WEEK, 'OR');
    }

    public function whereNextWeek(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_WEEK);
    }

    public function orWhereNextWeek(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_WEEK, 'OR');
    }

    public function whereLastMonth(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_MONTH);
    }

    public function orWhereLastMonth(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_MONTH, 'OR');
    }

    public function whereThisMonth(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_MONTH);
    }

    public function orWhereThisMonth(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_MONTH, 'OR');
    }

    public function whereNextMonth(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_MONTH);
    }

    public function orWhereNextMonth(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_MONTH, 'OR');
    }

    public function whereThisQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_QUARTER);
    }

    public function orWhereThisQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_QUARTER, 'OR');
    }

    public function whereLastQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_QUARTER);
    }

    public function orWhereLastQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_QUARTER, 'OR');
    }

    public function whereNextQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_QUARTER);
    }

    public function orWhereNextQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_QUARTER, 'OR');
    }

    public function whereThisYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_YEAR);
    }

    public function orWhereThisYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_YEAR, 'OR');
    }

    public function whereLastYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_YEAR);
    }

    public function orWhereLastYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_YEAR, 'OR');
    }

    public function whereNextYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_YEAR);
    }

    public function orWhereNextYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_YEAR, 'OR');
    }

    public function whereLast90Days(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_90_DAYS);
    }

    public function orWhereLast90Days(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_90_DAYS, 'OR');
    }

    public function whereNext90Days(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_90_DAYS);
    }

    public function orWhereNext90Days(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_90_DAYS, 'OR');
    }

    public function whereLastDays(string $dateField, int $days): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_DAYS.":$days");
    }

    public function orWhereLastDays(string $dateField, int $days): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_DAYS.":$days", 'OR');
    }

    public function whereNextDays(string $dateField, int $days): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_DAYS.":$days");
    }

    public function orWhereNextDays(string $dateField, int $days): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_DAYS.":$days", 'OR');
    }

    public function whereNextWeeks(string $dateField, int $weeks): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_WEEKS.":$weeks");
    }

    public function orWhereNextWeeks(string $dateField, int $weeks): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_WEEKS.":$weeks", 'OR');
    }

    public function whereLastWeeks(string $dateField, int $weeks): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_WEEKS.":$weeks");
    }

    public function orWhereLastWeeks(string $dateField, int $weeks): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_WEEKS.":$weeks", 'OR');
    }

    public function whereNextMonths(string $dateField, int $months): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_MONTHS.":$months");
    }

    public function orWhereNextMonths(string $dateField, int $months): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_MONTHS.":$months", 'OR');
    }

    public function whereLastMonths(string $dateField, int $months): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_MONTHS.":$months");
    }

    public function orWhereLastMonths(string $dateField, int $months): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_MONTHS.":$months", 'OR');
    }

    public function whereNextQuarters(string $dateField, int $quarters): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_QUARTERS.":$quarters");
    }

    public function orWhereNextQuarters(string $dateField, int $quarters): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_QUARTERS.":$quarters", 'OR');
    }

    public function whereLastQuarters(string $dateField, int $quarters): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_QUARTERS.":$quarters");
    }

    public function orWhereLastQuarters(string $dateField, int $quarters): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_QUARTERS.":$quarters", 'OR');
    }

    public function whereNextYears(string $dateField, int $years): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_YEARS.":$years");
    }

    public function orWhereNextYears(string $dateField, int $years): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_YEARS.":$years", 'OR');
    }

    public function whereLastYears(string $dateField, int $years): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_YEARS.":$years");
    }

    public function orWhereLastYears(string $dateField, int $years): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_YEARS.":$years", 'OR');
    }

    public function whereThisFiscalQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_FISCAL_QUARTER);
    }

    public function orWhereThisFiscalQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_FISCAL_QUARTER, 'OR');
    }

    public function whereLastFiscalQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_FISCAL_QUARTER);
    }

    public function orWhereLastFiscalQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_FISCAL_QUARTER, 'OR');
    }

    public function whereNextFiscalQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_FISCAL_QUARTER);
    }

    public function orWhereNextFiscalQuarter(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_FISCAL_QUARTER, 'OR');
    }

    public function whereNextFiscalQuarters(string $dateField, int $quarters): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_FISCAL_QUARTERS.":$quarters");
    }

    public function orWhereNextFiscalQuarters(string $dateField, int $quarters): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_FISCAL_QUARTERS.":$quarters", 'OR');
    }

    public function whereLastFiscalQuarters(string $dateField, int $quarters): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_FISCAL_QUARTERS.":$quarters");
    }

    public function orWhereLastFiscalQuarters(string $dateField, int $quarters): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_FISCAL_QUARTERS.":$quarters", 'OR');
    }

    public function whereThisFiscalYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_FISCAL_YEAR);
    }

    public function orWhereThisFiscalYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_FISCAL_YEAR, 'OR');
    }

    public function whereLastFiscalYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_FISCAL_YEAR);
    }

    public function orWhereLastFiscalYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_FISCAL_YEAR, 'OR');
    }

    public function whereNextFiscalYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_FISCAL_YEAR);
    }

    public function orWhereNextFiscalYear(string $dateField): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_FISCAL_YEAR, 'OR');
    }

    public function whereNextFiscalYears(string $dateField, int $years): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_FISCAL_YEARS.":$years");
    }

    public function orWhereNextFiscalYears(string $dateField, int $years): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_FISCAL_YEARS.":$years", 'OR');
    }

    public function whereLastFiscalYears(string $dateField, int $years): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_FISCAL_YEARS.":$years");
    }

    public function orWhereLastFiscalYears(string $dateField, int $years): SoqlQueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_FISCAL_YEARS.":$years", 'OR');
    }
}