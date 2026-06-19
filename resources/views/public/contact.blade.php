<!DOCTYPE html>
<html lang="en">
<head>
    @include('public.partials.head')
    <title>Contact Us - Aleto Clan Portal</title>
</head>
<body class="bg-background font-sans text-foreground">
@include('public.partials.nav')

<main class="flex-1 py-16 lg:py-24">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
      <div>
        <h1 class="text-4xl font-extrabold text-secondary mb-6">Get in Touch with the <br><span class="text-primary">Aleto Clan Council</span></h1>
        <p class="text-lg text-muted-foreground mb-12">Have questions about registration, grants, or community projects? Our dedicated support team is here to help.</p>
        <div class="space-y-8">
          <div class="flex gap-6">
            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary shrink-0"><iconify-icon icon="lucide:map-pin" class="text-2xl"></iconify-icon></div>
            <div><h4 class="text-lg font-bold text-secondary mb-1">Office Address</h4><p class="text-muted-foreground">Aleto Clan Community Hall,<br>Eleme LGA, Rivers State, Nigeria.</p></div>
          </div>
          <div class="flex gap-6">
            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary shrink-0"><iconify-icon icon="lucide:phone" class="text-2xl"></iconify-icon></div>
            <div><h4 class="text-lg font-bold text-secondary mb-1">Phone Support</h4><p class="text-muted-foreground">+234 800 000 0000</p></div>
          </div>
          <div class="flex gap-6">
            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary shrink-0"><iconify-icon icon="lucide:mail" class="text-2xl"></iconify-icon></div>
            <div><h4 class="text-lg font-bold text-secondary mb-1">Email</h4><p class="text-muted-foreground">info@aletoclan.com</p></div>
          </div>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="bg-card border border-border rounded-3xl p-8 lg:p-10 shadow-xl">
        <h3 class="text-2xl font-bold text-secondary mb-8">Send us a Message</h3>
        <form id="contactForm" onsubmit="submitContact(event)" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><label class="block text-sm font-bold text-secondary mb-2">Full Name *</label><input id="cName" type="text" placeholder="Enter your name" class="w-full px-4 py-3 bg-muted/30 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" required></div>
            <div><label class="block text-sm font-bold text-secondary mb-2">Phone *</label><input id="cPhone" type="text" placeholder="+234..." class="w-full px-4 py-3 bg-muted/30 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" required></div>
          </div>
          <div><label class="block text-sm font-bold text-secondary mb-2">Email</label><input id="cEmail" type="email" placeholder="example@mail.com" class="w-full px-4 py-3 bg-muted/30 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></div>
          <div><label class="block text-sm font-bold text-secondary mb-2">Category *</label>
            <select id="cType" class="w-full px-4 py-3 bg-muted/30 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" required>
              <option value="enquiry">General Enquiry</option><option value="complaint">Complaint</option><option value="suggestion">Suggestion</option><option value="grant_status">Grant Status</option>
            </select>
          </div>
          <div><label class="block text-sm font-bold text-secondary mb-2">Subject *</label><input id="cSubject" type="text" placeholder="Subject" class="w-full px-4 py-3 bg-muted/30 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" required></div>
          <div><label class="block text-sm font-bold text-secondary mb-2">Message *</label><textarea id="cMessage" rows="5" placeholder="How can we assist you?" class="w-full px-4 py-3 bg-muted/30 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" required></textarea></div>
          <button type="submit" id="cBtn" class="w-full py-4 bg-primary text-primary-foreground rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all">Submit Message</button>
          <div id="cResult" class="mt-4" style="display:none;"></div>
        </form>
      </div>
    </div>
  </div>
</main>

<footer class="bg-secondary text-white py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <p class="text-sm text-secondary-foreground/60 mb-4">Aleto Clan Community Portal Contact Center</p>
    <p class="text-xs text-secondary-foreground/40">Support Hours: 8AM - 5PM WAT | info@aletoclan.com</p>
  </div>
</footer>

<script>
function submitContact(e){
  e.preventDefault();const btn=document.getElementById('cBtn');btn.disabled=true;btn.textContent='Submitting...';
  fetch('/api/enquiries',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({full_name:document.getElementById('cName').value,phone:document.getElementById('cPhone').value,email:document.getElementById('cEmail').value,type:document.getElementById('cType').value,subject:document.getElementById('cSubject').value,message:document.getElementById('cMessage').value})})
  .then(r=>r.json()).then(d=>{document.getElementById('cResult').style.display='block';document.getElementById('cResult').innerHTML=`<div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-800"><p class="font-bold">Message Submitted!</p><p class="text-sm">Your ticket ID: <code class="font-mono bg-green-100 px-2 py-0.5 rounded">${d.ticket_id}</code></p><p class="text-xs mt-1">Save this ID to track your enquiry at <a href="/track-ticket" class="underline">/track-ticket</a></p></div>`;document.getElementById('contactForm').reset();btn.disabled=false;btn.textContent='Submit Message';})
  .catch(()=>{document.getElementById('cResult').style.display='block';document.getElementById('cResult').innerHTML=`<div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">Error submitting. Please try again.</div>`;btn.disabled=false;btn.textContent='Submit Message';});
}
</script>
</body>
</html>
