<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Freejnakw</title>
    <link href="https://fonts.googleapis.com/css2?family=Lateef&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;   /* يخليها ف النص رأسي */
            justify-content: center; /* يخليها ف النص أفقي */
            background-color: #f5f5f5;
            font-family: 'Cairo', sans-serif;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Kuwait_Towers.svg/1024px-Kuwait_Towers.svg.png');
            background-repeat: no-repeat;
            background-size: 300px;
            background-position: bottom left;
            opacity: 0.05;
            z-index: 0;
        }

        .container {
            position: relative;
            width: 95%;
            max-width: 730px;   /* العرض أكبر شوية */
            min-height: 75vh;   /* الطول أقل */
            border: 8px solid #4ea9b4;
            border-radius: 20px;
            padding: 40px 20px 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background: rgba(255, 255, 255, 0.96);
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        .logo-wrapper {
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }

        .logo-wrapper img {
            width: 135px;  /* أكبر شوية */
            border-radius: 30px;
        }

      .title {
            font-size: 54px;
            font-weight: bold;
            line-height: 44.98px;
            font-family: 'Cairo', sans-serif;
            text-align: center;
            color:#1a2626;
            word-break: break-word;
            overflow-wrap: break-word;
            align-items: center;
            color: #333;
            margin-bottom: 60px; /* ✅ مسافة بين العنوان والصورة */
        }


        .song-image img,
        .song-image video,
        .song-image audio {
            width: 100%;
            border-radius: 12px;
        }

        .question-box {
            background: #fff7e6;
            border: 1px solid #e0b96d;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 18px;
            margin-bottom: 15px;
            display: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 500px) {
            .container {
                max-width: 100%;   /* مرنة أكتر في الموبايل */
                min-height: 50vh;
                padding: 30px 15px;
            }
            .title { font-size: 22px; }
            .question-box { font-size: 16px; }
        }
    </style>

    <script>
        function showQuestion() {
            const box = document.getElementById('questionBox');
            box.style.display = 'block';
            setTimeout(() => {
                box.style.display = 'none';
            }, 5000);
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="logo-wrapper">
            <img src="{{asset('imagesfp/setting/d3k79ydx1745153448.png')}}" alt="logo">
        </div>

        <div class="question-box" id="questionBox">
            {{ $question->question }}
        </div>

        <div class="title">{{ $question->answer }}</div>
        <div class="song-image">
            @if(Str::endsWith($question->link_answer, ['.jpg','.jpeg','.png','.gif']))
                <img src="{{ asset($question->link_answer) }}" alt="Answer Image">
            @elseif(Str::endsWith($question->link_answer, ['.mp4','.webm']))
                <video controls>
                    <source src="https://admin.freejnakw.com/{{ $question->link_answer }}" type="video/mp4">
                    متصفحك لا يدعم تشغيل الفيديو.
                </video>
            @elseif(Str::endsWith($question->link_answer, ['.mp3','.wav','.ogg']))
                <audio controls>
                    <source src="https://admin.freejnakw.com/{{ $question->link_answer }}" type="audio/mpeg">
                    متصفحك لا يدعم تشغيل الصوت.
                </audio>
            @else
                <p>لا يوجد محتوى متاح.</p>
            @endif
        </div>
    </div>
</body>
</html>
