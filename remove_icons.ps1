# Script de loai bo tat ca icon khoi cac file blade
# Chay: powershell -ExecutionPolicy Bypass -File remove_icons.ps1

$files = Get-ChildItem -Path "resources/views" -Recurse -Filter "*.blade.php"

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $original = $content
    
    # Loai bo bootstrap-icons link
    $content = $content -replace '<link[^>]*bootstrap-icons[^>]*>', ''
    
    # Loai bo icon trong tag: <i class="bi bi-xxx"></i>
    $content = $content -replace '<i\s+class="[^"]*bi[^"]*"[^>]*></i>', ''
    $content = $content -replace '<i\s+class=''[^'']*bi[^'']*''[^>]*></i>', ''
    
    # Loai bo icon trong button/link co icon
    $content = $content -replace '(<[^>]*>)\s*<i[^>]*></i>\s*([^<]+)(</[^>]+>)', '$1$2$3'
    $content = $content -replace '([^>])\s*<i[^>]*></i>\s*(</[^>]+>)', '$1$2'
    
    # Loai bo icon trong label/span
    $content = $content -replace '<label[^>]*>\s*<i[^>]*></i>\s*', '<label>'
    $content = $content -replace '<span[^>]*>\s*<i[^>]*></i>\s*', '<span>'
    
    # Loai bo icon trong section page-icon
    $content = $content -replace "@section\('page-icon'[^)]*\)", ''
    
    if ($content -ne $original) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
        Write-Host "Updated: $($file.FullName)" -ForegroundColor Green
    }
}

Write-Host "`nHoan tat! Da loai bo icon khoi tat ca cac file blade." -ForegroundColor Cyan

