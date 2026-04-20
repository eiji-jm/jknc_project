param(
    [string]$Database = "jknc",
    [string]$DbHost = "127.0.0.1",
    [int]$Port = 3306,
    [string]$Username = "root",
    [string]$Password = "",
    [string]$MysqlExe = "C:\xampp\mysql\bin\mysql.exe",
    [string]$ExportPath = "database\data-export"
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

if (-not (Test-Path $MysqlExe)) {
    throw "MySQL client not found at $MysqlExe"
}

$resolvedExportPath = Resolve-Path $ExportPath

$tables = @(
    "role_permissions",
    "user_permissions",
    "users",
    "contacts",
    "companies",
    "company_contact",
    "company_history_entries",
    "company_bifs",
    "deal_stages",
    "deals",
    "deal_proposals",
    "catalog_change_requests",
    "services",
    "products",
    "projects",
    "project_starts",
    "project_sows",
    "project_sow_reports",
    "specimen_signatures"
)

function ConvertTo-SqlLiteral {
    param([object]$Value)

    if ($null -eq $Value) {
        return "NULL"
    }

    if ($Value -is [bool]) {
        return $(if ($Value) { "1" } else { "0" })
    }

    if ($Value -is [byte] -or $Value -is [int16] -or $Value -is [int32] -or $Value -is [int64]) {
        return $Value.ToString()
    }

    if ($Value -is [single] -or $Value -is [double] -or $Value -is [decimal]) {
        return $Value.ToString([System.Globalization.CultureInfo]::InvariantCulture)
    }

    $text = [string]$Value
    $text = $text.Replace("\", "\\")
    $text = $text.Replace("'", "''")
    $text = $text.Replace("`r", "\r")
    $text = $text.Replace("`n", "\n")

    return "'" + $text + "'"
}

function Quote-Identifier {
    param([string]$Name)

    return ([char]96) + $Name + ([char]96)
}

$sql = New-Object System.Collections.Generic.List[string]
$sql.Add("SET FOREIGN_KEY_CHECKS=0;")

foreach ($table in $tables) {
    $sql.Add("TRUNCATE TABLE " + (Quote-Identifier $table) + ";")
}

foreach ($table in $tables) {
    $filePath = Join-Path $resolvedExportPath ("jkandc." + $table + ".json")

    if (-not (Test-Path $filePath)) {
        continue
    }

    $raw = Get-Content $filePath -Raw
    $rows = @()

    if ($raw.Trim()) {
        $parsed = $raw | ConvertFrom-Json

        if ($parsed -is [System.Array]) {
            $rows = $parsed
        } elseif ($null -ne $parsed) {
            $rows = @($parsed)
        }
    }

    if ($rows.Count -eq 0) {
        continue
    }

    $columns = @($rows[0].PSObject.Properties.Name)
    $columnSql = ($columns | ForEach-Object { Quote-Identifier $_ }) -join ", "
    $valueRows = New-Object System.Collections.Generic.List[string]

    foreach ($row in $rows) {
        $values = foreach ($column in $columns) {
            ConvertTo-SqlLiteral $row.$column
        }

        $valueRows.Add("(" + ($values -join ", ") + ")")
    }

    $sql.Add("INSERT INTO " + (Quote-Identifier $table) + " ($columnSql) VALUES")
    $sql.Add(($valueRows -join ",`n") + ";")
}

$sql.Add("SET FOREIGN_KEY_CHECKS=1;")

$tempFile = Join-Path $env:TEMP "jknc-demo-import.sql"
Set-Content -LiteralPath $tempFile -Value ($sql -join "`n") -Encoding UTF8

$mysqlArgs = @(
    "-h", $DbHost,
    "-P", $Port.ToString(),
    "-u", $Username
)

if ($Password -ne "") {
    $mysqlArgs += "-p$Password"
}

$mysqlArgs += @($Database, "-e", "source $tempFile")

& $MysqlExe @mysqlArgs

Write-Host "Imported demo data into $Database using $tempFile"
