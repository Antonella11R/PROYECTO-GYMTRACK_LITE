# start.ps1

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "   Iniciando PROYECTO GYMTRACK LITE" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Configurar archivo .env
$envFile = "$PSScriptRoot\.env"
$envExampleFile = "$PSScriptRoot\.env.example"

if (-Not (Test-Path $envFile)) {
    Write-Host "[1/5] Creando archivo .env desde .env.example..." -ForegroundColor Yellow
    if (Test-Path $envExampleFile) {
        Copy-Item $envExampleFile $envFile
        
        # Modificar valores para la conexión con el contenedor Docker DB
        (Get-Content $envFile) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=db' `
                             -replace 'DB_PASSWORD=', 'DB_PASSWORD=root_password' | Set-Content $envFile
        Write-Host "      Archivo .env configurado correctamente para Docker." -ForegroundColor Green
    } else {
        Write-Host "      No se encontró .env.example, creando un .env por defecto..." -ForegroundColor Yellow
        @"
APP_ENV=local
APP_NAME="GymTrack Lite"
APP_URL=http://localhost:8000
SESSION_NAME=gymtrack_lite_session

DB_HOST=db
DB_PORT=3306
DB_DATABASE=gymtrack_lite
DB_USERNAME=root
DB_PASSWORD=root_password
"@ | Set-Content $envFile
    }
} else {
    Write-Host "[1/5] El archivo .env ya existe." -ForegroundColor Green
}

# 2. Levantar contenedores con Docker Compose
Write-Host "`n[2/5] Levantando contenedores Docker..." -ForegroundColor Yellow
docker compose up -d --build

# 3. Instalar dependencias con Composer
Write-Host "`n[3/5] Instalando dependencias de Composer..." -ForegroundColor Yellow
docker exec gymtrack-lite-app composer install

# 4. Esperar a que la base de datos esté lista
Write-Host "`n[4/5] Esperando a que la base de datos esté lista..." -ForegroundColor Yellow
$maxRetries = 30
$retryCount = 0
$dbReady = $false

while (-not $dbReady -and $retryCount -lt $maxRetries) {
    # Test connection inside container using PHP
    $result = docker exec gymtrack-lite-app php -r "try { new PDO('mysql:host=db;port=3306', 'root', 'root_password'); echo 'OK'; } catch (Exception `$e) { echo 'FAIL'; }"
    
    if ($result -match "OK") {
        $dbReady = $true
        Write-Host "      Base de datos lista." -ForegroundColor Green
    } else {
        Start-Sleep -Seconds 2
        $retryCount++
        Write-Host "      Esperando a MySQL... ($retryCount/$maxRetries)" -ForegroundColor Gray
    }
}

if (-not $dbReady) {
    Write-Host "`n[!] Error: No se pudo conectar a la base de datos después de $maxRetries intentos." -ForegroundColor Red
    Write-Host "    Revisa los logs con: docker compose logs db" -ForegroundColor Red
    exit 1
}

# 5. Ejecutar script de reinicio de la base de datos (Migraciones y Seeds)
Write-Host "`n[5/5] Reconstruyendo la base de datos (Migraciones y Seeds)..." -ForegroundColor Yellow
docker exec gymtrack-lite-app php script/reset_database.php

Write-Host "`n=========================================" -ForegroundColor Cyan
Write-Host "¡GymTrack Lite ha sido levantado con éxito!" -ForegroundColor Green
Write-Host "Accede a la aplicación en: http://localhost:8000" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
