#!/bin/bash
# =============================================================================
# Snazzy Sprocket — Seed Script
# Run with: wp-env run cli bash /var/www/html/wp-content/themes/snazzy-sprocket/bin/seed.sh
# Or locally: bash bin/seed.sh
# =============================================================================

echo "🔧 Snazzy Sprocket Seed Script"
echo "================================"

# --- Activate theme ---
wp theme activate snazzy-sprocket --quiet 2>/dev/null

# --- Create Pages ---
echo "📄 Creating pages..."

HOME_ID=$(wp post create --post_type=page --post_title="Home" --post_status=publish --post_name="home" --porcelain 2>/dev/null)
ABOUT_ID=$(wp post create --post_type=page --post_title="About" --post_status=publish --post_name="about" --porcelain 2>/dev/null)
CASE_STUDIES_ID=$(wp post create --post_type=page --post_title="Case Studies" --post_status=publish --post_name="case-studies" --porcelain 2>/dev/null)
CONTACT_ID=$(wp post create --post_type=page --post_title="Contact" --post_status=publish --post_name="contact" --porcelain 2>/dev/null)

# --- Seed About page ACF fields ---
if [ -n "$ABOUT_ID" ]; then
  # Hero
  wp post meta update "$ABOUT_ID" about_eyebrow "About us" 2>/dev/null
  wp post meta update "$ABOUT_ID" about_headline "We're the team behind your next big <em>launch</em>" 2>/dev/null
  wp post meta update "$ABOUT_ID" about_subheadline "Snazzy Sprocket started with a simple belief: the web should be fast, beautiful, and accessible to everyone. Eight years later, we're still proving it — one project at a time." 2>/dev/null

  # Our Story
  wp post meta update "$ABOUT_ID" story_eyebrow "Our story" 2>/dev/null
  wp post meta update "$ABOUT_ID" story_headline "From side project to full-service agency" 2>/dev/null
  wp post meta update "$ABOUT_ID" story_content "<p>What started as two developers freelancing out of a co-working space has grown into a team of 10 specialists spanning design, engineering, and strategy.</p><p>We've worked with startups finding product-market fit, mid-market companies scaling their digital presence, and enterprise organizations modernizing legacy platforms.</p><p>Our approach is simple: understand the business problem first, then build the right solution — not the trendiest one. We write clean code, ship on time, and pick up the phone when things break.</p>" 2>/dev/null

  # Values (repeater — 4 rows)
  wp post meta update "$ABOUT_ID" values 4 2>/dev/null
  wp post meta update "$ABOUT_ID" _values "field_ab_values" 2>/dev/null

  wp post meta update "$ABOUT_ID" values_0_value_title "Ship with Purpose" 2>/dev/null
  wp post meta update "$ABOUT_ID" _values_0_value_title "field_ab_value_title" 2>/dev/null
  wp post meta update "$ABOUT_ID" values_0_value_description "Every feature, every line of code should solve a real problem for real users. If it doesn't move the needle, it doesn't ship." 2>/dev/null
  wp post meta update "$ABOUT_ID" _values_0_value_description "field_ab_value_description" 2>/dev/null

  wp post meta update "$ABOUT_ID" values_1_value_title "Radical Candor" 2>/dev/null
  wp post meta update "$ABOUT_ID" _values_1_value_title "field_ab_value_title" 2>/dev/null
  wp post meta update "$ABOUT_ID" values_1_value_description "We tell clients what they need to hear, not just what they want to hear. Honest collaboration builds better products." 2>/dev/null
  wp post meta update "$ABOUT_ID" _values_1_value_description "field_ab_value_description" 2>/dev/null

  wp post meta update "$ABOUT_ID" values_2_value_title "Craft Over Hype" 2>/dev/null
  wp post meta update "$ABOUT_ID" _values_2_value_title "field_ab_value_title" 2>/dev/null
  wp post meta update "$ABOUT_ID" values_2_value_description "We'd rather build it right than build it fast. Quality compounds over time and outlasts every trend." 2>/dev/null
  wp post meta update "$ABOUT_ID" _values_2_value_description "field_ab_value_description" 2>/dev/null

  wp post meta update "$ABOUT_ID" values_3_value_title "Access for All" 2>/dev/null
  wp post meta update "$ABOUT_ID" _values_3_value_title "field_ab_value_title" 2>/dev/null
  wp post meta update "$ABOUT_ID" values_3_value_description "The web belongs to everyone. Accessibility and performance are non-negotiable baseline requirements." 2>/dev/null
  wp post meta update "$ABOUT_ID" _values_3_value_description "field_ab_value_description" 2>/dev/null

  # Team section intro
  wp post meta update "$ABOUT_ID" team_eyebrow "The team" 2>/dev/null
  wp post meta update "$ABOUT_ID" team_headline "Meet the people behind the pixels" 2>/dev/null
  wp post meta update "$ABOUT_ID" team_subheadline "A tight-knit crew of designers, developers, and strategists who care deeply about the work." 2>/dev/null

  # Join CTA
  wp post meta update "$ABOUT_ID" join_headline "Want to join the team?" 2>/dev/null
  wp post meta update "$ABOUT_ID" join_lede "We're always looking for talented people who care about craft. Check out our open roles." 2>/dev/null
  wp post meta update "$ABOUT_ID" join_button_label "View Open Positions" 2>/dev/null
  wp post meta update "$ABOUT_ID" join_button_url "$(wp option get home 2>/dev/null)/contact" 2>/dev/null
fi

# Set static front page
wp option update show_on_front page 2>/dev/null
wp option update page_on_front "$HOME_ID" 2>/dev/null

echo "   ✅ Pages created (Home: $HOME_ID, About: $ABOUT_ID)"

# --- Create Industry Terms ---
echo "🏷️  Creating taxonomy terms..."

wp term create industry "Healthcare" --slug=healthcare --quiet 2>/dev/null
wp term create industry "FinTech" --slug=fintech --quiet 2>/dev/null
wp term create industry "E-commerce" --slug=ecommerce --quiet 2>/dev/null
wp term create industry "Education" --slug=education --quiet 2>/dev/null
wp term create industry "SaaS" --slug=saas --quiet 2>/dev/null
wp term create industry "Nonprofit" --slug=nonprofit --quiet 2>/dev/null

wp term create technology "WordPress" --slug=wordpress --quiet 2>/dev/null
wp term create technology "React" --slug=react --quiet 2>/dev/null
wp term create technology "Next.js" --slug=nextjs --quiet 2>/dev/null
wp term create technology "Shopify" --slug=shopify --quiet 2>/dev/null
wp term create technology "Laravel" --slug=laravel --quiet 2>/dev/null
wp term create technology "Tailwind CSS" --slug=tailwind --quiet 2>/dev/null
wp term create technology "Node.js" --slug=nodejs --quiet 2>/dev/null
wp term create technology "Figma" --slug=figma --quiet 2>/dev/null

echo "   ✅ Taxonomy terms created"

# --- Create Case Studies ---
echo "📁 Creating case studies..."

CS1=$(wp post create --post_type=case_study --post_title="MedPortal Redesign" --post_status=publish \
  --post_content="<h2>The Challenge</h2><p>MedPortal's patient-facing dashboard was built in 2018 and hadn't been updated since. Patients struggled to find lab results, appointment scheduling was confusing, and mobile users were abandoning the site at alarming rates.</p><h2>Our Approach</h2><p>We ran a two-week discovery sprint with patients, nurses, and administrators to map the most critical user journeys. From there, we redesigned the information architecture, rebuilt the frontend in React, and connected everything to their existing FHIR-compliant API.</p><h2>The Result</h2><p>The new dashboard reduced average task completion time by 40% and mobile bounce rates dropped by 60%. Patient satisfaction scores improved from 3.2 to 4.7 out of 5.</p>" \
  --porcelain 2>/dev/null)
wp post term set "$CS1" industry healthcare 2>/dev/null
wp post term set "$CS1" technology react tailwind figma 2>/dev/null
wp post meta update "$CS1" project_overview "A HIPAA-compliant patient portal that unified appointment scheduling, lab results, and secure provider messaging into a single, accessible platform." 2>/dev/null
wp post meta update "$CS1" client_name      "MedPortal Health Systems" 2>/dev/null
wp post meta update "$CS1" project_year     "2025" 2>/dev/null
wp post meta update "$CS1" project_duration "14 Weeks" 2>/dev/null
wp post meta update "$CS1" project_services "Design, Development, Strategy" 2>/dev/null
wp post meta update "$CS1" project_url      "https://medportal.example.com" 2>/dev/null
wp post meta update "$CS1" project_results  "40% faster task completion · 60% drop in mobile bounce · CSAT 3.2 → 4.7" 2>/dev/null

CS2=$(wp post create --post_type=case_study --post_title="PayVault Brand Launch" --post_status=publish \
  --post_content="<h2>The Challenge</h2><p>PayVault needed to launch a new fintech brand from scratch — positioning, visual identity, and a marketing site — in just 8 weeks before their Series A announcement.</p><h2>Our Approach</h2><p>We embedded a designer and a developer directly with PayVault's founding team. We ran brand workshops in week one, had visual concepts by week two, and were building the production site by week three using Next.js and a headless CMS.</p><h2>The Result</h2><p>PayVault launched on time, the site converted at 12% on their waitlist signup, and the brand identity was featured in DesignRush's 'Best Fintech Brands of 2025.'</p>" \
  --porcelain 2>/dev/null)
wp post term set "$CS2" industry fintech 2>/dev/null
wp post term set "$CS2" technology nextjs tailwind nodejs figma 2>/dev/null
wp post meta update "$CS2" project_overview "A zero-to-launch fintech brand system — positioning, identity, and a marketing site — shipped in 8 weeks ahead of a Series A announcement." 2>/dev/null
wp post meta update "$CS2" client_name      "PayVault" 2>/dev/null
wp post meta update "$CS2" project_year     "2025" 2>/dev/null
wp post meta update "$CS2" project_duration "8 Weeks" 2>/dev/null
wp post meta update "$CS2" project_services "Brand, Design, Development" 2>/dev/null
wp post meta update "$CS2" project_results  "12% waitlist conversion · Launched on time · DesignRush 'Best Fintech 2025'" 2>/dev/null

CS3=$(wp post create --post_type=case_study --post_title="ThreadCraft E-Commerce Platform" --post_status=publish \
  --post_content="<h2>The Challenge</h2><p>ThreadCraft, a sustainable fashion brand, was outgrowing their Etsy storefront and needed a custom e-commerce experience that matched their premium brand positioning.</p><h2>Our Approach</h2><p>We built a custom Shopify Plus theme with a focus on editorial storytelling. Each product page tells the story of the artisan who made it. We integrated Klaviyo for email marketing and built custom collection filtering that felt fast and intuitive.</p><h2>The Result</h2><p>Revenue increased 85% in the first quarter after launch. Average session duration doubled, and the email list grew from 2,000 to 15,000 subscribers in 6 months.</p>" \
  --porcelain 2>/dev/null)
wp post term set "$CS3" industry ecommerce 2>/dev/null
wp post term set "$CS3" technology shopify tailwind figma 2>/dev/null
wp post meta update "$CS3" project_overview "A custom Shopify Plus storefront with editorial storytelling and artisan provenance on every product page." 2>/dev/null
wp post meta update "$CS3" client_name      "ThreadCraft" 2>/dev/null
wp post meta update "$CS3" project_year     "2024" 2>/dev/null
wp post meta update "$CS3" project_duration "12 Weeks" 2>/dev/null
wp post meta update "$CS3" project_services "Design, E-Commerce, Marketing Automation" 2>/dev/null
wp post meta update "$CS3" project_results  "+85% revenue (Q1 post-launch) · 2× avg. session · 2k → 15k email subs in 6 months" 2>/dev/null

CS4=$(wp post create --post_type=case_study --post_title="LearnPath LMS" --post_status=publish \
  --post_content="<h2>The Challenge</h2><p>LearnPath wanted to build a modern learning management system for corporate training. Existing LMS platforms were bloated, ugly, and frustrating for both learners and administrators.</p><h2>Our Approach</h2><p>We designed and built a custom LMS using Laravel and React, with a focus on clean UX and gamification elements. Course progress, certificates, and team analytics were core features from day one.</p><h2>The Result</h2><p>LearnPath signed 12 enterprise clients within 6 months of launch. Course completion rates averaged 78%, compared to the industry average of 20-30%.</p>" \
  --porcelain 2>/dev/null)
wp post term set "$CS4" industry education saas 2>/dev/null
wp post term set "$CS4" technology laravel react tailwind nodejs 2>/dev/null
wp post meta update "$CS4" project_overview "A modern corporate LMS built on Laravel + React, with gamification, certificates, and team-level analytics on day one." 2>/dev/null
wp post meta update "$CS4" client_name      "LearnPath" 2>/dev/null
wp post meta update "$CS4" project_year     "2024" 2>/dev/null
wp post meta update "$CS4" project_duration "20 Weeks" 2>/dev/null
wp post meta update "$CS4" project_services "Product Design, Full-Stack Development" 2>/dev/null
wp post meta update "$CS4" project_results  "12 enterprise clients in 6 months · 78% course completion (industry avg. 20–30%)" 2>/dev/null

CS5=$(wp post create --post_type=case_study --post_title="GreenRoots Foundation Website" --post_status=publish \
  --post_content="<h2>The Challenge</h2><p>GreenRoots Foundation needed a new website to increase online donations, tell their impact story, and make it easy for volunteers to sign up for local events.</p><h2>Our Approach</h2><p>We built a custom WordPress theme with ACF-powered content management so the small staff could update everything themselves. The donation flow was streamlined to three clicks, and we integrated with their existing Salesforce CRM.</p><h2>The Result</h2><p>Online donations increased 120% year-over-year. Volunteer sign-ups tripled, and the communications team went from dreading website updates to making them weekly.</p>" \
  --porcelain 2>/dev/null)
wp post term set "$CS5" industry nonprofit 2>/dev/null
wp post term set "$CS5" technology wordpress tailwind figma 2>/dev/null
wp post meta update "$CS5" project_overview "A fast, editable nonprofit site with a 3-click donation flow and Salesforce-synced volunteer sign-ups." 2>/dev/null
wp post meta update "$CS5" client_name      "GreenRoots Foundation" 2>/dev/null
wp post meta update "$CS5" project_year     "2024" 2>/dev/null
wp post meta update "$CS5" project_duration "10 Weeks" 2>/dev/null
wp post meta update "$CS5" project_services "Design, WordPress Development, CRM Integration" 2>/dev/null
wp post meta update "$CS5" project_results  "+120% YoY donations · 3× volunteer sign-ups · Weekly self-serve publishing" 2>/dev/null

CS6=$(wp post create --post_type=case_study --post_title="NexPay Dashboard" --post_status=publish \
  --post_content="<h2>The Challenge</h2><p>NexPay's internal analytics dashboard was a mess of spreadsheets and disconnected tools. Their team needed a single source of truth for transaction monitoring, fraud detection, and compliance reporting.</p><h2>Our Approach</h2><p>We built a real-time dashboard using React and Node.js, integrating with their payment processing APIs. The interface was designed for power users with keyboard shortcuts, customizable views, and exportable reports.</p><h2>The Result</h2><p>Fraud detection response time dropped from 4 hours to 15 minutes. The compliance team saved an estimated 20 hours per week on reporting.</p>" \
  --porcelain 2>/dev/null)
wp post term set "$CS6" industry fintech saas 2>/dev/null
wp post term set "$CS6" technology react nodejs tailwind 2>/dev/null
wp post meta update "$CS6" project_overview "A real-time analytics dashboard unifying transaction monitoring, fraud detection, and compliance reporting for power users." 2>/dev/null
wp post meta update "$CS6" client_name      "NexPay" 2>/dev/null
wp post meta update "$CS6" project_year     "2025" 2>/dev/null
wp post meta update "$CS6" project_duration "16 Weeks" 2>/dev/null
wp post meta update "$CS6" project_services "Product Design, Data Visualization, Engineering" 2>/dev/null
wp post meta update "$CS6" project_results  "Fraud response 4h → 15min · ~20h/week saved in compliance reporting" 2>/dev/null

echo "   ✅ 6 case studies created"

# --- Create Team Members ---
echo "👥 Creating team members..."

# Fields: name | title | short bio
declare -a MEMBERS=(
  "Jordan Kim|Founder & CEO|Full-stack strategist. 12 years in digital. Obsessed with performance budgets."
  "Sadie Patel|Creative Director|Brand-obsessed designer. Turns complex systems into intuitive interfaces."
  "Marcus Chen|Lead Engineer|WordPress core contributor. Writes code that other developers enjoy reading."
  "Aisha Robinson|UX Researcher|Turns user interviews into actionable design recommendations."
  "Tomás Navarro|Senior Developer|React and WordPress specialist. Accessibility advocate. Tailwind enthusiast."
  "Lily Whitfield|Project Manager|Keeps timelines tight and stakeholders happy. Certified Scrum Master."
  "Derek Olsen|SEO Strategist|Data nerd. Grew organic traffic 340% for a B2B client last quarter."
  "Rina Johal|UI Designer|Design systems thinker. Ensures every component feels intentional and cohesive."
  "Eliot Fang|DevOps Engineer|Manages infrastructure, CI/CD, and deployment pipelines. Uptime is his love language."
  "Nina Brooks|Content Strategist|Writes copy that converts. Believes every page should earn its place on the sitemap."
)

ORDER=0
for member_data in "${MEMBERS[@]}"; do
  IFS='|' read -r name title bio <<< "$member_data"
  TM_ID=$(wp post create --post_type=team_member --post_title="$name" --post_status=publish --menu_order=$ORDER --porcelain 2>/dev/null)
  wp post meta update "$TM_ID" job_title  "$title" 2>/dev/null
  wp post meta update "$TM_ID" short_bio  "$bio"   2>/dev/null
  wp post meta update "$TM_ID" linkedin_url "https://linkedin.com/in/example" 2>/dev/null
  ORDER=$((ORDER + 1))
done

echo "   ✅ 10 team members created"

# --- Create Navigation Menus ---
echo "🔗 Creating menus..."

PRIMARY_MENU=$(wp menu create "Primary Navigation" --porcelain 2>/dev/null)
wp menu item add-post "$PRIMARY_MENU" "$HOME_ID" --title="Home" --quiet 2>/dev/null
wp menu item add-post "$PRIMARY_MENU" "$ABOUT_ID" --title="About" --quiet 2>/dev/null
wp menu item add-post "$PRIMARY_MENU" "$CASE_STUDIES_ID" --title="Case Studies" --quiet 2>/dev/null
wp menu item add-post "$PRIMARY_MENU" "$CONTACT_ID" --title="Contact" --quiet 2>/dev/null
wp menu location assign "$PRIMARY_MENU" primary --quiet 2>/dev/null

FOOTER_MENU=$(wp menu create "Footer Navigation" --porcelain 2>/dev/null)
wp menu item add-post "$FOOTER_MENU" "$HOME_ID" --title="Home" --quiet 2>/dev/null
wp menu item add-post "$FOOTER_MENU" "$ABOUT_ID" --title="About" --quiet 2>/dev/null
wp menu item add-post "$FOOTER_MENU" "$CASE_STUDIES_ID" --title="Case Studies" --quiet 2>/dev/null
wp menu item add-post "$FOOTER_MENU" "$CONTACT_ID" --title="Contact" --quiet 2>/dev/null
wp menu location assign "$FOOTER_MENU" footer --quiet 2>/dev/null

echo "   ✅ Menus created and assigned"

# --- Set Permalink Structure ---
wp rewrite structure '/%postname%/' --quiet 2>/dev/null
wp rewrite flush --quiet 2>/dev/null

echo "   ✅ Permalinks set to /%postname%/"

# --- Activate Plugins ---
# NOTE: Timber 2.x ships with the theme via Composer (see functions.php).
# Do NOT activate the legacy `timber-library` plugin — it conflicts.
echo "🔌 Activating plugins..."
wp plugin activate advanced-custom-fields --quiet 2>/dev/null
wp plugin activate contact-form-7 --quiet 2>/dev/null

echo "   ✅ Plugins activated"

echo ""
echo "================================"
echo "🎉 Seed complete!"
echo "   Site: http://localhost:8888"
echo "   Admin: http://localhost:8888/wp-admin"
echo "   User: admin / password"
echo "================================"
