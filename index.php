<?php

require_once "config.php";

/* جلب أحدث 3 أخبار */
$news_sql = "
    SELECT id, title, content, created_at
    FROM news
    WHERE status = 'published'
    ORDER BY created_at DESC
    LIMIT 3
";

$news_result = $conn->query($news_sql);


/* حساب عدد الكليات */
$colleges_sql = "
    SELECT COUNT(*) AS total
    FROM colleges
";

$colleges_result = $conn->query($colleges_sql);

$colleges_count = 0;

if ($colleges_result) {
    $college_data = $colleges_result->fetch_assoc();
    $colleges_count = $college_data["total"];
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جامعة المستقبل</title>

    <link rel="stylesheet" href="style.css">
</head>

<body class="home-page">

    <header class="site-header">
        <div class="site-header__inner">
            <a class="brand" href="index.php" aria-label="جامعة المستقبل - الرئيسية">
                <span class="brand__mark" aria-hidden="true">FU</span>
                <span>
                    <strong>Future University</strong>
                    <small>البوابة الجامعية</small>
                </span>
            </a>

            <nav class="site-nav" aria-label="التنقل الرئيسي">
                <a class="is-active" href="index.php" aria-current="page">الرئيسية</a>
                <a href="pages/colleges.php">الكليات</a>
                <a href="pages/login.html">الطلاب</a>
                <a href="pages/teachers.php">أعضاء هيئة التدريس</a>
                <a href="pages/news.php">الأخبار</a>
                <a class="nav-login" href="pages/login.html">تسجيل الدخول</a>
            </nav>
        </div>
    </header>

    <main class="public-main">
        <section class="home-hero" aria-labelledby="home-title">
            <div class="home-hero__content">
                <p class="eyebrow hero-enter hero-enter--1"><span>01</span> Future University System</p>
                <h1 id="home-title" class="hero-enter hero-enter--2">المعرفة تبدأ من <em>هنا.</em></h1>
                <p class="hero-enter hero-enter--3">بوابة الجامعة الرسمية للوصول إلى المجتمع الأكاديمي، الأخبار، والكليات، مع مساحة آمنة لخدمات الطلاب.</p>
                <div class="hero-actions hero-enter hero-enter--4">
                    <a class="button button-arrow" href="pages/login.html">الدخول إلى البوابة <span aria-hidden="true">↗</span></a>
                    <a class="button button-secondary" href="pages/colleges.php">استكشف الكليات</a>
                </div>
                <div class="hero-meta hero-enter hero-enter--4"><span>جامعة المستقبل</span><span>بوابة أكاديمية موحدة</span></div>
            </div>
            <div class="home-hero__side">
                <div class="hero-visual hero-enter hero-enter--5" aria-hidden="true">
                    <div class="hero-visual__topline"><span>FU / 2026</span><span>ACADEMIC PORTAL</span></div>
                    <div class="hero-visual__headline"><span>THE</span><strong>FUTURE<br>OF LEARNING</strong></div>
                    <div class="hero-visual__monogram">FU</div>
                    <div class="hero-visual__grid"></div>
                    <div class="hero-visual__caption"><span>EST. 2026</span><strong>COMMUNITY / KNOWLEDGE</strong></div>
                </div>
                <div class="home-hero__note hero-enter hero-enter--5"><span class="section-kicker">نقطة البداية</span><strong>معلومات أكاديمية موثوقة</strong><p>كل ما تحتاجه لتبقى على اتصال بالجامعة.</p></div>
            </div>
        </section>

        <section class="page-section access-section" aria-labelledby="access-title">
            <div class="section-heading">
                <div>
                    <p class="section-kicker">الوصول السريع</p>
                    <h2 id="access-title">خدمات الجامعة</h2>
                </div>
                <p>روابط مباشرة إلى أكثر الأقسام استخدامًا.</p>
            </div>

            <div class="quick-links">
                <a class="quick-link" href="pages/login.html">
                    <span class="quick-link__index">01</span>
                    <span><strong>بوابة الطلاب</strong><small>الخدمات والبيانات الأكاديمية</small></span>
                    <span class="quick-link__arrow" aria-hidden="true">←</span>
                </a>
                <a class="quick-link" href="pages/colleges.php">
                    <span class="quick-link__index">02</span>
                    <span><strong>الكليات</strong><small>البرامج والقيادات الأكاديمية</small></span>
                    <span class="quick-link__arrow" aria-hidden="true">←</span>
                </a>
                <a class="quick-link" href="pages/teachers.php">
                    <span class="quick-link__index">03</span>
                    <span><strong>أعضاء هيئة التدريس</strong><small>التخصصات وبيانات التواصل</small></span>
                    <span class="quick-link__arrow" aria-hidden="true">←</span>
                </a>
                <a class="quick-link" href="pages/news.php">
                    <span class="quick-link__index">04</span>
                    <span><strong>أخبار الجامعة</strong><small>آخر المستجدات والإعلانات</small></span>
                    <span class="quick-link__arrow" aria-hidden="true">←</span>
                </a>
            </div>
        </section>

        <section class="overview-band" aria-labelledby="overview-title">
            <div>
                <p class="section-kicker">لمحة عن النظام</p>
                <h2 id="overview-title">معلومات الجامعة في متناولك</h2>
                <p>تجمع البوابة معلومات الكليات والأخبار المنشورة وأعضاء هيئة التدريس، مع وصول آمن لخدمات الطلاب.</p>
            </div>
            <div class="overview-stat">
                <strong data-count="<?php echo (int) $colleges_count; ?>">0</strong>
                <span>كلية مسجلة</span>
            </div>
        </section>

        <section class="page-section news-section" aria-labelledby="news-title">
            <div class="section-heading">
                <div>
                    <p class="section-kicker">آخر المستجدات</p>
                    <h2 id="news-title">أحدث الأخبار</h2>
                </div>
                <a class="text-link" href="pages/news.php">عرض كل الأخبار <span aria-hidden="true">←</span></a>
            </div>

            <div class="news-list">
                <?php if ($news_result && $news_result->num_rows > 0): ?>
                    <?php while ($news = $news_result->fetch_assoc()): ?>
                        <article class="news-item">
                            <div class="news-item__meta">خبر جامعي <span><?php echo htmlspecialchars($news["created_at"]); ?></span></div>
                            <h3><?php echo htmlspecialchars($news["title"]); ?></h3>
                            <p><?php echo htmlspecialchars($news["content"]); ?></p>
                            <a class="text-link" href="pages/news.php">قراءة الخبر <span aria-hidden="true">←</span></a>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-state">لا توجد أخبار منشورة حاليًا.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="site-footer__inner">
            <div>
                <strong>Future University</strong>
                <p>البوابة الجامعية الرسمية</p>
            </div>
            <p>&copy; <?php echo date("Y"); ?> جامعة المستقبل</p>
        </div>
    </footer>


    <!-- JavaScript -->
    <script src="script.js"></script>

</body>

</html>