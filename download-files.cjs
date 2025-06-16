const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

// Настройки
const DOMAIN = 'https://bsk-media.com';
const USE_WGET = true; // поменять на false, чтобы использовать curl
const OUTPUT_DIR = './downloads/previews/personalized_collar';

// Создаем папку для загрузок, если её нет
if (!fs.existsSync(OUTPUT_DIR)) {
    fs.mkdirSync(OUTPUT_DIR);
}

// Функция для скачивания файла
function downloadFile(url, filename) {
    return new Promise((resolve, reject) => {
        const command = USE_WGET
            ? `wget -O "${filename}" "${url}"`
            : `curl -o "${filename}" "${url}"`;

        exec(command, (error, stdout, stderr) => {
            if (error) {
                console.error(`Ошибка при загрузке ${url}:`, error.message);
                reject(error);
                return;
            }
            if (stderr) {
                console.error(`stderr при загрузке ${url}:`, stderr);
            }
            console.log(`Успешно загружен: ${url}`);
            resolve(stdout);
        });
    });
}

// Основная функция
async function main(relativeUrls) {
    console.log(`Начинаем загрузку файлов с ${DOMAIN}`);
    for (const relUrl of relativeUrls) {
        const fullUrl = relUrl.includes('https://') ? relUrl : DOMAIN + relUrl;
        const filename = path.join(OUTPUT_DIR, path.basename(relUrl));
        await downloadFile(fullUrl, filename);
    }
    console.log('Загрузка завершена.');
}

const labels = []
main(labels);
