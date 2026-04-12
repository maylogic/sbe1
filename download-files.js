import { mkdir, access, readFile, stat, readdir, unlink } from 'fs/promises';
import path, { join, dirname, basename } from 'path';
import { exec } from 'child_process';

// Настройки
const DOMAIN = 'https://bsk-media.com';
const USE_WGET = true; // поменять на false, чтобы использовать curl
const OUTPUT_DIR_PREFIX = './downloads'
const INPUT_DIR = './downloads/next_data'

async function ensureDir(dir) {
    await mkdir(dir, { recursive: true });
}

async function fileExists(filePath) {
    try {
        await access(filePath);
        return true;
    } catch {
        return false;
    }
}

/**
 * Выполняет промисы пачками (батчами) или с ограничением concurrency.
 * @param {Array} tasks - Массив функций, возвращающих промисы.
 * @param {number} limit - Максимальное количество одновременных задач.
 */
async function runWithLimit(tasks, limit) {
    const results = [];
    let index = 0;

    async function worker() {
        while (index < tasks.length) {
            const currentIndex = index++;
            try {
                results[currentIndex] = await tasks[currentIndex]();
            } catch (err) {
                results[currentIndex] = err; // Или обработка ошибки
            }
        }
    }

    // Запускаем 'limit' воркеров
    const workers = Array(Math.min(limit, tasks.length)).fill(null).map(worker);
    await Promise.all(workers);

    return results;
}

async function cleanEmptyFiles(dir) {
    try {
        const fullPath = join(OUTPUT_DIR_PREFIX, dir);
        // Проверяем существование директории
        try {
            await stat(fullPath);
        } catch {
            return; // Директория не существует, очищать нечего
        }

        const files = await readdir(fullPath);

        for (const file of files) {
            const filePath = join(fullPath, file);
            const stats = await stat(filePath);

            if (stats.isFile() && stats.size === 0) {
                await unlink(filePath);
                console.log(`Удален пустой файл: ${filePath}`);
            }
        }
    } catch (error) {
        console.error(`Ошибка при очистке директории ${dir}:`, error);
    }
}

function downloadFile(url, filename) {
    return new Promise(async (resolve, reject) => {
        // Нормализуем путь к абсолютному, чтобы wget/curl работали корректно
        const absoluteFilename = path.resolve(filename);
        const dir = dirname(absoluteFilename);

        // 1. Проверка существования
        if (await fileExists(absoluteFilename)) {
            const stats = await stat(absoluteFilename);
            if (stats.size > 0) {
                resolve(absoluteFilename);
                return;
            } else {
                // Если файл пустой, удаляем его и скачиваем заново
                await unlink(absoluteFilename);
            }
        }

        // 2. Создаем директорию
        try {
            await ensureDir(dir);
        } catch (err) {
            reject(err);
            return;
        }

        // 3. Формируем команду
        // Для curl: -f (fail silently on server errors), -s (silent), -S (show error), -L (follow redirects)
        // Для wget: --tries=3, --timeout=10
        const safeUrl = url.replace(/"/g, '\\"');
        const safeFilename = absoluteFilename.replace(/"/g, '\\"');

        let command;
        if (USE_WGET) {
            // wget -q (quiet), -O (output), --tries=1 (не пытаться бесконечно)
            command = `wget -q -O "${safeFilename}" --tries=1 --timeout=10 "${safeUrl}"`;
        } else {
            // curl -f (fail on HTTP error), -s (silent), -S (show error), -L (redirects)
            command = `curl -f -s -S -L -o "${safeFilename}" "${safeUrl}"`;
        }

        exec(command, async (error, stdout, stderr) => {
            if (error) {
                // Ошибка выполнения команды или HTTP ошибка (благодаря флагам -f/--tries)
                // Удаляем битый файл, если он создался
                try { await unlink(absoluteFilename); } catch {}
                reject(new Error(`Command failed: ${error.message}`));
                return;
            }

            // 4. Финальная проверка: файл существует и не пуст?
            try {
                const stats = await stat(absoluteFilename);
                if (stats.size === 0) {
                    await unlink(absoluteFilename);
                    reject(new Error(`File downloaded but is empty: ${url}`));
                    return;
                }
                resolve(absoluteFilename);
            } catch (statError) {
                reject(new Error(`File not found after download: ${absoluteFilename}`));
            }
        });
    });
}

async function downloadRelativeUrls(relativeUrls, targetDir) {
    if (!relativeUrls || relativeUrls.length === 0) return;

    // Создаем массив задач (функций), а не промисов
    const tasks = relativeUrls.map(relUrl => {
        return () => {
            const fullUrl = relUrl.startsWith('http') ? relUrl : `${DOMAIN}${relUrl}`;
            const filename = join(targetDir, basename(relUrl));
            return downloadFile(fullUrl, filename);
        };
    });

    // Ограничиваем до 5-10 одновременных скачиваний
    const results = await runWithLimit(tasks, 5);

    results.forEach((res, index) => {
        if (res instanceof Error) {
            console.warn(`[FAIL] ${relativeUrls[index]}: ${res.message}`);
        } else {
            console.log(`[OK] ${relativeUrls[index]}`);
        }
    });
}

async function getTemplateSvgs(inputFile, outputDir) {
    const svgDir = join(OUTPUT_DIR_PREFIX, outputDir);
    const iconsDir = join(OUTPUT_DIR_PREFIX, 'previews', outputDir);

    // Создаем директории заранее
    await Promise.all([
        ensureDir(svgDir),
        ensureDir(iconsDir)
    ]);

    try {
        const data = await readFile(join(INPUT_DIR, inputFile), 'utf8');
        const json = JSON.parse(data);

        const iconsSet = new Set();
        const svgsSet = new Set();

        for (const key in json) {
            const element = json[key];
            if (key.startsWith('PersonalizeTheme:')) {
                if (element.icon) iconsSet.add('/media/' + element.icon);
            } else if (key.startsWith('PersonalizeTemplateElement:') && element.class === 'label') {
                if (element.style?.imageUrl) svgsSet.add(element.style.imageUrl);
            }
        }

        const icons = Array.from(iconsSet);
        const svgs = Array.from(svgsSet);

        await Promise.all([
            downloadRelativeUrls(icons, iconsDir),
            downloadRelativeUrls(svgs, svgDir)
        ]);

    } catch (error) {
        console.error(`Error processing ${inputFile}:`, error);
        throw error;
    }
}

async function downloadTemplateSvgs() {
    const map = new Map();
    const daycareDir = 'daycare'
    const labelsDir = 'labels'
    map.set('daycare/backpack.json', daycareDir);
    map.set('daycare/backpack-and-lunch.json', daycareDir);
    map.set('daycare/bento-lunch-box.json', daycareDir);
    map.set('daycare/daycare-drink-bottles.json', daycareDir);
    map.set('daycare/daycare-supplies-combo.json', daycareDir);
    map.set('daycare/drawstring-bags.json', daycareDir);
    map.set('daycare/drink-bottles.json', daycareDir);
    map.set('daycare/librarybags.json', daycareDir);
    map.set('daycare/lunch-bags.json', daycareDir);
    map.set('daycare/pencilcases.json', daycareDir);
    map.set('daycare/pendil-tins.json', daycareDir);
    map.set('daycare/sketchpads.json', daycareDir);
    map.set('daycare/swim-bags.json', daycareDir);
    map.set('daycare/totebags.json', daycareDir);

    map.set('labels/baby-bottle.json', labelsDir);
    map.set('labels/bagtags.json', labelsDir);
    map.set('labels/basic-name-labels-value-kit.json', labelsDir);
    map.set('labels/daycare.json', labelsDir);
    map.set('labels/essential.json', labelsDir);
    map.set('labels/family.json', labelsDir);
    map.set('labels/initial-dot-cloth.json', labelsDir);
    map.set('labels/iron.json', labelsDir);
    map.set('labels/kids-drink-bottle-name-labels.json', labelsDir);
    map.set('labels/large-name-labels.json', labelsDir);
    map.set('labels/large-wrap-stick-cloth-name-labels.json', labelsDir);
    map.set('labels/lunch-box-name-labels.json', labelsDir);
    map.set('labels/medium-name-labels.json', labelsDir);
    map.set('labels/mini-bag-tag-twin.json', labelsDir);
    map.set('labels/mini-cloth.json', labelsDir);
    map.set('labels/mini-name-labels.json', labelsDir);
    map.set('labels/name-and-stamp-combo.json', labelsDir);
    map.set('labels/name-label-pouch.json', labelsDir);
    map.set('labels/plain.json', labelsDir);
    map.set('labels/preschool-and-kindy.json', labelsDir);
    map.set('labels/round-shoe.json', labelsDir);
    map.set('labels/school.json', labelsDir);
    map.set('labels/small.json', labelsDir);
    map.set('labels/small-cloth.json', labelsDir);
    map.set('labels/sock-name-stamps.json', labelsDir);
    map.set('labels/subject-book-name-labels.json', labelsDir);
    map.set('labels/teacher-name-labels-value-kit.json', labelsDir);
    map.set('labels/teacher-sticker-pack.json', labelsDir);
    map.set('labels/tiny-pencil.json', labelsDir);
    map.set('labels/ultimate.json', labelsDir);
    map.set('labels/white-wrap-stick-name-labels.json', labelsDir);
    map.set('labels/wrap.json', labelsDir);

    await Promise.all(['previews/labels', 'previews/daycare', 'daycare', 'labels'].map(cleanEmptyFiles))

    const tasks = [];
    for (const [inputFile, outputDir] of map) {
        tasks.push(() => getTemplateSvgs(inputFile, outputDir));
    }

    await runWithLimit(tasks, 3);

    console.log('Все процессы завершены.');
}

downloadTemplateSvgs();
