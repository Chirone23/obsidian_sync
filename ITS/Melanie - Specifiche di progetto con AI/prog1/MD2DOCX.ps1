# MD -> DOCX click & go
Add-Type -AssemblyName System.Windows.Forms

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$converter = Join-Path $scriptDir 'md2docx.js'

if (-not (Test-Path $converter)) {
    [System.Windows.Forms.MessageBox]::Show("md2docx.js non trovato in:`n$scriptDir", 'Errore', 'OK', 'Error') | Out-Null
    exit 1
}

$open = New-Object System.Windows.Forms.OpenFileDialog
$open.Title = 'Seleziona uno o piu file Markdown'
$open.Filter = 'Markdown (*.md;*.markdown)|*.md;*.markdown|Tutti i file (*.*)|*.*'
$open.Multiselect = $true
$open.InitialDirectory = [Environment]::GetFolderPath('MyDocuments')

if ($open.ShowDialog() -ne 'OK') { exit 0 }

$ok = 0
$errors = @()

foreach ($in in $open.FileNames) {
    $out = [System.IO.Path]::ChangeExtension($in, '.docx')
    Write-Host "Converto: $in"
    & node $converter $in $out 2>&1 | ForEach-Object { Write-Host "  $_" }
    if ($LASTEXITCODE -eq 0 -and (Test-Path $out)) {
        $ok++
    } else {
        $errors += $in
    }
}

$msg = "Convertiti: $ok file"
if ($errors.Count -gt 0) { $msg += "`nErrori: " + ($errors -join "`n") }

$result = [System.Windows.Forms.MessageBox]::Show(
    "$msg`n`nAprire la cartella di destinazione?",
    'Conversione completata', 'YesNo', 'Information'
)
if ($result -eq 'Yes' -and $open.FileNames.Count -gt 0) {
    $folder = Split-Path -Parent $open.FileNames[0]
    Start-Process explorer.exe $folder
}
