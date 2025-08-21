<?php

namespace App\Console\Commands;

use App\Models\Font;
use Illuminate\Console\Command;

class InstallFontsCommand extends Command
{
    /**
     * Имя и сигнатура команды.
     *
     * @var string
     */
    protected $signature = 'fonts:install';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Устанавливает шрифты из базы данных как системные в Ubuntu';

    /**
     * Выполнить команду.
     */
    public function handle(): int
    {
        // Получаем путь из конфига
        $fontDir = config('fonts.install_dir');

        // Проверяем, доступна ли директория
        if (!is_dir($fontDir)) {
            // Пытаемся создать директорию, если её нет
            if (!mkdir($fontDir, 0755, true)) {
                $this->error("Не удалось создать директорию шрифтов: " . $fontDir);
                return 1;
            }
            $this->info("Создана директория шрифтов: " . $fontDir);
        }

        // Проверяем, доступна ли директория
        if (!is_dir($fontDir)) {
            $this->error("Директория шрифтов не найдена: " . $fontDir);
            return 1;
        }

        // Получаем все шрифты из БД
        $fonts = Font::all();

        foreach ($fonts as $font) {
            $fontFile = $font->filename_ttf ?? $font->filename_otf;
            if (!$fontFile) {
                $this->warn("Шрифт {$font->name} не имеет файла TTF или OTF");
                continue;
            }

            // Полный путь к файлу в storage
            $sourcePath = storage_path('app/public/' . $fontFile);

            if (!file_exists($sourcePath)) {
                $this->error("Файл не найден: " . $sourcePath);
                continue;
            }

            $destPath = $fontDir . $fontFile;

            // Копируем файл
            if (!copy($sourcePath, $destPath)) {
                $this->error("Не удалось скопировать файл: " . $sourcePath);
                continue;
            }

            $this->info("Шрифт {$font->name} установлен.");
        }

        // Обновляем кэш шрифтов
        shell_exec('fc-cache -fv');

        $this->info("Кэш шрифтов обновлён.");

        return 0;
    }
}
