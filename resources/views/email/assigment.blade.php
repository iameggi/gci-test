<!DOCTYPE html>
<html lang="id">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Tugas Baru</title>
      <style>
            body {
                  font-family: Arial, sans-serif;
                  line-height: 1.6;
                  color: #333;
                  max-width: 600px;
                  margin: 0 auto;
                  padding: 20px;
            }

            .container {
                  background-color: #f9f9f9;
                  border-radius: 8px;
                  padding: 30px;
                  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .header {
                  background-color: #4CAF50;
                  color: white;
                  padding: 20px;
                  border-radius: 8px 8px 0 0;
                  text-align: center;
                  margin: -30px -30px 20px -30px;
            }

            .header h1 {
                  margin: 0;
                  font-size: 24px;
            }

            .content {
                  background-color: white;
                  padding: 20px;
                  border-radius: 8px;
            }

            .info-box {
                  background-color: #e8f5e9;
                  border-left: 4px solid #4CAF50;
                  padding: 15px;
                  margin: 15px 0;
            }

            .info-box strong {
                  color: #2e7d32;
            }

            .deadline {
                  background-color: #fff3e0;
                  border-left: 4px solid #ff9800;
                  padding: 15px;
                  margin: 15px 0;
            }

            .deadline strong {
                  color: #e65100;
            }

            .footer {
                  margin-top: 20px;
                  text-align: center;
                  color: #666;
                  font-size: 12px;
            }
      </style>
</head>

<body>
      <div class="container">
            <div class="header">
                  <h1> Tugas Baru Tersedia</h1>
            </div>

            <div class="content">
                  <p>Halo <strong>{{ $student->name }}</strong>,</p>

                  <p>Dosen <strong>{{ $lecturerName }}</strong> telah menambahkan tugas baru pada mata kuliah
                        <strong>{{ $courseName }}</strong>.
                  </p>

                  <div class="info-box">
                        <strong>Judul Tugas:</strong><br>
                        {{ $assignment->title }}
                  </div>

                  <div class="info-box">
                        <strong>Deskripsi:</strong><br>
                        {{ $assignment->description }}
                  </div>

                  <div class="deadline">
                        <strong>Deadline:</strong><br>
                        {{ $assignment->deadline->format('d F Y, H:i') }} WIB
                  </div>

                  <p style="margin-top: 20px;">Segera kerjakan dan submit tugas Anda sebelum deadline!</p>
            </div>
      </div>
</body>

</html>