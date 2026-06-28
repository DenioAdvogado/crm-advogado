<?php

namespace App\Console\Commands;

use App\Services\OneDriveService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Backup diário do banco (Bloco 10): gera o dump localmente (storage/app/backups/) e, se o
 * OneDrive estiver configurado no .env, envia uma cópia para lá também. Funciona tanto em
 * MySQL (produção) quanto SQLite (esta máquina de desenvolvimento), para o Command poder
 * ser testado localmente sem precisar de MySQL instalado.
 */
#[Signature('app:backup-database')]
#[Description('Gera o backup diário do banco e, se configurado, envia uma cópia para o OneDrive.')]
class BackupDatabase extends Command
{
    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir);

        $connection = config('database.default');
        $timestamp = Carbon::now()->format('Y-m-d_His');

        try {
            $localPath = $connection === 'sqlite'
                ? $this->backupSqlite($backupDir, $timestamp)
                : $this->backupMysql($backupDir, $timestamp);
        } catch (Throwable $exception) {
            $this->error('Falha ao gerar o backup local: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Backup local criado: {$localPath}");

        $this->uploadToOneDriveIfConfigured($localPath);
        $this->pruneOldBackups($backupDir);

        return self::SUCCESS;
    }

    private function backupMysql(string $backupDir, string $timestamp): string
    {
        $db = config('database.connections.mysql');
        $path = "{$backupDir}/backup-{$timestamp}.sql";

        $process = new Process([
            'mysqldump',
            '--host='.$db['host'],
            '--port='.$db['port'],
            '--user='.$db['username'],
            '--password='.$db['password'],
            $db['database'],
        ]);

        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        File::put($path, $process->getOutput());

        return $path;
    }

    /**
     * Ambiente de desenvolvimento local (sem MySQL instalado, ver CLAUDE.md Bloco 0): o
     * "backup" é só uma cópia do arquivo .sqlite, suficiente para validar que o Command
     * funciona de ponta a ponta (rotação + upload), mesmo sem mysqldump disponível.
     */
    private function backupSqlite(string $backupDir, string $timestamp): string
    {
        $source = config('database.connections.sqlite.database');
        $path = "{$backupDir}/backup-{$timestamp}.sqlite";

        File::copy($source, $path);

        return $path;
    }

    private function uploadToOneDriveIfConfigured(string $localPath): void
    {
        if (empty(config('services.onedrive.refresh_token'))) {
            $this->line('ONEDRIVE_REFRESH_TOKEN não configurado — backup mantido só localmente.');

            return;
        }

        try {
            $uploaded = (new OneDriveService)->upload($localPath, basename($localPath));

            $this->info($uploaded ? 'Backup enviado ao OneDrive com sucesso.' : 'Falha ao enviar o backup ao OneDrive — veja storage/logs/laravel.log.');
        } catch (Throwable $exception) {
            $this->error('Erro ao enviar o backup ao OneDrive: '.$exception->getMessage());
        }
    }

    /**
     * Mantém só os últimos N dias de backup local (padrão 14), configurável via
     * BACKUP_RETENTION_DAYS no .env — evita que o disco do VPS encha com o tempo.
     */
    private function pruneOldBackups(string $backupDir): void
    {
        $retentionDays = (int) env('BACKUP_RETENTION_DAYS', 14);
        $cutoff = Carbon::now()->subDays($retentionDays);

        foreach (File::files($backupDir) as $file) {
            if (Carbon::createFromTimestamp($file->getMTime())->lt($cutoff)) {
                File::delete($file->getPathname());
            }
        }
    }
}
