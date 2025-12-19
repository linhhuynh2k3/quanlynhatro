# Script PowerShell de tao thu muc va tai anh placeholder
# Yeu cau: Ket noi internet

Write-Host "Dang tao thu muc..." -ForegroundColor Cyan

# Tao thu muc
New-Item -ItemType Directory -Force -Path "storage/app/public/listings" | Out-Null
New-Item -ItemType Directory -Force -Path "storage/app/public/sliders" | Out-Null
New-Item -ItemType Directory -Force -Path "storage/app/public/news" | Out-Null

Write-Host "Dang tai anh placeholder cho listings (1-20)..." -ForegroundColor Cyan
$successCount = 0
$errorCount = 0

for ($i=1; $i -le 20; $i++) {
    try {
        $url = "https://picsum.photos/800/600?random=$i"
        $output = "storage/app/public/listings/$i.jpg"
        $webClient = New-Object System.Net.WebClient
        $webClient.DownloadFile($url, $output)
        $webClient.Dispose()
        $successCount++
        if ($i % 5 -eq 0) {
            Write-Host "  Da tai $i/20 anh..." -ForegroundColor Green
        }
    } catch {
        $errorCount++
    }
}

Write-Host "Dang tai anh placeholder cho sliders (1-3)..." -ForegroundColor Cyan
for ($i=1; $i -le 3; $i++) {
    try {
        $url = "https://picsum.photos/1920/600?random=$i"
        $output = "storage/app/public/sliders/$i.jpg"
        $webClient = New-Object System.Net.WebClient
        $webClient.DownloadFile($url, $output)
        $webClient.Dispose()
        Write-Host "  OK: Da tao slider $i" -ForegroundColor Green
    } catch {
        Write-Host "  ERROR: Loi khi tao slider $i" -ForegroundColor Red
    }
}

Write-Host "Dang tai anh placeholder cho news (1-3)..." -ForegroundColor Cyan
for ($i=1; $i -le 3; $i++) {
    try {
        $url = "https://picsum.photos/800/450?random=$i"
        $output = "storage/app/public/news/$i.jpg"
        $webClient = New-Object System.Net.WebClient
        $webClient.DownloadFile($url, $output)
        $webClient.Dispose()
        Write-Host "  OK: Da tao news $i" -ForegroundColor Green
    } catch {
        Write-Host "  ERROR: Loi khi tao news $i" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Hoan tat!" -ForegroundColor Green
Write-Host "  - Listings: $successCount/20 anh da tai" -ForegroundColor White
Write-Host "  - Sliders: 3 anh" -ForegroundColor White
Write-Host "  - News: 3 anh" -ForegroundColor White
Write-Host ""
Write-Host "Neu co loi, ban co the tai anh thu cong va dat vao cac thu muc:" -ForegroundColor Yellow
Write-Host "  - storage/app/public/listings/ (1.jpg den 20.jpg)" -ForegroundColor White
Write-Host "  - storage/app/public/sliders/ (1.jpg den 3.jpg)" -ForegroundColor White
Write-Host "  - storage/app/public/news/ (1.jpg den 3.jpg)" -ForegroundColor White
