# CLEANUP SCRIPT — REVIEW BEFORE RUNNING
# Sposta DELETE in .tmp\da_cancellare\ e ARCHIVE in .tmp\da_archiviare\
# Non elimina nulla: tutto si recupera dalla cartella .tmp

$dryRun = $true   # <-- METTI $false PER ESEGUIRE DAVVERO
$bin = 'C:\Users\Chirone\.tmp\claude_sessions_triage'
$delDir = Join-Path $bin 'da_cancellare'
$archDir = Join-Path $bin 'da_archiviare'
if (-not $dryRun) {
  New-Item -ItemType Directory -Force -Path $delDir | Out-Null
  New-Item -ItemType Directory -Force -Path $archDir | Out-Null
}

# Aggiorna il path se il json risiede altrove (es. nel vault)
$triage = Get-Content 'C:\tmp\triage_all.json' -Raw | ConvertFrom-Json
foreach ($t in $triage) {
  if ($t.verdict -eq 'KEEP') { continue }
  if (-not (Test-Path $t.path)) { Write-Warning "Missing: $($t.path)"; continue }
  $dest = if ($t.verdict -eq 'DELETE') { $delDir } else { $archDir }
  $newName = ($t.path -replace '[\\\:]','_')
  $target = Join-Path $dest $newName
  if ($dryRun) {
    Write-Host "[DRY] $($t.verdict) -> $target"
  } else {
    Move-Item -Path $t.path -Destination $target -Force
    Write-Host "[OK] $($t.verdict) $($t.path)"
  }
}
Write-Host "`nDone. dryRun = $dryRun"
