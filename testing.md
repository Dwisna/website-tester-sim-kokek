1. POST /service/token (Generate Token)

Ini adalah langkah wajib pertama untuk mendapatkan access_token.

    Method: POST

    URL: {{base_url}}/service/token

    Headers:

        Accept: application/json

        Content-Type: application/json

    Body (JSON):
    JSON

    {
        "client_id": "rup_client_xxxxxxxxxxxxxxx",
        "client_secret": "secret_xxxxxxxxxxxxxxx",
        "purpose": "gateway"
    }

2. GET /dashboard (Statistik & Tabel)

    Method: GET

    URL: {{base_url}}/dashboard?page=1&per_page=10

    Headers:

        Accept: application/json

        Authorization: Bearer {access_token_dari_langkah_1}

3. GET /records/{id} (Detail)

Ganti {id} dengan angka ID yang ada di database (contoh: /records/1).

    Method: GET

    URL: {{base_url}}/records/1

    Headers:

        Accept: application/json

        Authorization: Bearer {access_token}

4. GET /history & /notifications

    Method: GET

    URL: {{base_url}}/history atau {{base_url}}/notifications

    Headers:

        Accept: application/json

        Authorization: Bearer {access_token}

5. GET /api/download (Download Excel)

    Method: GET

    URL: {{base_url}}/api/download

    Headers:

        Authorization: Bearer {access_token}

    Cara Cek: Di Postman/Thunder Client, setelah klik Send, cari tombol "Save Response" atau "Download" agar Anda mendapatkan file .xlsx nya.

6. POST /n8n/webhook (Simulasi Webhook)

Endpoint ini biasanya digunakan untuk menerima kiriman data, jadi biasanya tidak membutuhkan Authorization (kecuali Anda ingin menambahkan proteksi khusus nanti).

    Method: POST

    URL: {{base_url}}/n8n/webhook

    Headers:

        Accept: application/json

        Content-Type: application/json

    Body (JSON):
    JSON

    {
        "source": "n8n",
        "event": "customer_message",
        "title": "Sinkronisasi Webhook",
        "message": "Data berhasil diterima dari n8n",
        "priority": "medium",
        "customer": "Test User"
    }

Tips Tambahan untuk Postman:

    Environment Variables: Di Postman, Anda bisa membuat Environment baru agar tidak perlu mengetik token berulang kali.

        Buat variabel token dan masukkan hasilnya setiap kali Anda melakukan request /service/token.

        Gunakan {{token}} di kolom Authorization pada request lainnya.

    Status Code:

        Jika muncul 200 OK: Berhasil.

        Jika muncul 401 Unauthorized: Token salah, belum dimasukkan, atau sudah kedaluwarsa.

        Jika muncul 404 Not Found: URL salah atau ID tidak ada di database.

        Jika muncul 500 Internal Server Error: Cek storage/logs/laravel.log di Server untuk melihat detail error PHP-nya.