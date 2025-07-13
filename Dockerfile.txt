# استخدم صورة PHP الرسمية مع Apache
FROM php:8.2-apache

# انسخ ملفاتك إلى مجلد الاستضافة داخل السيرفر
COPY public/ /var/www/html/

# أعط صلاحيات للرفع
RUN chmod -R 755 /var/www/html
