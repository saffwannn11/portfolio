"""Builds five homepage design options for Prime Recruitments as static HTML.

Run:  python3 build.py   (then render with Playwright, see shoot.js)
"""
import os

HERE = os.path.dirname(os.path.abspath(__file__))
IMG = "../prime-recruitments/assets/img/"

ICONS = {
    "users": '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
    "crown": '<path d="m2 18 2-12 5 5 3-7 3 7 5-5 2 12z"/><path d="M4 21h16"/>',
    "code": '<path d="m16 18 6-6-6-6M8 6l-6 6 6 6"/>',
    "clock": '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    "layers": '<path d="m12 2 10 5-10 5L2 7z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
    "zap": '<path d="M13 2 3 14h9l-1 8 10-12h-9z"/>',
    "heart": '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>',
    "globe": '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18 14 14 0 0 1 0-18"/>',
    "arrow": '<path d="M5 12h14M13 6l6 6-6 6"/>',
    "check": '<path d="m5 12 5 5L20 7"/>',
    "search": '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
    "pin": '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
    "phone": '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
    "mail": '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    "brief": '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
    "star": '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
    "shield": '<path d="M12 3 4 6v6c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V6z"/><path d="m9 12 2 2 4-4"/>',
    "quote": '<path d="M7 7h4v4c0 3-2 5-4 6M15 7h4v4c0 3-2 5-4 6"/>',
}


def ic(name, size=22, sw=1.8):
    return (f'<svg width="{size}" height="{size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
            f'stroke-width="{sw}" stroke-linecap="round" stroke-linejoin="round">{ICONS[name]}</svg>')


SERVICES = [
    ("users", "Permanent Recruitment", "Long-term hires for core roles, screened and interview-ready."),
    ("crown", "Executive Search", "Confidential headhunting for CXO, VP and director roles."),
    ("code", "IT Recruitment", "Developers, data, cloud and product talent, technically screened."),
    ("clock", "Contract Staffing", "Flexible, compliant workforce on our payroll."),
    ("layers", "RPO", "An embedded recruiting team that scales with you."),
    ("zap", "Bulk Hiring", "High-volume hiring drives for BPO, retail, sales and logistics."),
    ("heart", "Healthcare Recruitment", "Doctors, nurses, allied health and hospital management."),
    ("globe", "Overseas Recruitment", "Indian talent for employers in the Gulf, Europe and beyond."),
]
STATS = [("48", "hrs", "Target time to first shortlist"), ("18", "+", "Industries & functions"),
         ("3", "", "Screening stages"), ("100", "%", "Candidates interviewed")]
STEPS = [("Brief & calibrate", "A 30-minute intake call to define the role, skills, salary band and culture."),
         ("Search & map", "We search our network, job boards and passive talent and map the market."),
         ("Screen & shortlist", "Every candidate is interviewed and skills-checked before you see them."),
         ("Offer & onboard", "We manage interviews, negotiate the offer and follow up through joining.")]
INDUSTRIES = ["Technology", "BFSI & Fintech", "Healthcare", "Manufacturing", "Retail & E-commerce", "Logistics", "FMCG", "Overseas & Gulf"]
QUOTE = ("Prime sent four candidates for a role we had struggled with for two months. We hired two of them. "
         "The shortlist notes alone saved our managers hours.", "Head of Talent", "SaaS company, Bangalore")
NAV = ["Home", "Services", "Corporate Gifting", "Industries", "Jobs", "About", "Contact"]
GIFTS = ["Premium festive hampers", "Branded bags & travel kits", "Executive desk accessories", "Customised drinkware"]


def head(title, fonts, css):
    return f"""<!doctype html><html lang="en"><head><meta charset="utf-8"><title>{title}</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?{fonts}&display=swap" rel="stylesheet">
<style>*{{box-sizing:border-box;margin:0;padding:0}}img{{display:block;max-width:100%}}a{{color:inherit;text-decoration:none}}ul{{list-style:none}}
.wrap{{width:1200px;margin:0 auto}}{css}</style></head><body>"""


def nav_links(active_cls="on"):
    return "".join(f'<a class="{active_cls if i == 0 else ""}">{n}</a>' for i, n in enumerate(NAV))


# ---------------------------------------------------------------------------
# 1. CORPORATE TRUST — navy, gold, white. Split hero, photo with stat card.
# ---------------------------------------------------------------------------
def design1():
    css = """
body{font-family:'Inter',sans-serif;color:#0B2545;background:#fff;font-size:16px;line-height:1.65}
h1,h2,h3{font-family:'Manrope',sans-serif;letter-spacing:-.02em}
.top{background:#0B2545;color:#c9d4e6;font-size:13px}.top .wrap{display:flex;justify-content:space-between;padding:10px 0}
header{border-bottom:1px solid #e6eaf2}header .wrap{display:flex;align-items:center;justify-content:space-between;height:84px}
.logo{display:flex;align-items:center;gap:10px}.logo img{width:44px}.logo b{font-family:Manrope;font-size:20px;letter-spacing:.14em}.logo small{display:block;font-size:9px;letter-spacing:.34em;color:#A4801A;font-weight:700}
nav{display:flex;gap:30px;font-size:15px;font-weight:500;color:#3b4a66}nav .on{color:#0B2545;font-weight:700;box-shadow:inset 0 -2px #C9A227;padding-bottom:4px}
.btn{display:inline-flex;align-items:center;gap:10px;padding:15px 28px;border-radius:6px;font-weight:700;font-size:15px}
.gold{background:#C9A227;color:#0B2545}.navy{background:#0B2545;color:#fff}.ghost{border:1.5px solid #0B2545;color:#0B2545}
.hero{padding:80px 0 90px;background:linear-gradient(180deg,#F5F7FB,#fff)}.hero .wrap{display:grid;grid-template-columns:1.05fr .95fr;gap:70px;align-items:center}
.kick{display:inline-block;font-size:13px;font-weight:700;letter-spacing:.14em;color:#A4801A;text-transform:uppercase;margin-bottom:18px}
.hero h1{font-size:60px;line-height:1.08;font-weight:800}.hero h1 span{color:#A4801A}
.hero p.lead{margin:22px 0 34px;font-size:18.5px;color:#4a5874;max-width:540px}.acts{display:flex;gap:14px}
.ticks{display:flex;gap:26px;margin-top:34px;font-size:14.5px;font-weight:600}.ticks span{display:flex;gap:8px;align-items:center}.ticks svg{color:#C9A227}
.ph{position:relative}.ph img{width:100%;height:540px;object-fit:cover;border-radius:10px}
.ph .card{position:absolute;left:-40px;bottom:40px;background:#0B2545;color:#fff;padding:24px 28px;border-radius:10px;box-shadow:0 30px 60px -20px rgba(11,37,69,.5)}
.ph .card b{display:block;font-family:Manrope;font-size:44px;color:#C9A227;line-height:1}.ph .card small{color:#c9d4e6}
.ph .card2{position:absolute;right:-24px;top:40px;background:#fff;padding:16px 20px;border-radius:10px;box-shadow:0 20px 50px -20px rgba(11,37,69,.4);display:flex;gap:12px;align-items:center;font-weight:700;font-size:14px}
.ph .card2 i{width:38px;height:38px;border-radius:50%;background:#C9A227;display:grid;place-items:center;color:#0B2545}
.strip{border-top:1px solid #e6eaf2;border-bottom:1px solid #e6eaf2}.strip .wrap{display:flex;justify-content:space-between;padding:26px 0;color:#6b7891;font-weight:700;font-size:15px}
section.s{padding:100px 0}.sh{display:flex;justify-content:space-between;align-items:end;margin-bottom:50px}.sh h2{font-size:42px;font-weight:800;max-width:560px;line-height:1.15}.sh p{max-width:420px;color:#4a5874}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
.svc{padding:30px;border:1px solid #e6eaf2;border-radius:10px}.svc i{display:grid;place-items:center;width:52px;height:52px;border-radius:8px;background:#F5F1E3;color:#A4801A;margin-bottom:18px}
.svc h3{font-size:19px;margin-bottom:8px}.svc p{font-size:14.5px;color:#5a6782}.svc a{display:inline-flex;gap:6px;margin-top:14px;font-weight:700;font-size:14px;color:#0B2545}
.svc.hl{background:#0B2545;color:#fff;border-color:#0B2545}.svc.hl p{color:#c9d4e6}.svc.hl i{background:#C9A227;color:#0B2545}.svc.hl a{color:#C9A227}
.about{background:#F5F7FB}.about .wrap{display:grid;grid-template-columns:1fr 1fr;gap:70px;align-items:center}.about img{height:480px;width:100%;object-fit:cover;border-radius:10px}
.about h2{font-size:42px;line-height:1.15;font-weight:800;margin-bottom:18px}.about p{color:#4a5874;margin-bottom:16px}
.about li{display:flex;gap:10px;margin:12px 0;font-weight:600}.about li i{width:24px;height:24px;border-radius:50%;background:#C9A227;color:#0B2545;display:grid;place-items:center}
.stats{background:#0B2545;color:#fff}.stats .wrap{display:grid;grid-template-columns:repeat(4,1fr);padding:60px 0}
.stats div{text-align:center;border-left:1px solid rgba(255,255,255,.15)}.stats div:first-child{border:0}.stats b{font-family:Manrope;font-size:52px;color:#fff}.stats b em{font-style:normal;color:#C9A227;font-size:28px}.stats p{color:#c9d4e6;font-size:14.5px}
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:30px;counter-reset:s}.steps div{border-top:3px solid #C9A227;padding-top:22px}.steps b{font-family:Manrope;color:#A4801A;font-size:15px}.steps h3{font-size:20px;margin:6px 0 8px}.steps p{font-size:14.5px;color:#5a6782}
.quote{background:#F5F7FB}.quote .wrap{text-align:center;max-width:900px}.quote p.q{font-family:Manrope;font-size:28px;line-height:1.45;font-weight:600}.quote small{display:block;margin-top:22px;color:#5a6782}.quote svg{color:#C9A227;margin:0 auto 18px}
.cta .wrap{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;background:#0B2545;border-radius:14px;padding:60px;color:#fff}
.cta h2{font-size:40px;line-height:1.15}.cta p{color:#c9d4e6;margin-top:14px}.form{background:#fff;border-radius:10px;padding:30px;display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form span{border:1px solid #dfe5ef;border-radius:6px;padding:14px;color:#8a95ab;font-size:14px}.form .w{grid-column:1/-1}.form .btn{grid-column:1/-1;justify-content:center}
footer{background:#081B33;color:#c9d4e6;padding:70px 0 30px;margin-top:100px}footer .wrap{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:40px}footer h4{color:#fff;font-family:Manrope;margin-bottom:16px}footer li{margin:8px 0;font-size:14.5px}
.copy{border-top:1px solid rgba(255,255,255,.1);margin-top:50px;padding-top:22px;font-size:13px;grid-column:1/-1}
"""
    svc = "".join(f'<div class="svc{" hl" if i == 0 else ""}"><i>{ic(a)}</i><h3>{t}</h3><p>{d}</p><a>Learn more {ic("arrow",16)}</a></div>' for i, (a, t, d) in enumerate(SERVICES))
    stats = "".join(f'<div><b>{n}<em>{s}</em></b><p>{l}</p></div>' for n, s, l in STATS)
    steps = "".join(f'<div><b>STEP 0{i+1}</b><h3>{t}</h3><p>{d}</p></div>' for i, (t, d) in enumerate(STEPS))
    return head("Option 1 — Corporate Trust", "family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600;700", css) + f"""
<div class="top"><div class="wrap"><span>Mon – Sat, 9:30 AM – 6:30 PM IST</span><span>hr@primerecruitments.com · +91 82912 22211 · WhatsApp</span></div></div>
<header><div class="wrap"><div class="logo"><img src="{IMG}logo-mark.png"><div><b>PRIME</b><small>RECRUITMENTS</small></div></div><nav>{nav_links()}</nav><a class="btn gold">Hire Talent {ic("arrow",16)}</a></div></header>
<section class="hero"><div class="wrap"><div><span class="kick">Pan India Recruitment Solutions</span><h1>The right people.<br>Hired <span>faster.</span></h1>
<p class="lead">Prime Recruitments finds, interviews and shortlists the people your business needs — permanent, contract, executive and volume hiring across India.</p>
<div class="acts"><a class="btn navy">Hire talent {ic("arrow",16)}</a><a class="btn ghost">Find a job</a></div>
<div class="ticks"><span>{ic("check",18,2.4)} Every candidate interviewed</span><span>{ic("check",18,2.4)} Shortlists in 48–72 hrs</span></div></div>
<div class="ph"><img src="{IMG}photo-tower.webp"><div class="card"><b>48 hrs</b><small>Target time to first shortlist</small></div><div class="card2"><i>{ic("shield",18)}</i>Replacement guarantee</div></div></div></section>
<div class="strip"><div class="wrap">{"".join(f"<span>{x}</span>" for x in INDUSTRIES)}</div></div>
<section class="s"><div class="wrap"><div class="sh"><h2>Recruitment services built around your hiring goals</h2><p>From a single leadership search to hundreds of frontline hires — one accountable partner.</p></div><div class="grid">{svc}</div></div></section>
<section class="s about"><div class="wrap"><img src="{IMG}photo-stairs.webp"><div><span class="kick">Who we are</span><h2>We don't send CVs. We send people worth meeting.</h2><p>Every profile we share has been interviewed, skills-checked and salary-aligned, so your managers spend their time hiring, not sifting.</p>
<ul><li><i>{ic("check",14,2.6)}</i>Function-specialist consultants</li><li><i>{ic("check",14,2.6)}</i>Salary benchmarks with every brief</li><li><i>{ic("check",14,2.6)}</i>Replacement guarantee on permanent hires</li></ul><br><a class="btn navy">About Prime {ic("arrow",16)}</a></div></div></section>
<div class="stats"><div class="wrap">{stats}</div></div>
<section class="s"><div class="wrap"><div class="sh"><h2>From brief to signed offer in four steps</h2></div><div class="steps">{steps}</div></div></section>
<section class="s quote"><div class="wrap">{ic("quote",40)}<p class="q">“{QUOTE[0]}”</p><small><b>{QUOTE[1]}</b> · {QUOTE[2]}</small></div></section>
<section class="s cta"><div class="wrap"><div><span class="kick">Start a conversation</span><h2>Tell us who you need. We'll take it from there.</h2><p>A recruiter replies within one working day — no obligation.</p></div>
<div class="form"><span>Full name</span><span>Phone</span><span class="w">Email</span><span class="w">Role you are hiring for</span><a class="btn gold">Send enquiry {ic("arrow",16)}</a></div></div></section>
<footer><div class="wrap"><div><div class="logo"><img src="{IMG}logo-mark-light.png"><div><b style="color:#fff">PRIME</b><small>RECRUITMENTS</small></div></div><p style="margin-top:18px;font-size:14.5px">Pan India recruitment and corporate gifting partner.</p></div>
<div><h4>Services</h4><ul>{"".join(f"<li>{s[1]}</li>" for s in SERVICES[:5])}</ul></div><div><h4>Company</h4><ul><li>About</li><li>Corporate Gifting</li><li>Industries</li><li>Jobs</li><li>Contact</li></ul></div>
<div><h4>Contact</h4><ul><li>hr@primerecruitments.com</li><li>+91 82912 22211</li><li>Chat on WhatsApp</li></ul></div><div class="copy">© 2026 Prime Recruitments. All rights reserved.</div></div></footer></body></html>"""


# ---------------------------------------------------------------------------
# 2. EDITORIAL BOLD — black, cream, coral. Big type, magazine layout.
# ---------------------------------------------------------------------------
def design2():
    css = """
body{font-family:'Inter',sans-serif;background:#EDEBE4;color:#111;font-size:16px;line-height:1.6}
h1,h2,h3,.big{font-family:'Space Grotesk',sans-serif;letter-spacing:-.04em}
header .wrap{display:flex;align-items:center;justify-content:space-between;height:90px;border-bottom:1.5px solid #111}
.logo{font-family:'Space Grotesk';font-weight:700;font-size:22px;letter-spacing:.02em;display:flex;align-items:center;gap:10px}.logo img{width:38px}
nav{display:flex;gap:28px;font-size:15px;font-weight:500}nav .on{text-decoration:underline;text-underline-offset:6px;text-decoration-color:#F43F5E;text-decoration-thickness:2px}
.btn{display:inline-flex;align-items:center;gap:10px;padding:16px 26px;border-radius:999px;font-weight:600;font-size:15px}.blk{background:#111;color:#fff}.cor{background:#F43F5E;color:#111}.out{border:1.5px solid #111}
.hero{padding:70px 0 0}.hero h1{font-size:118px;line-height:.95;font-weight:700}.hero h1 em{font-style:normal;color:#F43F5E}
.hrow{display:grid;grid-template-columns:1fr 1fr;gap:60px;margin:46px 0 50px;align-items:end}.hrow p{font-size:20px;max-width:520px}.hrow .acts{display:flex;gap:12px;justify-content:flex-end}
.himg{height:440px;overflow:hidden;border-radius:24px;position:relative}.himg img{width:100%;height:100%;object-fit:cover;filter:grayscale(1) contrast(1.1)}
.himg .tag{position:absolute;left:30px;bottom:30px;background:#F43F5E;color:#111;padding:14px 20px;border-radius:999px;font-weight:700}
.nums{display:grid;grid-template-columns:repeat(4,1fr);border-top:1.5px solid #111;border-bottom:1.5px solid #111;margin-top:70px}
.nums div{padding:34px 26px;border-left:1.5px solid #111}.nums div:first-child{border-left:0}.nums b{font-family:'Space Grotesk';font-size:72px;line-height:1;font-weight:700}.nums b em{font-style:normal;color:#F43F5E}.nums p{margin-top:8px}
section.s{padding:110px 0}.lab{font-family:'Space Grotesk';font-weight:600;font-size:14px;letter-spacing:.1em;text-transform:uppercase;display:flex;gap:10px;align-items:center;margin-bottom:22px}.lab:before{content:"";width:10px;height:10px;background:#F43F5E;border-radius:50%}
.s h2{font-size:64px;line-height:1;font-weight:700;max-width:900px}
.rows{margin-top:56px;border-top:1.5px solid #111}.row{display:grid;grid-template-columns:90px 1fr 1.1fr 60px;align-items:center;padding:28px 0;border-bottom:1.5px solid #111}
.row b{font-family:'Space Grotesk';font-size:20px;color:#F43F5E}.row h3{font-size:34px;font-weight:600}.row p{color:#3d3d3d}.row i{width:52px;height:52px;border-radius:50%;border:1.5px solid #111;display:grid;place-items:center}
.row.on{background:#111;color:#EDEBE4;padding-left:20px;padding-right:20px;border-radius:16px}.row.on p{color:#c8c4b8}.row.on i{background:#F43F5E;border-color:#F43F5E;color:#111}
.dark{background:#111;color:#EDEBE4}.split{display:grid;grid-template-columns:1fr 1fr;gap:70px;align-items:center}.split img{height:520px;width:100%;object-fit:cover;border-radius:24px;filter:grayscale(1)}
.dark h2{color:#EDEBE4}.dark p{color:#c8c4b8;font-size:18px;margin:22px 0}.chips{display:flex;flex-wrap:wrap;gap:10px;margin-top:26px}.chips span{border:1.5px solid #EDEBE4;border-radius:999px;padding:10px 18px;font-weight:500}
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:56px}.steps div{background:#fff;border-radius:22px;padding:30px}.steps b{font-family:'Space Grotesk';font-size:56px;color:#F43F5E;line-height:1}.steps h3{font-size:24px;margin:16px 0 8px}
.q{font-family:'Space Grotesk';font-size:46px;line-height:1.15;font-weight:600;max-width:1050px}.q em{font-style:normal;color:#F43F5E}.who{margin-top:30px;display:flex;gap:14px;align-items:center}.who i{width:52px;height:52px;border-radius:50%;background:#111;color:#fff;display:grid;place-items:center;font-weight:700;font-style:normal}
.cta{background:#F43F5E}.cta h2{font-size:96px}.cta .wrap{display:flex;justify-content:space-between;align-items:end}
footer{background:#111;color:#c8c4b8;padding:80px 0 30px}footer .cols{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px}footer h4{color:#fff;font-family:'Space Grotesk';margin-bottom:14px}footer li{margin:8px 0}
.mega{font-family:'Space Grotesk';font-weight:700;font-size:300px;line-height:.8;letter-spacing:-.06em;color:#EDEBE4;margin:60px 0 20px}.mega em{font-style:normal;color:#F43F5E}
"""
    rows = "".join(f'<div class="row{" on" if i == 1 else ""}"><b>0{i+1}</b><h3>{t}</h3><p>{d}</p><i>{ic("arrow",20)}</i></div>' for i, (a, t, d) in enumerate(SERVICES[:6]))
    nums = "".join(f'<div><b>{n}<em>{s}</em></b><p>{l}</p></div>' for n, s, l in STATS)
    steps = "".join(f'<div><b>0{i+1}</b><h3>{t}</h3><p>{d}</p></div>' for i, (t, d) in enumerate(STEPS))
    return head("Option 2 — Editorial Bold", "family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600", css) + f"""
<header><div class="wrap"><div class="logo"><img src="{IMG}logo-mark-dark.png">PRIME RECRUITMENTS</div><nav>{nav_links()}</nav><a class="btn blk">Hire Talent {ic("arrow",16)}</a></div></header>
<section class="hero"><div class="wrap"><h1>We find the people<br>who <em>move</em> your<br>business forward.</h1>
<div class="hrow"><p>Pan-India recruitment for permanent, contract, executive and volume hiring — every candidate interviewed before you meet them.</p><div class="acts"><a class="btn blk">Hire talent {ic("arrow",16)}</a><a class="btn out">Browse jobs</a></div></div>
<div class="himg"><img src="{IMG}photo-windows.webp"><span class="tag">Shortlists in 48–72 hours</span></div>
<div class="nums">{nums}</div></div></section>
<section class="s"><div class="wrap"><div class="lab">Services</div><h2>Eight ways to hire. One accountable partner.</h2><div class="rows">{rows}</div></div></section>
<section class="s dark"><div class="wrap split"><img src="{IMG}photo-stairs.webp"><div><div class="lab">Who we are</div><h2>We don't send CVs. We send people worth meeting.</h2><p>Function-specialist consultants, salary benchmarks with every brief and a replacement guarantee on permanent hires.</p>
<div class="chips">{"".join(f"<span>{x}</span>" for x in INDUSTRIES)}</div></div></div></section>
<section class="s"><div class="wrap"><div class="lab">How it works</div><h2>Brief to signed offer in four steps.</h2><div class="steps">{steps}</div></div></section>
<section class="s" style="padding-top:0"><div class="wrap"><div class="lab">Client stories</div><p class="q">“Prime sent four candidates for a role we had struggled with for two months. <em>We hired two of them.</em>”</p><div class="who"><i>H</i><div><b>{QUOTE[1]}</b><br><small>{QUOTE[2]}</small></div></div></div></section>
<section class="s cta"><div class="wrap"><h2>Let's build<br>your team.</h2><a class="btn blk">Start a conversation {ic("arrow",16)}</a></div></section>
<footer><div class="wrap"><div class="cols"><div><div class="logo" style="color:#fff"><img src="{IMG}logo-mark-light.png">PRIME</div><p style="margin-top:16px;max-width:320px">Pan India recruitment and corporate gifting.</p></div><div><h4>Services</h4><ul>{"".join(f"<li>{s[1]}</li>" for s in SERVICES[:4])}</ul></div><div><h4>Company</h4><ul><li>About</li><li>Jobs</li><li>Corporate Gifting</li><li>Contact</li></ul></div><div><h4>Contact</h4><ul><li>hr@primerecruitments.com</li><li>+91 82912 22211</li></ul></div></div>
<div class="mega">PRIME<em>.</em></div><small>© 2026 Prime Recruitments</small></div></footer></body></html>"""


# ---------------------------------------------------------------------------
# 3. MODERN GRADIENT — indigo/violet, soft lavender, rounded cards, search box.
# ---------------------------------------------------------------------------
def design3():
    css = """
body{font-family:'Plus Jakarta Sans',sans-serif;background:#fff;color:#1E1B4B;font-size:16px;line-height:1.65}
h1,h2,h3{letter-spacing:-.03em}
.hero{background:radial-gradient(900px 500px at 15% 0%,#E0E7FF,transparent 60%),radial-gradient(800px 500px at 90% 10%,#F3E8FF,transparent 60%),#FAFAFF;padding-bottom:90px}
header .wrap{display:flex;align-items:center;justify-content:space-between;height:88px}.logo{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px}.logo img{width:40px}
nav{display:flex;gap:6px;background:#fff;padding:6px;border-radius:999px;box-shadow:0 10px 30px -15px rgba(79,70,229,.35);font-size:14.5px;font-weight:600}nav a{padding:9px 16px;border-radius:999px;color:#4c4a73}nav .on{background:#1E1B4B;color:#fff}
.btn{display:inline-flex;align-items:center;gap:10px;padding:15px 26px;border-radius:14px;font-weight:700;font-size:15px}.grad{background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff;box-shadow:0 16px 30px -14px rgba(124,58,237,.7)}.wh{background:#fff;color:#1E1B4B;box-shadow:0 10px 30px -18px rgba(30,27,75,.4)}
.hc{text-align:center;padding-top:70px}.pill{display:inline-flex;gap:8px;align-items:center;background:#fff;border:1px solid #E0E7FF;border-radius:999px;padding:8px 16px;font-size:13.5px;font-weight:700;color:#4F46E5}
.hc h1{font-size:72px;line-height:1.05;font-weight:800;margin:24px auto 0;max-width:950px}.hc h1 span{background:linear-gradient(90deg,#4F46E5,#A855F7);-webkit-background-clip:text;color:transparent}
.hc p{font-size:19px;color:#5b5a80;max-width:640px;margin:20px auto 0}
.search{margin:40px auto 0;width:880px;background:#fff;border-radius:22px;box-shadow:0 30px 70px -30px rgba(79,70,229,.45);padding:12px}
.tabs{display:flex;gap:6px;padding:4px 4px 12px}.tabs span{padding:9px 18px;border-radius:12px;font-weight:700;font-size:14px;color:#5b5a80}.tabs .on{background:#EEF2FF;color:#4F46E5}
.fields{display:grid;grid-template-columns:1.3fr 1fr auto;gap:10px}.fields div{display:flex;gap:10px;align-items:center;background:#F7F7FD;border-radius:14px;padding:16px;color:#8a89a8;text-align:left}
.trust{display:flex;justify-content:center;gap:30px;margin-top:30px;font-size:14px;font-weight:600;color:#5b5a80}.trust span{display:flex;gap:8px;align-items:center}.trust svg{color:#7C3AED}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:-50px;position:relative}.stats div{background:#fff;border-radius:20px;padding:26px;box-shadow:0 20px 50px -30px rgba(30,27,75,.4);text-align:center}
.stats b{font-size:44px;font-weight:800;background:linear-gradient(135deg,#4F46E5,#A855F7);-webkit-background-clip:text;color:transparent}.stats p{font-size:14px;color:#5b5a80}
section.s{padding:100px 0}.ch{text-align:center;max-width:720px;margin:0 auto 54px}.ch small{color:#7C3AED;font-weight:800;letter-spacing:.12em;font-size:13px}.ch h2{font-size:46px;font-weight:800;margin-top:10px;line-height:1.1}.ch p{color:#5b5a80;margin-top:12px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}.card{border-radius:22px;padding:28px;background:#fff;border:1px solid #EEF0FB}.card i{width:54px;height:54px;border-radius:16px;display:grid;place-items:center;color:#fff;background:linear-gradient(135deg,#4F46E5,#7C3AED);margin-bottom:18px}
.card h3{font-size:19px;margin-bottom:6px}.card p{font-size:14.5px;color:#5b5a80}.card.alt{background:#1E1B4B;color:#fff}.card.alt p{color:#c4c2e8}.card.alt i{background:#fff;color:#4F46E5}
.feat{background:#FAFAFF}.feat .wrap{display:grid;grid-template-columns:1fr 1fr;gap:70px;align-items:center}.imgbox{position:relative}.imgbox img{width:100%;height:500px;object-fit:cover;border-radius:28px}
.float{position:absolute;background:#fff;border-radius:18px;padding:16px 18px;box-shadow:0 24px 50px -24px rgba(30,27,75,.45);display:flex;gap:12px;align-items:center;font-size:14px}.float i{width:40px;height:40px;border-radius:12px;background:#EEF2FF;color:#4F46E5;display:grid;place-items:center}
.f1{left:-30px;top:40px}.f2{right:-30px;bottom:50px}.feat h2{font-size:44px;line-height:1.1;font-weight:800}.feat p{color:#5b5a80;margin:18px 0}.feat li{display:flex;gap:12px;margin:14px 0;font-weight:600}.feat li i{color:#7C3AED}
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}.steps div{border-radius:22px;padding:28px;background:#FAFAFF;position:relative}.steps b{display:grid;place-items:center;width:44px;height:44px;border-radius:12px;background:#1E1B4B;color:#fff;margin-bottom:16px}.steps h3{font-size:19px;margin-bottom:6px}.steps p{font-size:14.5px;color:#5b5a80}
.tst{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}.tst div{border-radius:22px;padding:28px;border:1px solid #EEF0FB}.tst .stars{color:#F59E0B;display:flex;gap:2px;margin-bottom:12px}.tst p{font-size:15px}.tst small{display:block;margin-top:14px;color:#5b5a80}
.cta .wrap{border-radius:32px;padding:70px;background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff;display:flex;justify-content:space-between;align-items:center}.cta h2{font-size:44px;max-width:620px;line-height:1.1}.cta p{color:#e0dcff;margin-top:10px}
footer{background:#1E1B4B;color:#c4c2e8;padding:70px 0 30px;margin-top:100px}footer .wrap{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:40px}footer h4{color:#fff;margin-bottom:14px}footer li{margin:8px 0;font-size:14.5px}
"""
    cards = "".join(f'<div class="card{" alt" if i == 3 else ""}"><i>{ic(a)}</i><h3>{t}</h3><p>{d}</p></div>' for i, (a, t, d) in enumerate(SERVICES))
    stats = "".join(f'<div><b>{n}{s}</b><p>{l}</p></div>' for n, s, l in STATS)
    steps = "".join(f'<div><b>{i+1}</b><h3>{t}</h3><p>{d}</p></div>' for i, (t, d) in enumerate(STEPS))
    star = "".join(ic("star", 16) for _ in range(5))
    tst = "".join(f'<div><span class="stars">{star}</span><p>“{q}”</p><small><b>{w}</b> · {r}</small></div>' for q, w, r in [
        (QUOTE[0][:110] + "…", QUOTE[1], QUOTE[2]),
        ("Our recruiter explained the role honestly and kept me updated at every stage. I joined within three weeks.", "Senior Analyst", "Placed in Mumbai"),
        ("They handled our volume hiring for three new stores without a single missed joining date.", "HR Manager", "Retail chain, Pune")])
    return head("Option 3 — Modern Gradient", "family=Plus+Jakarta+Sans:wght@400;500;600;700;800", css) + f"""
<div class="hero"><header><div class="wrap"><div class="logo"><img src="{IMG}logo-mark-dark.png">Prime</div><nav>{nav_links()}</nav><a class="btn grad">Hire Talent</a></div></header>
<div class="wrap hc"><span class="pill">{ic("star",14)} Pan India Recruitment Solutions</span><h1>Hire the <span>right people</span>,<br>faster than ever.</h1>
<p>Interviewed, skills-checked shortlists for permanent, contract, executive and volume roles — delivered by specialists.</p>
<div class="search"><div class="tabs"><span class="on">I'm hiring</span><span>I need a job</span><span>Corporate gifting</span></div><div class="fields"><div>{ic("brief",18)} Role you're hiring for</div><div>{ic("pin",18)} City</div><a class="btn grad">{ic("search",18)} Get shortlist</a></div></div>
<div class="trust"><span>{ic("check",16,2.4)} Every candidate interviewed</span><span>{ic("check",16,2.4)} Shortlists in 48–72 hrs</span><span>{ic("check",16,2.4)} No fees for job seekers</span></div></div></div>
<div class="wrap"><div class="stats">{stats}</div></div>
<section class="s"><div class="wrap"><div class="ch"><small>OUR SERVICES</small><h2>Everything you need to hire well</h2><p>One partner for every hiring model.</p></div><div class="grid">{cards}</div></div></section>
<section class="s feat"><div class="wrap"><div class="imgbox"><img src="{IMG}photo-atrium.webp"><div class="float f1"><i>{ic("users",18)}</i><div><b>4 candidates</b><br><small>shortlisted for you</small></div></div><div class="float f2"><i>{ic("shield",18)}</i><div><b>Replacement</b><br><small>guarantee included</small></div></div></div>
<div><small style="color:#7C3AED;font-weight:800;letter-spacing:.12em">WHO WE ARE</small><h2>We don't send CVs. We send people worth meeting.</h2><p>Every profile is interviewed, skills-checked and salary-aligned before it reaches you.</p><ul><li>{ic("check",20,2.4)} Function-specialist consultants</li><li>{ic("check",20,2.4)} Salary benchmarks with every brief</li><li>{ic("check",20,2.4)} Dedicated consultant from brief to joining</li></ul><a class="btn grad">About Prime {ic("arrow",16)}</a></div></div></section>
<section class="s"><div class="wrap"><div class="ch"><small>HOW IT WORKS</small><h2>Four simple steps</h2></div><div class="steps">{steps}</div></div></section>
<section class="s" style="padding-top:0"><div class="wrap"><div class="ch"><small>TESTIMONIALS</small><h2>Loved by employers and candidates</h2></div><div class="tst">{tst}</div></div></section>
<section class="cta"><div class="wrap"><div><h2>Ready to build your team?</h2><p>Talk to a consultant — we reply within one working day.</p></div><a class="btn wh">Start a conversation {ic("arrow",16)}</a></div></section>
<footer><div class="wrap"><div><div class="logo" style="color:#fff"><img src="{IMG}logo-mark-light.png">Prime Recruitments</div><p style="margin-top:16px">Pan India recruitment and corporate gifting.</p></div><div><h4>Services</h4><ul>{"".join(f"<li>{s[1]}</li>" for s in SERVICES[:4])}</ul></div><div><h4>Company</h4><ul><li>About</li><li>Jobs</li><li>Corporate Gifting</li><li>Contact</li></ul></div><div><h4>Contact</h4><ul><li>hr@primerecruitments.com</li><li>+91 82912 22211</li></ul></div></div></footer></body></html>"""


# ---------------------------------------------------------------------------
# 4. PREMIUM EXECUTIVE — deep emerald, gold, ivory. Cinematic photo hero.
# ---------------------------------------------------------------------------
def design4():
    css = """
body{font-family:'Lato',sans-serif;background:#FBF8F1;color:#14302A;font-size:16px;line-height:1.7}
h1,h2,h3{font-family:'Cormorant Garamond',serif;font-weight:600}
.hero{position:relative;height:880px;color:#fff;overflow:hidden}.hero>img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}.hero:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(9,33,28,.75),rgba(9,33,28,.6) 50%,rgba(9,33,28,.95))}
header{position:relative;z-index:2}header .wrap{display:flex;align-items:center;justify-content:space-between;height:96px;border-bottom:1px solid rgba(212,175,55,.35)}
.logo{display:flex;align-items:center;gap:12px;font-family:'Cormorant Garamond';font-size:26px;letter-spacing:.18em;font-weight:700}.logo img{width:44px}
nav{display:flex;gap:34px;font-size:13.5px;letter-spacing:.14em;text-transform:uppercase;color:#e8e2d0}nav .on{color:#D4AF37}
.btn{display:inline-flex;align-items:center;gap:12px;padding:16px 30px;font-size:13.5px;letter-spacing:.14em;text-transform:uppercase;font-weight:700}.gold{background:#D4AF37;color:#09211C}.line{border:1px solid #D4AF37;color:#fff}.dk{background:#09211C;color:#fff}
.hc{position:relative;z-index:2;text-align:center;padding-top:150px}.orn{display:flex;align-items:center;justify-content:center;gap:16px;color:#D4AF37;letter-spacing:.3em;font-size:13px;text-transform:uppercase}.orn:before,.orn:after{content:"";width:60px;height:1px;background:#D4AF37}
.hc h1{font-size:92px;line-height:1;margin:26px 0;font-weight:500}.hc h1 em{color:#D4AF37}.hc p{max-width:640px;margin:0 auto;font-size:19px;color:#ddd6c3}.hc .acts{display:flex;gap:16px;justify-content:center;margin-top:40px}
.bar{position:absolute;z-index:2;left:0;right:0;bottom:0}.bar .wrap{display:grid;grid-template-columns:repeat(4,1fr);border-top:1px solid rgba(212,175,55,.35)}.bar div{padding:34px 20px;text-align:center;border-left:1px solid rgba(212,175,55,.25)}.bar div:first-child{border:0}
.bar b{font-family:'Cormorant Garamond';font-size:52px;color:#D4AF37;line-height:1}.bar p{color:#ddd6c3;font-size:14px;letter-spacing:.06em}
section.s{padding:120px 0}.ct{text-align:center;max-width:760px;margin:0 auto 60px}.ct h2{font-size:58px;line-height:1.05}.ct p{color:#4c625c;margin-top:16px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:#e3dccb;border:1px solid #e3dccb}.svc{background:#FBF8F1;padding:40px 30px}.svc i{color:#B08F26;display:block;margin-bottom:22px}.svc h3{font-size:28px;margin-bottom:10px;line-height:1.1}.svc p{font-size:15px;color:#4c625c}
.svc.dk2{background:#09211C;color:#fff}.svc.dk2 p{color:#c8c2ae}.svc.dk2 i{color:#D4AF37}
.about{background:#09211C;color:#fff}.about .wrap{display:grid;grid-template-columns:1fr 1fr;gap:90px;align-items:center}.about .fr{position:relative;padding:20px}.about .fr:before{content:"";position:absolute;inset:0 40px 40px 0;border:1px solid #D4AF37}.about img{position:relative;height:540px;width:100%;object-fit:cover;margin:20px 0 0 20px;width:calc(100% - 20px)}
.about h2{font-size:56px;line-height:1.05}.about p{color:#c8c2ae;margin:22px 0}.about li{display:flex;gap:14px;margin:14px 0;color:#e8e2d0}.about li i{color:#D4AF37}
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:40px}.steps div{text-align:center}.steps b{display:grid;place-items:center;width:84px;height:84px;margin:0 auto 22px;border:1px solid #B08F26;border-radius:50%;font-family:'Cormorant Garamond';font-size:34px;color:#B08F26}.steps h3{font-size:28px}.steps p{font-size:15px;color:#4c625c;margin-top:8px}
.quote{background:#F1EBDD}.quote .wrap{text-align:center;max-width:920px}.quote p{font-family:'Cormorant Garamond';font-size:40px;line-height:1.3;font-style:italic}.quote small{display:block;margin-top:26px;letter-spacing:.14em;text-transform:uppercase;color:#4c625c}
.cta{text-align:center}.cta h2{font-size:64px}.cta p{color:#4c625c;margin:18px 0 36px}
footer{background:#09211C;color:#c8c2ae;padding:80px 0 30px}footer .wrap{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:40px}footer h4{font-family:'Cormorant Garamond';color:#D4AF37;font-size:22px;margin-bottom:14px}footer li{margin:8px 0;font-size:14.5px}
"""
    svc = "".join(f'<div class="svc{" dk2" if i in (1, 6) else ""}"><i>{ic(a,30,1.4)}</i><h3>{t}</h3><p>{d}</p></div>' for i, (a, t, d) in enumerate(SERVICES))
    bar = "".join(f'<div><b>{n}{s}</b><p>{l}</p></div>' for n, s, l in STATS)
    steps = "".join(f'<div><b>{["I","II","III","IV"][i]}</b><h3>{t}</h3><p>{d}</p></div>' for i, (t, d) in enumerate(STEPS))
    return head("Option 4 — Premium Executive", "family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Lato:wght@400;700", css) + f"""
<div class="hero"><img src="{IMG}photo-facade.webp"><header><div class="wrap"><div class="logo"><img src="{IMG}logo-mark-light.png">PRIME</div><nav>{nav_links()}</nav><a class="btn gold">Hire Talent</a></div></header>
<div class="wrap hc"><div class="orn">Pan India Recruitment Solutions</div><h1>Exceptional people.<br><em>Precisely</em> placed.</h1><p>A recruitment partner for organisations that care about who they hire — from leadership searches to high-volume teams.</p>
<div class="acts"><a class="btn gold">Hire talent</a><a class="btn line">Explore careers</a></div></div><div class="bar"><div class="wrap">{bar}</div></div></div>
<section class="s"><div class="wrap"><div class="ct"><div class="orn" style="color:#B08F26">Our expertise</div><h2>Recruitment services, crafted for every hiring need</h2></div><div class="grid">{svc}</div></div></section>
<section class="s about"><div class="wrap"><div class="fr"><img src="{IMG}photo-corridor.webp"></div><div><div class="orn" style="justify-content:flex-start">Who we are</div><h2>We don't send CVs.<br>We send people worth meeting.</h2><p>Every profile is personally interviewed, skills-checked and salary-aligned before it reaches your desk.</p>
<ul><li>{ic("check",20,2)} Function-specialist consultants</li><li>{ic("check",20,2)} Confidential executive searches</li><li>{ic("check",20,2)} Replacement guarantee on permanent hires</li></ul><br><a class="btn gold">Discover Prime</a></div></div></section>
<section class="s"><div class="wrap"><div class="ct"><div class="orn" style="color:#B08F26">Our process</div><h2>Four considered steps</h2></div><div class="steps">{steps}</div></div></section>
<section class="s quote"><div class="wrap"><p>“{QUOTE[0]}”</p><small>{QUOTE[1]} — {QUOTE[2]}</small></div></section>
<section class="s cta"><div class="wrap"><div class="orn" style="color:#B08F26">Begin</div><h2>Let us find your next great hire</h2><p>A consultant will respond within one working day.</p><a class="btn dk">Start a conversation</a></div></section>
<footer><div class="wrap"><div><div class="logo" style="color:#fff"><img src="{IMG}logo-mark-light.png">PRIME</div><p style="margin-top:16px">Pan India recruitment and corporate gifting.</p></div><div><h4>Services</h4><ul>{"".join(f"<li>{s[1]}</li>" for s in SERVICES[:4])}</ul></div><div><h4>Company</h4><ul><li>About</li><li>Careers</li><li>Corporate Gifting</li><li>Contact</li></ul></div><div><h4>Contact</h4><ul><li>hr@primerecruitments.com</li><li>+91 82912 22211</li></ul></div></div></footer></body></html>"""


# ---------------------------------------------------------------------------
# 5. FRESH & FRIENDLY — teal, coral, mint. Job search, rounded shapes.
# ---------------------------------------------------------------------------
def design5():
    css = """
body{font-family:'Nunito',sans-serif;background:#fff;color:#0F2E2C;font-size:16px;line-height:1.65}
h1,h2,h3{font-family:'Poppins',sans-serif;letter-spacing:-.02em}
header .wrap{display:flex;align-items:center;justify-content:space-between;height:88px}.logo{display:flex;align-items:center;gap:10px;font-family:Poppins;font-weight:700;font-size:20px}.logo img{width:42px}.logo span{color:#0F766E}
nav{display:flex;gap:28px;font-weight:700;font-size:15px;color:#3f5a57}nav .on{color:#0F766E}
.btn{display:inline-flex;align-items:center;gap:10px;padding:15px 26px;border-radius:14px;font-weight:800;font-size:15px}.teal{background:#0F766E;color:#fff}.coral{background:#FB7185;color:#3b0a12}.lt{background:#F0FDFA;color:#0F766E}
.hero{background:#F0FDFA;border-radius:0 0 60px 60px;padding:60px 0 90px}.hero .wrap{display:grid;grid-template-columns:1.05fr .95fr;gap:60px;align-items:center}
.tag{display:inline-flex;gap:8px;align-items:center;background:#fff;border-radius:999px;padding:8px 16px;font-weight:800;font-size:13.5px;color:#0F766E}.tag i{width:8px;height:8px;border-radius:50%;background:#FB7185}
.hero h1{font-size:62px;line-height:1.08;margin:22px 0 18px}.hero h1 span{color:#0F766E;position:relative}.hero h1 span:after{content:"";position:absolute;left:0;right:0;bottom:4px;height:12px;background:#FECDD3;z-index:-1;border-radius:6px}
.hero p{font-size:18.5px;color:#3f5a57;max-width:540px}.box{margin-top:30px;background:#fff;border-radius:20px;padding:10px;box-shadow:0 24px 50px -28px rgba(15,118,110,.5);display:grid;grid-template-columns:1.2fr 1fr auto;gap:8px}
.box div{display:flex;gap:10px;align-items:center;padding:14px;color:#7b928f;border-radius:12px;background:#F7FBFA}.cats{display:flex;flex-wrap:wrap;gap:8px;margin-top:20px}.cats span{background:#fff;border:1px solid #CCEFEA;border-radius:999px;padding:8px 14px;font-weight:700;font-size:14px;color:#0F766E}
.vis{position:relative}.blob{width:100%;height:540px;border-radius:46% 54% 42% 58%/52% 44% 56% 48%;overflow:hidden;background:#0F766E}.blob img{width:100%;height:100%;object-fit:cover;mix-blend-mode:luminosity;opacity:.85}
.st{position:absolute;background:#fff;border-radius:18px;padding:14px 18px;box-shadow:0 20px 40px -20px rgba(15,46,44,.45);display:flex;gap:12px;align-items:center;font-size:14px}.st i{width:42px;height:42px;border-radius:12px;display:grid;place-items:center}
.s1{left:-20px;top:60px}.s1 i{background:#FFE4E6;color:#E11D48}.s2{right:-10px;top:240px}.s2 i{background:#CCFBF1;color:#0F766E}.s3{left:40px;bottom:20px}.s3 i{background:#FEF3C7;color:#B45309}
.hero .wrap.nums{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:70px}.nums div{display:flex;gap:16px;align-items:center}.nums b{font-family:Poppins;font-size:42px;color:#0F766E}.nums p{font-size:14.5px;color:#3f5a57;line-height:1.35}
section.s{padding:100px 0}.hd{display:flex;justify-content:space-between;align-items:end;margin-bottom:46px}.hd small{color:#E11D48;font-weight:800;letter-spacing:.1em}.hd h2{font-size:44px;line-height:1.1;margin-top:8px;max-width:620px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}.c{border-radius:24px;padding:28px;background:#fff;border:1.5px solid #E6F4F1;position:relative;overflow:hidden}.c:before{content:"";position:absolute;left:0;right:0;top:0;height:6px;background:var(--c)}
.c i{width:54px;height:54px;border-radius:16px;display:grid;place-items:center;background:var(--b);color:var(--c);margin-bottom:16px}.c h3{font-size:18.5px;margin-bottom:6px}.c p{font-size:14.5px;color:#3f5a57}
.how{background:#0F2E2C;color:#fff;border-radius:60px}.how .hd h2{color:#fff}.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}.steps div{background:rgba(255,255,255,.06);border-radius:24px;padding:28px}.steps b{display:grid;place-items:center;width:46px;height:46px;border-radius:50%;background:#FB7185;color:#3b0a12;font-family:Poppins;margin-bottom:16px}.steps p{color:#b7cfcb;font-size:14.5px}
.jobs{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}.j{border:1.5px solid #E6F4F1;border-radius:22px;padding:26px}.j .t{display:inline-block;background:#F0FDFA;color:#0F766E;border-radius:999px;padding:5px 12px;font-weight:800;font-size:12.5px}.j h3{font-size:20px;margin:12px 0 6px}.j p{color:#3f5a57;font-size:14.5px}.j .m{display:flex;gap:14px;margin-top:14px;color:#3f5a57;font-size:14px}.j .m span{display:flex;gap:6px;align-items:center}
.q .wrap{display:grid;grid-template-columns:auto 1fr;gap:40px;align-items:center;background:#FFF1F2;border-radius:30px;padding:50px}.q .av{width:120px;height:120px;border-radius:50%;background:#FB7185;display:grid;place-items:center;font-family:Poppins;font-size:44px;color:#3b0a12}.q p{font-family:Poppins;font-size:24px;line-height:1.45}
.cta .wrap{display:flex;justify-content:space-between;align-items:center;background:#0F766E;border-radius:30px;padding:56px;color:#fff}.cta h2{font-size:40px}
footer{background:#0F2E2C;color:#b7cfcb;padding:70px 0 30px;margin-top:100px;border-radius:60px 60px 0 0}footer .wrap{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:40px}footer h4{color:#fff;font-family:Poppins;margin-bottom:14px}footer li{margin:8px 0;font-size:14.5px}
"""
    pal = [("#0F766E", "#CCFBF1"), ("#E11D48", "#FFE4E6"), ("#B45309", "#FEF3C7"), ("#6D28D9", "#EDE9FE")]
    cards = "".join(f'<div class="c" style="--c:{pal[i%4][0]};--b:{pal[i%4][1]}"><i>{ic(a)}</i><h3>{t}</h3><p>{d}</p></div>' for i, (a, t, d) in enumerate(SERVICES))
    nums = "".join(f'<div><b>{n}{s}</b><p>{l}</p></div>' for n, s, l in STATS)
    steps = "".join(f'<div><b>{i+1}</b><h3>{t}</h3><p>{d}</p></div>' for i, (t, d) in enumerate(STEPS))
    jobs = "".join(f'<div class="j"><span class="t">{c}</span><h3>{t}</h3><p>{d}</p><div class="m"><span>{ic("pin",15)}{l}</span><span>{ic("brief",15)}{e}</span></div></div>' for t, c, d, l, e in [
        ("Senior Java Developer", "Technology", "Build scalable backend services for a product company.", "Bangalore", "5–8 yrs"),
        ("Regional Sales Manager", "Sales", "Lead a team of 12 across North India for an FMCG brand.", "Delhi NCR", "8–12 yrs"),
        ("ICU Staff Nurse", "Healthcare", "Join a leading multi-speciality hospital's critical care team.", "Pune", "2–5 yrs")])
    return head("Option 5 — Fresh & Friendly", "family=Poppins:wght@600;700&family=Nunito:wght@400;600;700;800", css) + f"""
<header><div class="wrap"><div class="logo"><img src="{IMG}logo-mark-dark.png">Prime<span>Recruitments</span></div><nav>{nav_links()}</nav><a class="btn teal">Hire Talent</a></div></header>
<div class="hero"><div class="wrap"><div><span class="tag"><i></i>Pan India Recruitment Solutions</span><h1>Find great people.<br>Find your <span>next role.</span></h1><p>Recruitment for employers and careers for professionals — every candidate interviewed by a real recruiter.</p>
<div class="box"><div>{ic("search",18)} Job title or skill</div><div>{ic("pin",18)} City</div><a class="btn coral">Search</a></div><div class="cats">{"".join(f"<span>{x}</span>" for x in INDUSTRIES[:6])}</div></div>
<div class="vis"><div class="blob"><img src="{IMG}photo-stairs.webp"></div><div class="st s1"><i>{ic("check",20,2.4)}</i><div><b>Interviewed</b><br><small>every candidate</small></div></div><div class="st s2"><i>{ic("clock",20)}</i><div><b>48–72 hrs</b><br><small>to first shortlist</small></div></div><div class="st s3"><i>{ic("heart",20)}</i><div><b>Free</b><br><small>for job seekers</small></div></div></div></div>
<div class="wrap nums">{nums}</div></div>
<section class="s"><div class="wrap"><div class="hd"><div><small>OUR SERVICES</small><h2>Hiring solutions for every stage of growth</h2></div><a class="btn lt">All services {ic("arrow",16)}</a></div><div class="grid">{cards}</div></div></section>
<section class="s how"><div class="wrap"><div class="hd"><div><small style="color:#FB7185">HOW IT WORKS</small><h2>Simple, fast and transparent</h2></div></div><div class="steps">{steps}</div></div></section>
<section class="s"><div class="wrap"><div class="hd"><div><small>LATEST JOBS</small><h2>Roles hiring right now</h2></div><a class="btn lt">View all jobs {ic("arrow",16)}</a></div><div class="jobs">{jobs}</div></div></section>
<section class="s q" style="padding-top:0"><div class="wrap"><div class="av">H</div><div><p>“{QUOTE[0]}”</p><small style="display:block;margin-top:14px"><b>{QUOTE[1]}</b> · {QUOTE[2]}</small></div></div></section>
<section class="cta"><div class="wrap"><div><h2>Ready when you are.</h2><p style="color:#ccfbf1">Hiring, job search or corporate gifting — we reply within one working day.</p></div><a class="btn coral">Start a conversation {ic("arrow",16)}</a></div></section>
<footer><div class="wrap"><div><div class="logo" style="color:#fff"><img src="{IMG}logo-mark-light.png">Prime</div><p style="margin-top:16px">Pan India recruitment and corporate gifting.</p></div><div><h4>Services</h4><ul>{"".join(f"<li>{s[1]}</li>" for s in SERVICES[:4])}</ul></div><div><h4>Company</h4><ul><li>About</li><li>Jobs</li><li>Corporate Gifting</li><li>Contact</li></ul></div><div><h4>Contact</h4><ul><li>hr@primerecruitments.com</li><li>+91 82912 22211</li></ul></div></div></footer></body></html>"""


if __name__ == "__main__":
    for n, fn in enumerate([design1, design2, design3, design4, design5], 1):
        with open(os.path.join(HERE, f"option-{n}.html"), "w") as f:
            f.write(fn())
    print("built 5 options")
