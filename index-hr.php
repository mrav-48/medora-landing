<?php
define('RECIPIENT_EMAIL', 'reservations@medorahotels.com');
define('EMAIL_SUBJECT',   'New Proposal Request — Medora Hotels');
define('SUCCESS_MESSAGE',  'Thank you! Your request has been submitted. We\'ll be in touch within 48 hours.');
define('ERROR_MESSAGE',    'Something went wrong. Please try again or contact us directly at ' . RECIPIENT_EMAIL);
define('MAX_SUBMISSIONS',  3);
define('RATE_WINDOW',      600);
define('RATE_MESSAGE',     'You\'ve submitted too many requests. Please wait a few minutes and try again.');

session_start();

$status  = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['form_submissions'])) {
        $_SESSION['form_submissions'] = [];
    }
    $now = time();
    $_SESSION['form_submissions'] = array_filter(
        $_SESSION['form_submissions'],
        function ($ts) use ($now) { return ($now - $ts) < RATE_WINDOW; }
    );

    if (count($_SESSION['form_submissions']) >= MAX_SUBMISSIONS) {
        $status = RATE_MESSAGE;
    } elseif (!empty($_POST['website'])) {
        $status  = SUCCESS_MESSAGE;
        $success = true;
    } else {
        $name         = htmlspecialchars(trim($_POST['name'] ?? ''));
        $organisation = htmlspecialchars(trim($_POST['organisation'] ?? ''));
        $email        = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $program_type = htmlspecialchars(trim($_POST['program_type'] ?? ''));
        $group_size   = htmlspecialchars(trim($_POST['group_size'] ?? ''));
        $dates        = htmlspecialchars(trim($_POST['preferred_dates'] ?? ''));
        $details      = htmlspecialchars(trim($_POST['program_details'] ?? ''));

        $errors = [];
        if ($name === '')                               $errors[] = 'Name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
        if ($program_type === '')                        $errors[] = 'Please select a program type.';

        if (empty($errors)) {
            $body  = "NEW PROPOSAL REQUEST\n";
            $body .= str_repeat('-', 40) . "\n\n";
            $body .= "Name:            {$name}\n";
            $body .= "Organisation:    {$organisation}\n";
            $body .= "Email:           {$email}\n";
            $body .= "Program Type:    {$program_type}\n";
            $body .= "Group Size:      {$group_size}\n";
            $body .= "Preferred Dates: {$dates}\n\n";
            $body .= "Program Details:\n{$details}\n";

            $headers  = "From: {$name} <{$email}>\r\n";
            $headers .= "Reply-To: {$email}\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            if (mail(RECIPIENT_EMAIL, EMAIL_SUBJECT, $body, $headers)) {
                $_SESSION['form_submissions'][] = time();
                $status  = SUCCESS_MESSAGE;
                $success = true;
            } else {
                $status = ERROR_MESSAGE;
            }
        } else {
            $status = implode(' ', $errors);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medora — Retreati, kampovi i grupni boravci</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;1,400;1,500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- NAV -->
<nav>
  <a href="#" class="nav-logo">Medora <span>Hotels & Resorts</span></a>
  <ul class="nav-links">
    <li><a href="#properties">Objekti</a></li>
    <li><a href="#programs">Programi</a></li>
    <li><a href="#location">Lokacija</a></li>
    <li><a href="#why">Zašto Medora</a></li>
    <li><a href="#contact" class="nav-cta">Zatražite ponudu</a></li>
  </ul>
  <a href="#contact" class="nav-mobile-cta" data-en="Get a Proposal" data-hr="Zatražite ponudu">Zatražite ponudu</a>
  <a href="index.html" class="lang-toggle">EN</a>
</nav>

<!-- HERO -->
<div class="hero">
  <div class="hero-bg">
    <!-- Desktop: split side by side -->
    <div class="hero-split-desktop">
      <div style="overflow:hidden;position:relative;">
        <img src="HOTEL.jpg" alt="Medora Auri Hotel" class="hero-img" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:0.65;">
      </div>
      <div style="overflow:hidden;position:relative;">
        <img src="aerial camp.jpg" alt="Medora Orbis Camp" class="hero-img" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:0.65;">
      </div>
    </div>
    <!-- Mobile: crossfade -->
    <div class="hero-fade-mobile">
      <img src="HOTEL.jpg" alt="Medora Auri Hotel" class="hero-fade-img active">
      <img src="aerial camp.jpg" alt="Medora Orbis Camp" class="hero-fade-img">
    </div>
    <div class="hero-overlay"></div>
  </div>
  <div class="hero-content">
    <p class="hero-eyebrow">Makarška rivijera · Dalmacija · Hrvatska</p>
    <h1>Organizirajte svoj program<br>na <em>Makarskoj rivijeri.</em></h1>
    <p class="hero-sub">A venue for unforgettable stays, retreats and group experiences.<br><br><strong>Medora Auri Hotel & Medora Orbis Camp</strong><br>A beachfront hotel and premium campsite in Podgora.</p>
    <div class="hero-actions">
      <a href="#contact" class="btn-primary">Zatražite ponudu</a>
      <a href="#properties" class="btn-outline">Pogledajte objekte</a>
    </div>
  </div>
</div>

<!-- INTRO STRIP -->
<div class="intro-strip">
  <div class="intro-item">
    <div class="intro-label">Smještaj</div>
    <p class="intro-text">Hotelske sobe, mobilne kućice ili kombinacija. Fleksibilni rasporedi za svaku veličinu i format grupe.</p>
  </div>
  <div class="intro-item">
    <div class="intro-label">Food & Beverage</div>
    <p class="intro-text">Pouzdano ugostiteljstvo od dnevnog polupansiona do potpuno prilagođenih grupnih menija. Bez koordinacije s vaše strane.</p>
  </div>
  <div class="intro-item">
    <div class="intro-label">Spaces & Logistics</div>
    <p class="intro-text">Unutarnji i vanjski prostori spremni za korištenje. Aktivnosti, transferi i organizacijska podrška dostupni su putem nas.</p>
  </div>
</div>

<!-- PROPERTIES -->
<div id="properties"></div>
<div style="background: var(--white);">
  <div class="section-full-inner" style="padding-top: 6rem; padding-bottom: 2rem;">
    <p class="section-tag">Objekti</p>
    <h2>Dva objekta,<br>jedna organizacija.</h2>
    <p style="font-size:0.85rem; letter-spacing:0.08em; color:var(--ink-light); margin-bottom:1rem; text-transform:uppercase;">Medora Auri Hotel i Medora Orbis Camp</p>
    <p class="lead">Medora Auri Hotel i Medora Orbis Camp nalaze se jedan pored drugog u Podgori, na Makarskoj rivijeri. Koristite jedan, drugi ili oba — ovisno o potrebama vašeg programa.</p>
  </div>

  <!-- Mobile swipe tabs -->
  <div class="venue-tabs">
    <button class="venue-tab active" data-index="0">Medora Auri Hotel</button>
    <button class="venue-tab" data-index="1">Medora Orbis Camp</button>
  </div>

  <div class="properties-grid" id="propertiesGrid">
    <!-- Hotel -->
    <div class="property-card venue-slide active">
      <div class="property-img">
        <img src="Medora Auri Pool & Beach 11.jpg" alt="Medora Auri Hotel pool and beachfront">
      </div>
      <div class="property-info">
        <div class="property-stars">4★ Hotel uz more</div>
        <h3>Medora Auri Hotel</h3>
        <p class="property-desc">Moderan hotel neposredno uz more. Pogodan za wellness retreate, korporativne evente, brand boravke i grupe koje žele strukturirano hotelsko iskustvo.</p>
        <ul class="amenities">
          <li>Restoran — grupne večere i eventi</li>
          <li>Vanjski grijani bazeni</li>
          <li>Spa & wellness facilities</li>
          <li>Fitness centar s pogledom na more</li>
          <li>Yoga & activity spaces</li>
          <li>Konferencijske i meeting sobe</li>
        </ul>
      </div>
    </div>
    <!-- Camp -->
    <div class="property-card venue-slide">
      <div class="property-img">
        <img src="aerial camp.jpg" alt="Medora Orbis Camping aerial view">
      </div>
      <div class="property-info">
        <div class="property-stars">Premium kamp</div>
        <h3>Medora Orbis Camping</h3>
        <p class="property-desc">Deluxe mobilne kućice i kamp parcele, neke s privatnim grijanim bazenima. Opuštenija, outdoor baza, idealna za sportske kampove, aktivne retreate i grupe koje preferiraju prirodno okruženje.</p>
        <ul class="amenities">
          <li>Deluxe mobile homes & camping pitches</li>
          <li>Privatni grijani bazeni na odabranim mobilnim kućicama</li>
          <li>Outdoor terraces & BBQ areas</li>
          <li>Bicycles & active-use spaces</li>
          <li>Društvene vanjske zone</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- GALLERY -->
<div class="gallery-section" id="gallery">
  <div class="gallery-header">
    <p class="section-tag">Pogledajte</p>
    <h2>The spaces & facilities</h2>
  </div>
  <div class="gallery-grid">
    <div class="gallery-item wide">
      <img src="Medora Auri night photo pool.jpg" alt="Medora Auri hotel pool at night">
    </div>
    <div class="gallery-item">
      <img src="Medora Auri Wellness 4.jpg" alt="Medora Auri wellness centre">
    </div>
    <div class="gallery-item">
      <img src="Medora Auri Wellness 7.jpg" alt="Medora Auri spa facilities">
    </div>
    <div class="gallery-item">
      <img src="Medora Auri hotel fitness.jpg" alt="Sea-view fitness centre">
    </div>
    <div class="gallery-item">
      <img src="lobby bar.jpg" alt="Hotel lobby bar">
    </div>
    <div class="gallery-item wide">
      <img src="F&B.jpg" alt="Food and beverage options">
    </div>
    <div class="gallery-item">
      <img src="F&B_.jpg" alt="Group dining">
    </div>
    <div class="gallery-item">
      <img src="f&Bb.jpg" alt="Catering setup">
    </div>
    <div class="gallery-item">
      <img src="outside active.jpg" alt="Outdoor active spaces">
    </div>
    <div class="gallery-item">
      <img src="Pool.jpg" alt="Pool area at campsite">
    </div>
    <div class="gallery-item wide">
      <img src="plaža.jpg" alt="Beach access">
    </div>
    <div class="gallery-item">
      <img src="camp2.jpg" alt="Campsite facilities">
    </div>
    <div class="gallery-item">
      <img src="camp3.jpg" alt="Campsite outdoor zone">
    </div>
  </div>
</div>

<!-- PROGRAMS -->
<div style="background: var(--white);">
  <section id="programs">
    <p class="section-tag">Što nudimo</p>
    <h2>Programi koje organiziramo.</h2>
    <p class="lead">Surađujemo s organizatorima, brendovima i timovima kojima je potreban prostor koji se brine o infrastrukturi — kako bi se mogli fokusirati na program.</p>
    <div class="programs-grid">
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title">Creator & Brand Partnerships</div>
        <p class="program-desc">Organizirani boravci, produkcija sadržaja i brand aktivacije. Lokacija funkcionira vizualno i logistički: more, planine, vanjsko svjetlo i sadržaji koji dobro izgledaju na fotografijama.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title">Wellness & Lifestyle Retreats</div>
        <p class="program-desc">Yoga, fitness i retreati oporavka s prostorima spremnim za korištenje, strukturiranim obrocima i mirnim, prirodnim okruženjem izvan ljetnih gužvi.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title">Active & Sports Camps</div>
        <p class="program-desc">Nogometni kampovi, plesne grupe, fitness timovi i programi za mlade. Fleksibilan smještaj, vanjske zone za trening i puna F&B podrška.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title">Corporate & Team Retreats</div>
        <p class="program-desc">Offsiteovi, radionice i strateške sesije. Sobe za sastanke, catering i smještaj na jednom mjestu — bez koordinacije više dobavljača.</p>
      </div>
    </div>
  </section>
</div>

<!-- REVIEWS -->
<div class="reviews-section">
  <div class="reviews-inner">
    <p class="section-tag">Ocjene gostiju</p>
    <h2>Dosljedno vrhunski ocijenjeni<br>na Rivijeri.</h2>
    <p class="lead">Ocijenjeni među najboljim hotelima i kampovima na Makarskoj rivijeri na Googleu i Booking.com-u.</p>
    <div class="reviews-grid">
      <div class="review-property-block">
        <div class="review-property-name">Medora Auri Hotel</div>
        <div class="review-pair">
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Booking.com</div></div>
            <div class="review-score">9.2 <span>/ 10</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">Izvrsno · 3.016 recenzija</div>
          </div>
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Google</div></div>
            <div class="review-score">4.7 <span>/ 5</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">★★★★★ · 3.664 recenzije</div>
          </div>
        </div>
      </div>
      <div class="review-property-block">
        <div class="review-property-name">Medora Orbis Camp</div>
        <div class="review-pair">
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Booking.com</div></div>
            <div class="review-score">9.6 <span>/ 10</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">Izvrsno · 367 recenzija</div>
          </div>
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Google</div></div>
            <div class="review-score">4.7 <span>/ 5</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">★★★★★ · 1.175 recenzija</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- LOCATION -->
<section id="location">
  <p class="section-tag">Kako doći</p>
  <h2>Podgora, Makarška rivijera,<br>Dalmacija, Hrvatska.</h2>
  <p class="lead">Između planine Biokovo i Jadranskog mora, 10 minuta od Makarske. Jednostavan dolazak iz Splitske zračne luke i dobra povezanost s glavnim europskim gradovima.</p>
  <div class="location-grid">
    <div class="location-map">
      <img src="MAPA.png" alt="Map showing Medora location on the Makarska Riviera">
    </div>
    <div class="location-details">
      <div class="location-block">
        <h4>Kako doći</h4>
        <ul class="route-list">
          <li>Split Airport (SPU) <span>105 km · ~1h 30 min</span></li>
          <li>Dubrovnik Airport (DBV) <span>170 km · ~2h 30 min</span></li>
          <li>Highway exit Zagvozd (A1) <span>20 km · ~25 min</span></li>
        </ul>
      </div>
      <div class="location-block">
        <h4>Transferi</h4>
        <ul class="transfer-list">
          <li>Transferi s aerodroma (Split ↔ Podgora, Dubrovnik ↔ Podgora)</li>
          <li>Grupni minibus (8–20 putnika)</li>
          <li>Private day trips (Makarska, Split, Dubrovnik & islands)</li>
        </ul>
      </div>
      <div class="location-block">
        <h4>U blizini</h4>
        <ul class="nearby-list">
          <li>Makarska <span>10 min</span></li>
          <li>Split <span>1h 30 min</span></li>
          <li>Dubrovnik <span>2h 30 min</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ATTRACTIONS -->
<div class="attractions-section">
  <div class="attractions-inner">
    <p class="section-tag">Regija</p>
    <h2>Okolica.</h2>
    <p class="lead" style="margin-bottom: 0;">Makarška rivijera smještena je na raskrižju planina, mora i otoka. Izleti značajno obogaćuju ukupni doživljaj programa.</p>
    <div class="attractions-grid">
      <div class="attraction-card">
        <img src="Biokovo 1.jpg" alt="Biokovo mountain">
        <div class="attraction-label">
          <span class="attraction-name">Planina Biokovo</span>
          <span class="attraction-time">15–20 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="skywalk.jpg" alt="Biokovo Skywalk viewpoint">
        <div class="attraction-label">
          <span class="attraction-name">Vidikovac Skywalk</span>
          <span class="attraction-time">20 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Cetina 1.jpg" alt="Cetina river canyon">
        <div class="attraction-label">
          <span class="attraction-name">Kanjon rijeke Cetine</span>
          <span class="attraction-time">40 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Bacinska jezera.jpg" alt="Baćina Lakes">
        <div class="attraction-label">
          <span class="attraction-name">Baćina Lakes</span>
          <span class="attraction-time">5–10 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Crveno Jezero.jpg" alt="Crveno Jezero, Imotski">
        <div class="attraction-label">
          <span class="attraction-name">Crveno Jezero</span>
          <span class="attraction-time">1.5 hrs · Imotski</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Modro Jezero 1.jpg" alt="Modro Jezero, Imotski">
        <div class="attraction-label">
          <span class="attraction-name">Modro Jezero</span>
          <span class="attraction-time">1.5 hrs · Imotski</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Dubrovnik.jpg" alt="Dubrovnik Old Town">
        <div class="attraction-label">
          <span class="attraction-name">Dubrovnik</span>
          <span class="attraction-time">2 hrs 20 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Wine and olive oil tasting from local producers.jpg" alt="Local wine and olive oil tasting">
        <div class="attraction-label">
          <span class="attraction-name">Local Wine & Olive Oil</span>
          <span class="attraction-time">Lokalni proizvođači</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- WHY -->
<section id="why">
  <p class="section-tag">Zašto Medora</p>
  <h2>Zašto organizatori<br>biraju Medoru.</h2>
  <p class="lead">Glavni razlozi zbog kojih se organizatori, brendovi i timovi vraćaju.</p>
  <div class="why-grid">
    <div class="why-card">
      <div class="why-number">01</div>
      <div class="why-title">Flexible & Scalable</div>
      <p class="why-desc">Konkurentne cijene u predsezoni i postsezoni (travanj–lipanj i rujan–listopad), s paketima prilagođenim veličini grupe, formatu i budžetu.</p>
    </div>
    <div class="why-card">
      <div class="why-number">02</div>
      <div class="why-title">Sve je već na mjestu</div>
      <p class="why-desc">Spa, wellness, fitness, zdravi meniji, vanjske aktivnosti i prostori za sastanke. Sva infrastruktura potrebna za kompletan program, bez dodatne koordinacije.</p>
    </div>
    <div class="why-card">
      <div class="why-number">03</div>
      <div class="why-title">Iskustvo s velikim grupama</div>
      <p class="why-desc">Iskustvo u radu s velikim kapacitetima, uz organizirani operativni tim koji zna kako efikasno voditi grupne programe.</p>
    </div>
    <div class="why-card">
      <div class="why-number">04</div>
      <div class="why-title">Prepoznata kvaliteta</div>
      <p class="why-desc">Vrhunski ocijenjeni na svim glavnim OTA platformama sa snažnom i stabilnom prisutnošću na regionalnom tržištu. Vaši sudionici dolaze s pozitivnim očekivanjima.</p>
    </div>
    <div class="why-card">
      <div class="why-number">05</div>
      <div class="why-title">Dugoročno partnerstvo</div>
      <p class="why-desc">Fokusiramo se na izgradnju dugoročnih odnosa s preferiranim partnerima: posvećena podrška, prioritetna dostupnost i bolje cijene za ponovljene programe.</p>
    </div>
    <div class="why-card">
      <div class="why-number">06</div>
      <div class="why-title">Jednostavno od početka do kraja</div>
      <p class="why-desc">Jedna kontakt osoba, jasna komunikacija i prilagođena ponuda u roku 48 sati. Bez pregovaranja s više dobavljača.</p>
    </div>
  </div>
</section>


<!-- CONTACT -->
<div class="contact-section" id="contact">
  <div class="contact-inner">
    <div class="contact-info">
      <p class="section-tag">Kontaktirajte nas</p>
      <h2>Izgradimo pravi paket za vašu grupu.</h2>
      <p class="lead" style="margin-bottom: 2rem;">Koristite ovaj obrazac za planiranje retreata, brand suradnje, sportske kampove i korporativne evente. Odgovorit ćemo prilagođenom ponudom u roku 48 sati.</p>
      <ul class="contact-list">
        <li>Odgovorit ćemo prilagođenom ponudom u roku 48 sati</li>
        <li>Puna podrška u organizaciji programa: vi vodite, ili vas povezujemo s provjerenom agencijom</li>
        <li>Smještaj u hotelu i kampu, prilagođen veličini i konceptu vaše grupe</li>
        <li>Sva logistika na jednom mjestu: smještaj, obroci, prostori, aktivnosti</li>
        <li>Dodatne usluge dostupne na upit: transferi, izleti, posebne večere</li>
      </ul>
    </div>
    <div>
      <?php if ($status): ?>
        <div class="form-status-msg <?= $success ? 'form-status-success' : 'form-status-error' ?>">
          <?= htmlspecialchars($status) ?>
        </div>
      <?php endif; ?>
      <?php if (!$success): ?>
      <form class="contact-form" method="POST" action="#contact" novalidate>
        <div style="position:absolute;left:-9999px;opacity:0;height:0;width:0;overflow:hidden;">
          <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="name">Ime</label>
            <input type="text" id="name" name="name" placeholder="Vaše ime" required>
          </div>
          <div class="form-group">
            <label for="organisation">Organizacija</label>
            <input type="text" id="organisation" name="organisation" placeholder="Tvrtka ili agencija">
          </div>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="vas@email.com" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="program_type">Vrsta programa</label>
            <select id="program_type" name="program_type" required>
              <option value="">Odaberite vrstu</option>
              <option value="Kreator / Brand boravak">Kreator / Brand boravak</option>
              <option value="Wellness / Yoga retreat">Wellness / Yoga retreat</option>
              <option value="Aktivni / Sportski kamp">Aktivni / Sportski kamp</option>
              <option value="Korporativni eventi">Korporativni eventi</option>
              <option value="Other">Ostalo</option>
            </select>
          </div>
          <div class="form-group">
            <label for="group_size">Veličina grupe</label>
            <input type="text" id="group_size" name="group_size" placeholder="Otprilike broj osoba">
          </div>
        </div>
        <div class="form-group">
          <label for="preferred_dates">Željeni termini</label>
          <input type="text" id="preferred_dates" name="preferred_dates" placeholder="npr. travanj\u2013May 2025, flexible">
        </div>
        <div class="form-group">
          <label for="program_details">Detalji programa</label>
          <textarea id="program_details" name="program_details" placeholder="Brief description of what you&#39;re planning. We&#39;ll take it from there."></textarea>
        </div>
        <button type="submit" class="form-submit">Zatražite ponudu</button>
        <p class="form-consent">Slanjem ovog obrasca pristajete da vas Medora Hotels &amp; Resorts kontaktira u vezi vašeg upita. Vaši podaci neće biti dijeljeni s trećim stranama.</p>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">Medora Hotels & Resorts · Podgora, Makarska Riviera · Croatia</div>
  <div class="footer-copy">Partnership & Group Enquiries</div>
</footer>

<script src="main.js"></script>
</body>
</html>
