# Script để fix permissions cho storage trên Windows
# Chạy với quyền Administrator

Write-Host "Fixing storage permissions..." -ForegroundColor Green

# Tạo thư mục public/storage nếu chưa có
$publicStorage = "public\storage"
if (-not (Test-Path $publicStorage)) {
    New-Item -ItemType Directory -Path $publicStorage -Force
    Write-Host "Created $publicStorage" -ForegroundColor Yellow
}

# Đảm bảo thư mục có quyền ghi
$acl = Get-Acl $publicStorage
$permission = "BUILTIN\Users","FullControl","ContainerInherit,ObjectInherit","None","Allow"
$accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule $permission
$acl.SetAccessRule($accessRule)
Set-Acl $publicStorage $acl

Write-Host "Permissions fixed for $publicStorage" -ForegroundColor Green

# Kiểm tra storage/app/public
$storagePublic = "storage\app\public"
if (Test-Path $storagePublic) {
    $acl2 = Get-Acl $storagePublic
    $acl2.SetAccessRule($accessRule)
    Set-Acl $storagePublic $acl2
    Write-Host "Permissions fixed for $storagePublic" -ForegroundColor Green
}

Write-Host "Done! Storage permissions have been fixed." -ForegroundColor Green

