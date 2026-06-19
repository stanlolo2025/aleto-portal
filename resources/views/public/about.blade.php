<!DOCTYPE html>
<html lang="en">
<head>
    @include('public.partials.head')
    <title>About Us - Aleto Clan Portal</title>
</head>
<body class="bg-background font-sans text-foreground">
@include('public.partials.nav')

<!-- Header Section -->
<header class="bg-secondary py-16 lg:py-24 text-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h1 class="text-4xl lg:text-5xl font-extrabold mb-6 tracking-tight">Preserving Heritage, <br>Building a Digital Future</h1>
    <p class="text-secondary-foreground/80 text-lg max-w-3xl mx-auto leading-relaxed">
      The Aleto Clan Community Portal is more than just a registry; it's a commitment to transparency, fairness, and the collective well-being of our people.
    </p>
  </div>
</header>

<!-- Mission & Vision -->
<section class="py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
      <div class="p-8 bg-card border border-border rounded-2xl shadow-sm">
        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6">
          <iconify-icon icon="lucide:target" class="text-2xl"></iconify-icon>
        </div>
        <h2 class="text-2xl font-bold text-secondary mb-4">Our Mission</h2>
        <p class="text-muted-foreground leading-relaxed">
          To eliminate fraud and ensure that every legitimate member of the Aleto Clan has access to their rightful benefits, healthcare, and educational opportunities through a secure, biometric-backed digital registry.
        </p>
      </div>
      <div class="p-8 bg-card border border-border rounded-2xl shadow-sm">
        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6">
          <iconify-icon icon="lucide:eye" class="text-2xl"></iconify-icon>
        </div>
        <h2 class="text-2xl font-bold text-secondary mb-4">Our Vision</h2>
        <p class="text-muted-foreground leading-relaxed">
          To become a benchmark for community-led digital governance in Nigeria, where technology bridges the gap between resources and those who need them most, fostering trust and unity across generations.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- The Problem & Solution -->
<section class="py-20 bg-muted/30">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row items-center gap-16">
      <div class="lg:w-1/2 space-y-8">
        <div>
          <h2 class="text-3xl font-bold text-secondary mb-6">Why Digital Transformation?</h2>
          <p class="text-muted-foreground leading-relaxed">
            For years, our community relied on manual records which were prone to errors, double registrations, and "ghost" beneficiaries. This system led to unfair distribution of government stipends and grants.
          </p>
        </div>
        <div class="space-y-4">
          <div class="flex items-start gap-4">
            <div class="mt-1 flex-shrink-0 w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center"><iconify-icon icon="lucide:x" class="text-sm"></iconify-icon></div>
            <p class="text-sm font-medium text-secondary">Manual registries allowed for double registration and fraud.</p>
          </div>
          <div class="flex items-start gap-4">
            <div class="mt-1 flex-shrink-0 w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center"><iconify-icon icon="lucide:x" class="text-sm"></iconify-icon></div>
            <p class="text-sm font-medium text-secondary">Elderly and unbanked members were often excluded from grants.</p>
          </div>
          <div class="flex items-start gap-4">
            <div class="mt-1 flex-shrink-0 w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center"><iconify-icon icon="lucide:x" class="text-sm"></iconify-icon></div>
            <p class="text-sm font-medium text-secondary">Lack of data made it difficult to plan healthcare and education projects.</p>
          </div>
        </div>
      </div>
      <div class="lg:w-1/2 p-1 bg-gradient-to-br from-primary to-secondary rounded-3xl shadow-2xl">
        <div class="bg-card p-8 rounded-[1.4rem]">
          <h3 class="text-xl font-bold text-primary mb-6">The Aleto Digital Solution</h3>
          <ul class="space-y-6">
            <li class="flex gap-4">
              <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary font-bold">01</div>
              <div><h4 class="font-bold mb-1">Unique Clan IDs</h4><p class="text-xs text-muted-foreground">Every villager is assigned a permanent, non-transferable digital identity.</p></div>
            </li>
            <li class="flex gap-4">
              <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary font-bold">02</div>
              <div><h4 class="font-bold mb-1">Lifecycle Management</h4><p class="text-xs text-muted-foreground">Real-time updates for births and deaths to prevent ghost payments.</p></div>
            </li>
            <li class="flex gap-4">
              <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary font-bold">03</div>
              <div><h4 class="font-bold mb-1">Biometric Integrity</h4><p class="text-xs text-muted-foreground">Verification via fingerprint and NIN to ensure the right person receives the aid.</p></div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Committee Oversight -->
<section class="py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h2 class="text-3xl font-bold text-secondary mb-12">Governance &amp; Oversight</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="space-y-4">
        <div class="mx-auto w-24 h-24 bg-muted rounded-full flex items-center justify-center text-muted-foreground"><iconify-icon icon="lucide:user" class="text-4xl"></iconify-icon></div>
        <div><h4 class="font-bold text-lg">Clan Elders Council</h4><p class="text-xs text-primary font-semibold uppercase tracking-wider">Policy &amp; Tradition</p></div>
      </div>
      <div class="space-y-4">
        <div class="mx-auto w-24 h-24 bg-muted rounded-full flex items-center justify-center text-muted-foreground"><iconify-icon icon="lucide:user" class="text-4xl"></iconify-icon></div>
        <div><h4 class="font-bold text-lg">Digital Committee</h4><p class="text-xs text-primary font-semibold uppercase tracking-wider">Operations &amp; Security</p></div>
      </div>
      <div class="space-y-4">
        <div class="mx-auto w-24 h-24 bg-muted rounded-full flex items-center justify-center text-muted-foreground"><iconify-icon icon="lucide:user" class="text-4xl"></iconify-icon></div>
        <div><h4 class="font-bold text-lg">Audit Board</h4><p class="text-xs text-primary font-semibold uppercase tracking-wider">Transparency &amp; Compliance</p></div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-secondary text-white pt-16 pb-8 mt-auto">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
      <div class="space-y-6">
        <div class="flex items-center gap-2"><div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center"><iconify-icon icon="lucide:shield" class="text-primary-foreground text-2xl"></iconify-icon></div><span class="text-xl font-bold tracking-tight">Aleto Clan Portal</span></div>
        <p class="text-secondary-foreground/70 text-sm leading-relaxed">A digital gateway for the Aleto Clan, ensuring integrity in social welfare and community development.</p>
      </div>
      <div><h4 class="text-lg font-bold mb-6">Quick Links</h4><ul class="space-y-4 text-secondary-foreground/70 text-sm"><li><a href="/" class="hover:text-white">Home</a></li><li><a href="/about" class="hover:text-white">About Us</a></li><li><a href="/verify" class="hover:text-white">Verify Member</a></li><li><a href="/transparency" class="hover:text-white">Transparency</a></li><li><a href="/contact" class="hover:text-white">Contact</a></li><li><a href="/track-ticket" class="hover:text-white">Track Ticket</a></li></ul></div>
      <div><h4 class="text-lg font-bold mb-6">Contact Us</h4><ul class="space-y-4 text-secondary-foreground/70 text-sm"><li class="flex gap-3"><iconify-icon icon="lucide:map-pin" class="text-tertiary text-lg"></iconify-icon><span>Aleto Clan Community Hall, Eleme, Rivers State</span></li><li class="flex gap-3"><iconify-icon icon="lucide:phone" class="text-tertiary text-lg"></iconify-icon><span>+234 800 000 0000</span></li><li class="flex gap-3"><iconify-icon icon="lucide:mail" class="text-tertiary text-lg"></iconify-icon><span>info@aletoclan.com</span></li></ul></div>
      <div><h4 class="text-lg font-bold mb-6">Newsletter</h4><p class="text-secondary-foreground/70 text-sm mb-4">Stay updated on clan projects.</p><div class="flex gap-2"><input type="email" placeholder="Email" class="flex-1 bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-sm focus:outline-none"><button class="bg-primary px-4 py-2 rounded-lg"><iconify-icon icon="lucide:send"></iconify-icon></button></div></div>
    </div>
    <div class="border-t border-white/10 pt-8 text-center text-sm text-secondary-foreground/50"><p>&copy; {{ date('Y') }} Aleto Clan Community Portal. All rights reserved.</p></div>
  </div>
</footer>
</body>
</html>
