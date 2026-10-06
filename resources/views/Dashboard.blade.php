<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SMK Yadika Soreang – Sekolah Berkarakter & Berprestasi</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;900&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --blue-deep: #0a2463;
            --blue-mid: #1e3a8a;
            --blue-bright: #1d6fc4;
            --gold: #f4a913;
            --gold-light: #fbbf24;
            --white: #ffffff;
            --off-white: #f8f9fc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-900: #0f172a;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 20px rgba(10, 36, 99, 0.12);
            --shadow-lg: 0 12px 40px rgba(10, 36, 99, 0.18);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--white);
            color: var(--gray-900);
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(10, 36, 99, 0.97);
            backdrop-filter: blur(12px);
            border-bottom: 2px solid var(--gold);
            transition: all .3s;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 68px;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .nav-logo-icon {
            width: 44px;
            height: 44px;
            background: var(--gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 900;
            color: var(--blue-deep);
            font-family: 'Playfair Display', serif;
            flex-shrink: 0;
        }

        .nav-logo-text {
            line-height: 1.2;
        }

        .nav-logo-text span:first-child {
            display: block;
            font-size: 15px;
            font-weight: 800;
            color: var(--white);
            letter-spacing: .4px;
        }

        .nav-logo-text span:last-child {
            display: block;
            font-size: 11px;
            color: var(--gold-light);
            font-weight: 500;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all .2s;
            letter-spacing: .3px;
        }

        .nav-links a:hover {
            color: var(--white);
            background: rgba(244, 169, 19, 0.18);
        }

        .nav-cta {
            background: var(--gold);
            color: var(--blue-deep) !important;
            border-radius: 8px;
            padding: 8px 18px !important;
            font-weight: 700 !important;
        }

        .nav-cta:hover {
            background: var(--gold-light) !important;
            color: var(--blue-deep) !important;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 8px;
        }

        .hamburger span {
            display: block;
            width: 24px;
            height: 2.5px;
            background: var(--white);
            border-radius: 2px;
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #0a2463 0%, #1e3a8a 45%, #1d6fc4 100%);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 120px;
            background: linear-gradient(to top, var(--white), transparent);
            pointer-events: none;
        }

        .hero-shapes {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .hero-shapes .circle-1 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(244, 169, 19, 0.18) 0%, transparent 70%);
            top: -100px;
            right: -80px;
            border-radius: 50%;
            animation: floatA 8s ease-in-out infinite;
        }

        .hero-shapes .circle-2 {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.07) 0%, transparent 70%);
            bottom: 100px;
            left: -60px;
            border-radius: 50%;
            animation: floatB 10s ease-in-out infinite;
        }

        @keyframes floatA {

            0%,
            100% {
                transform: translateY(0) rotate(0deg)
            }

            50% {
                transform: translateY(-20px) rotate(5deg)
            }
        }

        @keyframes floatB {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(15px)
            }
        }

        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 24px 80px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(244, 169, 19, 0.2);
            border: 1px solid rgba(244, 169, 19, 0.5);
            border-radius: 100px;
            padding: 6px 16px;
            margin-bottom: 24px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--gold-light);
            letter-spacing: .8px;
            text-transform: uppercase;
            animation: fadeSlideUp .7s ease both;
        }

        .hero-badge::before {
            content: '●';
            font-size: 8px;
            color: var(--gold);
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(36px, 5vw, 58px);
            font-weight: 900;
            line-height: 1.1;
            color: var(--white);
            margin-bottom: 20px;
            animation: fadeSlideUp .8s ease .1s both;
        }

        .hero-title em {
            font-style: normal;
            color: var(--gold);
        }

        .hero-desc {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.75;
            max-width: 480px;
            margin-bottom: 36px;
            animation: fadeSlideUp .8s ease .2s both;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            animation: fadeSlideUp .8s ease .3s both;
        }

        .btn-primary {
            background: var(--gold);
            color: var(--blue-deep);
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all .25s;
            letter-spacing: .3px;
            box-shadow: 0 4px 20px rgba(244, 169, 19, 0.4);
        }

        .btn-primary:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(244, 169, 19, 0.5);
        }

        .btn-outline {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.4);
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all .25s;
        }

        .btn-outline:hover {
            border-color: var(--white);
            background: rgba(255, 255, 255, 0.1);
        }

        .hero-stats {
            display: flex;
            gap: 28px;
            margin-top: 44px;
            animation: fadeSlideUp .8s ease .4s both;
        }

        .hero-stat {
            text-align: left;
        }

        .hero-stat strong {
            display: block;
            font-size: 28px;
            font-weight: 800;
            color: var(--gold);
        }

        .hero-stat span {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.65);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .hero-visual {
            position: relative;
            animation: fadeSlideUp .9s ease .3s both;
        }

        .hero-card-main {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 32px;
            color: var(--white);
        }

        .hero-card-main h3 {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .hero-card-main p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
        }

        .service-pills {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .service-pill {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all .25s;
            cursor: pointer;
            text-decoration: none;
        }

        .service-pill:hover {
            background: rgba(244, 169, 19, 0.2);
            border-color: rgba(244, 169, 19, 0.4);
            transform: translateX(4px);
        }

        .pill-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .pill-icon.smbp {
            background: rgba(244, 169, 19, 0.25);
        }

        .pill-icon.pkl {
            background: rgba(34, 197, 94, 0.25);
        }

        .pill-icon.lms {
            background: rgba(168, 85, 247, 0.25);
        }

        .pill-text strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--white);
        }

        .pill-text span {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .pill-arrow {
            margin-left: auto;
            color: rgba(255, 255, 255, 0.4);
            font-size: 16px;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── SECTION COMMON ── */
        section {
            padding: 90px 24px;
        }

        .section-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-tag {
            display: inline-block;
            background: rgba(29, 111, 196, 0.1);
            color: var(--blue-bright);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 100px;
            margin-bottom: 14px;
            border: 1px solid rgba(29, 111, 196, 0.2);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 3.5vw, 40px);
            font-weight: 900;
            color: var(--blue-deep);
            line-height: 1.2;
            margin-bottom: 14px;
        }

        .section-sub {
            font-size: 16px;
            color: var(--gray-500);
            max-width: 560px;
            line-height: 1.7;
        }

        .section-header {
            margin-bottom: 52px;
        }

        /* ── MARQUEE STRIP ── */
        .marquee-strip {
            background: var(--blue-deep);
            padding: 14px 0;
            overflow: hidden;
            border-top: 3px solid var(--gold);
            border-bottom: 3px solid var(--gold);
        }

        .marquee-track {
            display: flex;
            gap: 48px;
            white-space: nowrap;
            animation: marquee 25s linear infinite;
        }

        .marquee-track span {
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .marquee-track span::after {
            content: '✦';
            color: var(--gold);
        }

        @keyframes marquee {
            from {
                transform: translateX(0)
            }

            to {
                transform: translateX(-50%)
            }
        }

        /* ── LAYANAN UTAMA ── */
        #layanan {
            background: var(--off-white);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .service-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all .35s;
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .service-card-header {
            padding: 32px 28px 24px;
            position: relative;
            overflow: hidden;
        }

        .service-card-header::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
        }

        .sc-smbp .service-card-header {
            background: linear-gradient(135deg, #0a2463, #1d6fc4);
        }

        .sc-pkl .service-card-header {
            background: linear-gradient(135deg, #14532d, #15803d);
        }

        .sc-lms .service-card-header {
            background: linear-gradient(135deg, #4c1d95, #7c3aed);
        }

        .service-icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 16px;
        }

        .service-card-header h3 {
            color: var(--white);
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .service-card-header p {
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
        }

        .service-card-body {
            padding: 24px 28px;
        }

        .service-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 24px;
        }

        .service-features li {
            font-size: 14px;
            color: var(--gray-700);
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.5;
        }

        .service-features li::before {
            content: '✓';
            color: var(--blue-bright);
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .sc-pkl .service-features li::before {
            color: #16a34a;
        }

        .sc-lms .service-features li::before {
            color: #7c3aed;
        }

        .btn-service {
            display: block;
            text-align: center;
            text-decoration: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            transition: all .25s;
        }

        .sc-smbp .btn-service {
            background: rgba(29, 111, 196, 0.1);
            color: var(--blue-bright);
        }

        .sc-smbp .btn-service:hover {
            background: var(--blue-bright);
            color: var(--white);
        }

        .sc-pkl .btn-service {
            background: rgba(22, 163, 74, 0.1);
            color: #16a34a;
        }

        .sc-pkl .btn-service:hover {
            background: #16a34a;
            color: var(--white);
        }

        .sc-lms .btn-service {
            background: rgba(124, 58, 237, 0.1);
            color: #7c3aed;
        }

        .sc-lms .btn-service:hover {
            background: #7c3aed;
            color: var(--white);
        }

        /* ── JURUSAN ── */
        .jurusan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .jurusan-card {
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all .3s;
            background: var(--white);
        }

        .jurusan-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .jurusan-img {
            height: 180px;
            object-fit: cover;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
        }

        .jc-pplg {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        }

        .jc-akun {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        }

        .jc-hotel {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
        }

        .jurusan-body {
            padding: 22px 24px;
        }

        .jurusan-body h4 {
            font-size: 17px;
            font-weight: 800;
            color: var(--blue-deep);
            margin-bottom: 8px;
        }

        .jurusan-body p {
            font-size: 13.5px;
            color: var(--gray-500);
            line-height: 1.65;
            margin-bottom: 16px;
        }

        .jurusan-tag {
            display: inline-block;
            background: rgba(29, 111, 196, 0.08);
            color: var(--blue-bright);
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .5px;
            padding: 4px 12px;
            border-radius: 100px;
        }

        /* ── SAMBUTAN ── */
        #sambutan {
            background: var(--blue-deep);
        }

        .sambutan-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 72px;
            align-items: center;
        }

        .sambutan-img-wrap {
            position: relative;
        }

        .sambutan-photo {
            width: 100%;
            max-width: 380px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 3px solid rgba(244, 169, 19, 0.4);
        }

        .sambutan-photo-placeholder {
            width: 100%;
            aspect-ratio: 3/4;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.06));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            font-size: 72px;
            border-radius: 21px;
        }

        .sambutan-photo-placeholder p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
            text-align: center;
        }

        .sambutan-badge-float {
            position: absolute;
            bottom: -16px;
            right: -16px;
            background: var(--gold);
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }

        .sambutan-badge-float strong {
            display: block;
            font-size: 24px;
            font-weight: 800;
            color: var(--blue-deep);
        }

        .sambutan-badge-float span {
            font-size: 11px;
            color: var(--blue-deep);
            font-weight: 600;
        }

        .sambutan-content .section-tag {
            background: rgba(244, 169, 19, 0.2);
            color: var(--gold-light);
            border-color: rgba(244, 169, 19, 0.3);
        }

        .sambutan-content .section-title {
            color: var(--white);
        }

        .sambutan-quote {
            font-size: 15.5px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            border-left: 4px solid var(--gold);
            padding-left: 20px;
            margin: 20px 0 28px;
            font-style: italic;
        }

        .sambutan-name strong {
            display: block;
            font-size: 16px;
            font-weight: 800;
            color: var(--white);
        }

        .sambutan-name span {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
        }

        /* ── KEGIATAN GRID ── */
        #kegiatan {
            background: var(--white);
        }

        .kegiatan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .kegiatan-card {
            border-radius: 16px;
            overflow: hidden;
            background: var(--gray-100);
            position: relative;
            aspect-ratio: 4/3;
            transition: all .3s;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }

        .kegiatan-card:hover .kegiatan-overlay {
            opacity: 1;
        }

        .kegiatan-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10, 36, 99, 0.9), transparent);
            opacity: 0;
            transition: .3s;
            display: flex;
            align-items: flex-end;
            padding: 20px;
        }

        .kegiatan-overlay p {
            color: var(--white);
            font-size: 14px;
            font-weight: 700;
        }

        .kegiatan-card.large {
            grid-column: span 2;
        }

        .k1 {
            background: linear-gradient(135deg, #dbeafe, #93c5fd);
        }

        .k2 {
            background: linear-gradient(135deg, #dcfce7, #86efac);
        }

        .k3 {
            background: linear-gradient(135deg, #fef3c7, #fcd34d);
        }

        .k4 {
            background: linear-gradient(135deg, #ede9fe, #c4b5fd);
        }

        .k5 {
            background: linear-gradient(135deg, #fee2e2, #fca5a5);
        }

        /* ── PENGUMUMAN ── */
        #pengumuman {
            background: var(--off-white);
        }

        .pengumuman-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
        }

        .news-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .news-item {
            background: var(--white);
            border-radius: 16px;
            padding: 20px 24px;
            display: flex;
            gap: 18px;
            align-items: flex-start;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all .25s;
        }

        .news-item:hover {
            box-shadow: var(--shadow-md);
            transform: translateX(4px);
        }

        .news-date {
            text-align: center;
            flex-shrink: 0;
            background: var(--blue-deep);
            border-radius: 12px;
            padding: 10px 14px;
            color: var(--white);
        }

        .news-date strong {
            display: block;
            font-size: 22px;
            font-weight: 800;
        }

        .news-date span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: rgba(255, 255, 255, 0.7);
        }

        .news-text h4 {
            font-size: 15px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 6px;
        }

        .news-text p {
            font-size: 13px;
            color: var(--gray-500);
            line-height: 1.6;
        }

        .news-cat {
            display: inline-block;
            margin-top: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 100px;
        }

        .cat-smbp {
            background: rgba(29, 111, 196, 0.12);
            color: var(--blue-bright);
        }

        .cat-pkl {
            background: rgba(22, 163, 74, 0.12);
            color: #16a34a;
        }

        .cat-lms {
            background: rgba(124, 58, 237, 0.12);
            color: #7c3aed;
        }

        .cat-umum {
            background: rgba(100, 116, 139, 0.12);
            color: var(--gray-500);
        }

        .sidebar-widget {
            background: var(--white);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-bottom: 20px;
        }

        .sidebar-widget h4 {
            font-size: 15px;
            font-weight: 800;
            color: var(--blue-deep);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gray-200);
        }

        .quick-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .quick-links a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--gray-700);
            font-size: 14px;
            font-weight: 500;
            padding: 10px 12px;
            border-radius: 10px;
            transition: all .2s;
        }

        .quick-links a:hover {
            background: var(--off-white);
            color: var(--blue-bright);
        }

        .quick-links a .ql-icon {
            font-size: 18px;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .contact-row {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            font-size: 13.5px;
            color: var(--gray-700);
        }

        .contact-row span:first-child {
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── MITRA STRIP ── */
        #mitra {
            padding: 60px 24px;
            background: var(--white);
        }

        .mitra-logos {
            display: flex;
            gap: 40px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 40px;
        }

        .mitra-logo {
            height: 56px;
            background: var(--gray-100);
            border-radius: 12px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--gray-500);
            border: 1px solid var(--gray-200);
            transition: all .25s;
            white-space: nowrap;
        }

        .mitra-logo:hover {
            background: var(--blue-deep);
            color: var(--white);
            transform: translateY(-2px);
        }

        /* ── CTA BANNER ── */
        .cta-banner {
            background: linear-gradient(135deg, var(--blue-deep), var(--blue-bright));
            padding: 80px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 50%, rgba(244, 169, 19, 0.15), transparent 60%);
        }

        .cta-banner-inner {
            position: relative;
            z-index: 1;
            max-width: 680px;
            margin: 0 auto;
        }

        .cta-banner h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 4vw, 44px);
            font-weight: 900;
            color: var(--white);
            margin-bottom: 16px;
        }

        .cta-banner p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 36px;
            line-height: 1.7;
        }

        .cta-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--gray-900);
            padding: 64px 24px 24px;
            color: rgba(255, 255, 255, 0.75);
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-brand .nav-logo {
            margin-bottom: 16px;
        }

        .footer-brand p {
            font-size: 13.5px;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.55);
        }

        .footer-socials {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .social-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all .2s;
            cursor: pointer;
            text-decoration: none;
        }

        .social-btn:hover {
            background: var(--gold);
        }

        .footer-col h5 {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: var(--white);
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-col a {
            color: rgba(255, 255, 255, 0.55);
            text-decoration: none; 
            font-size: 13.5px;
            transition: color .2s;
        }

        .footer-col a:hover {
            color: var(--gold);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.35);
            flex-wrap: wrap;
            gap: 8px;
        }

        .footer-bottom a {
            color: var(--gold);
            text-decoration: none;
        }

        /* ── FLOATING WA BUTTON ── */
        .wa-float {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 999;
            background: #25d366;
            color: var(--white);
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            text-decoration: none;
            transition: all .25s;
        }

        .wa-float:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 28px rgba(37, 211, 102, 0.55);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .hero-inner {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-desc {
                max-width: 100%;
            }

            .hero-actions {
                justify-content: center;
            }

            .hero-stats {
                justify-content: center;
            }

            .hero-visual {
                display: none;
            }

            .services-grid,
            .jurusan-grid {
                grid-template-columns: 1fr;
            }

            .sambutan-inner {
                grid-template-columns: 1fr;
            }

            .sambutan-photo {
                max-width: 260px;
                margin: 0 auto;
            }

            .sambutan-badge-float {
                right: 0;
            }

            .pengumuman-grid {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 32px;
            }

            .kegiatan-grid {
                grid-template-columns: 1fr 1fr;
            }

            .kegiatan-card.large {
                grid-column: span 1;
            }

            .nav-links {
                display: none;
            }

            .hamburger {
                display: flex;
            }
        }

        @media (max-width: 480px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .kegiatan-grid {
                grid-template-columns: 1fr;
            }

            .hero-stats {
                flex-direction: column;
                gap: 12px;
                align-items: center;
            }
        }

        /* ── SCROLL REVEAL ── */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity .6s ease, transform .6s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .btn-grad {
  background-image: linear-gradient(to right, #314755 0%, #26a0da 51%, #314755 100%);
}

.btn-grad {
  margin: 10px;
  padding: 15px 45px;
  text-align: center;
  text-transform: uppercase;
  transition: 0.5s;
  background-size: 200% auto;
  background-image: linear-gradient(to right, #314755 0%, #26a0da 51%, #314755 100%);
  color: white;
  box-shadow: 0 0 20px #eee;
  border-radius: 10px;
  display: block;
  outline: none;
  border: none;
  text-decoration: none; /* hilangkan underline */
}

.btn-grad:hover {
  background-position: right center;
  color: #fff;
  text-decoration: none;
}
    </style>
</head>

<body>

    <!-- NAV -->
    <nav id="navbar">
        <div class="nav-inner">
            <a class="nav-logo" href="#">
                <div class="nav-logo-icon">Y</div>
                <div class="nav-logo-text">
                    <span>SMK Yadika Soreang</span>
                    <span>Sekolah Berkarakter & Berprestasi</span>
                </div>
            </a>
            <ul class="nav-links">
                <li><a href="#layanan">Layanan</a></li>
                <li><a href="#jurusan">Jurusan</a></li>
                <li><a href="#kegiatan">Kegiatan</a></li>
                <li><a href="#pengumuman">Pengumuman</a></li>
                <li><a href="#sambutan">Tentang Kami</a></li>
                <li><a href="/spmb" class="nav-cta">🎓 Daftar SMBP</a></li>
            </ul>
            <div class="hamburger" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero" id="home">
        <div class="hero-shapes">
            <div class="circle-1"></div>
            <div class="circle-2"></div>
        </div>
        <div class="hero-inner">
            <div>
                <div class="hero-badge">Tahun Ajaran 2025/2026</div>
                <h1 class="hero-title">Selamat Datang di<br><em>SMK Yadika</em><br>Soreang</h1>
                <p class="hero-desc">Sekolah Menengah Kejuruan unggulan di Soreang, Kabupaten Bandung — membangun
                    generasi berkarakter, kompeten, dan siap menghadapi industri global.</p>
                <div class="hero-actions">
                    <a href="/spmb" class="btn-primary">🎓 Daftar SMBP Sekarang</a>
                    <a href="/lms" class="btn-grad">💻 Masuk LMS</a>
                    <a href="#layanan" class="btn-outline">Lihat Layanan →</a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat"><strong>15+</strong><span>Tahun Berdiri</span></div>
                    <div class="hero-stat"><strong>3</strong><span>Program Keahlian</span></div>
                    <div class="hero-stat"><strong>95%</strong><span>Keterserapan Kerja</span></div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card-main">
                    <h3>Akses Layanan Digital</h3>
                    <p>Platform terintegrasi untuk siswa & orang tua</p>
                    <div class="service-pills">
                        <a class="service-pill" href="/spmb">
                            <div class="pill-icon spmb">🎓</div>
                            <div class="pill-text">
                                <strong>SPMB</strong>
                                <span>Seleksi Masuk Berbasis Prestasi</span>
                            </div>
                            <span class="pill-arrow">›</span>
                        </a>
                        <a class="service-pill" href="/pkl">
                            <div class="pill-icon pkl">🏭</div>
                            <div class="pill-text">
                                <strong>PKL / Magang</strong>
                                <span>Praktik Kerja Lapangan</span>
                            </div>
                            <span class="pill-arrow">›</span>
                        </a>
                        <a class="service-pill" href="/lms">
                            <div class="pill-icon lms">💻</div>
                            <div class="pill-text">
                                <strong>LMS</strong>
                                <span>Learning Management System</span>
                            </div>
                            <span class="pill-arrow">›</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MARQUEE -->
    <div class="marquee-strip">
        <div class="marquee-track">
            <span>SMBP 2025 Dibuka</span>
            <span>Pendaftaran PKL Semester Ganjil</span>
            <span>LMS Tersedia 24 Jam</span>
            <span>Jurusan PPLG – Akuntansi – Perhotelan</span>
            <span>Prestasi Nasional 2024</span>
            <span>SMBP 2025 Dibuka</span>
            <span>Pendaftaran PKL Semester Ganjil</span>
            <span>LMS Tersedia 24 Jam</span>
            <span>Jurusan PPLG – Akuntansi – Perhotelan</span>
            <span>Prestasi Nasional 2024</span>
        </div>
    </div>

    <!-- LAYANAN UTAMA -->
    <section id="layanan">
        <div class="section-inner">
            <div class="section-header reveal">
                <div class="section-tag">Layanan Digital</div>
                <h2 class="section-title">Tiga Layanan Unggulan<br>SMK Yadika Soreang</h2>
                <p class="section-sub">Platform digital terintegrasi untuk mendukung proses belajar, magang, dan
                    penerimaan peserta didik baru secara transparan dan mudah.</p>
            </div>
            <div class="services-grid">

                <!-- SMBP -->
                <div class="service-card sc-smbp reveal" id="smbp">
                    <div class="service-card-header">
                        <div class="service-icon-wrap">🎓</div>
                        <h3>SMBP</h3>
                        <p>Seleksi Masuk Berbasis Prestasi</p>
                    </div>
                    <div class="service-card-body">
                        <ul class="service-features">
                            <li>Pendaftaran online 24 jam tanpa antri</li>
                            <li>Seleksi transparan berbasis nilai & prestasi akademik</li>
                            <li>Notifikasi hasil seleksi real-time</li>
                            <li>Pembayaran & verifikasi dokumen digital</li>
                            <li>Jalur reguler dan jalur prestasi tersedia</li>
                        </ul>
                        <a class="btn-service" href="/spmb">Daftar SMBP Sekarang →</a>
                    </div>
                </div>

                <!-- PKL -->
                <div class="service-card sc-pkl reveal" id="pkl" style="transition-delay:.1s">
                    <div class="service-card-header">
                        <div class="service-icon-wrap">🏭</div>
                        <h3>PKL / Magang</h3>
                        <p>Praktik Kerja Lapangan</p>
                    </div>
                    <div class="service-card-body">
                        <ul class="service-features">
                            <li>Penempatan di 50+ perusahaan mitra industri</li>
                            <li>Monitoring absensi & laporan online</li>
                            <li>Jurnal PKL digital terintegrasi</li>
                            <li>Nilai & sertifikat langsung dari DU/DI</li>
                            <li>Kolaborasi dengan BBPPMPV BMTI Bandung</li>
                        </ul>
                        <a class="btn-service" href="/pkl">Ajukan PKL Sekarang →</a>
                    </div>
                </div>

                <!-- LMS -->
                <div class="service-card sc-lms reveal" id="lms" style="transition-delay:.2s">
                    <div class="service-card-header">
                        <div class="service-icon-wrap">💻</div>
                        <h3>LMS</h3>
                        <p>Learning Management System</p>
                    </div>
                    <div class="service-card-body">
                        <ul class="service-features">
                            <li>Akses materi pembelajaran kapanpun & dimanapun</li>
                            <li>Ujian & tugas online berbatas waktu</li>
                            <li>Forum diskusi interaktif guru–siswa</li>
                            <li>Rekap nilai & progres belajar real-time</li>
                            <li>Integrasi modul dari BMTI & mitra industri</li>
                        </ul>
                        <a class="btn-service" href="/lms">Masuk LMS →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JURUSAN -->
    <section id="jurusan">
        <div class="section-inner">
            <div class="section-header reveal">
                <div class="section-tag">Program Keahlian</div>
                <h2 class="section-title">Pilih Jurusan yang<br>Tepat untuk Masa Depanmu</h2>
                <p class="section-sub">Tiga program keahlian unggulan yang dirancang untuk mempersiapkan siswa menjadi
                    tenaga profesional siap kerja.</p>
            </div>
            <div class="jurusan-grid">
                <div class="jurusan-card reveal">
                    <div class="jurusan-img jc-pplg">💻</div>
                    <div class="jurusan-body">
                        <h4>Pengembangan Perangkat Lunak & Gim (PPLG)</h4>
                        <p>Belajar membangun aplikasi web, mobile, dan pengembangan game menggunakan teknologi terkini.
                            Lulusan siap bekerja sebagai developer, UI/UX designer, atau game developer.</p>
                        <span class="jurusan-tag">Teknologi Informasi</span>
                    </div>
                </div>
                <div class="jurusan-card reveal" style="transition-delay:.1s">
                    <div class="jurusan-img jc-akun">📊</div>
                    <div class="jurusan-body">
                        <h4>Akuntansi & Keuangan Lembaga</h4>
                        <p>Menguasai pencatatan keuangan, perpajakan, dan laporan akuntansi berbasis software modern.
                            Lulusan siap berkarir di perbankan, akuntan publik, dan UMKM.</p>
                        <span class="jurusan-tag">Bisnis & Manajemen</span>
                    </div>
                </div>
                <div class="jurusan-card reveal" style="transition-delay:.2s">
                    <div class="jurusan-img jc-hotel">🏨</div>
                    <div class="jurusan-body">
                        <h4>Perhotelan & Pariwisata</h4>
                        <p>Mempersiapkan siswa dengan keahlian perhotelan, tata boga, dan pelayanan wisatawan bertaraf
                            internasional. Peluang kerja luas di industri pariwisata dan hospitality.</p>
                        <span class="jurusan-tag">Pariwisata</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SAMBUTAN -->
    <section id="sambutan">
        <div class="section-inner">
            <div class="sambutan-inner">
                <div class="sambutan-img-wrap reveal">
                    <div class="sambutan-photo">
                        <div class="sambutan-photo-placeholder">
                            👩‍💼
                            <p>Yetti Nuraini, S.Pd., Gr.<br>Kepala Sekolah</p>
                        </div>
                    </div>
                    <div class="sambutan-badge-float">
                        <strong>2025</strong>
                        <span>Tahun Ajaran Baru</span>
                    </div>
                </div>
                <div class="sambutan-content reveal" style="transition-delay:.15s">
                    <div class="section-tag">Sambutan Kepala Sekolah</div>
                    <h2 class="section-title" style="color:var(--white)">Bersama Membangun<br>Generasi Unggul</h2>
                    <blockquote class="sambutan-quote">
                        "Assalammualaikum Wr. Wb. Website SMK Yadika Soreang ini dihadirkan sebagai media informasi,
                        publikasi kegiatan, dan jembatan komunikasi antara sekolah, siswa, orang tua, dan alumni. Kami
                        berharap website ini menjadi ruang inovasi serta sarana kolaborasi untuk meningkatkan kualitas
                        layanan pendidikan di sekolah kami."
                    </blockquote>
                    <div class="sambutan-name">
                        <strong>Yetti Nuraini, S.Pd., Gr.</strong>
                        <span>Kepala SMK Yadika Soreang</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KEGIATAN -->
    <section id="kegiatan">
        <div class="section-inner">
            <div class="section-header reveal">
                <div class="section-tag">Dokumentasi</div>
                <h2 class="section-title">Kegiatan Siswa<br>SMK Yadika Soreang</h2>
            </div>
            <div class="kegiatan-grid">
                <div class="kegiatan-card large k1 reveal">
                    <span>💻</span>
                    <div class="kegiatan-overlay">
                        <p>Praktik Pembuatan Aplikasi – Jurusan PPLG</p>
                    </div>
                </div>
                <div class="kegiatan-card k2 reveal" style="transition-delay:.1s">
                    <span>🏆</span>
                    <div class="kegiatan-overlay">
                        <p>Lomba IT Tingkat Kota & Provinsi</p>
                    </div>
                </div>
                <div class="kegiatan-card k3 reveal" style="transition-delay:.15s">
                    <span>🏭</span>
                    <div class="kegiatan-overlay">
                        <p>Kunjungan Industri – Program PKL</p>
                    </div>
                </div>
                <div class="kegiatan-card k4 reveal" style="transition-delay:.2s">
                    <span>📚</span>
                    <div class="kegiatan-overlay">
                        <p>Workshop Pelatihan LMS</p>
                    </div>
                </div>
                <div class="kegiatan-card k5 reveal" style="transition-delay:.25s">
                    <span>🤝</span>
                    <div class="kegiatan-overlay">
                        <p>Kegiatan Bakti Sosial</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PENGUMUMAN -->
    <section id="pengumuman">
        <div class="section-inner">
            <div class="pengumuman-grid">
                <div>
                    <div class="section-header reveal">
                        <div class="section-tag">Informasi Terkini</div>
                        <h2 class="section-title">Pengumuman &<br>Berita Sekolah</h2>
                    </div>
                    <div class="news-list">
                        <div class="news-item reveal">
                            <div class="news-date"><strong>15</strong><span>Jun</span></div>
                            <div class="news-text">
                                <h4>Pembukaan Pendaftaran SMBP Tahun Ajaran 2025/2026</h4>
                                <p>Seleksi Masuk Berbasis Prestasi resmi dibuka. Daftar online melalui portal SMBP kami
                                    dengan membawa dokumen rapor dan sertifikat prestasi.</p>
                                <span class="news-cat cat-smbp">SMBP</span>
                            </div>
                        </div>
                        <div class="news-item reveal" style="transition-delay:.1s">
                            <div class="news-date"><strong>10</strong><span>Jun</span></div>
                            <div class="news-text">
                                <h4>Penempatan PKL Semester Ganjil 2025 Telah Diumumkan</h4>
                                <p>Sebanyak 120 siswa kelas XI telah mendapatkan penempatan PKL di 45 mitra industri.
                                    Cek portal PKL untuk informasi detail lokasi dan pembimbing.</p>
                                <span class="news-cat cat-pkl">PKL</span>
                            </div>
                        </div>
                        <div class="news-item reveal" style="transition-delay:.15s">
                            <div class="news-date"><strong>05</strong><span>Jun</span></div>
                            <div class="news-text">
                                <h4>Update Modul LMS: Materi Baru PPLG & Akuntansi Tersedia</h4>
                                <p>LMS SMK Yadika Soreang kini hadir dengan modul terbaru kolaborasi BBPPMPV BMTI. Login
                                    dan mulai belajar sekarang.</p>
                                <span class="news-cat cat-lms">LMS</span>
                            </div>
                        </div>
                        <div class="news-item reveal" style="transition-delay:.2s">
                            <div class="news-date"><strong>01</strong><span>Jun</span></div>
                            <div class="news-text">
                                <h4>SMK Yadika Soreang Raih Juara 2 Lomba Web Design Tingkat Provinsi</h4>
                                <p>Tim dari jurusan PPLG berhasil meraih Juara 2 dalam ajang kompetisi web design yang
                                    diikuti oleh 120 sekolah se-Jawa Barat.</p>
                                <span class="news-cat cat-umum">Prestasi</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="sidebar-widget reveal">
                        <h4>🔗 Akses Cepat</h4>
                        <ul class="quick-links">
                            <li><a href="#smbp"><span class="ql-icon">🎓</span>Portal SMBP</a></li>
                            <li><a href="#pkl"><span class="ql-icon">🏭</span>Sistem PKL</a></li>
                            <li><a href="#lms"><span class="ql-icon">💻</span>Login LMS</a></li>
                            <li><a href="#"><span class="ql-icon">📋</span>Cek Status Pendaftar</a></li>
                            <li><a href="#"><span class="ql-icon">📄</span>Download Brosur</a></li>
                            <li><a href="#"><span class="ql-icon">🗓️</span>Kalender Akademik</a></li>
                        </ul>
                    </div>
                    <div class="sidebar-widget reveal" style="transition-delay:.1s">
                        <h4>📍 Kontak Sekolah</h4>
                        <div class="contact-info">
                            <div class="contact-row"><span>📍</span><span>Jl. Raya Soreang, Kabupaten Bandung, Jawa
                                    Barat</span></div>
                            <div class="contact-row"><span>📞</span><span>(022) 5892xxxx</span></div>
                            <div class="contact-row"><span>💬</span><span>WhatsApp: 0812-3456-789</span></div>
                            <div class="contact-row"><span>✉️</span><span>info@smkyadikasoreang.sch.id</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MITRA -->
    <section id="mitra">
        <div class="section-inner">
            <div class="section-header reveal" style="text-align:center">
                <div class="section-tag">Kemitraan</div>
                <h2 class="section-title">Mitra Industri &<br>Institusi Pendukung</h2>
            </div>
            <div class="mitra-logos reveal">
                <div class="mitra-logo">🏛️ BBPPMPV BMTI</div>
                <div class="mitra-logo">⚙️ Kemendikdasmen</div>
                <div class="mitra-logo">🏢 DUDI Jabar</div>
                <div class="mitra-logo">💼 BNI</div>
                <div class="mitra-logo">🔧 PT Astra</div>
                <div class="mitra-logo">🖥️ Telkom Indonesia</div>
                <div class="mitra-logo">🏨 Hilton Hotel</div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <div class="cta-banner" id="spmb">
        <div class="cta-banner-inner reveal">
            <h2>Bergabung bersama SMK Yadika Soreang – Wujudkan Karier Impianmu!</h2>
            <p>Daftarkan diri lewat jalur SMBP sekarang. Kuota terbatas — jangan sampai kehabisan!</p>
            <div class="cta-actions">
                <a href="/spmb" class="btn-primary">🎓 Daftar SMBP Sekarang</a>
                <a href="https://wa.me/6281234567890" class="btn-outline">💬 Hubungi via WhatsApp</a>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-inner">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="nav-logo" style="margin-bottom:16px">
                        <div class="nav-logo-icon">Y</div>
                        <div class="nav-logo-text">
                            <span>SMK Yadika Soreang</span>
                            <span>Sekolah Berkarakter & Berprestasi</span>
                        </div>
                    </div>
                    <p>Lembaga pendidikan kejuruan unggulan di Soreang, Kabupaten Bandung. Mencetak lulusan kompeten,
                        berkarakter, dan siap berkarir di dunia industri.</p>
                    <div class="footer-socials">
                        <a class="social-btn" href="#">📘</a>
                        <a class="social-btn" href="#">📸</a>
                        <a class="social-btn" href="#">🎬</a>
                        <a class="social-btn" href="#">🐦</a>
                    </div>
                </div>
                <div class="footer-col">
                    <h5>Layanan</h5>
                    <ul>
                        <li><a href="/spmb">Portal SMBP</a></li>
                        <li><a href="/pkl">Sistem PKL</a></li>
                        <li><a href="/spmb">LMS Online</a></li>
                        <li><a href="#">E-Raport</a></li>
                        <li><a href="#">Pengaduan</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h5>Jurusan</h5>
                    <ul>
                        <li><a href="#">PPLG</a></li>
                        <li><a href="#">Akuntansi</a></li>
                        <li><a href="#">Perhotelan</a></li>
                        <li><a href="#">Ekstrakurikuler</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h5>Informasi</h5>
                    <ul>
                        <li><a href="#">Profil Sekolah</a></li>
                        <li><a href="#">Kalender Akademik</a></li>
                        <li><a href="#">Berita & Kegiatan</a></li>
                        <li><a href="#">Hubungi Kami</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2025 SMK Yadika Soreang. Hak cipta dilindungi.</span>
                <span>Dikembangkan dengan 💛 — Kolaborasi bersama <a
                        href="https://bbppmpvbmti.kemendikdasmen.go.id/">BBPPMPV BMTI</a></span>
            </div>
        </div>
    </footer>

    <!-- WA Float -->
    <a class="wa-float" href="https://wa.me/6281234567890" title="Chat WhatsApp">💬</a>

    <script>
        // Scroll reveal
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });
        reveals.forEach(el => observer.observe(el));

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 60) {
                nav.style.background = 'rgba(10,36,99,0.99)';
                nav.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
            } else {
                nav.style.background = 'rgba(10,36,99,0.97)';
                nav.style.boxShadow = 'none';
            }
        });

        // Mobile menu (simple toggle)
        function toggleMenu() {
            const links = document.querySelector('.nav-links');
            if (links.style.display === 'flex') {
                links.style.display = 'none';
            } else {
                links.style.cssText =
                    'display:flex;flex-direction:column;position:absolute;top:68px;left:0;right:0;background:rgba(10,36,99,0.99);padding:16px 24px;gap:4px;';
            }
        }
    </script>
</body>

</html>
