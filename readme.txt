Here is a complete breakdown of every image and asset this website needs, section by section:

---

## 🖼️ HEADER / LOGO

**`logo.png`** — The CBS Education logo mark
- Transparent background (.png)
- Minimum 400×200px, ideally SVG for crispness
- Should work on both white/ivory backgrounds
- **Best format: `logo.svg`** (scales perfectly at any size)

**`favicon.png`** — The tiny icon that appears in the browser tab
- 32×32px or 64×64px
- Just the "CBS" lettermark or a simple icon version of the logo
- Format: `favicon.ico` or `favicon.png`

---

## 🦸 HERO SECTION (The Split Left/Right Panel)

The right side has a **3-cell mosaic grid** — these are the three most prominent images on the entire page. They need to be high-quality and professional.

**`hero-main.jpg`** — Large left cell (takes up 2/3 of the mosaic height)
- A wide shot of students in a lecture hall, collaborative workspace, or a dynamic classroom setting — ideally diverse students, engaged and focused
- Recommended size: **1200×1600px** (portrait orientation)
- Dark navy/blue colour tones will complement the site palette
- Format: `.jpg` (compressed for web, under 300KB)

**`hero-top-right.jpg`** — Top-right cell
- A close-up or candid of a student studying, reading, or working — something intimate and focused
- Recommended size: **800×600px**
- Format: `.jpg`

**`hero-bottom-right.jpg`** — Bottom-right cell
- A shot of a campus building exterior, a lab, or a modern learning space
- Recommended size: **800×400px** (landscape)
- Format: `.jpg`

---

## 📌 OFFERINGS SECTION (3 Alternating Rows)

Each row has a visual panel on one side. Currently using emoji placeholders — replace with real imagery.

**`offering-undergraduate.jpg`** — Row 1 (Undergraduate)
- Students in a large lecture theatre or seminar, engaged in learning
- Should work overlaid on a deep navy gradient
- Recommended size: **1200×900px**
- Format: `.jpg`

**`offering-postgraduate.jpg`** — Row 2 (Postgraduate)
- A smaller group of mature/postgrad students around a boardroom table or in discussion with a professor — conveys advanced, serious study
- Recommended size: **1200×900px**
- Format: `.jpg`

**`offering-professional.jpg`** — Row 3 (Professional Development)
- A professional/corporate setting — someone presenting to colleagues, or a workshop environment with adults in business attire
- Recommended size: **1200×900px**
- Format: `.jpg`

---

## 📊 IMPACT / NUMBERS SECTION

This section is purely dark background with statistics — **no images needed here**. It uses CSS radial gradients for atmosphere. No photo assets required.

---

## 🎓 DISCIPLINES SECTION (6 Cards)

The discipline cards use emoji icons right now. If you want to replace them with real icons:

**`icon-business.svg`** — Briefcase / chart icon for Business & Management
**`icon-law.svg`** — Scales / gavel icon for Law & Governance
**`icon-science.svg`** — Flask / atom icon for Sciences & Technology
**`icon-arts.svg`** — Palette / pen nib icon for Arts & Humanities
**`icon-health.svg`** — Stethoscope / cross icon for Health & Medicine
**`icon-education.svg`** — Graduation cap / open book for Education & Social Work

- All icons: **60×60px minimum**, single colour (so CSS can colour them)
- Format: **`.svg`** (essential — so they stay sharp at all sizes and can be tinted with CSS)
- Place in a folder: `assets/icons/`

---

## 💬 TESTIMONIALS SECTION

Currently uses initials-only avatars (text boxes). If you want real headshot photos:

**`testimonial-oluwaseun.jpg`** — Headshot of first testimonial author
**`testimonial-rachel.jpg`** — Headshot of second testimonial author
**`testimonial-kenji.jpg`** — Headshot of third testimonial author

- Square crop, close-up face shot, professional but approachable
- Recommended size: **200×200px** (displayed at 44×44px so doesn't need to be huge)
- Format: `.jpg`
- Place in: `assets/testimonials/`

If these are real people, use real photos. If placeholder, a service like **ui-avatars.com** or **randomuser.me** generates clean headshots.

---

## 🏅 PARTNERS / ACCREDITATION SECTION

Currently shows accreditation names as styled text (AACSB, EQUIS, etc). If you have official logo files from these bodies:

**`logo-aacsb.png`** — AACSB accreditation logo
**`logo-equis.png`** — EQUIS accreditation logo
**`logo-amba.png`** — AMBA accreditation logo
**`logo-qaa.png`** — QAA accreditation logo
**`logo-iso.png`** — ISO 9001 logo
**`logo-unesco.png`** — UNESCO logo

- Use the official greyscale versions (they look cleaner and more subtle on the strip)
- Recommended height: **40–60px**, width variable
- Format: `.png` with transparent background, or `.svg`
- Place in: `assets/partners/`

---

## 📣 CTA SECTION

This is a dark ink background — **no images needed**. Pure CSS/text layout. No photo assets.

---

## 🦶 FOOTER

**`logo-footer.svg`** or reuse `logo.svg` — Same logo, but used on a dark background
- Make sure your logo has a version that works on dark/black — either white text version or the gold version
- If your logo only works on light, you'll need: `logo-light.svg` (white/gold variant for dark backgrounds)
- Format: `.svg`

**Social media icons** (if you want image-based rather than text):
**`icon-linkedin.svg`**
**`icon-twitter.svg`** (or X logo)
**`icon-instagram.svg`**
**`icon-youtube.svg`**
**`icon-facebook.svg`**

- Format: **`.svg`** — monochrome/white so CSS hover effects work
- Size: 20×20px display size
- Place in: `assets/social/`

---

## 📁 Recommended Folder Structure

```
assets/
├── logo.svg               ← main logo (dark bg compatible)
├── logo-light.svg         ← white version for dark sections
├── favicon.ico
├── images/
│   ├── hero-main.jpg
│   ├── hero-top-right.jpg
│   ├── hero-bottom-right.jpg
│   ├── offering-undergraduate.jpg
│   ├── offering-postgraduate.jpg
│   └── offering-professional.jpg
├── testimonials/
│   ├── testimonial-oluwaseun.jpg
│   ├── testimonial-rachel.jpg
│   └── testimonial-kenji.jpg
├── icons/
│   ├── icon-business.svg
│   ├── icon-law.svg
│   ├── icon-science.svg
│   ├── icon-arts.svg
│   ├── icon-health.svg
│   └── icon-education.svg
├── social/
│   ├── icon-linkedin.svg
│   ├── icon-twitter.svg
│   ├── icon-instagram.svg
│   ├── icon-youtube.svg
│   └── icon-facebook.svg
└── partners/
    ├── logo-aacsb.png
    ├── logo-equis.png
    ├── logo-amba.png
    ├── logo-qaa.png
    ├── logo-iso.png
    └── logo-unesco.png
```

---

## ⚡ Quick Tips

- **All hero/offering photos** should ideally be shot with a slight **dark or cool tone** — they'll blend with the navy/ink colour palette naturally
- **Compress all JPGs** before uploading — use a tool like squoosh.app to keep them under 200KB each without visible quality loss
- **Never use PNGs for photographs** — JPG is far smaller for photos
- **Always use SVG for logos and icons** — they're infinitely scalable and tiny in file size
- The three hero mosaic images are the most visible on the page — invest the most in getting those right