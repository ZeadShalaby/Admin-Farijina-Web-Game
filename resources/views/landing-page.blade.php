<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>مباشر ديل</title>
    <link
      rel="shortcut icon"
      href="./assets/landing-img/arabicLogo.svg"
      type="image/x-icon"
    />
    <style>
      * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
      }

      body {
        font-family: "Arial", sans-serif;
      }

      .b {
        position: relative;
        text-shadow: 3px 4px 3px rgba(245, 245, 245, 0.15);

        font-size: 20px;
      }

      .logo-icon {
        width: 100px;
        position: relative;
        height: 100px;
        object-fit: cover;
      }

      .arabiclogo {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }

      .header-parent {
        scale: 1.1;

        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        justify-content: center;
        gap: 10px;
      }

      .b1 {
        margin-bottom: 15px;

        position: relative;
      }

      .navigation {
        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        justify-content: space-between;
        gap: 5vw;
        text-align: right;
        font-size: 32px;
      }

      .header {
        height: 163px;
        position: relative;
        background-color: #80172b;
        display: flex;
        padding: 0px 10%;
        gap: 50px;
        text-align: center;
        font-size: 24px;
        color: #fff;
        font-family: Cairo;

        flex-direction: row-reverse;
        align-items: center;
        justify-content: space-between;
      }

      .hamburger-icon-container {
        display: none;
        cursor: pointer;
      }

      .hamburger-menu {
        width: 300px;
        padding: 80px 0;
        border-radius: 30px;
        background-color: #80172b;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);

        position: absolute;
        top: -100vh;
        /* top: 150px; */
        left: 50%;
        translate: -50% 0;

        display: flex;
        flex-direction: column;
        align-items: stretch;

        transition: top 0.5s ease;
      }

      .hamburger-menu.active {
        top: 150px;
      }

      .hamburger-menu__navigation {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 50px;
      }

      /* Add additional styles as needed welcome-section */
      .welcome-section {
        background-color: #ffffff;
        /* White background */
        padding: 40px 0;
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        align-items: center;
      }

      .welcome-section .container {
        gap: 40px;
      }

      .container {
        width: 90%;
        margin: 0 auto;
        display: flex;
        flex-direction: row-reverse;
        justify-content: space-between;
        align-items: center;
      }

      .image-container {
        flex: 1;
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        align-items: center;
      }

      .image-container img {
        max-width: 100%;
        height: auto;
      }

      .text-container {
        flex: 2;
        text-align: right;
        padding-right: 20px;
      }

      .text-container h1 {
        font-size: 40px;
        color: #000;
        margin-bottom: 20px;
      }

      .text-container h1 span {
        color: #800000;
        /* Maroon color */
      }

      .text-container b {
        width: 50%;
        font-weight: 400;
        color: #333;
        font-size: 35px;
        line-height: 1.4;
        margin-bottom: 10px;
      }

      /* Add additional styles as needed download-section 3*/
      .download-section {
        background: linear-gradient(to right, #800000, #cc6666);
        /* Gradient background */
        padding: 50px 0;
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        align-items: center;
        color: #fff;
      }

      .container {
        width: 90%;
        margin: 0 auto;
        display: flex;
        flex-direction: row-reverse;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
      }

      .app-preview {
        flex: 1;
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        align-items: center;
        gap: 10px;
      }

      .app-preview img {
        height: 500px;
        width: auto;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      }

      .download-info {
        flex: 1;
        text-align: right;
        padding: 20px;
      }

      .download-info h2 {
        color: #000000;
        align-items: center;
        text-align: center;
        font-size: 3em;
        margin-bottom: 20px;
      }

      .download-info h2 span {
        color: #ffffff;
        /* Lighter color for Direct Deal */
      }

      .download-info p {
        color: black;
        align-items: center;
        font-size: 3em;
        margin-bottom: 20px;
      }

      .download-buttons {
        display: flex;
        flex-direction: row;
        gap: 20px;
        justify-content: flex-start;
        align-items: center;
        margin-top: 50px;
      }

      .download-buttons a {
        text-decoration: none;
      }

      .download-buttons .appstore-button,
      .download-buttons .googleplay-button {
        width: 200px;
        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        border: 2px solid #000;
        /* Black border */
        border-radius: 10px;
        padding: 10px 20px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
        text-align: center;
      }

      .download-buttons .appstore-button:hover,
      .download-buttons .googleplay-button:hover {
        background-color: #f0f0f0;
        /* Light gray background on hover */
        border-color: #800000;
        /* Maroon border on hover */
      }

      .download-buttons .button-content {
        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        gap: 10px;
      }

      .download-buttons .button-content img {
        width: 30px;
        height: auto;
      }

      .download-buttons .button-content span {
        font-size: 1.2em;
        color: #000;
      }

      /* Add additional styles as needed services-section 4 */
      .services-section {
        margin: 70px 0;
        padding: 40px 0;
        background-color: #ffffff;
        text-align: center;
      }

      .container-services {
        align-items: center;
        justify-content: center;
        margin: 0 auto;
      }

      .services-section h2 {
        margin-bottom: 80px;

        font-size: 3em;
        font-weight: bold;
        color: #800000;
        /* Maroon color */
      }

      .services-slider {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        position: relative;
      }

      .slider-btn {
        background-color: transparent;
        border: none;
        cursor: pointer;

        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-width: 80px;
      }

      .slider-btn span {
        font-size: 30px;
        font-weight: 600;
        color: #800000;

        transition: color 0.3s ease;
      }

      .slider-btn:hover span {
        color: #cc6666;
      }

      .services-cards {
        width: 100%;

        display: flex;
        flex-direction: row-reverse;
        gap: 50px;
        overflow-x: hidden;
      }

      .services-slider-track {
        display: flex;
        flex-direction: row-reverse;
        gap: 50px;

        animation: auto-scroll 20s linear infinite;
      }

      .service-card {
        padding: 20px;
        border: 1px solid #800000;
        border-radius: 5px;
        background-color: #fff;

        min-width: 350px;
        flex: 1 0 20%;

        transition: box-shadow 0.3s ease;
      }

      .service-card h3 {
        font-size: 1.5em;
        color: #800000;
        /* Maroon color */
        margin-bottom: 10px;
      }

      .service-card p {
        font-size: 1em;
        color: #333;
      }

      .service-card:hover {
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
      }

      .services-cards:has(.service-card:hover) .services-slider-track {
        animation-play-state: paused;
      }

      /* Add additional styles as needed footer */
      .group-child {
        height: 1px;
        border-top: 1px solid #bdbdbd;
        box-sizing: border-box;
      }

      .b {
        position: relative;
        letter-spacing: -0.03em;
        line-height: 28px;
      }

      .money-visa {
        width: 48px;
        position: relative;
        height: 48px;
      }

      .div {
        overflow: hidden;
        display: flex;
        flex-direction: row-reverse;
        align-items: flex-start;
        justify-content: flex-start;
        padding: 6px 0px;
        gap: 10px;
      }

      .parent {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        gap: 13px;
      }

      .span {
        font-weight: 900;
        font-size: 22px;
      }

      .frame-parent {
        margin-top: 50px;

        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        justify-content: space-between;
        gap: 50px;
      }

      .line-parent {
        width: 100%;
      }

      .b1 {
        margin: 0;

        position: relative;
        font-size: 24px;
      }

      .supportdirectdealio {
        position: relative;
        line-height: 27px;
      }

      .vector-icon {
        position: relative;
      }

      .supportdirectdealio-parent {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 18px;
      }

      .iconamoonphone-thin {
        position: relative;
        right: -4px;

        overflow: hidden;
        flex-shrink: 0;
      }

      .container {
        margin: 0;

        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        justify-content: flex-start;
        column-gap: 10px;
        row-gap: 50px;
      }

      .group,
      .frame-div {
        margin-top: 50px;
      }

      .group {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 13px;
      }

      .wrapper {
        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        justify-content: flex-start;
      }

      .frame-div {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 13px;
      }

      .p {
        margin: 0;

        line-height: 32px;
      }

      .logo-icon {
        width: 100px;
        position: relative;
        height: 100px;
        object-fit: cover;
      }

      .b8 {
        position: relative;
        text-shadow: 3px 4px 3px rgba(245, 245, 245, 0.15);
      }

      .arabiclogo {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 24px;
      }

      .parent1 {
        display: flex;
        flex-direction: column-reverse;
        align-items: flex-start;
        justify-content: center;
        gap: 30px;
      }

      .frame-group {
        width: 100%;
        margin-bottom: 100px;

        display: flex;
        flex-direction: row-reverse;
        align-items: flex-start;
        justify-content: space-evenly;
        gap: 50px;
      }

      .footer {
        /* height: 394px; */
        padding: 70px 10% 50px 10%;

        position: relative;
        background-color: #80172b;
        text-align: right;
        font-size: 20px;
        color: #fff;
        font-family: Cairo;

        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-start;
      }

      /*# sourceMappingURL=landing-page.css.map */
      .span {
        font-weight: 600;
        font-family: Cairo;
        color: #000;
      }

      .direct-deal1 {
        font-family: Cairo;
      }

      .span1 {
        font-weight: 600;
      }

      .direct-deal {
        margin: 0 auto;
      }

      .direct-deal .span {
        font-size: 40px;
      }

      .p-deal-container {
        font-family: Cairo;
        margin: 0;
        color: #000;
        font-size: 20px;
      }

      .p-deal-container .span1 {
        font-weight: 400;
        font-size: 35px;
      }

      .direct-deal-container {
        max-width: 606px;

        position: relative;
        font-size: 15px;
        color: #fff;
        font-family: Cairo;
      }
    </style>
    <style>
      .container-graphic {
        max-width: 1200px;
        margin: 0 auto;

        padding: 0 20px;
      }

      .title {
        margin-bottom: 60px;

        text-align: center;
        font-size: 3rem;
        font-weight: bold;
        color: #7f1d1d;
        /* Dark red color */
      }

      .grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
      }

      .card {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
      }

      .card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        text-align: center;
        /* opacity: 0.8; */
        background-color: #000;
      }

      .overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.5);
        color: #ffffff;
        padding: 10px;
        text-align: center;
        align-content: center;
        justify-content: center;
        text-align: center;
        font-weight: bold;
        font-size: 22px;
        letter-spacing: 1px;
        height: 100%;

        transition: letter-spacing 0.3s ease;
      }

      .card:hover {
        transform: scale(1.05);
      }

      .card:hover .overlay {
        letter-spacing: initial;
      }

      @keyframes auto-scroll {
        from {
          transform: translateX(calc(-100% - 50px));
        }
        to {
          transform: translateX(0);
        }
      }

      @media (max-width: 1075px) {
        .hamburger-icon-container {
          display: block;
        }

        .navigation {
          display: none;
        }

        .welcome-section {
          padding: 80px 0 50px 0;
        }

        .welcome-section .container {
          flex-direction: column-reverse;
          row-gap: 10px;
        }

        .footer {
          padding: 70px 7% 50px;
        }
      }

      @media (max-width: 900px) {
        .grid {
          grid-template-columns: repeat(2, 1fr);
        }

        .footer .frame-group {
          flex-wrap: wrap-reverse;
          align-items: flex-end;
        }

        .footer .frame-group .parent1 {
          flex-basis: 100%;
          flex-direction: row-reverse;
          align-items: center;
          column-gap: 50px;
        }

        .footer .frame-group .parent1 .p {
          font-size: 27px;
          line-height: 45px;
        }
      }

      @media (max-width: 600px) {
        .header {
          height: 120px;
        }

        .header-parent .b {
          display: none;
        }

        .text-container {
          padding-right: 0;
        }

        .download-info {
          padding: 0;
        }

        .app-preview img {
          width: 85vw;
          height: auto;
        }

        .download-buttons {
          flex-direction: column;
          align-items: flex-start;
        }

        .slider-btn {
          display: none;
        }

        .services-cards {
          width: 100%;
          margin: 0;
        }

        .footer .frame-group {
          flex-direction: column-reverse;
          align-items: flex-end;
        }

        .footer .frame-group .parent1 {
          flex-direction: column-reverse;
          align-items: flex-start;
        }

        .footer .frame-group .group,
        .footer .frame-group .frame-div {
          margin-top: 10px;
        }

        .footer .line-parent .frame-parent {
          gap: 35px;

          flex-direction: column;
        }
      }

      @media (max-width: 440px) {
        .grid {
          grid-template-columns: repeat(1, 1fr);
        }
      }
    </style>
  </head>

  <body>
    <div class="header">
      <div class="header-parent">
        <b class="b">مباشر ديل</b>
        <div class="arabiclogo">
          <img
            class="logo-icon"
            alt=""
            src="./assets/landing-img/arabicLogo.svg"
          />
        </div>
      </div>
      <div class="navigation">
        <b class="b1">تواصل معنا</b>
        <b class="b1">من نحن</b>
        <b class="b1">الرئيسية</b>
      </div>
      <div class="hamburger-icon-container" onclick="toggleHamburgerMenu()">
        <img
          src="./assets/landing-img/hamburger.png"
          alt="hamburger icon"
          width="50px"
          height="50px"
        />
      </div>
      <div class="hamburger-menu">
        <div class="hamburger-menu__navigation">
          <b class="b1">تواصل معنا</b>
          <b class="b1">من نحن</b>
          <b class="b1">الرئيسية</b>
        </div>
      </div>
    </div>

    <section class="welcome-section">
      <div class="container">
        <div class="image-container">
          <img
            src="./assets/landing-img/Banner 1.png"
            alt="Handshake between two phones"
          />
        </div>
        <div class="text-container">
          <h1>مرحباً بك في <span>مباشر ديل</span></h1>
          <b class="b-hear"
            >منصة مخصصة لربط المستقلين مع العملاء بطريقة مباشرة وسهلة.</b
          >
          <b class="b-hear"
            >سواء كنت تبحث عن مستقلين موهوبين أو تبحث عن فرص عمل جديدة، نحن هنا
            لنساعدك.</b
          >
        </div>
      </div>
    </section>
    <section class="download-section">
      <div class="container">
        <div class="app-preview">
          <img
            src="./assets/landing-img/Group 34339.png"
            alt="App Screenshot 1"
          />
        </div>
        <div class="download-info">
          <div class="direct-deal-container">
            <p class="direct-deal">
              <span class="span">قم بتنزيل تطبيق </span>
              <span>
                <b class="direct-deal1">Direct Deal</b>
              </span>
              <span class="span1">
                <span> </span>
              </span>
            </p>
            <p class="p-deal-container">
              <span class="span1">
                <span>الآن وابدأ في عقد الصفقات أينما كنت وفي أي وقت.</span>
              </span>
            </p>
          </div>
          <div class="download-buttons">
            <a href="your-appstore-link" class="appstore-button">
              <div class="button-content">
                <img
                  src="./assets/landing-img/Apple.svg"
                  alt="Download on the App Store"
                />
                <span>Download on the App Store</span>
              </div>
            </a>
            <a href="your-googleplay-link" class="googleplay-button">
              <div class="button-content">
                <img
                  src="./assets/landing-img/Playstore.svg"
                  alt="Get it on Google Play"
                />
                <span>GET IT ON Google Play</span>
              </div>
            </a>
          </div>
        </div>
      </div>
    </section>

    <section class="services-section">
      <div class="container-services">
        <h2>خدماتنا المميزة</h2>
        <div class="services-slider">
          <!-- <button class="slider-btn prev-btn">
            {{-- <span>&#8594;</span> --}}
          </button> -->
          <div class="services-cards">
            <div class="services-slider-track">
              <div class="service-card">
                <h3>تواصل مباشر</h3>
                <p>تواصل مع العملاء والمستقلين بدون وسطاء.</p>
              </div>
              <div class="service-card">
                <h3>إدارة المشاريع</h3>
                <p>قم بتقديم مشاريعك واحصل على النتائج في الوقت الفعلي.</p>
              </div>
              <div class="service-card">
                <h3>نظام دفع آمن</h3>
                <p>تعاملات مالية آمنة وموثوقة لكلا الطرفين.</p>
              </div>
              <div class="service-card">
                <h3>تنوع الخدمات</h3>
                <p>اختر من مجموعة واسعة من الخدمات التي يقدمها المستقلون.</p>
              </div>
            </div>
            <div class="services-slider-track">
              <div class="service-card">
                <h3>تواصل مباشر</h3>
                <p>تواصل مع العملاء والمستقلين بدون وسطاء.</p>
              </div>
              <div class="service-card">
                <h3>إدارة المشاريع</h3>
                <p>قم بتقديم مشاريعك واحصل على النتائج في الوقت الفعلي.</p>
              </div>
              <div class="service-card">
                <h3>نظام دفع آمن</h3>
                <p>تعاملات مالية آمنة وموثوقة لكلا الطرفين.</p>
              </div>
              <div class="service-card">
                <h3>تنوع الخدمات</h3>
                <p>اختر من مجموعة واسعة من الخدمات التي يقدمها المستقلون.</p>
              </div>
            </div>
          </div>
          <!-- <button class="slider-btn next-btn">
            {{-- <span>&#8592;</span> --}}
          </button> -->
        </div>
      </div>
    </section>

    <div class="container-graphic">
      <h1 class="title">التصنيفات</h1>
      <div class="grid">
        <div class="card">
          <img
            src="./assets/landing-img/unsplash_15wIddvL5dU.png"
            alt="Graphic Design"
          />
          <div class="overlay">
            <p>Graphic Design</p>
          </div>
        </div>
        <div class="card">
          <img
            src="./assets/landing-img/unsplash_gxGtqG5ul2g.png"
            alt="Cartoon Animation"
          />
          <div class="overlay">
            <p>Cartoon Animation</p>
          </div>
        </div>
        <div class="card">
          <img
            src="./assets/landing-img/unsplash_jPd5A9_mS-g.png"
            alt="Illustration"
          />
          <div class="overlay">
            <p>Illustration</p>
          </div>
        </div>
        <div class="card">
          <img
            src="./assets/landing-img/unsplash_qnWPjzewewA.png"
            alt="Flyers & Vouchers"
          />
          <div class="overlay">
            <p>Flyers & Vouchers</p>
          </div>
        </div>
        <div class="card">
          <img
            src="./assets/landing-img/unsplash_s9CC2SKySJM.png"
            alt="Logo Design"
          />
          <div class="overlay">
            <p>Logo Design</p>
          </div>
        </div>
        <div class="card">
          <img
            src="./assets/landing-img/unsplash_uhtDG9ePLQI.png"
            alt="Social Graphics"
          />
          <div class="overlay">
            <p>Social Graphics</p>
          </div>
        </div>
        <div class="card">
          <img
            src="./assets/landing-img/unsplash_v9vII5gV8Lw.png"
            alt="Article Writing"
          />
          <div class="overlay">
            <p>Article Writing</p>
          </div>
        </div>
        <div class="card">
          <img
            src="./assets/landing-img/unsplash_VW2oU66mwbc.png"
            alt="Video Editing"
          />
          <div class="overlay">
            <p>Video Editing</p>
          </div>
        </div>
      </div>
    </div>

    <br />
    <br />

    <br />
    <br />

    <div class="footer">
      <div class="frame-group">
        <div class="group">
          <b class="b1">تواصل معنا</b>
          <div class="supportdirectdealio-parent">
            <img
              class="vector-icon"
              alt=""
              src="./assets/landing-img/Vector.svg"
              width="28px"
              height="28px"
            />
            <b class="supportdirectdealio">support@directdeal.io</b>
          </div>
          <div class="supportdirectdealio-parent">
            <img
              class="iconamoonphone-thin"
              alt=""
              src="./assets/landing-img/iconamoon_phone-thin.svg"
              width="40px"
              height="40px"
            />
            <b class="supportdirectdealio">976 123 456 789</b>
          </div>
        </div>
        <div class="frame-div">
          <b class="b1">المساعدة</b>
          <div class="wrapper">
            <b class="supportdirectdealio">من نحن</b>
          </div>
          <div class="wrapper">
            <b class="supportdirectdealio">سياسة وأحكام التطبيق</b>
          </div>
          <div class="wrapper">
            <b class="supportdirectdealio">سياسة إالغاء واسترجاع المبلغ</b>
          </div>
        </div>
        <div class="parent1">
          <b class="supportdirectdealio">
            <p class="p">
              احصل على خدمات عالية الجودة، وتابع تقدم مشاريعك بسهولة،
            </p>
            <p class="p">واستفد من نظام دفع آمن وموثوق.</p>
          </b>
          <div class="arabiclogo">
            <img
              class="logo-icon"
              alt=""
              src="./assets/landing-img/arabicLogo.svg"
            />
            <b class="b8">مباشر ديل</b>
          </div>
        </div>
      </div>

      <div class="line-parent">
        <div class="group-child"></div>
        <div class="frame-parent">
          <div class="parent">
            <b class="b">وسائل الدفع المتاحة</b>
            <div class="div">
              <img
                class="money-visa"
                alt=""
                src="./assets/landing-img/Visa.svg"
              />
              <img
                class="money-visa"
                alt=""
                src="./assets/landing-img/Mastercard.svg"
              />
              <img
                class="money-visa"
                alt=""
                src="./assets/landing-img/Paypal.svg"
              />
            </div>
          </div>
          <div class="b">
            <span>2024 جميع الحقوق محفوظة لموقع </span>
            <span class="span">مباشر ديل</span>
          </div>
        </div>
      </div>
    </div>

    <script>
      const hamburgerMenu = document.querySelector(".hamburger-menu");

      function toggleHamburgerMenu() {
        hamburgerMenu.classList.toggle("active");
      }

      document.onscroll = () => {
        hamburgerMenu.classList.remove("active");
      };
    </script>
  </body>
</html>
