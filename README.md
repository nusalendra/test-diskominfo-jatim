- Requirements
  1. PHP Version : > 8.1
  2. Laravel Framework Version : > 11.9
  3. NPM Version : 10.5.0
  4. Menggunakan basis data MYSQL

- Langkah Instalasi
  1. Clone project repository ini.
  2. Jalankan “composer install” untuk menginstall dependensi PHP.
  3. Generate APP KEY dengan perintah “php artisan key:generate”.
  4. Ubah nama file .env.example menjadi .env.
  5. Sesuaikan port, username, dan password database pada file .env, lalu buat database terlebih dahulu. Setelah itu, jalankan perintah "php artisan migrate" di terminal atau CMD project
     untuk membuat kolom dari migrasi di database.
  6. Terakhir, jalankan server dengan perintah php artisan serve.

- Path API : Ada beberapa path API yang identik, namun dengan tipe yang berbeda. Path API yang dapat digunakan antara lain    :
  1. Products :
     - http://PROJECT_URL/api/products : Dapat digunakan untuk tipe GET dan POST
     - http://PROJECT_URL/api/products/{id} : Dapat digunakan untuk tipe get by id, update, dan delete.
       
  3. Orders :
     - http://PROJECT_URL/api/orders : Dapat digunakan untuk tipe GET dan POST.
     - http://PROJECT_URL/api/orders/{id} : Dapat digunakan untuk tipe get by id, update, dan delete.
  
- Upload Postman : Saya telah mengunggah file Postman di drive saya yang bisa digunakan untuk keperluan pengujian https://drive.google.com/drive/folders/1OlF3GHG39C_cB3LHoJ94CjQKxt-XvMsq?usp=sharing.
  
- ERD Database : Terdapat ERD database yang dapat dilihat, dan ERD ini disusun berdasarkan pemikiran saya sendiri
  ![drawSQL-image-export-2024-09-22](https://github.com/user-attachments/assets/aad0aa7f-e8f0-42f8-bc42-5bb954b591fc)

  1. Tabel orders : Digunakan untuk menyimpan informasi data yang akan dipesan. Di sini, saya hanya memerlukan ID order untuk dapat terhubung dengan tabel order_product.
  2. Tabel products : Digunakan untuk menyimpan informasi data products.
  3. Tabel order_products : Tabel ini menyimpan pasangan ID dari tabel orders (yang berisi informasi tentang setiap order) dan tabel products (yang berisi informasi tentang setiap produk). Dengan menggunakan tabel pivot, dapat menghubungkan banyak order dengan banyak produk, sehingga satu order bisa mencakup beberapa produk, dan satu produk bisa ada di berbagai order. 
