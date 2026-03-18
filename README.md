DEPENDENCIES:
1. ANY IDE
2. XAMPP

CARA NYALAIN:
1. CLONE PROJECTNYA DULU
2. TARUH DI FOLDER htdocs didalam folder xampp. misalnya "C:\xampp\htdocs"
3. start xampp servicenya, nyalain bagian apache dan mysql. ingat harus sampai nyala (hijau)
4. SETUP DATABASE DI PHPMYADMIN, buka browser terus ke http://localhost/phpmyadmin, click database, create baru lalu kasih nama dbnya db_gkjwsukolilo terus create. selanjutnya import, choose file database (ada di folder database project) lalu klik import.
5. Buka koneksi.php di folder config project, lalu sesuaikan dengan local xampp default bagian ini:
Host/Hostname: localhost (or 127.0.0.1)

Database Name: db_gkjwsukolilo

Username: root

Password: (biarin aja kosong karena xampp default gaada passwordnya)

5. terakhir Run the Project. tinggal buka di browser http://localhost/nama-foldernya
