<?php

use myoutdeskllc\SalesforcePhp\Exceptions\InvalidQueryException;
use myoutdeskllc\SalesforcePhp\QueryBuilder\SoqlQueryBuilder;

test('builds base query', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name', 'Description'])
        ->where('Name', '=', 'Mikhail')
        ->orderBy('Name')
        ->limit(10)
        ->offset(15);

    expect($qb->toSoql())->toBe("SELECT Id, Name, Description FROM Account WHERE Name = 'Mikhail' ORDER BY Name ASC LIMIT 10 OFFSET 15");
});

test('handles boolean where', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name', 'Description'])
        ->where('IsChecked', '=', true);

    expect($qb->toSoql())->toBe("SELECT Id, Name, Description FROM Account WHERE IsChecked = true");
});

test('handles number where', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name', 'Description'])
        ->where('Amount', '=', 0);

    expect($qb->toSoql())->toBe("SELECT Id, Name, Description FROM Account WHERE Amount = 0");
});

test('supports several order clauses', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name', 'Description'])
        ->orderBy('Name')
        ->orderByDesc('Description');

    expect($qb->toSoql())->toBe('SELECT Id, Name, Description FROM Account ORDER BY Name ASC, Description DESC');
});

test('handles orWhere', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name', 'Description'])
        ->orWhere('A', '=', 'B')
        ->orWhere('C', '=', 'D');

    expect($qb->toSoql())->toBe("SELECT Id, Name, Description FROM Account WHERE A = 'B' OR C = 'D'");
});

test('handles whereIn', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->where('G', '=', 'G')
        ->whereIn('Name', ["A", "B", "C"]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Acc WHERE G = 'G' AND Name IN ('A', 'B', 'C')");
});

test('handles whereNotIn', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->where('G', '=', 'G')
        ->whereNotIn('Name', ["A", "B", "C"]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Acc WHERE G = 'G' AND Name NOT IN ('A', 'B', 'C')");
});

test('handles orWhereIn', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->where('G', '=', 'G')
        ->orWhereIn('Name', ["A", "B", "C"]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Acc WHERE G = 'G' OR Name IN ('A', 'B', 'C')");
});

test('handles orWhereNotIn', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->where('G', '=', 'G')
        ->orWhereNotIn('Name', ["A", "B", "C"]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Acc WHERE G = 'G' OR Name NOT IN ('A', 'B', 'C')");
});

test('throws exception when query has no fields', function () {
    expect(fn () => (new SoqlQueryBuilder())
        ->from('Account')
        ->orderBy('Name')
        ->orderByDesc('Description')
        ->toSoql()
    )->toThrow(InvalidQueryException::class);
});

test('throws exception when query has no SObject', function () {
    expect(fn () => (new SoqlQueryBuilder())
        ->select(['Id', 'Name', 'Description'])
        ->orderBy('Name')
        ->orderByDesc('Description')
        ->toSoql()
    )->toThrow(InvalidQueryException::class);
});

test('can add selection', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->addSelect('Description');

    expect($qb->toSoql())->toBe("SELECT Id, Name, Description FROM Acc");
});

test('supports whereColumn', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->whereColumn([['A', '>', 3], ['B', '<', 8]]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Acc WHERE A > 3 AND B < 8");
});

test('handles where with null', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->where('A', '=', null);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Acc WHERE A = null");
});

test('handles whereDate', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->whereDate("A", "=", "2019-10-10");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Acc WHERE A = 2019-10-10");
});

test('handles orWhereDate', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->select(['Id', 'Name'])
        ->whereDate("A", "=", "2019-10-10")
        ->orWhereDate("B", "=", "2019-10-09");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Acc WHERE A = 2019-10-10 OR B = 2019-10-09");
});

test('prevents duplicate selects', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Acc')
        ->addSelect("Id")
        ->addSelect("Id");

    expect($qb->toSoql())->toBe("SELECT Id FROM Acc");
});

test('supports whereFunction', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Object')
        ->addSelect('Id')
        ->whereFunction('F', 'func1', 'chs1')
        ->whereFunction('F', 'func2', 'chs2')
        ->whereFunction('F', 'func3', 'chs3', 'OR')
        ->whereFunction('F', 'func4', 'chs4');

    expect($qb->toSoql())->toBe("SELECT Id FROM Object WHERE F func1('chs1') AND F func2('chs2') OR F func3('chs3') AND F func4('chs4')");
});

test('supports whereFunction with array', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Object')
        ->addSelect('Id')
        ->whereFunction('F', 'func1', ['chs1;chs2', 'chs3', 'chs4']);

    expect($qb->toSoql())->toBe("SELECT Id FROM Object WHERE F func1('chs1;chs2', 'chs3', 'chs4')");
});

test('groups conditional expressions', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Androids__c')
        ->addSelect('Id')
        ->where('Warranty', '=', 'Expired')
        ->startWhere()
        ->orWhere('Warranty', '=', 'Active')
        ->where('Days_Left__c', '<=', '60')
        ->endWhere();

    expect($qb->toSoql())->toBe("SELECT Id FROM Androids__c WHERE Warranty = 'Expired' OR (Warranty = 'Active' AND Days_Left__c <= '60')");
});

test('groups conditional expressions alone', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Androids__c')
        ->addSelect('Id')
        ->where('Warranty', '=', 'Expired')
        ->startWhere()
        ->where('Days_Left__c', '<=', '60')
        ->endWhere();

    expect($qb->toSoql())->toBe("SELECT Id FROM Androids__c WHERE Warranty = 'Expired' AND (Days_Left__c <= '60')");
});

test('groups expressions in multiple locations', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Androids__c')
        ->addSelect('Id')
        ->startWhere()
        ->where('Warranty', '=', 'Active')
        ->where('Days_Left__c', '<=', '60')
        ->endWhere()
        ->startWhere()
        ->orWhere('Warranty', '=', 'Expired')
        ->where('Days_Expired__c', '<=', '30')
        ->endWhere();

    expect($qb->toSoql())->toBe("SELECT Id FROM Androids__c WHERE (Warranty = 'Active' AND Days_Left__c <= '60') OR (Warranty = 'Expired' AND Days_Expired__c <= '30')");
});

test('groups conditional expressions nested', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Androids__c')
        ->addSelect('Id')
        ->startWhere()
        ->startWhere()
        ->where('Warranty', '=', 'Active')
        ->where('Days_Left__c', '<=', '60')
        ->endWhere()
        ->startWhere()
        ->orWhere('Warranty', '=', 'Expired')
        ->where('Days_Expired__c', '<=', '30')
        ->endWhere()
        ->orWhere('Select_This_Anyway__c', '=', 'true')
        ->endWhere();

    expect($qb->toSoql())->toBe("SELECT Id FROM Androids__c WHERE ((Warranty = 'Active' AND Days_Left__c <= '60') OR (Warranty = 'Expired' AND Days_Expired__c <= '30') OR Select_This_Anyway__c = 'true')");
});

test('throws exception for mismatched grouping', function () {
    expect(fn () => (new SoqlQueryBuilder())
        ->from('Androids__c')
        ->addSelect('Id')
        ->startWhere()
        ->where('Warranty', '=', 'Active')
        ->toSoql()
    )->toThrow(InvalidQueryException::class);
});

test('throws exception for missing object', function () {
    expect(fn () => (new SoqlQueryBuilder())
        ->from('')
        ->addSelect('Id')
        ->toSoql()
    )->toThrow(InvalidQueryException::class);
});

test('throws exception for missing fields', function () {
    expect(fn () => (new SoqlQueryBuilder())
        ->from('Androids')
        ->toSoql()
    )->toThrow(InvalidQueryException::class);
});

// ============================================
// SOQL Escaping & Injection Prevention Tests
// ============================================

test('escapes single quotes in string values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Name'])
        ->where('LastName', '=', "O'Brien");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Contact WHERE LastName = 'O''Brien'");
});

test('escapes multiple single quotes in string values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Name', '=', "L'Oreal's Best");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Name = 'L''Oreal''s Best'");
});

test('escapes backslashes in string values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Description', '=', 'Path: C:\Users\Admin');

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Description = 'Path: C:\\\\Users\\\\Admin'");
});

test('escapes newline characters in string values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Description', '=', "Line 1\nLine 2");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Description = 'Line 1\\nLine 2'");
});

test('escapes carriage return characters in string values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Description', '=', "Line 1\rLine 2");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Description = 'Line 1\\rLine 2'");
});

test('escapes tab characters in string values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Description', '=', "Column1\tColumn2");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Description = 'Column1\\tColumn2'");
});

test('escapes all special characters combined', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Name'])
        ->where('Notes', '=', "O'Brien said:\n\t\"C:\\Path\\Here\"");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Contact WHERE Notes = 'O''Brien said:\\n\\t\"C:\\\\Path\\\\Here\"'");
});

test('prevents SOQL injection via string concatenation attempt', function () {
    $maliciousInput = "test' OR '1'='1";
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Name', '=', $maliciousInput);

    // The single quotes should be escaped, preventing the injection
    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Name = 'test'' OR ''1''=''1'");
});

test('prevents SOQL injection with comment injection attempt', function () {
    $maliciousInput = "test' --";
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Name', '=', $maliciousInput);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Name = 'test'' --'");
});

test('prevents SOQL injection with UNION attempt', function () {
    $maliciousInput = "test' UNION SELECT Id, Password FROM User WHERE '1'='1";
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Name', '=', $maliciousInput);

    // All quotes escaped, making this a literal string search
    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Name = 'test'' UNION SELECT Id, Password FROM User WHERE ''1''=''1'");
});

test('prevents SOQL injection with subquery injection attempt', function () {
    $maliciousInput = "test' AND Id IN (SELECT Id FROM Account WHERE Name = 'Evil') AND '1'='1";
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Name'])
        ->where('Email', '=', $maliciousInput);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Contact WHERE Email = 'test'' AND Id IN (SELECT Id FROM Account WHERE Name = ''Evil'') AND ''1''=''1'");
});

test('escapes quotes in whereIn values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Name'])
        ->whereIn('LastName', ["O'Brien", "D'Angelo", "L'Oreal"]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Contact WHERE LastName IN ('O''Brien', 'D''Angelo', 'L''Oreal')");
});

test('escapes quotes in whereNotIn values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Name'])
        ->whereNotIn('LastName', ["O'Brien", "St. John's"]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Contact WHERE LastName NOT IN ('O''Brien', 'St. John''s')");
});

test('escapes quotes in orWhereIn values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Name'])
        ->where('Status', '=', 'Active')
        ->orWhereIn('LastName', ["O'Brien", "McDonald's"]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Contact WHERE Status = 'Active' OR LastName IN ('O''Brien', 'McDonald''s')");
});

test('escapes quotes in whereFunction values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Name'])
        ->whereFunction('LastName', 'INCLUDES', "O'Brien;D'Angelo");

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Contact WHERE LastName INCLUDES('O''Brien;D''Angelo')");
});

test('escapes quotes in whereFunction array values', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Name'])
        ->whereFunction('LastName', 'INCLUDES', ["O'Brien", "D'Angelo"]);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Contact WHERE LastName INCLUDES('O''Brien', 'D''Angelo')");
});

test('handles complex injection attempt with backslashes and quotes', function () {
    $maliciousInput = "\\' OR 1=1 --";
    $qb = (new SoqlQueryBuilder())
        ->from('Account')
        ->select(['Id', 'Name'])
        ->where('Name', '=', $maliciousInput);

    expect($qb->toSoql())->toBe("SELECT Id, Name FROM Account WHERE Name = '\\\\'' OR 1=1 --'");
});

test('handles email addresses with valid special characters', function () {
    $qb = (new SoqlQueryBuilder())
        ->from('Contact')
        ->select(['Id', 'Email'])
        ->where('Email', '=', "user+test@domain.com");

    expect($qb->toSoql())->toBe("SELECT Id, Email FROM Contact WHERE Email = 'user+test@domain.com'");
});