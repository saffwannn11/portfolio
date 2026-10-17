# Prime Recruitments: WordPress Theme (Elementor-ready)

A high-end, animated recruitment-agency theme. Every page is pre-built in Elementor using 22 custom Prime widgets, so you can edit all of it visually.

## Install (5 minutes)

1. **Upload the theme:** WordPress → **Appearance → Themes → Add New → Upload Theme** → choose `prime-recruitments.zip` → **Install** → **Activate**.
2. **Pages are created automatically.** Activating the theme does the following:
   - creates all **19 pages**: Home + 18 SEO landing pages
   - builds every page as an **Elementor layout**
   - sets **Home** as your homepage
   - turns on keyword URLs (`/%postname%/`)
3. **Install Elementor** (free): **Plugins → Add New → search "Elementor" → Install → Activate**. The theme also shows a one-click "Install Elementor" notice.
4. **Edit visually:** open any page → **Edit with Elementor**. Every section is a widget; click it to edit text, links, images, lists, colours and spacing.

Order doesn't matter: theme first, Elementor first, or Elementor added months later all work. Without Elementor the site still renders exactly the same; you just can't edit it visually.

## Editing in Elementor

- **Find the widgets:** in the Elementor panel, search **"Prime"** or scroll to the **Prime Recruitments** category. All 22 widgets are there.
- **Content tab:** every text, button, link, image and list item. Repeaters let you add, remove, reorder and duplicate cards, FAQs, jobs, stats, steps and testimonials.
- **Style tab** (on every Prime widget):
  - accent colour, background colour, heading colour and text colour
  - heading typography
  - top and bottom padding (responsive)
- **Advanced tab:** Elementor's standard margin, padding, CSS classes, hide-on-mobile and so on.
- **Navigator:** sections are named ("Hero (homepage)", "FAQ (with FAQ schema)" …). Drag to reorder, or right-click to duplicate or delete.
- **Mixing widgets:** you can place native Elementor widgets (Heading, Text, Image, Video…) between Prime sections.
- **Accent words:** in headings, wrap words in `*asterisks*` to get the blue italic accent, e.g. `Hire *faster*`.
- **New pages:** create a page → **Edit with Elementor** → drag in Prime widgets. They arrive fully designed with sample content. You can also choose the **"Prime – Full width (for Elementor)"** template.
- **Reset:** **Appearance → Prime Setup → "Reset all pages to original design"** restores the original layouts. This overwrites your edits on those pages and cannot be undone.

### The 22 widgets

| Widget | What it is |
|---|---|
| Hero (homepage) | Particle background, rotating headline word, typing code card |
| Kinetic marquee | Two-row scrolling headline marquee |
| Scroll statement | Words light up as you scroll |
| Services slider | Pinned horizontal-scroll service cards |
| Animated stats | Count-up numbers with rings |
| Process | Sticky heading + steps with a line that draws on scroll |
| Industries hover list | Large rows with a floating image on hover |
| Why us bento grid | Bento cards + animated funnel |
| Testimonials slider | Auto-play, progress bars, swipe |
| Locations + India map | City cards + animated map |
| Job listings | Filterable job cards |
| FAQ | Accordion that also outputs FAQ schema automatically |
| CTA + enquiry form | Fluent Forms / built-in form |
| Page banner | 5 animated styles, breadcrumb, AEO quick-answer card |
| Contact cards | Email / phone / CV / address cards |
| Intro + at a glance | Rich text + sticky "at a glance" card |
| Service card grid | Card grid linking to service pages |
| Feature grid | Dark feature grid |
| Roles marquee | Scrolling role chips |
| Process timeline | Horizontal timeline |
| Key facts + areas served | GEO fact sheet + service-area chips |
| Related pages | Hover-invert link cards |

## Before going live

1. **Logo:** Appearance → Customize → Site Identity → Logo. It replaces the "P Prime" placeholder in the header and footer.
2. **Brand colour, contact details, social links:** Customize → **Prime Recruitments**. The phone number and address are placeholders.
3. **Fluent Forms:**
   - Install Fluent Forms and build your form.
   - Paste its ID in Customize → Prime Recruitments → Contact form. That applies the form site-wide.
   - Alternatively, paste a shortcode into any **CTA + enquiry form** widget.
   - Until then, a built-in form emails enquiries to the admin email. Use an SMTP plugin for reliable delivery.
4. **Replace the sample content:**
   - the **testimonials** and **job listings** are samples; use real, approved quotes and live roles
   - the **homepage stats** are placeholders; set your real figures
   - you can edit all of these directly in Elementor
5. **Header and footer** come from the theme and are edited in Customize and Appearance → Menus. Assigning a menu to "Primary" replaces the built-in mega menu. Elementor Free can't edit headers and footers; Elementor Pro's Theme Builder can, if you ever want that.

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

Every page has a meta title (≤60 characters), a meta description (≤160 characters), a focus keyword and secondary keywords. Edit them per page in the **Prime SEO** box below the editor. If Yoast SEO or Rank Math is installed, they are pre-filled there instead and the theme steps aside.

## SEO / AEO / GEO

- **AEO:** a "quick answer" card in each banner and FAQ accordions. **FAQPage schema is generated from the FAQ widget**, so it stays in sync when you edit questions in Elementor.
- **GEO (AI search):** a key-facts block, an "Updated" date on each page, and `/llms.txt`.
- **GEO (local):**
  - city landing pages
  - an areas-served block on every page
  - `Service` schema with `areaServed`
- **Schema:** `EmploymentAgency`, `WebSite`, `WebPage`/`AboutPage`/`ContactPage`, `BreadcrumbList`, `Service`, `FAQPage`.

Keywords were chosen as established high-intent terms for Indian recruitment. Search volumes were not checked with a keyword tool, so confirm them in Search Console or Ahrefs after launch.

## Tech notes

- Requires WordPress 6.0+ and PHP 7.4+. Tested with WordPress 6.8 and Elementor 3.35.
- Animations use GSAP 3.13 + ScrollTrigger + SplitText and Lenis smooth scroll, all bundled locally.
- Respects `prefers-reduced-motion`. Animations pause in the Elementor editor so you see the final design while editing.
- The homepage industry hover images load from Unsplash. Replace them with your own images in the Industries widget.
