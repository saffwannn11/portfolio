# Prime Recruitments: WordPress Theme v1.2 (Elementor-ready)

A navy-and-gold recruitment and corporate gifting theme, built from your business card's branding. Every page is pre-built in Elementor using 24 custom Prime widgets.

## Install or update

**New install**
1. Go to **Appearance → Themes → Add New Theme → Upload Theme**.
2. Choose `prime-recruitments.zip`, then **Install** and **Activate**.

Upload it as a **theme**, not as a plugin; the plugin uploader reports "No valid plugins were found".

**Updating from v1.0 / v1.1**
1. Upload the zip the same way and choose **"Replace current with uploaded"**.
2. Open any admin page once. The update then runs by itself:
   - creates the new **Corporate Gifting** page
   - gives every page you **haven't** edited the new design
   - keeps pages you **have** edited in Elementor as they are, but adds the Corporate Gifting section to your homepage and swaps the old placeholder phone/email for your real details

Activating creates **20 pages** automatically and sets the homepage and URLs. Then:

3. Install the free **Elementor** plugin and open any page with **Edit with Elementor**.
4. *(Optional)* Install **Fluent Forms**. The theme then creates a ready **"Prime Recruitments Enquiry"** form for you, connects it to the popup and every CTA, and sends enquiries to your contact email.

## What's in v1.2

- **Brand:** navy and gold from your business card, with your logo in the header, footer and preloader. All colour combinations, including hover states, were checked for readability.
- **Logo:** to change it, use **Customize → Site Identity** (upload), or **Customize → Prime Recruitments → Logo**. There you can upload or paste an image URL, and set the logo link URL and height.
- **Enquiry popup:** opens in the centre of the screen.
  - **Left:** brand panel with a mini business card and Call/WhatsApp/Email buttons. You can add your own background photo in Customize → Contact form.
  - **Right:** the form.
  - It opens from every "Hire talent", "Get a shortlist", "Submit your CV", "Apply" and "Get a gifting quote" button, preset to the right enquiry type (hiring / job / corporate gifting).
  - Works on phones and tablets, closes with Esc, and keeps keyboard focus inside while open.
- **Corporate Gifting:**
  - a dedicated page, `/corporate-gifting/`, with SEO, AEO answer, process, facts and FAQs
  - a showcase section on the homepage
  - your full gift list, "Ideal for" occasions, and the Bulk orders / Customisation / Personalised branding / Pan-India delivery badges
- **Business card section** on the Contact page:
  - a recreation of your card (Fahim Dalla, HR Manager)
  - Call, WhatsApp and Email buttons
  - a **Save contact** button that downloads a vCard to the phone
- **WhatsApp and call:**
  - floating WhatsApp and Call buttons on desktop
  - a Call / WhatsApp / Enquire bar on mobile
  - WhatsApp opens a chat with a pre-filled message
- **Social links:** add LinkedIn, Instagram, Facebook, X and YouTube in Customize → Prime Recruitments → Social links. They appear in the footer, mobile menu and Contact page.
- **Faster:**
  - the preloader is a short logo animation, shown only on a visitor's first page and gone as soon as the page is ready
  - scripts load deferred, with fewer font files
  - phones skip the particle effect, and industry images load only when needed
- **No repetition:** the footer CTA that repeated the form above it is gone, as are repeated "48–72 hour" claims and the duplicate contact cards on the Contact page.
- **SEO:**
  - exactly one H1 per page (homepage included)
  - meta title, description and focus keyword on every page
  - schema with your logo, contact point, social profiles and Corporate Gifting service

## Your details (pre-filled from the business card)

All of these are in **Customize → Prime Recruitments → Contact details**:
- Contact person: Fahim Dalla, HR Manager
- Email: hr@primerecruitments.com
- Phone and WhatsApp: +91 82912 22211
- Address: Pan India Recruitment Solutions

Note: your printed card says "www.primerecruit**e**ments.com" (with an extra "e"). The website uses primerecruitments.com.

## Before going live

1. Add your social profile links.
2. Replace the **sample testimonials and job listings** (editable in Elementor) with real ones.
3. Set the homepage **stats** to your real figures.
4. Use **one** SMTP plugin (FluentSMTP *or* WP Mail SMTP, not both) so enquiry emails are delivered reliably.

## Pages and focus keywords

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
| **Corporate Gifting** | `/corporate-gifting/` | corporate gifting in India |
| Industries | `/industries/` | industry specific recruitment |
| Jobs | `/jobs/` | latest job vacancies in India |
| For Employers | `/hire-talent/` | hire employees in India |
| For Job Seekers | `/job-seekers/` | job consultancy in India |
| Mumbai | `/recruitment-agency-in-mumbai/` | recruitment agency in Mumbai |
| Delhi NCR | `/recruitment-agency-in-delhi-ncr/` | recruitment agency in Delhi NCR |
| Bangalore | `/recruitment-agency-in-bangalore/` | recruitment agency in Bangalore |
| Contact | `/contact/` | contact recruitment agency |

Keyword search volumes were not checked with a keyword tool. Confirm them in Search Console or Ahrefs after launch.

## Editing in Elementor

- Search **"Prime"** in the Elementor panel to find the 24 widgets.
- Every widget has three tabs:
  - **Content:** all text, links, images and lists
  - **Style:** accent, background, heading and text colours, heading font, padding
  - **Advanced:** Elementor's standard options
- In headings, wrap words in `*asterisks*` for the gold italic accent.
- Any link to `#enquiry` opens the popup.
- **Appearance → Prime Setup** can:
  - create missing pages
  - (re)create the Fluent Forms enquiry form
  - reset all pages to the original design (this overwrites your edits and can't be undone)

## Tech notes

- Requires WordPress 6.0+ and PHP 7.4+.
- Tested on WordPress 6.8 with Elementor 3.35.
- Fluent Forms couldn't be run in the test environment. The auto-created form was built from Fluent Forms 6.2's own source and template format. If it can't be created on your site, the theme shows a notice and keeps the built-in form working.
- Animations use GSAP 3.13 + ScrollTrigger + SplitText and Lenis, all bundled locally.
- Motion is reduced for visitors who prefer it, and paused inside the Elementor editor.
