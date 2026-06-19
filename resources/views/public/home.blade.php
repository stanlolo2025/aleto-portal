<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aleto Clan Community Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <style type="text/tailwindcss">
      @import "tailwindcss";
      @theme inline {
        --color-background: #f8fafc;
        --color-foreground: #0f172a;
        --color-primary: #064e3b;
        --color-primary-foreground: #ffffff;
        --color-secondary: #1e3a8a;
        --color-secondary-foreground: #ffffff;
        --color-muted: #f1f5f9;
        --color-muted-foreground: #64748b;
        --color-card: #ffffff;
        --color-border: #e2e8f0;
        --color-tertiary: #10b981;
        --font-family-sans: Inter, sans-serif;
      }
    </style>
</head>
<body class="bg-background font-sans text-foreground">

<!-- Announcement Ticker -->
<div id="announcementBar" class="bg-tertiary text-white text-sm py-2 overflow-hidden" style="display:none;">
  <div class="max-w-7xl mx-auto px-4"><span id="tickerContent" class="inline-block whitespace-nowrap animate-[ticker_30s_linear_infinite]"></span></div>
</div>
<style>@keyframes ticker{0%{transform:translateX(100%)}100%{transform:translateX(-100%)}}</style>

<!-- Navigation -->
<nav class="sticky top-0 z-50 w-full bg-card border-b border-border shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <div class="flex items-center gap-2">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
          <iconify-icon icon="lucide:shield" class="text-primary-foreground text-2xl"></iconify-icon>
        </div>
        <span class="text-xl font-bold text-primary tracking-tight">Aleto Clan Portal</span>
      </div>
      <div class="hidden lg:flex items-center gap-6">
        <a href="/" class="text-primary font-semibold border-b-2 border-primary pb-1">Home</a>
        <a href="#about" class="text-muted-foreground hover:text-primary transition-colors">About</a>
        <a href="/members" class="text-muted-foreground hover:text-primary transition-colors">Members</a>
        <a href="#verify" class="text-muted-foreground hover:text-primary transition-colors">Verify</a>
        <a href="#transparency" class="text-muted-foreground hover:text-primary transition-colors">Transparency</a>
        <a href="#contact" class="text-muted-foreground hover:text-primary transition-colors">Contact</a>
        <a href="#track" class="bg-primary text-primary-foreground px-5 py-2 rounded-lg font-medium hover:opacity-90">Track Ticket</a>
      </div>
      <button class="lg:hidden p-2 text-muted-foreground" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
      </button>
    </div>
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden pb-4 space-y-2">
      <a href="/" class="block py-2 text-primary font-semibold">Home</a>
      <a href="#about" class="block py-2 text-muted-foreground">About</a>
      <a href="/members" class="block py-2 text-muted-foreground">Members</a>
      <a href="#verify" class="block py-2 text-muted-foreground">Verify</a>
      <a href="#transparency" class="block py-2 text-muted-foreground">Transparency</a>
      <a href="#contact" class="block py-2 text-muted-foreground">Contact</a>
      <a href="#track" class="block py-2 text-primary font-semibold">Track Ticket</a>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="relative w-full py-20 lg:py-32 overflow-hidden">
  <div class="absolute inset-0 z-0">
    <img src="https://images.unsplash.com/photo-1509099836639-18ba1795216d?w=1920&q=80" alt="Community" class="w-full h-full object-cover opacity-20">
    <div class="absolute inset-0 bg-gradient-to-b from-background via-background/80 to-background"></div>
  </div>
  <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-wider text-primary uppercase bg-primary/10 rounded-full">Official Social Registry</span>
    <h1 class="text-4xl md:text-6xl font-extrabold text-secondary leading-tight mb-6">
      Securing Our Future, <br><span class="text-primary">One Member at a Time</span>
    </h1>
    <p class="max-w-2xl mx-auto text-lg text-muted-foreground mb-10">
      The Aleto Clan Community Portal ensures fair distribution of stipends, quality healthcare, and transparent development projects through a unified digital registry.
    </p>
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <a href="#verify" class="w-full sm:w-auto px-8 py-4 bg-primary text-primary-foreground rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:scale-105 transition-transform text-center">Verify Identity</a>
      <a href="/members" class="w-full sm:w-auto px-8 py-4 bg-white border-2 border-primary text-primary rounded-xl font-bold text-lg hover:bg-primary/5 transition-colors text-center">View Members</a>
    </div>
  </div>
</section>

<!-- Impact Stats -->
<section class="py-12 bg-secondary" id="transparency">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
      <div class="p-4"><p class="text-3xl md:text-4xl font-bold text-white mb-1" id="statTotal">-</p><p class="text-secondary-foreground/70 text-sm font-medium uppercase tracking-wide">Registered Members</p></div>
      <div class="p-4"><p class="text-3xl md:text-4xl font-bold text-white mb-1" id="statActive">-</p><p class="text-secondary-foreground/70 text-sm font-medium uppercase tracking-wide">Active Members</p></div>
      <div class="p-4"><p class="text-3xl md:text-4xl font-bold text-white mb-1" id="statGrants">-</p><p class="text-secondary-foreground/70 text-sm font-medium uppercase tracking-wide">Grants Distributed</p></div>
      <div class="p-4"><p class="text-3xl md:text-4xl font-bold text-white mb-1" id="statDeceased">-</p><p class="text-secondary-foreground/70 text-sm font-medium uppercase tracking-wide">Deceased/Archived</p></div>
    </div>
  </div>
</section>

<!-- Core Modules -->
<section class="py-20 bg-background" id="about">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16">
      <h2 class="text-3xl font-bold text-secondary mb-4">Core Ecosystem Modules</h2>
      <p class="text-muted-foreground max-w-xl mx-auto">Comprehensive tools designed to eliminate fraud and empower every household in the Aleto community.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div class="group p-8 bg-card rounded-2xl border border-border hover:border-primary transition-all shadow-sm hover:shadow-xl">
        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors"><iconify-icon icon="lucide:users" class="text-3xl"></iconify-icon></div>
        <h3 class="text-xl font-bold mb-3">Community Registry</h3>
        <p class="text-muted-foreground mb-6">Digital social registry capturing newborns, adults, and elderly with unique clan IDs.</p>
      </div>
      <div class="group p-8 bg-card rounded-2xl border border-border hover:border-primary transition-all shadow-sm hover:shadow-xl">
        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors"><iconify-icon icon="lucide:credit-card" class="text-3xl"></iconify-icon></div>
        <h3 class="text-xl font-bold mb-3">Grant Distribution</h3>
        <p class="text-muted-foreground mb-6">Transparent stipend allocation with proxy support for elderly and duplicate detection.</p>
      </div>
      <div class="group p-8 bg-card rounded-2xl border border-border hover:border-primary transition-all shadow-sm hover:shadow-xl">
        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors"><iconify-icon icon="lucide:shield-check" class="text-3xl"></iconify-icon></div>
        <h3 class="text-xl font-bold mb-3">Fraud Prevention</h3>
        <p class="text-muted-foreground mb-6">Biometric verification and NIN integration to eliminate ghost beneficiaries.</p>
      </div>
      <div class="group p-8 bg-card rounded-2xl border border-border hover:border-primary transition-all shadow-sm hover:shadow-xl">
        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors"><iconify-icon icon="lucide:activity" class="text-3xl"></iconify-icon></div>
        <h3 class="text-xl font-bold mb-3">Healthcare Tracking</h3>
        <p class="text-muted-foreground mb-6">Monitoring vaccinations, chronic conditions, and clinic visits for a healthier clan.</p>
      </div>
      <div class="group p-8 bg-card rounded-2xl border border-border hover:border-primary transition-all shadow-sm hover:shadow-xl">
        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors"><iconify-icon icon="lucide:graduation-cap" class="text-3xl"></iconify-icon></div>
        <h3 class="text-xl font-bold mb-3">Education Management</h3>
        <p class="text-muted-foreground mb-6">Scholarship tracking and student performance monitoring across the community.</p>
      </div>
      <div class="group p-8 bg-card rounded-2xl border border-border hover:border-primary transition-all shadow-sm hover:shadow-xl">
        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors"><iconify-icon icon="lucide:map" class="text-3xl"></iconify-icon></div>
        <h3 class="text-xl font-bold mb-3">Project Mapping</h3>
        <p class="text-muted-foreground mb-6">Real-time tracking of water, road, and electrification projects with community feedback.</p>
      </div>
    </div>
  </div>
</section>

<!-- Community Members Preview -->
<section class="py-20 bg-muted/50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold text-secondary mb-4">Our Community Members</h2>
      <p class="text-muted-foreground">Registered and verified members of the Aleto Clan community.</p>
    </div>
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="membersGrid"></div>
    <div class="text-center mt-8">
      <a href="/members" class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-primary-foreground rounded-xl font-bold hover:opacity-90 transition-opacity">View All Members <iconify-icon icon="lucide:arrow-right"></iconify-icon></a>
    </div>
  </div>
</section>

<!-- Verify Member Section -->
<section class="py-20 bg-background" id="verify">
  <div class="max-w-2xl mx-auto px-4 text-center">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-primary/10 rounded-full text-primary mb-6">
      <iconify-icon icon="lucide:user-check" class="text-4xl"></iconify-icon>
    </div>
    <h2 class="text-3xl font-bold text-secondary mb-3">Verify Membership</h2>
    <p class="text-muted-foreground mb-8">Enter a member's Unique ID to verify their registration. Only non-sensitive details are shown.</p>
    <div class="bg-card border border-border rounded-2xl p-8 shadow-sm">
      <div class="relative mb-4">
        <iconify-icon icon="lucide:tag" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"></iconify-icon>
        <input type="text" id="verifyInput" placeholder="e.g., ALC-20260615-00001" class="w-full pl-10 pr-4 py-3 bg-muted/30 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
      </div>
      <button onclick="verifyMember()" class="w-full py-4 bg-primary text-primary-foreground rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
        <iconify-icon icon="lucide:shield-check" class="text-2xl"></iconify-icon> Verify Member
      </button>
      <div id="verifyResult" class="mt-6 text-left" style="display:none;"></div>
    </div>
  </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-muted/50" id="contact">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
      <div>
        <h2 class="text-3xl font-bold text-secondary mb-6">Contact Us</h2>
        <p class="text-muted-foreground mb-8">Have a complaint, enquiry, or suggestion? Fill out the form and we'll respond promptly.</p>
        <div class="space-y-4">
          <div class="flex gap-3"><iconify-icon icon="lucide:phone" class="text-primary text-xl mt-1"></iconify-icon><div><p class="font-bold">Phone</p><p class="text-muted-foreground text-sm">+234 800 000 0000</p></div></div>
          <div class="flex gap-3"><iconify-icon icon="lucide:mail" class="text-primary text-xl mt-1"></iconify-icon><div><p class="font-bold">Email</p><p class="text-muted-foreground text-sm">info@aletoclan.com</p></div></div>
          <div class="flex gap-3"><iconify-icon icon="lucide:map-pin" class="text-primary text-xl mt-1"></iconify-icon><div><p class="font-bold">Office</p><p class="text-muted-foreground text-sm">Aleto Clan Community Hall, Eleme, Rivers State</p></div></div>
        </div>
      </div>
      <div class="bg-card border border-border rounded-2xl p-8 shadow-sm">
        <form id="enquiryForm" onsubmit="submitEnquiry(event)">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-bold mb-1">Full Name *</label><input id="enqName" class="w-full px-4 py-2.5 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" required></div>
            <div><label class="block text-sm font-bold mb-1">Phone *</label><input id="enqPhone" class="w-full px-4 py-2.5 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" required></div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-bold mb-1">Email</label><input id="enqEmail" type="email" class="w-full px-4 py-2.5 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20"></div>
            <div><label class="block text-sm font-bold mb-1">Type *</label><select id="enqType" class="w-full px-4 py-2.5 border border-border rounded-lg text-sm focus:outline-none" required><option value="enquiry">General Enquiry</option><option value="complaint">Complaint</option><option value="suggestion">Suggestion</option><option value="grant_status">Grant Status</option></select></div>
          </div>
          <div class="mb-4"><label class="block text-sm font-bold mb-1">Subject *</label><input id="enqSubject" class="w-full px-4 py-2.5 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" required></div>
          <div class="mb-4"><label class="block text-sm font-bold mb-1">Message *</label><textarea id="enqMessage" rows="4" class="w-full px-4 py-2.5 border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" required></textarea></div>
          <button type="submit" id="enqBtn" class="w-full py-3 bg-primary text-primary-foreground rounded-xl font-bold hover:opacity-90 transition-opacity">Submit Enquiry</button>
          <div id="enqResult" class="mt-4" style="display:none;"></div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Beyond Registration: Community Development -->
<section class="py-20 bg-muted/50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row items-center gap-12">
      <div class="lg:w-1/2">
        <img src="https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?w=600&q=80" alt="Community Development" class="rounded-2xl shadow-2xl border-4 border-white">
      </div>
      <div class="lg:w-1/2">
        <h2 class="text-3xl font-bold text-secondary mb-6">Beyond Registration: <br>Community Development</h2>
        <div class="space-y-6">
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary"><iconify-icon icon="lucide:activity"></iconify-icon></div>
            <div><h4 class="font-bold text-lg mb-1">Healthcare Tracking</h4><p class="text-muted-foreground">Monitoring vaccinations, chronic conditions, and clinic visits for a healthier clan.</p></div>
          </div>
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary"><iconify-icon icon="lucide:graduation-cap"></iconify-icon></div>
            <div><h4 class="font-bold text-lg mb-1">Education Management</h4><p class="text-muted-foreground">Scholarship tracking and student performance monitoring across the community.</p></div>
          </div>
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary"><iconify-icon icon="lucide:map"></iconify-icon></div>
            <div><h4 class="font-bold text-lg mb-1">Project Mapping</h4><p class="text-muted-foreground">Real-time tracking of water, road, and electrification projects with community feedback.</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Track Ticket -->
<section class="py-20 bg-background" id="track">
  <div class="max-w-lg mx-auto px-4 text-center">
    <h2 class="text-3xl font-bold text-secondary mb-3">Track Your Ticket</h2>
    <p class="text-muted-foreground mb-6">Enter your ticket ID to check the status of your complaint or enquiry.</p>
    <div class="flex gap-2">
      <input type="text" id="trackInput" placeholder="TKT-XXXXXXXX" class="flex-1 px-4 py-3 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
      <button onclick="trackTicket()" class="px-6 py-3 bg-primary text-primary-foreground rounded-xl font-bold hover:opacity-90">Track</button>
    </div>
    <div id="trackResult" class="mt-6 text-left" style="display:none;"></div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-secondary text-white pt-16 pb-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
      <div class="space-y-4">
        <div class="flex items-center gap-2"><div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center"><iconify-icon icon="lucide:shield" class="text-primary-foreground text-2xl"></iconify-icon></div><span class="text-xl font-bold">Aleto Clan Portal</span></div>
        <p class="text-secondary-foreground/70 text-sm">A digital gateway for the Aleto Clan, ensuring integrity in social welfare and community development.</p>
      </div>
      <div><h4 class="text-lg font-bold mb-6">Quick Links</h4><ul class="space-y-3 text-secondary-foreground/70 text-sm"><li><a href="#about" class="hover:text-white">About Us</a></li><li><a href="/members" class="hover:text-white">Members</a></li><li><a href="#verify" class="hover:text-white">Verify Member</a></li><li><a href="#transparency" class="hover:text-white">Transparency</a></li><li><a href="#contact" class="hover:text-white">Contact</a></li></ul></div>
      <div><h4 class="text-lg font-bold mb-6">Contact</h4><ul class="space-y-3 text-secondary-foreground/70 text-sm"><li class="flex gap-2"><iconify-icon icon="lucide:map-pin" class="text-primary"></iconify-icon>Aleto Clan Community Hall, Eleme, Rivers State</li><li class="flex gap-2"><iconify-icon icon="lucide:phone" class="text-primary"></iconify-icon>+234 800 000 0000</li><li class="flex gap-2"><iconify-icon icon="lucide:mail" class="text-primary"></iconify-icon>info@aletoclan.com</li></ul></div>
      <div><h4 class="text-lg font-bold mb-6">Newsletter</h4><p class="text-secondary-foreground/70 text-sm mb-4">Stay updated on clan projects and grant cycles.</p><div class="flex gap-2"><input type="email" placeholder="Email address" class="flex-1 bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-sm focus:outline-none"><button class="bg-primary px-4 py-2 rounded-lg hover:opacity-90"><iconify-icon icon="lucide:send"></iconify-icon></button></div></div>
    </div>
    <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-secondary-foreground/50">
      <p>&copy; {{ date('Y') }} Aleto Clan Community Portal. All rights reserved.</p>
      <div class="flex gap-6"><a href="#" class="hover:text-white">Privacy Policy</a><a href="#" class="hover:text-white">Terms of Service</a></div>
    </div>
  </div>
</footer>

<!-- WhatsApp Float -->
<a href="https://wa.me/2348000000000?text=Hello%2C%20I%20need%20assistance" target="_blank" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
  <iconify-icon icon="logos:whatsapp-icon" class="text-3xl"></iconify-icon>
</a>

<script>
// Load stats
fetch('/api/public/stats').then(r=>r.json()).then(d=>{
  document.getElementById('statTotal').textContent=d.total_members?.toLocaleString()||'0';
  document.getElementById('statActive').textContent=d.active?.toLocaleString()||'0';
  document.getElementById('statDeceased').textContent=((d.deceased||0)+(d.archived||0)).toLocaleString();
  document.getElementById('statGrants').textContent=(d.grant_stats||[]).length;
}).catch(()=>{});

// Load members preview
fetch('/api/public/members?per_page=12').then(r=>r.json()).then(d=>{
  const grid=document.getElementById('membersGrid');
  (d.data||[]).forEach(m=>{
    const photo=m.passport_photo?`/storage/${m.passport_photo}`:'';
    const initials=m.full_name.split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase();
    grid.innerHTML+=`<div class="text-center"><div class="w-16 h-16 mx-auto rounded-full ${photo?'':'bg-gradient-to-br from-primary to-secondary'} flex items-center justify-center text-white font-bold overflow-hidden">${photo?`<img src="${photo}" class="w-full h-full object-cover">`:`${initials}`}</div><p class="text-xs font-semibold mt-2 truncate">${m.full_name}</p></div>`;
  });
}).catch(()=>{});

// Load announcements
fetch('/api/public/announcements').then(r=>r.json()).then(d=>{
  if(d.length>0){document.getElementById('tickerContent').textContent=d.map(a=>a.title+': '+a.content).join('   |   ');document.getElementById('announcementBar').style.display='block';}
}).catch(()=>{});

// Verify Member
function verifyMember(){
  const uid=document.getElementById('verifyInput').value.trim();
  if(!uid)return alert('Please enter a Unique ID');
  fetch('/api/public/search',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({unique_id:uid})})
  .then(r=>r.json()).then(d=>{
    const el=document.getElementById('verifyResult');el.style.display='block';
    if(d.data){const v=d.data;el.innerHTML=`<div class="p-4 bg-green-50 border border-green-200 rounded-xl"><p class="font-bold text-green-800 mb-2">Member Verified</p><table class="w-full text-sm"><tr><td class="py-1 font-semibold w-32">Name</td><td>${v.full_name}</td></tr><tr><td class="py-1 font-semibold">ID</td><td class="font-mono">${v.unique_id}</td></tr><tr><td class="py-1 font-semibold">Household</td><td>${v.household_id}</td></tr><tr><td class="py-1 font-semibold">Status</td><td><span class="px-2 py-0.5 rounded-full text-xs font-bold ${v.status==='active'?'bg-green-100 text-green-800':'bg-gray-100 text-gray-800'}">${v.status}</span></td></tr><tr><td class="py-1 font-semibold">Village</td><td>${v.village||'N/A'}</td></tr></table></div>`;}
    else{el.innerHTML=`<div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">No member found with this ID.</div>`;}
  }).catch(()=>{document.getElementById('verifyResult').style.display='block';document.getElementById('verifyResult').innerHTML=`<div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">Member not found.</div>`;});
}

// Submit Enquiry
function submitEnquiry(e){
  e.preventDefault();const btn=document.getElementById('enqBtn');btn.disabled=true;btn.textContent='Submitting...';
  fetch('/api/enquiries',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({full_name:document.getElementById('enqName').value,phone:document.getElementById('enqPhone').value,email:document.getElementById('enqEmail').value,type:document.getElementById('enqType').value,subject:document.getElementById('enqSubject').value,message:document.getElementById('enqMessage').value})})
  .then(r=>r.json()).then(d=>{document.getElementById('enqResult').style.display='block';document.getElementById('enqResult').innerHTML=`<div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-800"><p class="font-bold">Submitted!</p><p>Ticket ID: <code class="font-mono">${d.ticket_id}</code></p></div>`;document.getElementById('enquiryForm').reset();btn.disabled=false;btn.textContent='Submit Enquiry';})
  .catch(()=>{document.getElementById('enqResult').style.display='block';document.getElementById('enqResult').innerHTML=`<div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">Error submitting.</div>`;btn.disabled=false;btn.textContent='Submit Enquiry';});
}

// Track Ticket
function trackTicket(){
  const tid=document.getElementById('trackInput').value.trim();if(!tid)return alert('Enter ticket ID');
  fetch('/api/enquiries/track',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({ticket_id:tid})})
  .then(r=>r.json()).then(d=>{const el=document.getElementById('trackResult');el.style.display='block';
    if(d.data){const t=d.data;el.innerHTML=`<div class="p-4 bg-card border border-border rounded-xl text-left"><p class="font-bold mb-2">${t.subject}</p><p class="text-sm text-muted-foreground mb-1">Status: <span class="font-bold">${t.status}</span></p><p class="text-sm text-muted-foreground mb-1">Submitted: ${t.submitted_at}</p>${t.response?`<div class="mt-3 p-3 bg-green-50 rounded-lg text-sm"><p class="font-bold">Response:</p><p>${t.response}</p></div>`:''}</div>`;}
    else{el.innerHTML=`<div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">Ticket not found.</div>`;}
  }).catch(()=>{document.getElementById('trackResult').style.display='block';document.getElementById('trackResult').innerHTML=`<div class="p-4 bg-red-50 rounded-xl text-red-800">Not found.</div>`;});
}
</script>
</body>
</html>
