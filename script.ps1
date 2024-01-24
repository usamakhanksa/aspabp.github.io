$currentLocation = $PWD.Path
$directoryPath = Read-Host -Prompt 'Enter path name (without the fatoora folder name in the path): '

# Step 1: Search for 'fatoora' directory
$fatooraPath = Get-ChildItem -Path $directoryPath -Filter "fatoora" -Recurse -Directory | Select-Object -First 1
Write-Host "Searched and got Fatoora folder: $($fatooraPath.FullName)"

# Step 2: Read and copy lines 14 to 17 from '/laragon/www/fatoora/app/database/Db.php'
$dbFile = Get-Content -Path "$($fatooraPath.FullName)\app\database\Db.php" 
Write-Host "Copied Db lines $($dbFile)"

# Step 3: Read and copy the first line from 'js/config.js'
$configFile = Get-Content -Path "$($fatooraPath.FullName)\laragon\www\fatoora\js\config.js" 
Write-Host "Copied Config lines $($configFile)"

# # Step 4: Save the copied lines to a temp file
# $dbFile | Out-File "$env:TEMP\db_lines.txt"
# $configFile | Out-File "$env:TEMP\config_lines.txt"

# Step 5: Navigate to the fatoora directory and pull the latest changes using Git
Set-Location $fatooraPath.FullName
git stash
git pull origin main

# # Step 6: Replace lines in 'Db.php' and 'config.js' with saved content
# $dbContent = Get-Content -Path "$env:TEMP\db_lines.txt"
# $configContent = Get-Content -Path "$env:TEMP\config_lines.txt"

$dbContent = $dbFile
$configContent = $configFile

# Replace lines 14-17 in Db.php
$dbPath = "$($fatooraPath.FullName)\laragon\www\fatoora\app\database\Db.php"
$dbContent | Set-Content -Path $dbPath

# Replace the first line in config.js
$configPath = "$($fatooraPath.FullName)\laragon\www\fatoora\js\config.js"
$configContent | Set-Content -Path $configPath

Write-Host "Replaced the Db and Config lines with saved values"
Set-Location $currentLocation

$currentLocation = $PWD.Path
$directoryPath = Read-Host -Prompt 'Enter path name (without the fatoora folder name in the path): '

# Step 1: Search for 'fatoora' directory
$fatooraPath = Get-ChildItem -Path $directoryPath -Filter "fatoora" -Recurse -Directory | Select-Object -First 1

if ($fatooraPath -eq $null) {
    Write-Host "Error: 'fatoora' directory not found."
} else {
    Write-Host "Searched and got Fatoora folder: $($fatooraPath.FullName)"

    # Step 2: Read and copy lines 14 to 17 from '/laragon/www/fatoora/app/database/Db.php'
    $dbFile = Get-Content -Path "$($fatooraPath.FullName)\app\database\Db.php"
    Write-Host "Copied Db lines $($dbFile)"

    # Step 3: Read and copy the first line from 'js/config.js'
    $configFile = Get-Content -Path "$($fatooraPath.FullName)\laragon\www\fatoora\js\config.js"
    Write-Host "Copied Config lines $($configFile)"

    # # Step 4: Save the copied lines to a temp file
    # $dbFile | Out-File "$env:TEMP\db_lines.txt"
    # $configFile | Out-File "$env:TEMP\config_lines.txt"

    # Step 5: Navigate to the fatoora directory and pull the latest changes using Git
    Set-Location $fatooraPath.FullName
    git stash
    git pull origin main

    # # Step 6: Replace lines in 'Db.php' and 'config.js' with saved content
    # $dbContent = Get-Content -Path "$env:TEMP\db_lines.txt"
    # $configContent = Get-Content -Path "$env:TEMP\config_lines.txt"

    $dbContent = $dbFile
    $configContent = $configFile

    # Replace lines 14-17 in Db.php
    $dbPath = "$($fatooraPath.FullName)\laragon\www\fatoora\app\database\Db.php"
    $dbContent | Set-Content -Path $dbPath

    # Replace the first line in config.js
    $configPath = "$($fatooraPath.FullName)\laragon\www\fatoora\js\config.js"
    $configContent | Set-Content -Path $configPath

    Write-Host "Replaced the Db and Config lines with saved values"
    Set-Location $currentLocation
}

