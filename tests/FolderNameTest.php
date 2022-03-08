<?php

use myoutdeskllc\SalesforcePhp\Support\SalesforceRules;

it('does not allow folders to be created that dont match the salesforce API spec', function () {
    // The Folder API Name can only contain underscores and alphanumeric characters.
    // It must be unique, begin with a letter, not include spaces, not end with an underscore, and not contain two consecutive underscores.
    $folderRegex = SalesforceRules::getFolderNameValidation();

    expect(preg_match($folderRegex, '1FolderSakura'))->toBe(0);
    expect(preg_match($folderRegex, '1Folder__'))->toBe(0);
    expect(preg_match($folderRegex, 'Sakura_Folder__'))->toBe(0);
    expect(preg_match($folderRegex, 'Sakura__Folder__'))->toBe(0);
    expect(preg_match($folderRegex, 'Sakura_Folder_'))->toBe(0);
    expect(preg_match($folderRegex, 'Sakura_Folder$&@*_'))->toBe(0);

    expect(preg_match($folderRegex, 'Real_Folder_Name'))->not()->toBe(0);
    expect(preg_match($folderRegex, 'Sakura_Folder'))->not()->toBe(0);
    expect(preg_match($folderRegex, 'RinFolder1'))->not()->toBe(0);
    expect(preg_match($folderRegex, 'SakuraFolder_1'))->not()->toBe(0);
});
