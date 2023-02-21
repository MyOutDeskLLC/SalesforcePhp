<?php

namespace myoutdeskllc\SalesforcePhp\Support;

use myoutdeskllc\SalesforcePhp\Constants\SoqlDates;
use SalesforceQueryBuilder\QueryBuilder;

class SoqlQueryBuilder extends QueryBuilder
{
    public function whereYesterday(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', '=', SoqlDates::YESTERDAY);
    }

    public function orWhereYesterday(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::YESTERDAY, 'OR');
    }

    public function whereToday(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::TODAY);
    }

    public function orWhereToday(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::TODAY, 'OR');
    }

    public function whereTomorrow(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::TOMORROW);
    }

    public function orWhereTomorrow(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::TOMORROW, 'OR');
    }

    public function whereLastWeek(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_WEEK);
    }

    public function orWhereLastWeek(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_WEEK, 'OR');
    }

    public function whereThisWeek(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_WEEK);
    }

    public function orWhereThisWeek(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_WEEK, 'OR');
    }

    public function whereNextWeek(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_WEEK);
    }

    public function orWhereNextWeek(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_WEEK, 'OR');
    }

    public function whereLastMonth(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_MONTH);
    }

    public function orWhereLastMonth(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_MONTH, 'OR');
    }

    public function whereThisMonth(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_MONTH);
    }

    public function orWhereThisMonth(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_MONTH, 'OR');
    }

    public function whereNextMonth(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_MONTH);
    }

    public function orWhereNextMonth(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_MONTH, 'OR');
    }

    public function whereThisQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_QUARTER);
    }

    public function orWhereThisQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_QUARTER, 'OR');
    }

    public function whereLastQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_QUARTER);
    }

    public function orWhereLastQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_QUARTER, 'OR');
    }

    public function whereNextQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_QUARTER);
    }

    public function orWhereNextQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_QUARTER, 'OR');
    }

    public function whereThisYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_YEAR);
    }

    public function orWhereThisYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_YEAR, 'OR');
    }

    public function whereLastYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_YEAR);
    }

    public function orWhereLastYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_YEAR, 'OR');
    }

    public function whereNextYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_YEAR);
    }

    public function orWhereNextYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_YEAR, 'OR');
    }

    public function whereLast90Days(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_90_DAYS);
    }

    public function orWhereLast90Days(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_90_DAYS, 'OR');
    }

    public function whereNext90Days(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_90_DAYS);
    }

    public function orWhereNext90Days(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_90_DAYS, 'OR');
    }

    public function whereLastDays(string $dateField, int $days): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_DAYS.":$days");
    }

    public function orWhereLastDays(string $dateField, int $days): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_DAYS.":$days", 'OR');
    }

    public function whereNextDays(string $dateField, int $days): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_DAYS.":$days");
    }

    public function orWhereNextDays(string $dateField, int $days): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_DAYS.":$days", 'OR');
    }

    public function whereNextWeeks(string $dateField, int $weeks): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_WEEKS.":$weeks");
    }

    public function orWhereNextWeeks(string $dateField, int $weeks): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_WEEKS.":$weeks", 'OR');
    }

    public function whereLastWeeks(string $dateField, int $weeks): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_WEEKS.":$weeks");
    }

    public function orWhereLastWeeks(string $dateField, int $weeks): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_WEEKS.":$weeks", 'OR');
    }

    public function whereNextMonths(string $dateField, int $months): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_MONTHS.":$months");
    }

    public function orWhereNextMonths(string $dateField, int $months): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_MONTHS.":$months", 'OR');
    }

    public function whereLastMonths(string $dateField, int $months): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_MONTHS.":$months");
    }

    public function orWhereLastMonths(string $dateField, int $months): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_MONTHS.":$months", 'OR');
    }

    public function whereNextQuarters(string $dateField, int $quarters): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_QUARTERS.":$quarters");
    }

    public function orWhereNextQuarters(string $dateField, int $quarters): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_QUARTERS.":$quarters", 'OR');
    }

    public function whereLastQuarters(string $dateField, int $quarters): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_QUARTERS.":$quarters");
    }

    public function orWhereLastQuarters(string $dateField, int $quarters): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_QUARTERS.":$quarters", 'OR');
    }

    public function whereNextYears(string $dateField, int $years): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_YEARS.":$years");
    }

    public function orWhereNextYears(string $dateField, int $years): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_YEARS.":$years", 'OR');
    }

    public function whereLastYears(string $dateField, int $years): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_YEARS.":$years");
    }

    public function orWhereLastYears(string $dateField, int $years): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_YEARS.":$years", 'OR');
    }

    public function whereThisFiscalQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_FISCAL_QUARTER);
    }

    public function orWhereThisFiscalQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_FISCAL_QUARTER, 'OR');
    }

    public function whereLastFiscalQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_FISCAL_QUARTER);
    }

    public function orWhereLastFiscalQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_FISCAL_QUARTER, 'OR');
    }

    public function whereNextFiscalQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_FISCAL_QUARTER);
    }

    public function orWhereNextFiscalQuarter(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_FISCAL_QUARTER, 'OR');
    }

    public function whereNextFiscalQuarters(string $dateField, int $quarters): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_FISCAL_QUARTERS.":$quarters");
    }

    public function orWhereNextFiscalQuarters(string $dateField, int $quarters): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_FISCAL_QUARTERS.":$quarters", 'OR');
    }

    public function whereLastFiscalQuarters(string $dateField, int $quarters): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_FISCAL_QUARTERS.":$quarters");
    }

    public function orWhereLastFiscalQuarters(string $dateField, int $quarters): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_FISCAL_QUARTERS.":$quarters", 'OR');
    }

    public function whereThisFiscalYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_FISCAL_YEAR);
    }

    public function orWhereThisFiscalYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::THIS_FISCAL_YEAR, 'OR');
    }

    public function whereLastFiscalYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_FISCAL_YEAR);
    }

    public function orWhereLastFiscalYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_FISCAL_YEAR, 'OR');
    }

    public function whereNextFiscalYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_FISCAL_YEAR);
    }

    public function orWhereNextFiscalYear(string $dateField): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_FISCAL_YEAR, 'OR');
    }

    public function whereNextFiscalYears(string $dateField, int $years): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_FISCAL_YEARS.":$years");
    }

    public function orWhereNextFiscalYears(string $dateField, int $years): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::NEXT_N_FISCAL_YEARS.":$years", 'OR');
    }

    public function whereLastFiscalYears(string $dateField, int $years): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_FISCAL_YEARS.":$years");
    }

    public function orWhereLastFiscalYears(string $dateField, int $years): QueryBuilder
    {
        return $this->whereDate($dateField, '=', SoqlDates::LAST_N_FISCAL_YEARS.":$years", 'OR');
    }
}
