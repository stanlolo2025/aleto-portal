<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aleto Clan Community Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --primary: #1a5276; --secondary: #27ae60; --accent: #f39c12; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; }

        /* Navbar */
        .navbar-custom { background: var(--primary); padding: 12px 0; }
        .navbar-custom .navbar-brand { font-weight: 700; font-size: 1.4rem; }
        .navbar-custom .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 500; }
        .navbar-custom .nav-link:hover { color: #fff !important; }

        /* Hero Slider */
        .hero-section { position: relative; height: 85vh; min-height: 500px; overflow: hidden; }
        .hero-slide { position: absolute; inset: 0; opacity: 0; transition: opacity 1.5s ease; background-size: cover; background-position: center; }
        .hero-slide.active { opacity: 1; }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(26,82,118,0.85), rgba(39,174,96,0.7)); }
        .hero-content { position: relative; z-index: 2; height: 100%; display: flex; align-items: center; }
        .hero-content h1 { font-size: 3.2rem; font-weight: 800; text-shadow: 2px 2px 8px rgba(0,0,0,0.3); }
        .hero-content p { font-size: 1.3rem; opacity: 0.95; }
        .hero-indicators { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 3; }
        .hero-indicators span { display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.5); margin: 0 5px; cursor: pointer; transition: 0.3s; }
        .hero-indicators span.active { background: #fff; transform: scale(1.3); }

        /* Sections */
        .section-title { font-weight: 700; color: var(--primary); position: relative; padding-bottom: 10px; }
        .section-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 4px; background: var(--secondary); border-radius: 2px; }
        .section-title.text-center::after { left: 50%; transform: translateX(-50%); }

        /* Feature Cards */
        .feature-card { border: none; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; height: 100%; }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
        .feature-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 15px; }

        /* Stats */
        .stat-card { background: var(--primary); color: white; border-radius: 12px; padding: 30px; text-align: center; }
        .stat-card h2 { font-size: 2.5rem; font-weight: 800; }

        /* Contact Form */
        .contact-section { background: #f8f9fa; }
        .form-control:focus { border-color: var(--secondary); box-shadow: 0 0 0 0.2rem rgba(39,174,96,0.15); }

        /* Footer */
        .footer { background: #1a2a3a; color: #ccc; padding: 50px 0 20px; }
        .footer h5 { color: #fff; font-weight: 600; }
        .footer a { color: #aaa; text-decoration: none; }
        .footer a:hover { color: var(--secondary); }

        /* WhatsApp Float */
        .whatsapp-float { position: fixed; bottom: 25px; right: 25px; z-index: 9999; width: 60px; height: 60px; background: #25d366; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(37,211,102,0.4); transition: transform 0.3s; }
        .whatsapp-float:hover { transform: scale(1.1); }
        .whatsapp-float i { font-size: 2rem; color: white; }

        /* Announcements Ticker */
        .announcement-bar { background: var(--accent); color: #000; padding: 8px 0; font-weight: 500; overflow: hidden; }
        .ticker-content { display: inline-block; white-space: nowrap; animation: ticker 30s linear infinite; }
        @keyframes ticker { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2rem; }
            .hero-section { height: 70vh; }
        }
    </style>
</head>
<body>

<!-- Announcement Ticker -->
<div class="announcement-bar" id="announcementBar" style="display:none;">
    <div class="container">
        <span class="ticker-content" id="tickerContent"></span>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">🏘️ Aleto Clan Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#verify">Verify Member</a></li>
                <li class="nav-item"><a class="nav-link" href="#transparency">Transparency</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="#track">Track Ticket</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Slider -->
<section class="hero-section" id="home">
    <div class="hero-slide active" style="background-image: url('https://images.unsplash.com/photo-1509099836639-18ba1795216d?w=1920&q=80')"></div>
    <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=1920&q=80')"></div>
    <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?w=1920&q=80')"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="container text-white">
            <div class="row">
                <div class="col-lg-8">
                    <h1>Empowering Our Community<br>Through Transparency</h1>
                    <p class="mt-3">The Aleto Clan Community Portal ensures fair distribution of grants and stipends to every deserving member. No fraud. No double registration. Complete accountability.</p>
                    <a href="#contact" class="btn btn-lg btn-light me-2 mt-3"><i class="bi bi-envelope"></i> Submit Enquiry</a>
                    <a href="#track" class="btn btn-lg btn-outline-light mt-3"><i class="bi bi-search"></i> Track Your Ticket</a>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-indicators">
        <span class="active" onclick="goToSlide(0)"></span>
        <span onclick="goToSlide(1)"></span>
        <span onclick="goToSlide(2)"></span>
    </div>
</section>

<!-- About Section -->
<section class="py-5" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="section-title mb-4">About Aleto Clan Portal</h2>
                <p class="lead">A digital social registry that captures every member of the Aleto Clan community and ensures transparent, fair distribution of government and community grants.</p>
                <p>Our portal prevents fraud, eliminates ghost beneficiaries, and provides a complete audit trail for every transaction. Whether it's financial stipends, healthcare support, educational scholarships, or community development projects — every naira is tracked and accounted for.</p>
                <div class="row mt-4">
                    <div class="col-6"><div class="d-flex align-items-center"><i class="bi bi-shield-check text-success fs-3 me-2"></i><span>Fraud Prevention</span></div></div>
                    <div class="col-6"><div class="d-flex align-items-center"><i class="bi bi-people text-primary fs-3 me-2"></i><span>Full Registry</span></div></div>
                    <div class="col-6 mt-3"><div class="d-flex align-items-center"><i class="bi bi-clipboard-data text-warning fs-3 me-2"></i><span>Audit Trail</span></div></div>
                    <div class="col-6 mt-3"><div class="d-flex align-items-center"><i class="bi bi-bank text-info fs-3 me-2"></i><span>Direct Payments</span></div></div>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <img src="https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?w=600&q=80" alt="Community" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Services/Features -->
<section class="py-5 bg-light" id="services">
    <div class="container">
        <h2 class="section-title text-center mb-5">What We Do</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card shadow-sm p-4">
                    <div class="feature-icon bg-primary bg-opacity-10 text-primary">👥</div>
                    <h5>Community Registry</h5>
                    <p class="text-muted">Every clan member is registered with unique identification, biometrics, and household details. Complete demographic records.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card shadow-sm p-4">
                    <div class="feature-icon bg-success bg-opacity-10 text-success">💰</div>
                    <h5>Grant Distribution</h5>
                    <p class="text-muted">Transparent selection of beneficiaries for government and community grants. Verified payment tracking to prevent fraud.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card shadow-sm p-4">
                    <div class="feature-icon bg-danger bg-opacity-10 text-danger">🛡️</div>
                    <h5>Fraud Prevention</h5>
                    <p class="text-muted">Biometric verification, duplicate detection, NIN cross-referencing. No ghost beneficiaries or double payments.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card shadow-sm p-4">
                    <div class="feature-icon bg-info bg-opacity-10 text-info">🏥</div>
                    <h5>Healthcare Support</h5>
                    <p class="text-muted">Medical records, clinic visits, preventive care alerts, and health-related grants for community welfare.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card shadow-sm p-4">
                    <div class="feature-icon bg-warning bg-opacity-10 text-warning">📚</div>
                    <h5>Education Programs</h5>
                    <p class="text-muted">Student enrollment, scholarship management, exam tracking, and adult literacy programs.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card shadow-sm p-4">
                    <div class="feature-icon bg-secondary bg-opacity-10 text-secondary">🏗️</div>
                    <h5>Development Projects</h5>
                    <p class="text-muted">Track community infrastructure projects — water, roads, electrification — with beneficiary mapping and impact analytics.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact / Enquiry Form -->
<section class="py-5 contact-section" id="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="section-title mb-4">Contact Us</h2>
                <p>Have a complaint, enquiry, or suggestion? Fill out the form and we'll respond promptly. You'll receive a ticket ID to track your submission.</p>
                <div class="mt-4">
                    <div class="d-flex mb-3"><i class="bi bi-telephone-fill text-primary fs-5 me-3"></i><div><strong>Phone</strong><br>+234 800 000 0000</div></div>
                    <div class="d-flex mb-3"><i class="bi bi-envelope-fill text-primary fs-5 me-3"></i><div><strong>Email</strong><br>info@aletoclan.org</div></div>
                    <div class="d-flex mb-3"><i class="bi bi-geo-alt-fill text-primary fs-5 me-3"></i><div><strong>Office</strong><br>Aleto Clan Community Hall, Rivers State</div></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 p-4">
                    <form id="enquiryForm" onsubmit="submitEnquiry(event)">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="enqName" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="text" class="form-control" id="enqPhone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="enqEmail">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type *</label>
                                <select class="form-select" id="enqType" required>
                                    <option value="enquiry">General Enquiry</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="suggestion">Suggestion</option>
                                    <option value="grant_status">Grant Status Check</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subject *</label>
                                <input type="text" class="form-control" id="enqSubject" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea class="form-control" id="enqMessage" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="enqBtn">Submit Enquiry</button>
                        <div id="enqResult" class="mt-3" style="display:none;"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Verify Membership -->
<section class="py-5 bg-light" id="verify">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h2 class="section-title text-center mb-4">Verify Membership</h2>
                <p class="text-muted">Enter a member's Unique ID to verify their registration. Only non-sensitive details are shown.</p>
                <div class="input-group mt-4 mb-3" style="max-width:500px; margin:0 auto;">
                    <input type="text" class="form-control form-control-lg" id="verifyInput" placeholder="Enter Unique ID (e.g. ALC-20260615-00001)">
                    <button class="btn btn-success btn-lg" onclick="verifyMember()"><i class="bi bi-shield-check"></i> Verify</button>
                </div>
                <div id="verifyResult" class="mt-4" style="display:none;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Transparency Dashboard -->
<section class="py-5" id="transparency">
    <div class="container">
        <h2 class="section-title text-center mb-5">Transparency Dashboard</h2>
        <p class="text-center text-muted mb-4">Real-time community statistics — verifiable by all members.</p>
        <div class="row g-4 justify-content-center" id="statsCards">
            <div class="col-md-3"><div class="card text-center shadow-sm py-4"><h3 class="text-primary" id="statTotal">-</h3><p class="text-muted mb-0">Total Registered</p></div></div>
            <div class="col-md-3"><div class="card text-center shadow-sm py-4"><h3 class="text-success" id="statActive">-</h3><p class="text-muted mb-0">Active Members</p></div></div>
            <div class="col-md-3"><div class="card text-center shadow-sm py-4"><h3 class="text-secondary" id="statDeceased">-</h3><p class="text-muted mb-0">Deceased</p></div></div>
            <div class="col-md-3"><div class="card text-center shadow-sm py-4"><h3 class="text-warning" id="statArchived">-</h3><p class="text-muted mb-0">Archived</p></div></div>
        </div>
        <div class="mt-4" id="grantStatsSection" style="display:none;">
            <h5 class="text-center mb-3">Grant Beneficiaries</h5>
            <div class="row g-3 justify-content-center" id="grantStatsCards"></div>
        </div>
    </div>
</section>

<!-- Track Ticket -->
<section class="py-5 bg-light" id="track">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h2 class="section-title text-center mb-4">Track Your Ticket</h2>
                <p class="text-muted">Enter your ticket ID to check the status of your complaint or enquiry.</p>
                <div class="input-group mt-4">
                    <input type="text" class="form-control form-control-lg" id="trackInput" placeholder="Enter Ticket ID (e.g. TKT-XXXXXXXX)">
                    <button class="btn btn-primary btn-lg" onclick="trackTicket()"><i class="bi bi-search"></i> Track</button>
                </div>
                <div id="trackResult" class="mt-4" style="display:none;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>🏘️ Aleto Clan Portal</h5>
                <p class="small">A digital social registry ensuring transparent and fair distribution of grants and stipends to community members.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#services">Our Services</a></li>
                    <li><a href="#verify">Verify Member</a></li>
                    <li><a href="#transparency">Transparency</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#track">Track Ticket</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Contact Info</h5>
                <p class="small mb-1"><i class="bi bi-telephone"></i> +234 800 000 0000</p>
                <p class="small mb-1"><i class="bi bi-envelope"></i> info@aletoclan.org</p>
                <p class="small"><i class="bi bi-geo-alt"></i> Aleto Clan, Rivers State, Nigeria</p>
            </div>
        </div>
        <hr style="border-color: #333;">
        <p class="text-center small mb-0">&copy; {{ date('Y') }} Aleto Clan Community Portal. All rights reserved.</p>
    </div>
</footer>

<!-- WhatsApp Float Button -->
<a href="https://wa.me/2348000000000?text=Hello%2C%20I%20need%20assistance%20from%20Aleto%20Clan%20Portal" target="_blank" class="whatsapp-float" title="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Hero Slider
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const indicators = document.querySelectorAll('.hero-indicators span');

    function goToSlide(index) {
        slides[currentSlide].classList.remove('active');
        indicators[currentSlide].classList.remove('active');
        currentSlide = index;
        slides[currentSlide].classList.add('active');
        indicators[currentSlide].classList.add('active');
    }

    setInterval(() => {
        goToSlide((currentSlide + 1) % slides.length);
    }, 5000);

    // Load announcements
    fetch('/api/public/announcements')
        .then(r => r.json())
        .then(data => {
            if (data.length > 0) {
                const ticker = data.map(a => `📢 ${a.title}: ${a.content}`).join('   |   ');
                document.getElementById('tickerContent').textContent = ticker;
                document.getElementById('announcementBar').style.display = 'block';
            }
        }).catch(() => {});

    // Load transparency stats
    fetch('/api/public/stats')
        .then(r => r.json())
        .then(data => {
            document.getElementById('statTotal').textContent = data.total_members;
            document.getElementById('statActive').textContent = data.active;
            document.getElementById('statDeceased').textContent = data.deceased;
            document.getElementById('statArchived').textContent = data.archived;
            if (data.grant_stats && data.grant_stats.length > 0) {
                document.getElementById('grantStatsSection').style.display = 'block';
                const container = document.getElementById('grantStatsCards');
                data.grant_stats.forEach(g => {
                    container.innerHTML += `<div class="col-md-4"><div class="card shadow-sm p-3 text-center"><h5 class="text-info">${g.beneficiaries}</h5><p class="text-muted small mb-0">${g.name}</p></div></div>`;
                });
            }
        }).catch(() => {});

    // Verify Membership
    function verifyMember() {
        const uid = document.getElementById('verifyInput').value.trim();
        if (!uid) return alert('Please enter a Unique ID');
        fetch('/api/public/search', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ unique_id: uid })
        })
        .then(r => r.json())
        .then(data => {
            const el = document.getElementById('verifyResult');
            el.style.display = 'block';
            if (data.data) {
                const d = data.data;
                const statusColor = { active: 'success', deceased: 'secondary', archived: 'warning' }[d.status] || 'secondary';
                el.innerHTML = `
                    <div class="card text-start shadow-sm" style="max-width:500px; margin:0 auto;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">${d.full_name}</h5>
                                <span class="badge bg-${statusColor}">${d.status.toUpperCase()}</span>
                            </div>
                            <table class="table table-sm mb-0">
                                <tr><th>Unique ID</th><td><code>${d.unique_id}</code></td></tr>
                                <tr><th>Household</th><td>${d.household_id}</td></tr>
                                <tr><th>Gender</th><td>${d.gender}</td></tr>
                                <tr><th>Village</th><td>${d.village || 'N/A'}</td></tr>
                                <tr><th>Ward</th><td>${d.ward || 'N/A'}</td></tr>
                                <tr><th>Registered</th><td>${new Date(d.created_at).toLocaleDateString()}</td></tr>
                            </table>
                            <p class="text-success mt-2 mb-0"><i class="bi bi-check-circle"></i> This member is verified in the Aleto Clan Registry.</p>
                        </div>
                    </div>`;
            } else {
                el.innerHTML = `<div class="alert alert-danger" style="max-width:500px; margin:0 auto;"><i class="bi bi-x-circle"></i> No member found with this ID. Please check and try again.</div>`;
            }
        })
        .catch(() => {
            document.getElementById('verifyResult').style.display = 'block';
            document.getElementById('verifyResult').innerHTML = `<div class="alert alert-danger" style="max-width:500px; margin:0 auto;">Member not found. Please verify the ID.</div>`;
        });
    }

    // Submit Enquiry
    function submitEnquiry(e) {
        e.preventDefault();
        const btn = document.getElementById('enqBtn');
        btn.disabled = true; btn.textContent = 'Submitting...';

        fetch('/api/enquiries', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                full_name: document.getElementById('enqName').value,
                phone: document.getElementById('enqPhone').value,
                email: document.getElementById('enqEmail').value,
                type: document.getElementById('enqType').value,
                subject: document.getElementById('enqSubject').value,
                message: document.getElementById('enqMessage').value,
            })
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('enqResult').style.display = 'block';
            document.getElementById('enqResult').innerHTML = `<div class="alert alert-success"><strong>✅ Submitted!</strong><br>Your ticket ID is: <code>${data.ticket_id}</code><br>Save this ID to track your enquiry status.</div>`;
            document.getElementById('enquiryForm').reset();
            btn.disabled = false; btn.textContent = 'Submit Enquiry';
        })
        .catch(err => {
            document.getElementById('enqResult').style.display = 'block';
            document.getElementById('enqResult').innerHTML = `<div class="alert alert-danger">Error submitting. Please try again.</div>`;
            btn.disabled = false; btn.textContent = 'Submit Enquiry';
        });
    }

    // Track Ticket
    function trackTicket() {
        const ticketId = document.getElementById('trackInput').value.trim();
        if (!ticketId) return alert('Please enter a ticket ID');

        fetch('/api/enquiries/track', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ ticket_id: ticketId })
        })
        .then(r => r.json())
        .then(data => {
            const el = document.getElementById('trackResult');
            el.style.display = 'block';
            if (data.data) {
                const d = data.data;
                const statusColor = { pending: 'warning', in_progress: 'info', resolved: 'success', closed: 'secondary' }[d.status] || 'secondary';
                el.innerHTML = `
                    <div class="card text-start shadow-sm">
                        <div class="card-body">
                            <h6>Ticket: <code>${d.ticket_id}</code></h6>
                            <p><strong>Subject:</strong> ${d.subject}<br>
                            <strong>Type:</strong> ${d.type}<br>
                            <strong>Submitted:</strong> ${d.submitted_at}<br>
                            <strong>Status:</strong> <span class="badge bg-${statusColor}">${d.status}</span></p>
                            ${d.response ? `<hr><strong>Response:</strong><br>${d.response}<br><small class="text-muted">Responded: ${d.responded_at}</small>` : '<p class="text-muted">No response yet. Please check back later.</p>'}
                        </div>
                    </div>`;
            } else {
                el.innerHTML = `<div class="alert alert-danger">Ticket not found. Please check the ID.</div>`;
            }
        })
        .catch(() => {
            document.getElementById('trackResult').style.display = 'block';
            document.getElementById('trackResult').innerHTML = `<div class="alert alert-danger">Ticket not found.</div>`;
        });
    }
</script>
</body>
</html>
