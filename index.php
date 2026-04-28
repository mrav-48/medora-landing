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
<title>Medora — Retreats, Camps & Brand Stays</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;1,400;1,500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- NAV -->
<nav>
  <a href="#" class="nav-logo">Medora <span>Hotels & Resorts</span></a>
  <ul class="nav-links">
    <li><a href="#properties">Properties</a></li>
    <li><a href="#programs">Programs</a></li>
    <li><a href="#location">Location</a></li>
    <li><a href="#why">Why Medora</a></li>
    <li><a href="#contact" class="nav-cta">Get a Proposal</a></li>
  </ul>
  <a href="#contact" class="nav-mobile-cta">Get a Proposal</a>
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
    <p class="hero-eyebrow">Makarska Riviera · Dalmatia · Croatia</p>
    <h1>Host your program<br>on the <em>Makarska Riviera.</em></h1>
    <p class="hero-sub">A venue for retreats, brand stays and group programs.<br><br><strong>Medora Auri Hotel & Medora Orbis Camp</strong><br>A beachfront hotel and premium campsite in Podgora.</p>
    <div class="hero-actions">
      <a href="#contact" class="btn-primary">Get a Proposal</a>
      <a href="#properties" class="btn-outline">Explore the venues</a>
    </div>
  </div>
</div>

<!-- INTRO STRIP -->
<div class="intro-strip">
  <div class="intro-item">
    <div class="intro-label">Accommodation</div>
    <p class="intro-text">Hotel rooms, mobile homes, or a combination. Flexible setups for any group size and format.</p>
  </div>
  <div class="intro-item">
    <div class="intro-label">Food & Beverage</div>
    <p class="intro-text">Reliable catering from daily half board to fully tailored group menus. No coordination required on your end.</p>
  </div>
  <div class="intro-item">
    <div class="intro-label">Spaces & Logistics</div>
    <p class="intro-text">Indoor and outdoor areas ready for use. Activities, transfers and event support all available through us.</p>
  </div>
</div>

<!-- PROPERTIES -->
<div id="properties"></div>
<div style="background: var(--white);">
  <div class="section-full-inner" style="padding-top: 6rem; padding-bottom: 2rem;">
    <p class="section-tag">The Venues</p>
    <h2>Two properties,<br>one organisation.</h2>
    <p style="font-size:0.85rem; letter-spacing:0.08em; color:var(--ink-light); margin-bottom:1rem; text-transform:uppercase;">Medora Auri Hotel and Medora Orbis Camp</p>
    <p class="lead">Medora Auri Hotel and Medora Orbis Camp sit side by side in Podgora, on the Makarska Riviera. Use one, the other, or both — depending on what your program needs.</p>
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
        <div class="property-stars">4★ Beachfront Hotel</div>
        <h3>Medora Auri Hotel</h3>
        <p class="property-desc">A modern hotel directly on the sea. Suitable for wellness retreats, corporate offsites, brand stays and groups that want a more structured, hotel-grade experience.</p>
        <ul class="amenities">
          <li>Restaurant — group dining and events</li>
          <li>Outdoor heated pools</li>
          <li>Spa & wellness facilities</li>
          <li>Sea-view fitness centre</li>
          <li>Yoga & activity spaces</li>
          <li>Conference and meeting rooms</li>
        </ul>
      </div>
    </div>
    <!-- Camp -->
    <div class="property-card venue-slide">
      <div class="property-img">
        <img src="aerial camp.jpg" alt="Medora Orbis Camping aerial view">
      </div>
      <div class="property-info">
        <div class="property-stars">Premium Camping</div>
        <h3>Medora Orbis Camping</h3>
        <p class="property-desc">Deluxe mobile homes and camping pitches, some units with private heated pools. A more relaxed, outdoor-oriented base, well suited to sports camps, active retreats and groups that prefer a natural setting.</p>
        <ul class="amenities">
          <li>Deluxe mobile homes & camping pitches</li>
          <li>Private heated pools on selected mobile homes</li>
          <li>Outdoor terraces & BBQ areas</li>
          <li>Bicycles & active-use spaces</li>
          <li>Social outdoor zones</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- GALLERY -->
<div class="gallery-section" id="gallery">
  <div class="gallery-header">
    <p class="section-tag">A Look Around</p>
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
    <p class="section-tag">What We Host</p>
    <h2>Programs we host.</h2>
    <p class="lead">We work with organizers, brands and teams that need a venue that handles the infrastructure, so they can focus on their program.</p>
    <div class="programs-grid">
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title">Creator & Brand Partnerships</div>
        <p class="program-desc">Hosted stays, content production and brand activations. The location works visually and logistically: sea, mountains, outdoor light and facilities that photograph well.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title">Wellness & Lifestyle Retreats</div>
        <p class="program-desc">Yoga, fitness and recovery retreats with ready-to-use spaces, structured meal options and a calm, natural setting away from the summer crowds.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title">Active & Sports Camps</div>
        <p class="program-desc">Football camps, dance groups, fitness teams and youth programs. Flexible accommodation, outdoor training areas and full F&B support built in.</p>
      </div>
      <div class="program-card">
        <div class="program-icon"></div>
        <div class="program-title">Corporate & Team Retreats</div>
        <p class="program-desc">Offsites, workshops and strategy sessions. Meeting rooms, catering and accommodation in one place, no need to coordinate multiple suppliers.</p>
      </div>
    </div>
  </section>
</div>

<!-- REVIEWS -->
<div class="reviews-section">
  <div class="reviews-inner">
    <p class="section-tag">Guest Ratings</p>
    <h2>Consistently top-rated<br>on the Riviera.</h2>
    <p class="lead">Rated among the best hotels and campsites on the Makarska Riviera across Google and Booking.com.</p>
    <div class="reviews-grid">
      <div class="review-property-block">
        <div class="review-property-name">Medora Auri Hotel</div>
        <div class="review-pair">
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Booking.com</div></div>
            <div class="review-score">9.2 <span>/ 10</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">Superb</div>
          </div>
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Google</div></div>
            <div class="review-score">4.6 <span>/ 5</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">★★★★★</div>
          </div>
        </div>
      </div>
      <div class="review-property-block">
        <div class="review-property-name">Medora Orbis Camp</div>
        <div class="review-pair">
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Booking.com</div></div>
            <div class="review-score">9.4 <span>/ 10</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">Superb</div>
          </div>
          <div class="review-card">
            <div class="review-source-row"><div class="review-source-dot"></div><div class="review-source">Google</div></div>
            <div class="review-score">4.6 <span>/ 5</span></div>
            <div class="review-divider"></div>
            <div class="review-stars">★★★★★</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- LOCATION -->
<section id="location">
  <p class="section-tag">Getting Here</p>
  <h2>Podgora, Makarska Riviera,<br>Dalmatia, Croatia.</h2>
  <p class="lead">Between the Biokovo mountains and the Adriatic, 10 minutes from Makarska town. Straightforward to reach from Split Airport and well connected to major EU cities.</p>
  <div class="location-grid">
    <div class="location-map">
      <img src="MAPA.png" alt="Map showing Medora location on the Makarska Riviera">
    </div>
    <div class="location-details">
      <div class="location-block">
        <h4>Getting Here</h4>
        <ul class="route-list">
          <li>Split Airport (SPU) <span>105 km · ~1h 30 min</span></li>
          <li>Dubrovnik Airport (DBV) <span>170 km · ~2h 30 min</span></li>
          <li>Highway exit Zagvozd (A1) <span>20 km · ~25 min</span></li>
        </ul>
      </div>
      <div class="location-block">
        <h4>Transfer Services</h4>
        <ul class="transfer-list">
          <li>Airport transfers (Split ↔ Podgora, Dubrovnik ↔ Podgora)</li>
          <li>Group minibus (8–20 passengers)</li>
          <li>Private day trips (Makarska, Split, Dubrovnik & islands)</li>
        </ul>
      </div>
      <div class="location-block">
        <h4>Nearby</h4>
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
    <p class="section-tag">The Region</p>
    <h2>The surrounding region.</h2>
    <p class="lead" style="margin-bottom: 0;">The Makarska Riviera sits at a crossroads of mountains, coast and islands. Day trips make a real difference to the overall program experience.</p>
    <div class="attractions-grid">
      <div class="attraction-card">
        <img src="Biokovo 1.jpg" alt="Biokovo mountain">
        <div class="attraction-label">
          <span class="attraction-name">Biokovo Mountain</span>
          <span class="attraction-time">15–20 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="skywalk.jpg" alt="Biokovo Skywalk viewpoint">
        <div class="attraction-label">
          <span class="attraction-name">Skywalk Viewpoint</span>
          <span class="attraction-time">20 min</span>
        </div>
      </div>
      <div class="attraction-card">
        <img src="Cetina 1.jpg" alt="Cetina river canyon">
        <div class="attraction-label">
          <span class="attraction-name">Cetina River Canyon</span>
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
          <span class="attraction-time">Regional producers</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- WHY -->
<section id="why">
  <p class="section-tag">Why Medora</p>
  <h2>Why organizers<br>choose Medora.</h2>
  <p class="lead">The main reasons organizers, brands and teams come back.</p>
  <div class="why-grid">
    <div class="why-card">
      <div class="why-number">01</div>
      <div class="why-title">Flexible & Scalable</div>
      <p class="why-desc">Competitive pricing in shoulder season (April–June and September–October), with packages adapted to your group size, format and budget.</p>
    </div>
    <div class="why-card">
      <div class="why-number">02</div>
      <div class="why-title">Everything Already in Place</div>
      <p class="why-desc">Spa, wellness, fitness, healthy menus, outdoor activities and meeting spaces. All the infrastructure needed for a complete program, without extra coordination.</p>
    </div>
    <div class="why-card">
      <div class="why-number">03</div>
      <div class="why-title">Proven for Large Groups</div>
      <p class="why-desc">Experience handling large capacities, backed by an organised operational team that knows how to run group programs efficiently.</p>
    </div>
    <div class="why-card">
      <div class="why-number">04</div>
      <div class="why-title">Recognised Quality</div>
      <p class="why-desc">Top-rated across all major OTA platforms with a strong, stable presence on the regional market. Your participants arrive with positive expectations.</p>
    </div>
    <div class="why-card">
      <div class="why-number">05</div>
      <div class="why-title">Long-Term Partnership</div>
      <p class="why-desc">We focus on building ongoing relationships with preferred partners: dedicated support, priority availability and better rates for repeat programs.</p>
    </div>
    <div class="why-card">
      <div class="why-number">06</div>
      <div class="why-title">Simple from Start to Finish</div>
      <p class="why-desc">One point of contact, clear communication and a tailored proposal within 48 hours. No back-and-forth with multiple suppliers.</p>
    </div>
  </div>
</section>


<!-- CONTACT -->
<div class="contact-section" id="contact">
  <div class="contact-inner">
    <div class="contact-info">
      <p class="section-tag">Get in Touch</p>
      <h2>Let's build the right package for your group.</h2>
      <p class="lead" style="margin-bottom: 2rem;">Use this form for retreat planning, brand collaborations, sports camps and corporate offsites. We'll reply with a tailored proposal within 48 hours.</p>
      <ul class="contact-list">
        <li>We'll get back to you with a tailored proposal within 48 hours</li>
        <li>Full support in organizing your program: you lead, or we connect you with a trusted agency</li>
        <li>Accommodation across hotel and campsite, adapted to your group size and concept</li>
        <li>All logistics handled in one place: stay, meals, spaces, activities</li>
        <li>Additional services available on request: transfers, excursions, special dining</li>
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

        <!-- Honeypot -->
        <div style="position:absolute;left:-9999px;opacity:0;height:0;width:0;overflow:hidden;">
          <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Your name" required
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="organisation">Organisation</label>
            <input type="text" id="organisation" name="organisation" placeholder="Company or agency"
                   value="<?= htmlspecialchars($_POST['organisation'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="your@email.com" required
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="program_type">Program Type</label>
            <select id="program_type" name="program_type" required>
              <option value="">Select a type</option>
              <option value="Creator / Brand Stay" <?= ($_POST['program_type'] ?? '') === 'Creator / Brand Stay' ? 'selected' : '' ?>>Creator / Brand Stay</option>
              <option value="Wellness / Yoga Retreat" <?= ($_POST['program_type'] ?? '') === 'Wellness / Yoga Retreat' ? 'selected' : '' ?>>Wellness / Yoga Retreat</option>
              <option value="Active / Sports Camp" <?= ($_POST['program_type'] ?? '') === 'Active / Sports Camp' ? 'selected' : '' ?>>Active / Sports Camp</option>
              <option value="Corporate Events" <?= ($_POST['program_type'] ?? '') === 'Corporate Events' ? 'selected' : '' ?>>Corporate Events</option>
              <option value="Other" <?= ($_POST['program_type'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="group_size">Group Size</label>
            <input type="text" id="group_size" name="group_size" placeholder="Approx. number of people"
                   value="<?= htmlspecialchars($_POST['group_size'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="preferred_dates">Preferred Dates</label>
          <input type="text" id="preferred_dates" name="preferred_dates"
                 placeholder="e.g. April–May 2025, flexible"
                 value="<?= htmlspecialchars($_POST['preferred_dates'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="program_details">Program Details</label>
          <textarea id="program_details" name="program_details"
                    placeholder="Brief description of what you're planning. We'll take it from there."><?= htmlspecialchars($_POST['program_details'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="form-submit">Request a Proposal</button>
        <p class="form-consent">By submitting this form, you agree to be contacted by Medora Hotels & Resorts regarding your enquiry. Your information will not be shared with third parties.</p>
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
