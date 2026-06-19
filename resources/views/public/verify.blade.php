<!DOCTYPE html>
<html lang="en">
<head>
    @include('public.partials.head')
    <title>Verify Member - Aleto Clan Portal</title>
</head>
<body class="bg-background font-sans text-foreground">
@include('public.partials.nav')

<main class="flex-1 flex flex-col items-center justify-center py-12 px-4">
  <div class="max-w-2xl w-full">
    <div class="text-center mb-10">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-primary/10 rounded-full text-primary mb-6">
        <iconify-icon icon="lucide:user-check" class="text-4xl"></iconify-icon>
      </div>
      <h1 class="text-3xl font-bold text-secondary mb-3">Identity Verification</h1>
      <p class="text-muted-foreground">Securely verify membership to access stipends, grants, and community services.</p>
    </div>

    <!-- Verification Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="group p-6 bg-card border border-border rounded-2xl text-left hover:border-primary hover:shadow-lg transition-all">
        <div class="w-12 h-12 bg-muted rounded-xl flex items-center justify-center text-muted-foreground group-hover:bg-primary group-hover:text-primary-foreground mb-4 transition-colors">
          <iconify-icon icon="lucide:fingerprint" class="text-2xl"></iconify-icon>
        </div>
        <h3 class="font-bold text-lg mb-1">NIN Verification</h3>
        <p class="text-xs text-muted-foreground">Link your National Identity Number to your clan profile.</p>
      </div>
      <div class="group p-6 bg-card border border-border rounded-2xl text-left hover:border-primary hover:shadow-lg transition-all">
        <div class="w-12 h-12 bg-muted rounded-xl flex items-center justify-center text-muted-foreground group-hover:bg-primary group-hover:text-primary-foreground mb-4 transition-colors">
          <iconify-icon icon="lucide:scan-face" class="text-2xl"></iconify-icon>
        </div>
        <h3 class="font-bold text-lg mb-1">Face Recognition</h3>
        <p class="text-xs text-muted-foreground">Use AI-powered facial recognition for instant verification.</p>
      </div>
    </div>

    <!-- Verification Form -->
    <div class="bg-card border border-border rounded-2xl p-8 shadow-sm">
      <div class="mb-6">
        <label class="block text-sm font-bold text-secondary mb-2">Select Verification Method</label>
        <div class="grid grid-cols-3 gap-3">
          <button onclick="setMethod('id')" id="btnId" class="px-4 py-2 bg-primary/10 border border-primary text-primary text-xs font-bold rounded-lg">Clan ID</button>
          <button onclick="setMethod('nin')" id="btnNin" class="px-4 py-2 bg-muted border border-border text-muted-foreground text-xs font-bold rounded-lg hover:bg-muted/80">NIN</button>
          <button onclick="setMethod('phone')" id="btnPhone" class="px-4 py-2 bg-muted border border-border text-muted-foreground text-xs font-bold rounded-lg hover:bg-muted/80">Phone</button>
        </div>
      </div>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-bold text-secondary mb-2" id="inputLabel">Enter Clan ID</label>
          <div class="relative">
            <iconify-icon icon="lucide:tag" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"></iconify-icon>
            <input type="text" id="verifyInput" placeholder="e.g., ALC-20260615-00001" class="w-full pl-10 pr-4 py-3 bg-muted/30 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
          </div>
        </div>
        <div class="flex items-center gap-2 p-4 bg-blue-50 border border-blue-100 rounded-xl text-blue-800">
          <iconify-icon icon="lucide:info" class="text-xl shrink-0"></iconify-icon>
          <p class="text-xs leading-relaxed">Verification requires an active internet connection and may take a few seconds to sync with the registry.</p>
        </div>
        <button onclick="verifyMember()" class="w-full py-4 bg-primary text-primary-foreground rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
          <iconify-icon icon="lucide:shield-check" class="text-2xl"></iconify-icon> Proceed to Verify
        </button>
      </div>
      <div id="verifyResult" class="mt-6" style="display:none;"></div>
    </div>

    <!-- Security Trust Badges -->
    <div class="mt-12 flex flex-wrap items-center justify-center gap-8 opacity-50 grayscale hover:grayscale-0 transition-all">
      <div class="flex items-center gap-2"><iconify-icon icon="lucide:lock" class="text-xl"></iconify-icon><span class="text-xs font-bold uppercase tracking-widest">256-bit SSL</span></div>
      <div class="flex items-center gap-2"><iconify-icon icon="lucide:shield" class="text-xl"></iconify-icon><span class="text-xs font-bold uppercase tracking-widest">NIMC Integrated</span></div>
      <div class="flex items-center gap-2"><iconify-icon icon="lucide:database" class="text-xl"></iconify-icon><span class="text-xs font-bold uppercase tracking-widest">GDPR Compliant</span></div>
    </div>
  </div>
</main>

<footer class="bg-secondary text-white py-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
    <div class="flex items-center gap-2"><iconify-icon icon="lucide:shield" class="text-tertiary text-xl"></iconify-icon><span class="text-sm font-bold">Aleto Secure Verification</span></div>
    <p class="text-xs text-secondary-foreground/50">Report suspicious activity to security@aletoclan.com</p>
    <div class="flex gap-4"><a href="/about" class="text-xs hover:text-tertiary">Privacy</a><a href="/about" class="text-xs hover:text-tertiary">Security Policy</a></div>
  </div>
</footer>

<script>
function setMethod(m){
  document.querySelectorAll('[id^=btn]').forEach(b=>{b.className='px-4 py-2 bg-muted border border-border text-muted-foreground text-xs font-bold rounded-lg hover:bg-muted/80';});
  document.getElementById('btn'+m.charAt(0).toUpperCase()+m.slice(1)).className='px-4 py-2 bg-primary/10 border border-primary text-primary text-xs font-bold rounded-lg';
  const labels={id:'Enter Clan ID',nin:'Enter NIN (11 digits)',phone:'Enter Phone Number'};
  const placeholders={id:'e.g., ALC-20260615-00001',nin:'e.g., 12345678901',phone:'e.g., +2348012345678'};
  document.getElementById('inputLabel').textContent=labels[m];
  document.getElementById('verifyInput').placeholder=placeholders[m];
}
function verifyMember(){
  const uid=document.getElementById('verifyInput').value.trim();
  if(!uid)return alert('Please enter a value');
  fetch('/api/public/search',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({unique_id:uid})})
  .then(r=>r.json()).then(d=>{
    const el=document.getElementById('verifyResult');el.style.display='block';
    if(d.data){const v=d.data;el.innerHTML=`<div class="p-6 bg-green-50 border border-green-200 rounded-xl"><div class="flex items-center gap-2 mb-3"><iconify-icon icon="lucide:check-circle" class="text-green-600 text-2xl"></iconify-icon><span class="font-bold text-green-800">Member Verified</span></div><table class="w-full text-sm"><tr><td class="py-1.5 font-semibold w-32">Name</td><td>${v.full_name}</td></tr><tr><td class="py-1.5 font-semibold">ID</td><td class="font-mono">${v.unique_id}</td></tr><tr><td class="py-1.5 font-semibold">Household</td><td>${v.household_id}</td></tr><tr><td class="py-1.5 font-semibold">Status</td><td><span class="px-2 py-0.5 rounded-full text-xs font-bold ${v.status==='active'?'bg-green-100 text-green-800':'bg-gray-100 text-gray-800'}">${v.status}</span></td></tr><tr><td class="py-1.5 font-semibold">Village</td><td>${v.village||'N/A'}</td></tr><tr><td class="py-1.5 font-semibold">Registered</td><td>${new Date(v.created_at).toLocaleDateString()}</td></tr></table></div>`;}
    else{el.innerHTML=`<div class="p-6 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3"><iconify-icon icon="lucide:x-circle" class="text-red-600 text-2xl"></iconify-icon><span class="text-red-800 font-bold">No member found with this ID.</span></div>`;}
  }).catch(()=>{document.getElementById('verifyResult').style.display='block';document.getElementById('verifyResult').innerHTML=`<div class="p-6 bg-red-50 border border-red-200 rounded-xl text-red-800">Member not found. Please check and try again.</div>`;});
}
</script>
</body>
</html>
