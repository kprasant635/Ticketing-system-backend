<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Land Record Ticketing System | NIC</title>
    <meta name="description" content="Raise, track and resolve Land Record grievances online — fast, transparent, paperless.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --gold:#D4A017;--gold-l:#F5C842;--gold-d:#A07810;
            --green:#196d35;--green-l:#22994a;
            --navy:#0B1B38;--navy-l:#122040;--navy-2:#0D2347;
            --red:#E53935;--blue:#1565C0;
            --w:rgba(255,255,255,.07);--wb:rgba(34,153,74,.22);
        }
        html{scroll-behavior:smooth}
        body{font-family:'Inter',sans-serif;background:var(--navy);color:#fff;min-height:100vh;overflow-x:hidden}

        /* BG */
        #bg-canvas{position:fixed;inset:0;z-index:0;pointer-events:none}
        .orb{position:fixed;border-radius:50%;filter:blur(110px);opacity:.15;pointer-events:none;z-index:0;animation:oF 16s ease-in-out infinite alternate}
        .o1{width:550px;height:550px;background:var(--gold);top:-180px;left:-180px}
        .o2{width:450px;height:450px;background:#22994a;bottom:-120px;right:-120px;animation-delay:-7s}
        .o3{width:300px;height:300px;background:#1565C0;top:35%;right:10%;animation-delay:-13s}
        @keyframes oF{0%{transform:translate(0,0)}100%{transform:translate(35px,55px)}}

        /* NAV */
        nav{position:fixed;top:0;left:0;right:0;z-index:100;display:flex;align-items:center;justify-content:space-between;padding:.9rem 2.5rem;background:rgba(11,27,56,.75);backdrop-filter:blur(18px);border-bottom:1px solid rgba(212,160,23,.15);transition:box-shadow .3s}
        .nb{display:flex;align-items:center;gap:.75rem;text-decoration:none}
        .nl{width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,var(--gold-l),var(--gold-d));display:flex;align-items:center;justify-content:center;font-size:1.3rem;box-shadow:0 4px 16px rgba(212,160,23,.4)}
        .nt span:first-child{font-family:'Outfit',sans-serif;font-size:.95rem;font-weight:800;background:linear-gradient(90deg,var(--gold-l),#fff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;display:block}
        .nt span:last-child{font-size:.6rem;color:rgba(255,255,255,.4);letter-spacing:.1em;text-transform:uppercase;display:block}
        .nav-r{display:flex;align-items:center;gap:.4rem}
        .nav-r a{text-decoration:none;color:rgba(255,255,255,.7);padding:.42rem 1rem;border-radius:8px;font-size:.85rem;font-weight:500;transition:all .2s}
        .nav-r a:hover{color:#fff;background:rgba(255,255,255,.09)}
        .a-login{border:1px solid rgba(212,160,23,.5)!important;color:var(--gold-l)!important}
        .a-login:hover{background:rgba(212,160,23,.12)!important}
        .a-reg{background:linear-gradient(135deg,var(--gold-l),var(--gold-d))!important;color:var(--navy)!important;font-weight:700!important;box-shadow:0 4px 16px rgba(212,160,23,.38)}
        .a-reg:hover{transform:translateY(-1px);box-shadow:0 6px 24px rgba(212,160,23,.52)!important}
        .a-dash{background:linear-gradient(135deg,#22994a,#196d35)!important;color:#fff!important;font-weight:700!important}

        /* HERO */
        .hero{position:relative;z-index:1;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:6.5rem 2rem 3rem;text-align:center}
        .hero-in{max-width:820px}
        .badge{display:inline-flex;align-items:center;gap:.5rem;background:rgba(212,160,23,.1);border:1px solid rgba(212,160,23,.35);color:var(--gold-l);padding:.32rem .95rem;border-radius:999px;font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:1.4rem;animation:fdU .8s ease both}
        .bdot{width:7px;height:7px;border-radius:50%;background:var(--gold-l);animation:pulse 1.4s infinite}
        @keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(1.4)}}
        h1{font-family:'Outfit',sans-serif;font-size:clamp(2.2rem,5vw,4rem);font-weight:900;line-height:1.1;margin-bottom:1.1rem;animation:fdU .9s ease .1s both}
        .h1-gold{background:linear-gradient(90deg,var(--gold-l),var(--gold),#fff6cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .hero-sub{font-size:clamp(.9rem,1.8vw,1.1rem);color:rgba(255,255,255,.58);max-width:600px;margin:0 auto 2.2rem;line-height:1.8;animation:fdU 1s ease .2s both}
        .cta-row{display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;animation:fdU 1.1s ease .3s both}
        .btn-p{padding:.85rem 2rem;border-radius:12px;font-size:.95rem;font-weight:700;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.55rem;background:linear-gradient(135deg,var(--gold-l),var(--gold));color:var(--navy);box-shadow:0 6px 26px rgba(212,160,23,.42);transition:all .3s}
        .btn-p:hover{transform:translateY(-3px);box-shadow:0 10px 34px rgba(212,160,23,.58)}
        .btn-s{padding:.85rem 2rem;border-radius:12px;font-size:.95rem;font-weight:600;border:1px solid rgba(255,255,255,.2);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.55rem;color:rgba(255,255,255,.82);background:rgba(255,255,255,.05);backdrop-filter:blur(8px);transition:all .3s}
        .btn-s:hover{background:rgba(255,255,255,.11);border-color:rgba(255,255,255,.38);transform:translateY(-2px)}
        @keyframes fdU{from{opacity:0;transform:translateY(-18px)}to{opacity:1;transform:translateY(0)}}
        .scroll-cue{position:absolute;bottom:1.8rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:.35rem;color:rgba(255,255,255,.3);font-size:.65rem;letter-spacing:.1em;animation:bob 2.5s infinite}
        @keyframes bob{0%,100%{transform:translateX(-50%) translateY(0)}50%{transform:translateX(-50%) translateY(8px)}}

        /* STATS */
        .stats{position:relative;z-index:1;background:rgba(255,255,255,.03);border-top:1px solid rgba(212,160,23,.13);border-bottom:1px solid rgba(212,160,23,.13)}
        .stats-in{max-width:1080px;margin:0 auto;display:flex;flex-wrap:wrap}
        .si{flex:1;min-width:140px;display:flex;flex-direction:column;align-items:center;padding:1.8rem 1rem;border-right:1px solid rgba(255,255,255,.06)}
        .si:last-child{border-right:none}
        .si-n{font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;background:linear-gradient(90deg,var(--gold-l),#fff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .si-l{font-size:.72rem;color:rgba(255,255,255,.4);margin-top:.2rem;text-align:center}

        /* SECTIONS */
        section{position:relative;z-index:1}
        .sec{max-width:1080px;margin:0 auto;padding:4.5rem 2rem}
        .stag{display:inline-block;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--gold-l);background:rgba(212,160,23,.1);border:1px solid rgba(212,160,23,.25);padding:.28rem .8rem;border-radius:999px;margin-bottom:.8rem}
        .sec-h{font-family:'Outfit',sans-serif;font-size:clamp(1.6rem,2.8vw,2.3rem);font-weight:800;line-height:1.25;margin-bottom:.7rem}
        .sec-p{color:rgba(255,255,255,.48);font-size:.9rem;line-height:1.75;max-width:500px}
        .divl{width:52px;height:3px;background:linear-gradient(90deg,var(--gold),transparent);border-radius:999px;margin-bottom:1.3rem}

        /* TICKET FLOW CARDS */
        .flow-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.2rem;margin-top:2.5rem}
        .fc{background:var(--w);border:1px solid var(--wb);border-radius:18px;padding:1.8rem 1.5rem;transition:all .35s;position:relative;overflow:hidden}
        .fc::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(212,160,23,.07),transparent);opacity:0;transition:opacity .35s}
        .fc:hover{transform:translateY(-5px);border-color:rgba(212,160,23,.5);box-shadow:0 18px 50px rgba(0,0,0,.35)}
        .fc:hover::before{opacity:1}
        .fc-num{font-family:'Outfit',sans-serif;font-size:2.5rem;font-weight:900;color:rgba(212,160,23,.18);line-height:1;margin-bottom:.8rem}
        .fc-icon{font-size:1.8rem;margin-bottom:.9rem}
        .fc-t{font-size:.98rem;font-weight:700;margin-bottom:.4rem}
        .fc-d{font-size:.8rem;color:rgba(255,255,255,.45);line-height:1.6}
        .fc-badge{display:inline-block;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding:.2rem .6rem;border-radius:999px;margin-top:.8rem}
        .fc-badge.gold{background:rgba(212,160,23,.15);border:1px solid rgba(212,160,23,.35);color:var(--gold-l)}
        .fc-badge.green{background:rgba(34,153,74,.15);border:1px solid rgba(34,153,74,.4);color:#4cdb80}
        .fc-badge.blue{background:rgba(21,101,192,.18);border:1px solid rgba(21,101,192,.4);color:#90caf9}
        .fc-badge.red{background:rgba(229,57,53,.15);border:1px solid rgba(229,57,53,.35);color:#ef9a9a}

        /* TICKET TYPES */
        .ttype-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-top:2.5rem}
        .tt{background:var(--w);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:1.4rem 1rem;text-align:center;cursor:pointer;transition:all .3s;text-decoration:none;color:#fff;display:flex;flex-direction:column;align-items:center;gap:.6rem}
        .tt:hover{border-color:rgba(212,160,23,.4);background:rgba(212,160,23,.06);transform:translateY(-4px)}
        .tt-icon{font-size:1.9rem}
        .tt-name{font-size:.83rem;font-weight:600}
        .tt-cnt{font-size:.7rem;color:rgba(255,255,255,.35)}

        /* STATUS TRACKER */
        .tracker{background:rgba(255,255,255,.03);border-top:1px solid rgba(255,255,255,.05);border-bottom:1px solid rgba(255,255,255,.05)}
        .tracker-in{max-width:1080px;margin:0 auto;padding:4.5rem 2rem;display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center}
        .status-mock{background:rgba(255,255,255,.04);border:1px solid rgba(212,160,23,.2);border-radius:20px;padding:1.5rem;overflow:hidden}
        .sm-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.2rem}
        .sm-tid{font-family:'Outfit',sans-serif;font-size:.85rem;font-weight:700;color:var(--gold-l)}
        .sm-status{display:inline-flex;align-items:center;gap:.4rem;font-size:.7rem;font-weight:700;background:rgba(34,153,74,.15);border:1px solid rgba(34,153,74,.4);color:#4cdb80;padding:.25rem .7rem;border-radius:999px}
        .sm-sdot{width:6px;height:6px;border-radius:50%;background:#4cdb80;animation:pulse 1.2s infinite}
        .sm-steps{display:flex;flex-direction:column;gap:.7rem}
        .sm-step{display:flex;align-items:flex-start;gap:.9rem}
        .sms-dot{width:28px;height:28px;min-width:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;border:2px solid}
        .sms-dot.done{background:rgba(34,153,74,.18);border-color:rgba(34,153,74,.55);color:#4cdb80}
        .sms-dot.active{background:rgba(212,160,23,.15);border-color:var(--gold);color:var(--gold-l);animation:pulse 1.5s infinite}
        .sms-dot.pending{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.15);color:rgba(255,255,255,.25)}
        .sms-body{flex:1}
        .sms-t{font-size:.82rem;font-weight:600}
        .sms-d{font-size:.72rem;color:rgba(255,255,255,.38)}
        .sm-prog{margin-top:1.2rem}
        .sm-prog-l{display:flex;justify-content:space-between;font-size:.7rem;color:rgba(255,255,255,.45);margin-bottom:.4rem}
        .sm-prog-bar{height:6px;border-radius:999px;background:rgba(255,255,255,.08)}
        .sm-prog-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,#22994a,var(--gold));width:65%;transition:width 1s ease}
        .cl{display:flex;flex-direction:column;gap:.75rem;margin-top:1.4rem}
        .cl li{display:flex;align-items:flex-start;gap:.8rem;font-size:.87rem;color:rgba(255,255,255,.72)}
        .ck{width:20px;height:20px;min-width:20px;border-radius:50%;background:rgba(34,153,74,.18);border:1px solid rgba(34,153,74,.45);display:flex;align-items:center;justify-content:center;font-size:.62rem;color:#4cdb80;margin-top:1px}

        /* NOTICES */
        .bg-alt{background:rgba(0,0,0,.18)}
        .notice-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.1rem;margin-top:2.5rem}
        .nc{background:var(--w);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:1.4rem;transition:all .3s}
        .nc:hover{border-color:rgba(212,160,23,.3);background:rgba(255,255,255,.06);transform:translateY(-3px)}
        .nc-date{font-size:.68rem;color:var(--gold-l);font-weight:600;margin-bottom:.4rem}
        .nc-t{font-size:.88rem;font-weight:700;margin-bottom:.35rem}
        .nc-b{font-size:.76rem;color:rgba(255,255,255,.42);line-height:1.6}
        .ntag{display:inline-block;margin-top:.7rem;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:.18rem .55rem;border-radius:999px}
        .ntag.gold{background:rgba(212,160,23,.12);border:1px solid rgba(212,160,23,.3);color:var(--gold-l)}
        .ntag.green{background:rgba(34,153,74,.14);border:1px solid rgba(34,153,74,.38);color:#4cdb80}
        .ntag.blue{background:rgba(21,101,192,.15);border:1px solid rgba(21,101,192,.4);color:#90caf9}

        /* CTA */
        .cta-wrap{max-width:720px;margin:0 auto;background:linear-gradient(135deg,rgba(212,160,23,.1),rgba(34,153,74,.1));border:1px solid rgba(212,160,23,.22);border-radius:26px;padding:3.5rem 2rem;text-align:center;position:relative;overflow:hidden}
        .cta-wrap::before{content:'';position:absolute;top:-50%;left:50%;transform:translateX(-50%);width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(212,160,23,.18),transparent 70%)}
        .cta-wrap h2{font-family:'Outfit',sans-serif;font-size:clamp(1.4rem,2.8vw,2.1rem);font-weight:800;margin-bottom:.9rem}
        .cta-wrap p{color:rgba(255,255,255,.5);font-size:.9rem;margin-bottom:1.8rem}

        /* FOOTER */
        footer{position:relative;z-index:1;background:rgba(0,0,0,.38);border-top:1px solid rgba(212,160,23,.13)}
        .fi{max-width:1080px;margin:0 auto;padding:3rem 2rem 1.5rem;display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:2.5rem}
        .fb p{font-size:.78rem;color:rgba(255,255,255,.38);margin-top:.7rem;line-height:1.7}
        .fc-col h4{font-size:.78rem;font-weight:700;color:var(--gold-l);margin-bottom:.8rem;text-transform:uppercase;letter-spacing:.08em}
        .fc-col a{display:block;font-size:.75rem;color:rgba(255,255,255,.4);text-decoration:none;margin-bottom:.4rem;transition:color .2s}
        .fc-col a:hover{color:var(--gold-l)}
        .fbot{max-width:1080px;margin:0 auto;padding:1rem 2rem;border-top:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between;font-size:.7rem;color:rgba(255,255,255,.28)}

        /* REVEAL */
        .rv{opacity:0;transform:translateY(28px);transition:opacity .7s ease,transform .7s ease}
        .rv.vis{opacity:1;transform:translateY(0)}

        @media(max-width:768px){
            nav{padding:.7rem 1.2rem}
            .tracker-in{grid-template-columns:1fr;gap:2rem}
            .fi{grid-template-columns:1fr 1fr}
        }
        @media(max-width:480px){.fi{grid-template-columns:1fr}.si{padding:1.2rem .5rem}}
    </style>
</head>
<body>
<canvas id="bg-canvas"></canvas>
<div class="orb o1"></div>
<div class="orb o2"></div>
<div class="orb o3"></div>

<!-- NAV -->
<nav id="main-nav">
    <a href="#" class="nb">
        <div class="nl">🎫</div>
        <div class="nt">
            <span>LR Ticketing System</span>
            <span>Land Record · NIC · Govt. of India</span>
        </div>
    </a>
    <div class="nav-r">
        <a href="#how">How It Works</a>
        <a href="#types">Ticket Types</a>
        <a href="#notices">Notices</a>
        @if(Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="a-dash">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="a-login">Log In</a>
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="a-reg">Raise Ticket</a>
                @endif
            @endauth
        @endif
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-in">
        <div class="badge"><span class="bdot"></span>Digital India — Land Record Grievance Portal</div>
        <h1>Resolve Land Record Issues<br><span class="h1-gold">Raise a Ticket Today</span></h1>
        <p class="hero-sub">A unified grievance redressal system for all land record issues — mutation errors, ownership disputes, encumbrance queries, and more. Track your ticket in real time.</p>
        <div class="cta-row">
            @if(Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-p">🗂️ My Dashboard</a>
                @else
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-p">🎫 Raise a Ticket</a>
                    @endif
                    <a href="{{ route('login') }}" class="btn-s">🔐 Track My Ticket</a>
                @endauth
            @endif
        </div>
    </div>
    <div class="scroll-cue">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
        SCROLL
    </div>
</section>

<!-- STATS -->
<div class="stats">
    <div class="stats-in">
        <div class="si"><span class="si-n" data-target="24800">0</span><span class="si-l">Tickets Resolved</span></div>
        <div class="si"><span class="si-n" data-target="98">0</span><span class="si-l">% Resolution Rate</span></div>
        <div class="si"><span class="si-n" data-target="72">0</span><span class="si-l">Avg. Resolution (hrs)</span></div>
        <div class="si"><span class="si-n" data-target="38">0</span><span class="si-l">Districts Covered</span></div>
        <div class="si"><span class="si-n" data-target="4200">0</span><span class="si-l">Villages Online</span></div>
    </div>
</div>

<!-- HOW IT WORKS -->
<section id="how">
    <div class="sec rv">
        <span class="stag">How It Works</span>
        <div class="divl"></div>
        <h2 class="sec-h">Raise & Resolve in 4 Easy Steps</h2>
        <p class="sec-p">No office visits. No paperwork. Submit your land record grievance online and get it resolved by the concerned officer.</p>
        <div class="flow-grid">
            <div class="fc">
                <div class="fc-num">01</div>
                <div class="fc-icon">📝</div>
                <div class="fc-t">Register & Login</div>
                <div class="fc-d">Create your account with Aadhaar-linked mobile OTP for instant secure access to the portal.</div>
                <span class="fc-badge gold">5 Seconds</span>
            </div>
            <div class="fc">
                <div class="fc-num">02</div>
                <div class="fc-icon">🎫</div>
                <div class="fc-t">Raise a Ticket</div>
                <div class="fc-d">Select your issue type, fill in land parcel details, attach supporting documents and submit.</div>
                <span class="fc-badge blue">2 Minutes</span>
            </div>
            <div class="fc">
                <div class="fc-num">03</div>
                <div class="fc-icon">🔄</div>
                <div class="fc-t">Officer Review</div>
                <div class="fc-d">The concerned Revenue Officer reviews, verifies, and processes your ticket with real-time updates.</div>
                <span class="fc-badge red">7 Working Days</span>
            </div>
            <div class="fc">
                <div class="fc-num">04</div>
                <div class="fc-icon">✅</div>
                <div class="fc-t">Resolved & Closed</div>
                <div class="fc-d">Receive SMS + email notification on resolution. Download the official order directly from the portal.</div>
                <span class="fc-badge green">Instant Download</span>
            </div>
        </div>
    </div>
</section>

<!-- TICKET TYPES -->
<section id="types" style="background:rgba(0,0,0,.15)">
    <div class="sec">
        <div class="rv" style="text-align:center">
            <span class="stag">Ticket Categories</span>
            <div class="divl" style="margin:0 auto 1.3rem"></div>
            <h2 class="sec-h">What Issues Can You Report?</h2>
            <p class="sec-p" style="margin:0 auto">Select the category that matches your land record grievance and get assigned to the right officer.</p>
        </div>
        <div class="ttype-grid rv">
            <a href="#" class="tt"><span class="tt-icon">🔄</span><span class="tt-name">Mutation Error</span><span class="tt-cnt">Most Reported</span></a>
            <a href="#" class="tt"><span class="tt-icon">📜</span><span class="tt-name">RoR Correction</span><span class="tt-cnt">Khatoni Issues</span></a>
            <a href="#" class="tt"><span class="tt-icon">🗺️</span><span class="tt-name">Boundary Dispute</span><span class="tt-cnt">Map / Survey</span></a>
            <a href="#" class="tt"><span class="tt-icon">🏠</span><span class="tt-name">Ownership Dispute</span><span class="tt-cnt">Title Issues</span></a>
            <a href="#" class="tt"><span class="tt-icon">🔏</span><span class="tt-name">Encumbrance Query</span><span class="tt-cnt">Lien / Mortgage</span></a>
            <a href="#" class="tt"><span class="tt-icon">💰</span><span class="tt-name">Stamp Duty Issue</span><span class="tt-cnt">Registration</span></a>
            <a href="#" class="tt"><span class="tt-icon">📊</span><span class="tt-name">Land-Use Change</span><span class="tt-cnt">Conversion</span></a>
            <a href="#" class="tt"><span class="tt-icon">❓</span><span class="tt-name">Other / General</span><span class="tt-cnt">Any Query</span></a>
        </div>
    </div>
</section>

<!-- TICKET TRACKER SECTION -->
<section class="tracker">
    <div class="tracker-in rv">
        <div>
            <span class="stag">Live Ticket Tracking</span>
            <div class="divl"></div>
            <h2 class="sec-h">Track Your Ticket Status in Real Time</h2>
            <p class="sec-p">Every ticket gets a unique ID. Use it to check progress, view officer comments, and download the final order — 24×7.</p>
            <ul class="cl">
                <li><span class="ck">✓</span> Instant SMS & email alerts on every status change</li>
                <li><span class="ck">✓</span> View officer name and department assigned to your ticket</li>
                <li><span class="ck">✓</span> Escalate automatically if not resolved in 7 days</li>
                <li><span class="ck">✓</span> Download digitally-signed resolution order</li>
                <li><span class="ck">✓</span> Rate your experience after closure</li>
            </ul>
        </div>
        <div class="status-mock">
            <div class="sm-header">
                <span class="sm-tid">🎫 TKT-2026-08741</span>
                <span class="sm-status"><span class="sm-sdot"></span>In Progress</span>
            </div>
            <div class="sm-steps">
                <div class="sm-step">
                    <div class="sms-dot done">✓</div>
                    <div class="sms-body"><div class="sms-t">Ticket Submitted</div><div class="sms-d">07 Apr 2026, 10:32 AM</div></div>
                </div>
                <div class="sm-step">
                    <div class="sms-dot done">✓</div>
                    <div class="sms-body"><div class="sms-t">Assigned to Revenue Officer</div><div class="sms-d">07 Apr 2026, 11:05 AM</div></div>
                </div>
                <div class="sm-step">
                    <div class="sms-dot active">⚙</div>
                    <div class="sms-body"><div class="sms-t">Under Review — Tehsildar</div><div class="sms-d">07 Apr 2026, 02:15 PM · Active</div></div>
                </div>
                <div class="sm-step">
                    <div class="sms-dot pending">○</div>
                    <div class="sms-body"><div class="sms-t">Resolution Order</div><div class="sms-d">Pending</div></div>
                </div>
                <div class="sm-step">
                    <div class="sms-dot pending">○</div>
                    <div class="sms-body"><div class="sms-t">Ticket Closed</div><div class="sms-d">Pending</div></div>
                </div>
            </div>
            <div class="sm-prog">
                <div class="sm-prog-l"><span>Progress</span><span>65%</span></div>
                <div class="sm-prog-bar"><div class="sm-prog-fill"></div></div>
            </div>
        </div>
    </div>
</section>

<!-- NOTICES -->
<section id="notices" class="bg-alt">
    <div class="sec">
        <div class="rv">
            <span class="stag">Notices</span>
            <div class="divl"></div>
            <h2 class="sec-h">Latest Announcements</h2>
            <p class="sec-p">Important updates from the Land Record Department and NIC.</p>
        </div>
        <div class="notice-grid rv">
            <div class="nc">
                <div class="nc-date">07 April 2026</div>
                <div class="nc-t">Auto-Escalation Feature Now Live</div>
                <div class="nc-b">Tickets not resolved within 7 working days will now auto-escalate to the District Collector's office.</div>
                <span class="ntag gold">New Feature</span>
            </div>
            <div class="nc">
                <div class="nc-date">01 April 2026</div>
                <div class="nc-t">Mutation Tickets — Faster Processing</div>
                <div class="nc-b">Processing time for mutation-related tickets reduced to 5 working days across all districts.</div>
                <span class="ntag green">Improvement</span>
            </div>
            <div class="nc">
                <div class="nc-date">25 March 2026</div>
                <div class="nc-t">System Maintenance — 28 March</div>
                <div class="nc-b">Portal will be under maintenance 28 March, 01:00–05:00 IST. Please raise tickets before or after.</div>
                <span class="ntag blue">Maintenance</span>
            </div>
            <div class="nc">
                <div class="nc-date">15 March 2026</div>
                <div class="nc-t">Mobile App Now on Play Store</div>
                <div class="nc-b">Raise and track land record tickets from your smartphone with the new Android app.</div>
                <span class="ntag gold">App Update</span>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section>
    <div class="sec" style="text-align:center">
        <div class="cta-wrap rv">
            <h2>Have a Land Record Issue?<br>Don't Wait — Raise a Ticket Now</h2>
            <p>Get a resolution within 7 working days. No office visit required.</p>
            <div style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap">
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-p">🎫 Raise a Ticket Free</a>
                @endif
                @if(Route::has('login'))
                    @guest<a href="{{ route('login') }}" class="btn-s">🔐 Login to Track</a>@endguest
                @endif
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="fi">
        <div class="fb">
            <div style="display:flex;align-items:center;gap:.7rem">
                <div class="nl" style="width:38px;height:38px;font-size:1.1rem">🎫</div>
                <span style="font-family:'Outfit',sans-serif;font-weight:800;font-size:.95rem;background:linear-gradient(90deg,var(--gold-l),#fff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">LR Ticketing System</span>
            </div>
            <p>A grievance redressal portal for Land Record issues. Built by NIC under the Digital India initiative, Ministry of Electronics &amp; IT.</p>
        </div>
        <div class="fc-col">
            <h4>Services</h4>
            <a href="#">Raise Ticket</a>
            <a href="#">Track Status</a>
            <a href="#">View RoR</a>
            <a href="#">Mutation Status</a>
            <a href="#">Encumbrance Cert.</a>
        </div>
        <div class="fc-col">
            <h4>Quick Links</h4>
            <a href="#">About Portal</a>
            <a href="#">User Guide</a>
            <a href="#">FAQs</a>
            <a href="#">Officer Login</a>
            <a href="#">RTI</a>
        </div>
        <div class="fc-col">
            <h4>Legal</h4>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
            <a href="#">Disclaimer</a>
            <a href="#">Accessibility</a>
            <a href="#">Contact Us</a>
        </div>
    </div>
    <div class="fbot">
        <span>© {{ date('Y') }} National Informatics Centre — Government of India. All rights reserved.</span>
        <span>🇮🇳 Digital India</span>
    </div>
</footer>

<script>
// Particles
const cv=document.getElementById('bg-canvas'),cx=cv.getContext('2d');
function rsz(){cv.width=innerWidth;cv.height=innerHeight}rsz();addEventListener('resize',rsz);
const pts=Array.from({length:100},()=>({
    x:Math.random()*innerWidth,y:Math.random()*innerHeight,
    vx:(Math.random()-.5)*.3,vy:(Math.random()-.5)*.3,
    r:Math.random()*1.4+.3,
    a:Math.random()*.3+.05,
    g:Math.random()>.55
}));
function draw(){
    cx.clearRect(0,0,cv.width,cv.height);
    pts.forEach((p,i)=>{
        p.x+=p.vx;p.y+=p.vy;
        if(p.x<0||p.x>cv.width)p.vx*=-1;
        if(p.y<0||p.y>cv.height)p.vy*=-1;
        cx.beginPath();cx.arc(p.x,p.y,p.r,0,Math.PI*2);
        cx.fillStyle=p.g?`rgba(212,160,23,${p.a})`:`rgba(255,255,255,${p.a*.5})`;
        cx.fill();
        for(let j=i+1;j<pts.length;j++){
            const dx=p.x-pts[j].x,dy=p.y-pts[j].y,d=Math.sqrt(dx*dx+dy*dy);
            if(d<90){cx.beginPath();cx.strokeStyle=`rgba(212,160,23,${.1*(1-d/90)})`;cx.lineWidth=.5;cx.moveTo(p.x,p.y);cx.lineTo(pts[j].x,pts[j].y);cx.stroke()}
        }
    });
    requestAnimationFrame(draw);
}
draw();

// Navbar shadow
addEventListener('scroll',()=>{document.getElementById('main-nav').style.boxShadow=scrollY>20?'0 8px 36px rgba(0,0,0,.5)':'none'});

// Reveal
const obs=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('vis');obs.unobserve(e.target)}}),{threshold:.1});
document.querySelectorAll('.rv').forEach(el=>obs.observe(el));

// Counters
function cnt(el){
    const t=parseInt(el.dataset.target);
    let suf=t>=1000?'K+':'+';
    if(el.dataset.target==='98'||el.dataset.target==='72')suf=el.dataset.target==='98'?'%':'hrs';
    const disp=t>=1000?Math.round(t/1000):t;
    let cur=0;const step=disp/70;
    const iv=setInterval(()=>{cur+=step;if(cur>=disp){cur=disp;clearInterval(iv)}el.textContent=Math.floor(cur)+(t>=1000&&el.dataset.target!=='4200'?'K':t===4200?'':'')+suf},22);
}
// fix suffix display
const snums=document.querySelectorAll('.si-n');
const stats=new IntersectionObserver(es=>{
    if(es[0].isIntersecting){
        snums.forEach(el=>{
            const t=parseInt(el.dataset.target);
            let suf='+',disp=t;
            if(t>=10000){disp=t/1000;suf='K+'}
            if(el.dataset.target==='98')suf='%';
            if(el.dataset.target==='72')suf='hrs';
            if(el.dataset.target==='38')suf='';
            let cur=0;const step=disp/70;
            const iv=setInterval(()=>{cur+=step;if(cur>=disp){cur=disp;clearInterval(iv)}
                el.textContent=(disp%1===0?Math.floor(cur):cur.toFixed(1))+(t>=10000?'K+':suf);
            },22);
        });
        stats.unobserve(es[0].target);
    }
},{threshold:.5});
const sb=document.querySelector('.stats');
if(sb)stats.observe(sb);
</script>
</body>
</html>
