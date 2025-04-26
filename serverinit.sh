#!/bin/bash

# Обновление системы
echo "Обновление системы..."
sudo apt update && sudo apt upgrade -y

# Установка необходимых инструментов
echo "Установка необходимых инструментов..."
sudo apt install -y software-properties-common curl wget git unzip openssh-client

# Добавление репозитория для PHP
echo "Добавление репозитория PHP..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Установка PHP (предполагая, что вы имели в виду PHP 8.2)
PHP_VERSION="8.4"
echo "Установка PHP $PHP_VERSION..."
sudo apt install -y php$PHP_VERSION php$PHP_VERSION-common php$PHP_VERSION-cli php$PHP_VERSION-fpm \
php$PHP_VERSION-mysql php$PHP_VERSION-mbstring php$PHP_VERSION-xml php$PHP_VERSION-curl \
php$PHP_VERSION-zip php$PHP_VERSION-bcmath php$PHP_VERSION-gd php$PHP_VERSION-intl

# Установка Composer
echo "Установка Composer..."
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Установка MySQL 8.0.32
echo "Установка MySQL 8.0.32..."
wget https://dev.mysql.com/get/mysql-apt-config_0.8.24-1_all.deb
sudo dpkg -i mysql-apt-config_0.8.24-1_all.deb
sudo apt update
sudo apt install -y mysql-server

# Настройка MySQL (опционально: задание пароля root)
echo "Настройка MySQL..."
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_secure_password';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Запуск и включение автозапуска MySQL
sudo systemctl start mysql
sudo systemctl enable mysql

# Установка Node.js и npm (необходимо для Laravel Mix)
echo "Установка Node.js и npm..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Создание базы данных для Laravel
echo "Создание базы данных для Laravel..."
DB_NAME="laravel_db"
DB_USER="laravel_user"
DB_PASSWORD="your_secure_db_password"
sudo mysql -e "CREATE DATABASE $DB_NAME;"
sudo mysql -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Создание директории .ssh, если её нет
echo "Создание директории ~/.ssh..."
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Запись приватного ключа в файл
echo "Запись приватного ключа..."
SSH_KEY_PATH="$HOME/.ssh/id_rsa_git"
cat <<EOF > "$SSH_KEY_PATH"
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAMwAAAAtzc2gtZW
QyNTUxOQAAACBnbSsLOaWEjfP82LddqajWw/GClJ6G+mangMj2iht0CQAAAJjSskFR0rJB
UQAAAAtzc2gtZWQyNTUxOQAAACBnbSsLOaWEjfP82LddqajWw/GClJ6G+mangMj2iht0CQ
AAAEDo3Bbyt0QgfCxq80wJ1hRI0ZqXm9kHD0rhgvreEkuGFWdtKws5pYSN8/zYt12pqNbD
8YKUnob6ZqeAyPaKG3QJAAAAE2FydEBERVNLVE9QLUM1VUlQNlUBAg==
-----END OPENSSH PRIVATE KEY-----
EOF

# Установка прав доступа для приватного ключа
chmod 600 "$SSH_KEY_PATH"

# Добавление ключа в SSH агент (опционально)
echo "Добавление ключа в SSH агент..."
eval "$(ssh-agent -s)"
ssh-add "$SSH_KEY_PATH"

# Настройка SSH для работы с Git
echo "Настройка SSH для работы с Git..."
SSH_CONFIG_PATH="$HOME/.ssh/config"
if [ ! -f "$SSH_CONFIG_PATH" ]; then
    touch "$SSH_CONFIG_PATH"
    chmod 600 "$SSH_CONFIG_PATH"
fi

cat <<EOF >> "$SSH_CONFIG_PATH"
Host github.com
    HostName github.com
    User maylogic
    IdentityFile $SSH_KEY_PATH
    IdentitiesOnly yes
EOF

echo "Приватный ключ успешно настроен для работы с Git."

echo "Установка проекта Laravel..."

# Настройка .env файла для Laravel
echo "Настройка .env файла для Laravel..."
mkdir -p /var/www/html
cd ~/$PROJECT_NAME
cp .env.example .env
sed -i "s/DB_DATABASE=laravel/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=root/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" .env

# Генерация ключа приложения Laravel
php artisan key:generate
php artisan migrate --seed

# Установка Node.js 22.12.0
echo "Установка Node.js 22.12.0..."
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

# Проверка версии Node.js
echo "Установленная версия Node.js: $(node --version)"
echo "Установленная версия npm: $(npm --version)"

# Создание директории для проекта
PROJECT_DIR="$HOME/nuxt3-project"
echo "Создание директории для проекта: $PROJECT_DIR..."
mkdir -p "$PROJECT_DIR"
cd "$PROJECT_DIR"

# Клонирование репозитория через SSH
echo "Клонирование репозитория Nuxt 3 проекта..."
REPO_URL="git@github.com:username/repository.git" # Замените на ваш URL репозитория
git clone "$REPO_URL" .
if [ $? -ne 0 ]; then
    echo "Ошибка при клонировании репозитория. Проверьте SSH-ключ и URL."
    exit 1
fi

# Установка зависимостей
echo "Установка зависимостей проекта..."
npm install
if [ $? -ne 0 ]; then
    echo "Ошибка при установке зависимостей. Проверьте package.json."
    exit 1
fi

# Сборка проекта для продакшена
echo "Сборка проекта для продакшена..."
npm run build
if [ $? -ne 0 ]; then
    echo "Ошибка при сборке проекта. Проверьте конфигурацию."
    exit 1
fi

# Запуск проекта в продакшен режиме
echo "Запуск проекта в продакшен режиме..."
PORT=3000 # Укажите порт, на котором будет работать приложение
nohup npm run start -- -p $PORT > app.log 2>&1 &
echo "Проект запущен на порту $PORT. Логи доступны в файле app.log."

# Проверка запущенного процесса
echo "Проверка запущенного процесса..."
sleep 5
ps aux | grep "[n]ode" | grep "start"
if [ $? -eq 0 ]; then
    echo "Проект успешно запущен."
else
    echo "Не удалось запустить проект. Проверьте логи в app.log."
fi

# Завершение
echo "Установка завершена!"
echo "MySQL установлен с версией $(mysql --version)"
echo "PHP установлен с версией $(php --version | head -n 1)"
echo "Laravel проект создан в ~/my_laravel_project"
