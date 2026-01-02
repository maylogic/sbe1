import os
import glob

# Укажите путь к вашей папке с SVG-файлами
folder_path = "downloads/dog_tags"

# Найти все файлы, оканчивающиеся на -Download_SVG.svg
pattern = os.path.join(folder_path, "*-Download_SVG.svg")
files = glob.glob(pattern)

if not files:
    print("Не найдено файлов с суффиксом -Download_SVG.svg")
else:
    missing = []
    for file in files:
        try:
            with open(file, 'r', encoding='utf-8') as f:
                content = f.read()
                if '<g id="cutline">' not in content:
                    missing.append(file)
        except Exception as e:
            print(f"Ошибка при чтении файла {file}: {e}")

    if missing:
        print("Следующие файлы НЕ содержат <g id=\"cutline\">:")
        for f in missing:
            print(f)
    else:
        print("Все файлы содержат <g id=\"cutline\">.")
