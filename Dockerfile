# 1. استخدام نسخة PHP جاهزة ومدمج معها خادم Apache
FROM php:8.2-apache

# 2. تثبيت المكتبات الأساسية في السيرفر وإضافات PHP المطلوبة (GD, ZIP, PDO)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd zip

# 3. تفعيل (mod_rewrite) في أباتشي لكي تعمل روابط لارافل (Routes) بدون أخطاء
RUN a2enmod rewrite

# 4. توجيه السيرفر ليقرأ الموقع من مجلد (public) وهو السلوك الصحيح في لارافل
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. تثبيت أداة Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. تحديد مسار العمل ونسخ ملفات مشروعك من GitHub إلى السيرفر
WORKDIR /var/www/html
COPY . .

# 7. إعطاء الصلاحيات اللازمة للسيرفر للكتابة في مجلدات التخزين (مهم جداً لتجنب أخطاء 500)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. تثبيت حزم لارافل من ملف composer.json بدون حزم التطوير
RUN composer install --optimize-autoloader --no-dev