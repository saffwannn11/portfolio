# Prime Recruitments: WordPress Theme

A high-end, animated WordPress theme for Prime Recruitments. It comes with a cinematic homepage, 18 SEO landing pages, AEO/GEO content, schema markup, Fluent Forms support and a slot for your logo.

**Install:** upload `prime-recruitments.zip` in **Appearance → Themes → Add New → Upload Theme**, then click **Activate**.

On activation the theme does the following:

- creates all 18 pages (plus a **Home** page) and sets Home as the static front page
- switches permalinks to `/%postname%/` so the keyword URLs work
- pre-fills Yoast SEO / Rank Math fields with each page's meta title, description and focus keyword, so they carry over if you install either plugin later

Existing pages are never overwritten. You can re-run this any time from **Appearance → Prime Setup**, which also lists every page with its meta title and focus keyword.

## Before going live (10 minutes)

1. **Logo:** Appearance → Customize → Site Identity → *Logo*. It replaces the "P Prime" placeholder in the header and footer.
2. **Contact details:** Customize → *Prime Recruitments → Contact details*. Set email, phone, WhatsApp (this shows a floating button), address and hours. The defaults are placeholders.
3. **Fluent Forms (CTA form):**
   - Install **Fluent Forms** and create a form. Suggested fields: Name, Email, Phone, "I am hiring / looking for a job" (radio), Company / Current role, Role, Message, Consent.
   - Copy the form ID (the number in `[fluentform id="3"]`).
   - Paste it in Customize → *Prime Recruitments → Contact form*.
   - The theme restyles Fluent Forms to match the design.
   - Until you add an ID, a built-in form emails enquiries to the admin email (or the address set there). Use an SMTP plugin so mail is delivered reliably.
4. **Stats:** Customize → *Homepage stats*. Replace with your real figures.
5. **Replace sample content:**
   - **Testimonials** (`prime_testimonials()` in `inc/pages-data.php`) are samples. Use real, approved quotes only.
   - **Job listings** (`prime_jobs()`) are examples. Replace them with live roles.
6. **Social links:** Customize → *Social links*.

## Pages, URLs and focus keywords

| Page | URL | Focus keyword |
|---|---|---|
| Home | `/` | recruitment agency in India |
| About Us | `/about-us/` | recruitment agency in India |
| Services | `/services/` | recruitment services |
| Permanent Recruitment | `/services/permanent-recruitment/` | permanent recruitment services |
| Executive Search | `/services/executive-search/` | executive search firm in India |
| IT Recruitment | `/services/it-recruitment/` | IT recruitment agency |
| Contract Staffing | `/services/contract-staffing/` | contract staffing services |
| RPO | `/services/recruitment-process-outsourcing/` | recruitment process outsourcing |
| Bulk Hiring | `/services/bulk-hiring/` | bulk hiring agency |
| Healthcare Recruitment | `/services/healthcare-recruitment/` | healthcare recruitment agency |
| Overseas Recruitment | `/services/overseas-recruitment/` | overseas recruitment agency in India |
| Industries | `/industries/` | industry specific recruitment |
| Jobs | `/jobs/` | latest job vacancies in India |
| For Employers | `/hire-talent/` | hire employees in India |
| For Job Seekers | `/job-seekers/` | job consultancy in India |
| Mumbai | `/recruitment-agency-in-mumbai/` | recruitment agency in Mumbai |
| Delhi NCR | `/recruitment-agency-in-delhi-ncr/` | recruitment agency in Delhi NCR |
| Bangalore | `/recruitment-agency-in-bangalore/` | recruitment agency in Bangalore |
| Contact | `/contact/` | contact recruitment agency |

Every page has a meta title (≤60 characters), a meta description (≤160 characters), a focus keyword and secondary keywords. You can edit them per page in the **Prime SEO** box in the editor; when an SEO plugin is active, edit them in that plugin instead.

## SEO / AEO / GEO built in

- **AEO:** each page opens with a "quick answer" card, a 40–60-word direct answer written for featured snippets and AI answers. FAQs use question headings and are output as `FAQPage` schema.
- **GEO (generative engines):**
  - a "Key facts" block of short, quotable entity statements
  - an "Updated" date on each page
  - `/llms.txt`, a plain-text site summary for AI crawlers
- **GEO (local):**
  - city landing pages
  - an "Areas served" block on every page
  - `Service` schema with `areaServed`
  - an India city map on the homepage
- **Schema:** `EmploymentAgency`, `WebSite`, `WebPage`/`AboutPage`/`ContactPage`, `BreadcrumbList`, `Service`, `FAQPage`.
- **Plugin coexistence:** if Yoast, Rank Math, AIOSEO or SEOPress is active, the theme stops printing its own title, description and OG tags, and keeps only the FAQ and Service schema.

Keywords were chosen as established high-intent search terms for Indian recruitment. Search volumes were not checked with a keyword tool, so confirm them in Google Search Console / Ahrefs after launch.

## Editing copy

All landing-page copy lives in `inc/pages-data.php`, with one array per page. Text in `*asterisks*` inside headings is set in the italic serif accent. Anything you type in a page's editor is also shown below that page's overview section.

## Tech notes

- Animations: GSAP 3.13 + ScrollTrigger + SplitText, plus Lenis smooth scroll, all bundled locally in `assets/js/vendor/`. Respects `prefers-reduced-motion`. Content stays visible if JavaScript fails.
- Fonts: Plus Jakarta Sans, Instrument Serif, JetBrains Mono (Google Fonts).
- The industry hover images on the homepage load from Unsplash. Swap the photo IDs in `front-page.php` for your own images if you prefer.
- Requires WordPress 6.0+ and PHP 7.4+.
