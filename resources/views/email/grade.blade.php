<!DOCTYPE html>
<html lang="id">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Nilai Baru</title>
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
                  background-color: #2196F3;
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
                  background-color: #e3f2fd;
                  border-left: 4px solid #2196F3;
                  padding: 15px;
                  margin: 15px 0;
            }

            .info-box strong {
                  color: #1565c0;
            }

            .score-box {
                  background-color: #fff;
                  border: 3px solid #4CAF50;
                  border-radius: 8px;
                  padding: 20px;
                  margin: 20px 0;
                  text-align: center;
            }

            .score-box .score {
                  font-size: 48px;
                  font-weight: bold;
                  color: #4CAF50;
                  margin: 10px 0;
            }

            .score-box .label {
                  color: #666;
                  font-size: 14px;
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
                  <h1>Tugas Anda Telah Dinilai</h1>
            </div>

            <div class="content">
                  <p>Halo <strong>{{ $student->name }}</strong>,</p>

                  <p>Dosen telah memberikan nilai untuk tugas Anda pada mata kuliah <strong>{{ $courseName }}</strong>.
                  </p>

                  <div class="info-box">
                        <strong>Judul Tugas:</strong><br>
                        {{ $assignmentTitle }}
                  </div>

                  <div class="score-box">
                        <div class="label">NILAI ANDA</div>
                        <div class="score">{{ $score }}</div>
                        <div class="label">dari 100</div>
                  </div>

                  @if($score >= 80)
                        <p style="color: #4CAF50; font-weight: bold; text-align: center;">Luar biasa! Pertahankan prestasi
                              Anda!</p>
                  @elseif($score >= 60)
                        <p style="color: #ff9800; font-weight: bold; text-align: center;">Bagus! Terus tingkatkan!</p>
                  @else
                        <p style="color: #f44336; font-weight: bold; text-align: center;">Jangan menyerah! Terus belajar!</p>
                  @endif

                  <p style="margin-top: 30px;">
                        Salam,<br>
                        <strong>Sistem E-Learning Kampus</strong>
                  </p>
            </div>

            <div class="footer">
                  <p>Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.</p>
            </div>
      </div>
</body>

</html>