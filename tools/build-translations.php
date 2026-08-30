<?php
/**
 * Regenerates the Bengali (bn_BD) .po/.mo translation files for the
 * Tripdesh theme and tripdesh-core plugin from the tables below.
 *
 * Run with: php tools/build-translations.php
 *
 * Why this exists instead of a standard xgettext/msgfmt toolchain: this
 * environment has no gettext CLI tools installed, and the translation set
 * is curated by hand (professional Bangladeshi Bengali, not machine
 * translation) rather than extracted+auto-translated. To add or change a
 * string: add/edit the matching msgid => msgstr pair below, then re-run
 * this script and commit the updated .po/.mo files.
 *
 * Both the theme and the plugin use the same text domain ("tripdesh") but
 * load their .mo from their own languages/ directory, so each needs its
 * own compiled file even where some strings overlap.
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( "Run from the CLI: php tools/build-translations.php\n" );
}

$root = dirname( __DIR__ );

/**
 * NOTE ON ADMIN vs FRONT-END:
 * These translations only ever apply on the front end (and REST API
 * responses) — wp-content/plugins/tripdesh-core/tripdesh-core.php forces
 * the bn_BD locale for !is_admin() requests only. wp-admin keeps whatever
 * locale the site is actually configured for (typically English), so
 * admin-only strings translated below are harmless: they simply never
 * load in wp-admin unless the site owner also sets their WP admin
 * language to Bengali.
 *
 * One deliberate exclusion: the AI concierge's English-language fallback
 * message (in class-ai-concierge.php) is NOT translated here, because a
 * visitor who explicitly toggles the chat widget to English should still
 * see English — that toggle is independent of the site-wide locale.
 */

$theme_strings = array(
	"Sylhet's Tea Gardens" => 'সিলেটের চা-বাগান',
	"Tell the assistant your starting point, dates, budget, and who you're travelling with — it will suggest destinations and tour packages we actually offer, and build a day-by-day plan. Booking still goes through a real person before you pay."
		=> 'আপনার যাত্রা শুরুর স্থান, তারিখ, বাজেট এবং কাদের সাথে ভ্রমণ করছেন তা জানান — আমাদের প্রকৃত গন্তব্য ও ট্যুর প্যাকেজ অনুযায়ী পরামর্শ দেবে এবং দিনভিত্তিক পরিকল্পনা তৈরি করে দেবে। পেমেন্টের আগে বুকিং সবসময় আমাদের টিমের মাধ্যমে নিশ্চিত হয়।',
	'%1$s days / %2$s nights' => '%1$s দিন / %2$s রাত',
	'%s / night' => '%s / রাত',
	'A real team confirms every booking before you pay.' => 'পেমেন্টের আগে প্রতিটি বুকিং যাচাই করে আমাদের প্রকৃত টিম।',
	'AI Trip Planner' => 'AI ট্রিপ প্ল্যানার',
	'AI Trip Planning' => 'AI ভ্রমণ পরিকল্পনা',
	'About Us' => 'আমাদের সম্পর্কে',
	'Activities' => 'অভিজ্ঞতা',
	'Add a contact form via a plugin (e.g. Contact Form 7) below this content block, or configure phone/email/WhatsApp under Settings → Tripdesh.'
		=> 'এই কনটেন্ট ব্লকের নিচে কোনো প্লাগইন (যেমন Contact Form 7) দিয়ে যোগাযোগ ফর্ম যুক্ত করুন, অথবা Settings → Tripdesh থেকে ফোন/ইমেইল/হোয়াটসঅ্যাপ সেট করুন।',
	'Add a newsletter signup widget under Appearance → Widgets → Footer Column 1.'
		=> 'Appearance → Widgets → Footer Column 1 থেকে নিউজলেটার সাবস্ক্রিপশন উইজেট যুক্ত করুন।',
	'Adventure Tours' => 'অ্যাডভেঞ্চার ট্যুর',
	'All rights reserved.' => 'সর্বস্বত্ব সংরক্ষিত।',
	'Amenities' => 'সুযোগ-সুবিধা',
	'Ask the AI to Plan a Trip Here' => 'AI দিয়ে এখানকার ভ্রমণ পরিকল্পনা করুন',
	'Best Time to Visit' => 'ভ্রমণের সেরা সময়',
	'Blog Sidebar' => 'ব্লগ সাইডবার',
	'Breadcrumb' => 'পথনির্দেশনা',
	'Cancellation Policy' => 'বাতিলকরণ নীতি',
	'Chat with our AI Travel Assistant in Bangla or English and get a personalized plan.'
		=> 'বাংলা অথবা ইংরেজিতে আমাদের AI ভ্রমণ সহকারীর সাথে কথা বলুন এবং নিজের জন্য পরিকল্পনা তৈরি করুন।',
	'Clear BDT pricing, no hidden fees.' => 'স্বচ্ছ টাকার হিসাব, কোনো লুকানো খরচ নেই।',
	'Company' => 'প্রতিষ্ঠান',
	'Contact' => 'যোগাযোগ',
	'Contact:' => 'যোগাযোগ:',
	'Couple Packages' => 'কাপল প্যাকেজ',
	'Day %d' => 'দিন %d',
	'Day-by-Day Itinerary' => 'দিনভিত্তিক ভ্রমণ পরিকল্পনা',
	'Deals' => 'অফার',
	'Departs from %s' => '%s থেকে যাত্রা শুরু',
	'Destinations' => 'গন্তব্যসমূহ',
	'Email' => 'ইমেইল',
	'Email:' => 'ইমেইল:',
	'Estimated budget: %s / person' => 'আনুমানিক বাজেট: %s / জনপ্রতি',
	'Explore' => 'ঘুরে দেখুন',
	'FAQ' => 'সচরাচর জিজ্ঞাসা',
	'Family Tours' => 'পারিবারিক ট্যুর',
	'Find the best travel experiences in Bangladesh, matched to your budget, time, and taste.'
		=> 'আপনার বাজেট, সময় ও পছন্দ অনুযায়ী বাংলাদেশের সেরা ভ্রমণ অভিজ্ঞতা খুঁজে নিন।',
	'Food' => 'খাবার',
	'Footer Column 1' => 'ফুটার কলাম ১',
	'Footer Menu' => 'ফুটার মেনু',
	'From %s / person' => '%s থেকে শুরু / জনপ্রতি',
	'Home' => 'হোম',
	'Hotels' => 'হোটেল',
	'How to Reach' => 'কীভাবে যাবেন',
	'Human Support' => 'মানবিক সহায়তা',
	'Local Experts' => 'স্থানীয় বিশেষজ্ঞ',
	'Local Transportation' => 'স্থানীয় যাতায়াত',
	'Menu' => 'মেনু',
	'Newsletter' => 'নিউজলেটার',
	'No tour packages listed yet for this destination.' => 'এই গন্তব্যের জন্য এখনো কোনো ট্যুর প্যাকেজ যুক্ত করা হয়নি।',
	'Not Included' => 'যা অন্তর্ভুক্ত নয়',
	'Not sure where to go?' => 'কোথায় যাবেন বুঝতে পারছেন না?',
	'Nothing found.' => 'কিছু পাওয়া যায়নি।',
	'Phone' => 'ফোন',
	'Phone:' => 'ফোন:',
	'Plan My Trip' => 'আমার ভ্রমণ পরিকল্পনা করুন',
	'Policies' => 'নীতিমালা',
	'Popular Destinations' => 'জনপ্রিয় গন্তব্য',
	'Popular Tour Packages' => 'জনপ্রিয় ট্যুর প্যাকেজ',
	'Primary Menu' => 'প্রধান মেনু',
	'Primary' => 'প্রধান',
	'Privacy Policy' => 'গোপনীয়তা নীতি',
	'Read more' => 'আরও পড়ুন',
	'Real knowledge of Bangladesh destinations, not generic packages.' => 'বাংলাদেশের গন্তব্য সম্পর্কে প্রকৃত অভিজ্ঞতা, সাধারণ কোনো প্যাকেজ নয়।',
	'Recommended Hotels' => 'প্রস্তাবিত হোটেল',
	'Room Types' => 'রুমের ধরন',
	'Safety Information' => 'নিরাপত্তা তথ্য',
	'Seasonal offers and travel guides, straight to your inbox.' => 'মৌসুমি অফার ও ভ্রমণ গাইড সরাসরি আপনার ইনবক্সে।',
	'Skip to content' => 'মূল কনটেন্টে যান',
	'Tell us your budget and dates — get a plan in seconds, in Bangla or English.'
		=> 'আপনার বাজেট ও তারিখ জানান — কয়েক সেকেন্ডে বাংলা অথবা ইংরেজিতে পরিকল্পনা পেয়ে যান।',
	'Terms & Conditions' => 'শর্তাবলী',
	'Things to Do' => 'যা করবেন',
	'Tour Packages' => 'ট্যুর প্যাকেজ',
	'Tours to this Destination' => 'এই গন্তব্যের ট্যুরসমূহ',
	'Transparent Pricing' => 'স্বচ্ছ মূল্য নির্ধারণ',
	'Transportation' => 'যাতায়াত',
	'Travel Bangladesh, with Tripdesh' => 'বাংলাদেশ ঘুরুন, ট্রিপদেশের সাথে',
	'Travel Guides' => 'ভ্রমণ গাইড',
	"Explore Sylhet's Tea Gardens" => 'সিলেটের চা-বাগানে ঘুরে আসুন',
	'Deal' => 'অফার',
	'No active deals right now — check back soon.' => 'এখন কোনো চলমান অফার নেই — শীঘ্রই আবার দেখুন।',
	'View all' => 'সব দেখুন',
	'Weekend Trips' => 'উইকএন্ড ট্রিপ',
	'What Travelers Say' => 'ভ্রমণকারীদের মতামত',
	"What's Included" => 'যা অন্তর্ভুক্ত থাকছে',
	'WhatsApp' => 'হোয়াটসঅ্যাপ',
	'WhatsApp:' => 'হোয়াটসঅ্যাপ:',
	'Where do you want to go?' => 'কোথায় যেতে চান?',
	'Where to Stay' => 'কোথায় থাকবেন',
	'Why Choose Tripdesh' => 'কেন ট্রিপদেশ বেছে নেবেন',
	"Your trusted partner for exploring Bangladesh — Cox's Bazar, Sylhet, Bandarban, Sundarbans and beyond. Bengali & English support."
		=> 'বাংলাদেশ ভ্রমণে আপনার বিশ্বস্ত সঙ্গী — কক্সবাজার, সিলেট, বান্দরবান, সুন্দরবনসহ আরও অনেক গন্তব্য। বাংলা ও ইংরেজি — দুই ভাষাতেই সহায়তা।',
);

$theme_plurals = array(
	// [ singular, plural, bengali (single form covers both — Bengali nouns don't inflect for count) ]
	array( '%s day', '%s days', '%s দিন' ),
);

$plugin_strings = array(
	"A new booking request has come in.\n\nReview it here: %s" => "নতুন একটি বুকিং অনুরোধ এসেছে।\n\nবিস্তারিত দেখুন এখানে: %s",
	"Cox's Bazar, Sylhet…" => 'কক্সবাজার, সিলেট…',
	"Hi %1\$s,\n\nThanks for your booking request. Your reference is %2\$s.\n\nOur team will confirm availability and reach out to arrange payment. If you have questions, reply to this email.\n\n— Tripdesh"
		=> "প্রিয় %1\$s,\n\nআপনার বুকিং অনুরোধের জন্য ধন্যবাদ। আপনার রেফারেন্স নম্বর: %2\$s।\n\nআমাদের টিম প্রাপ্যতা যাচাই করে পেমেন্টের ব্যবস্থার জন্য যোগাযোগ করবে। কোনো প্রশ্ন থাকলে এই ইমেইলে উত্তর দিন।\n\n— ট্রিপদেশ",
	"Hi! Tell me your budget, number of days, and who you're travelling with, and I'll suggest destinations in Bangladesh."
		=> 'হাই! আপনার বাজেট, কত দিনের ভ্রমণ এবং কাদের সাথে যাচ্ছেন তা জানান, আমি বাংলাদেশের সেরা গন্তব্য সাজেস্ট করবো।',
	'AI Concierge' => 'AI কনসিয়ার্জ',
	'API Key' => 'API কী',
	'Activities' => 'অভিজ্ঞতা',
	'Activity' => 'অভিজ্ঞতা',
	'Add New %s' => 'নতুন %s যুক্ত করুন',
	'Address' => 'ঠিকানা',
	'Amenities' => 'সুযোগ-সুবিধা',
	'Any' => 'যেকোনো',
	'Availability Notes' => 'প্রাপ্যতা সংক্রান্ত তথ্য',
	'Awaiting Payment' => 'পেমেন্টের অপেক্ষায়',
	'Bangla Demo Content' => 'বাংলা ডেমো কনটেন্ট',
	'Best Time to Visit' => 'ভ্রমণের সেরা সময়',
	'Booking Status' => 'বুকিং স্ট্যাটাস',
	'Booking Summary' => 'বুকিং সারসংক্ষেপ',
	'Booking request failed.' => 'বুকিং অনুরোধ ব্যর্থ হয়েছে।',
	'Booking' => 'বুকিং',
	'Bookings are captured without payment until a gateway is implemented against these settings. See ARCHITECTURE.md.'
		=> 'পেমেন্ট গেটওয়ে চালু না হওয়া পর্যন্ত বুকিং কোনো পেমেন্ট ছাড়াই গ্রহণ করা হয়। বিস্তারিত দেখুন ARCHITECTURE.md-এ।',
	'Bookings' => 'বুকিংসমূহ',
	'Budget (BDT)' => 'বাজেট (টাকা)',
	'Business Info' => 'ব্যবসায়িক তথ্য',
	'Cancelled' => 'বাতিল হয়েছে',
	'Confirm manually once payment is verified. Automated status transitions arrive with the Phase 2 payment gateway.'
		=> 'পেমেন্ট নিশ্চিত হলে হাতে-কলমে স্ট্যাটাস কনফার্ম করুন। পেমেন্ট গেটওয়ে চালু হলে (দ্বিতীয় ধাপে) এটি স্বয়ংক্রিয় হবে।',
	'Confirmed' => 'নিশ্চিত হয়েছে',
	'Contact Email' => 'যোগাযোগের ইমেইল',
	'Contact Phone' => 'যোগাযোগের ফোন নম্বর',
	'Could not create the booking. Please try again.' => 'বুকিং তৈরি করা যায়নি। আবার চেষ্টা করুন।',
	'Creates the 25 core Bangladesh destinations (Bengali titles, English URL slugs) if they do not already exist, and tags the Sylhet tea-garden group for the homepage featured section. Safe to run more than once — existing destinations (matched by slug) are never modified or duplicated.'
		=> 'বাংলাদেশের ২৫টি মূল গন্তব্য (বাংলা শিরোনাম, ইংরেজি URL স্লাগসহ) তৈরি করে, যদি সেগুলো আগে থেকে না থাকে, এবং হোমপেজের ফিচার্ড সেকশনের জন্য সিলেটের চা-বাগান গ্রুপ ট্যাগ করে দেয়। একাধিকবার চালানো নিরাপদ — বিদ্যমান গন্তব্য (স্লাগ অনুযায়ী মিলিয়ে) কখনও পরিবর্তন বা ডুপ্লিকেট করা হয় না।',
	'Currency' => 'মুদ্রা',
	'Customer Name' => 'গ্রাহকের নাম',
	'Customer Price (BDT, per person)' => 'গ্রাহক মূল্য (টাকা, জনপ্রতি)',
	'Customer' => 'গ্রাহক',
	'Deal / Sale Price (BDT, optional — leave blank if not on offer)' => 'অফার মূল্য (টাকা, ঐচ্ছিক — অফার না থাকলে খালি রাখুন)',
	'Day-by-Day Itinerary' => 'দিনভিত্তিক ভ্রমণ পরিকল্পনা',
	'Departure Location' => 'যাত্রা শুরুর স্থান',
	'Destination' => 'গন্তব্য',
	'Destinations' => 'গন্তব্যসমূহ',
	'Duration (days)' => 'সময়কাল (দিন)',
	'Duration (nights)' => 'সময়কাল (রাত)',
	'Duration' => 'সময়কাল',
	'Edit %s' => '%s সম্পাদনা করুন',
	'Email' => 'ইমেইল',
	'Estimated Budget (BDT, per person)' => 'আনুমানিক বাজেট (টাকা, জনপ্রতি)',
	'Exclusions' => 'যা অন্তর্ভুক্ত নয়',
	'Featured Collection Key (e.g. tea_garden)' => 'ফিচার্ড কালেকশন কী (যেমন: tea_garden)',
	'Food' => 'খাবার',
	'From' => 'থেকে',
	'Full Name' => 'পূর্ণ নাম',
	'Gateway' => 'গেটওয়ে',
	'Hotel' => 'হোটেল',
	'Hotels' => 'হোটেল',
	'How many travelers?' => 'কতজন ভ্রমণ করবেন?',
	'How to Reach' => 'কীভাবে যাবেন',
	'Import Bangla Demo Destinations' => 'বাংলা ডেমো গন্তব্য ইমপোর্ট করুন',
	'Inclusions' => 'যা অন্তর্ভুক্ত থাকছে',
	'Invalid request body.' => 'অনুরোধের তথ্য সঠিক নয়।',
	'Leave the API key blank to keep the AI concierge in fallback mode (a friendly bilingual message instead of live recommendations). The key is used server-side only and never sent to the browser.'
		=> 'API কী খালি রাখলে AI কনসিয়ার্জ ফলব্যাক মোডে থাকবে (লাইভ সাজেশনের বদলে একটি বন্ধুত্বপূর্ণ দ্বিভাষিক বার্তা দেখাবে)। কী শুধুমাত্র সার্ভার সাইডে ব্যবহৃত হয়, ব্রাউজারে কখনো পাঠানো হয় না।',
	'Local Transportation' => 'স্থানীয় যাতায়াত',
	'Location' => 'অবস্থান',
	'Locations' => 'অবস্থানসমূহ',
	'Max Travelers per Booking' => 'প্রতি বুকিংয়ে সর্বোচ্চ ভ্রমণকারী',
	'Message is too long.' => 'বার্তাটি অনেক বড়।',
	'Mode (bus/train/car/microbus/boat/launch)' => 'ধরন (বাস/ট্রেন/গাড়ি/মাইক্রোবাস/নৌকা/লঞ্চ)',
	'Model' => 'মডেল',
	'New booking request — %s' => 'নতুন বুকিং অনুরোধ — %s',
	'No %s found' => 'কোনো %s পাওয়া যায়নি',
	'No payment is taken now. Our team will confirm availability and follow up with payment instructions.'
		=> 'এখনই কোনো পেমেন্ট নেওয়া হচ্ছে না। আমাদের টিম প্রাপ্যতা নিশ্চিত করে পেমেন্টের নির্দেশনা জানাবে।',
	'None (manual confirmation)' => 'কোনোটি না (হাতে-কলমে নিশ্চিতকরণ)',
	'Notes (optional)' => 'মন্তব্য (ঐচ্ছিক)',
	'Notes' => 'মন্তব্য',
	'Number of Travelers' => 'ভ্রমণকারীর সংখ্যা',
	'One line per day: "Day N: description". Rendered as a numbered itinerary on the front end.'
		=> 'প্রতিদিনের জন্য একটি লাইন: "Day N: বিবরণ"। ফ্রন্ট-এন্ডে ক্রমিক আকারে দেখানো হবে।',
	'Operator' => 'অপারেটর',
	'Payment Gateway (Phase 2)' => 'পেমেন্ট গেটওয়ে (দ্বিতীয় ধাপ)',
	'Pending' => 'অপেক্ষমাণ',
	'Phone' => 'ফোন',
	'Please enter a message.' => 'একটি বার্তা লিখুন।',
	'Please provide your name, a valid phone number, and a valid email address.'
		=> 'অনুগ্রহ করে আপনার নাম, সঠিক ফোন নম্বর এবং সঠিক ইমেইল ঠিকানা দিন।',
	'Policies (check-in/out, cancellation)' => 'নীতিমালা (চেক-ইন/আউট, বাতিলকরণ)',
	'Price (BDT)' => 'মূল্য (টাকা)',
	'Price (BDT, per person)' => 'মূল্য (টাকা, জনপ্রতি)',
	'Price per Night (BDT)' => 'প্রতি রাতের মূল্য (টাকা)',
	'Product' => 'পণ্য',
	'Provider' => 'প্রোভাইডার',
	'Rating (1-5)' => 'রেটিং (১-৫)',
	'Recommended Trip Length (days)' => 'প্রস্তাবিত ভ্রমণকাল (দিন)',
	'Reference' => 'রেফারেন্স',
	'Request Booking' => 'বুকিং নিশ্চিত করুন',
	'Request to Book' => 'এখনই বুক করুন',
	'Room Types' => 'রুমের ধরন',
	'SSLCommerz Store ID' => 'SSLCommerz স্টোর আইডি',
	'SSLCommerz Store Password' => 'SSLCommerz স্টোর পাসওয়ার্ড',
	'Safety Information' => 'নিরাপত্তা তথ্য',
	'Sandbox Mode' => 'স্যান্ডবক্স মোড',
	'Search %s' => '%s খুঁজুন',
	'Search Trips' => 'ভ্রমণ খুঁজুন',
	'Send' => 'পাঠান',
	'Sending…' => 'পাঠানো হচ্ছে…',
	'Something went wrong. Please try again.' => 'কিছু একটা ভুল হয়েছে। আবার চেষ্টা করুন।',
	'Star Rating (1-5)' => 'স্টার রেটিং (১-৫)',
	'Supplier Price (BDT, internal)' => 'সরবরাহকারী মূল্য (টাকা, অভ্যন্তরীণ)',
	'Terms & Conditions' => 'শর্তাবলী',
	'Testimonial' => 'প্রতিক্রিয়া',
	'Testimonials' => 'প্রতিক্রিয়াসমূহ',
	'The selected tour, hotel, or activity could not be found.' => 'নির্বাচিত ট্যুর, হোটেল বা অভিজ্ঞতাটি খুঁজে পাওয়া যায়নি।',
	'Things to Do' => 'যা করবেন',
	'Thinking…' => 'ভাবছি…',
	'To' => 'পর্যন্ত',
	'Too many requests. Please try again shortly.' => 'অনেকবার অনুরোধ করা হয়েছে। কিছুক্ষণ পর আবার চেষ্টা করুন।',
	'Total Price' => 'সর্বমোট মূল্য',
	'Tour Package' => 'ট্যুর প্যাকেজ',
	'Tour Packages' => 'ট্যুর প্যাকেজ',
	'Tour Tier' => 'ট্যুর মান',
	'Tour Tiers' => 'ট্যুর মানসমূহ',
	'Transport Option' => 'যাতায়াত অপশন',
	'Transportation' => 'যাতায়াত',
	'Travel Date' => 'ভ্রমণের তারিখ',
	'Travel Style' => 'ভ্রমণের ধরন',
	'Travel Styles' => 'ভ্রমণের ধরনসমূহ',
	'Travel Type' => 'ভ্রমণের ধরন',
	'Travelers' => 'ভ্রমণকারী',
	'Trip Taken' => 'ভ্রমণকৃত ট্রিপ',
	'Tripdesh AI Travel Assistant' => 'ট্রিপদেশ AI ভ্রমণ সহকারী',
	'Tripdesh Details' => 'ট্রিপদেশ বিস্তারিত তথ্য',
	'Tripdesh Settings' => 'ট্রিপদেশ সেটিংস',
	'Tripdesh booking request received — %s' => 'ট্রিপদেশ বুকিং অনুরোধ গৃহীত হয়েছে — %s',
	'Tripdesh' => 'ট্রিপদেশ',
	'Use sandbox/test credentials' => 'স্যান্ডবক্স/টেস্ট ক্রেডেনশিয়াল ব্যবহার করুন',
	'View %s' => '%s দেখুন',
	"What's Included" => 'যা অন্তর্ভুক্ত থাকছে',
	'WhatsApp Number' => 'হোয়াটসঅ্যাপ নম্বর',
	'Where to Stay' => 'কোথায় থাকবেন',
	'You are not allowed to do this.' => 'এই কাজটি করার অনুমতি আপনার নেই।',
	'Your booking number' => 'আপনার বুকিং নম্বর',
	'Your request has been received. Our team will confirm availability and contact you shortly.'
		=> 'বুকিং সফল হয়েছে! আপনার অনুরোধ গৃহীত হয়েছে। আমাদের টিম প্রাপ্যতা যাচাই করে শীঘ্রই যোগাযোগ করবে।',
	'e.g. 3 days, 20,000 BDT, family from Dhaka…' => 'যেমন: ৩ দিন, ২০,০০০ টাকা, ঢাকা থেকে পরিবার নিয়ে…',
	'per person' => 'জনপ্রতি',
);

$plugin_plurals = array(
	array( '%d destination created.', '%d destinations created.', '%d টি গন্তব্য তৈরি হয়েছে।' ),
);

/**
 * Compiles $strings/$plurals into .po (human-readable) and .mo (binary,
 * what WordPress actually loads) files at $dir/tripdesh-bn_BD.{po,mo}.
 */
function tripdesh_write_translation( string $dir, array $strings, array $plurals ): void {
	if ( ! is_dir( $dir ) ) {
		mkdir( $dir, 0775, true );
	}

	$po = tripdesh_build_po( $strings, $plurals );
	file_put_contents( $dir . '/tripdesh-bn_BD.po', $po );

	$mo = tripdesh_build_mo( $strings, $plurals );
	file_put_contents( $dir . '/tripdesh-bn_BD.mo', $mo );

	printf( "Wrote %d strings + %d plural forms -> %s\n", count( $strings ), count( $plurals ), $dir );
}

function tripdesh_po_escape( string $s ): string {
	return str_replace( array( '\\', "\"", "\n", "\t" ), array( '\\\\', '\\"', '\\n', '\\t' ), $s );
}

function tripdesh_build_po( array $strings, array $plurals ): string {
	$out = "msgid \"\"\nmsgstr \"\"\n"
		. "\"Project-Id-Version: Tripdesh 1.0\\n\"\n"
		. "\"POT-Creation-Date: 2026-08-30\\n\"\n"
		. "\"MIME-Version: 1.0\\n\"\n"
		. "\"Content-Type: text/plain; charset=UTF-8\\n\"\n"
		. "\"Content-Transfer-Encoding: 8bit\\n\"\n"
		. "\"Language: bn_BD\\n\"\n"
		. "\"Plural-Forms: nplurals=2; plural=(n != 1);\\n\"\n\n";

	foreach ( $strings as $msgid => $msgstr ) {
		$out .= 'msgid "' . tripdesh_po_escape( $msgid ) . "\"\n";
		$out .= 'msgstr "' . tripdesh_po_escape( $msgstr ) . "\"\n\n";
	}

	foreach ( $plurals as $p ) {
		list( $singular, $plural, $bn ) = $p;
		$out .= 'msgid "' . tripdesh_po_escape( $singular ) . "\"\n";
		$out .= 'msgid_plural "' . tripdesh_po_escape( $plural ) . "\"\n";
		$out .= 'msgstr[0] "' . tripdesh_po_escape( $bn ) . "\"\n";
		$out .= 'msgstr[1] "' . tripdesh_po_escape( $bn ) . "\"\n\n";
	}

	return $out;
}

/**
 * Minimal GNU MO (binary gettext) writer. Format reference:
 * https://www.gnu.org/software/gettext/manual/html_node/MO-Files.html
 */
function tripdesh_build_mo( array $strings, array $plurals ): string {
	$entries = array();

	$header = "Project-Id-Version: Tripdesh 1.0\n"
		. "POT-Creation-Date: 2026-08-30\n"
		. "MIME-Version: 1.0\n"
		. "Content-Type: text/plain; charset=UTF-8\n"
		. "Content-Transfer-Encoding: 8bit\n"
		. "Language: bn_BD\n"
		. "Plural-Forms: nplurals=2; plural=(n != 1);\n";
	$entries[''] = $header;

	foreach ( $strings as $msgid => $msgstr ) {
		$entries[ $msgid ] = $msgstr;
	}
	foreach ( $plurals as $p ) {
		list( $singular, $plural, $bn ) = $p;
		$key             = $singular . "\x00" . $plural;
		$entries[ $key ] = $bn . "\x00" . $bn;
	}

	ksort( $entries, SORT_STRING );

	$n            = count( $entries );
	$ids          = array_keys( $entries );
	$strs         = array_values( $entries );

	$ids_offsets  = array();
	$strs_offsets = array();

	$ids_blob  = '';
	$strs_blob = '';

	foreach ( $ids as $id ) {
		$ids_offsets[] = array( strlen( $id ), strlen( $ids_blob ) );
		$ids_blob     .= $id . "\x00";
	}
	foreach ( $strs as $str ) {
		$strs_offsets[] = array( strlen( $str ), strlen( $strs_blob ) );
		$strs_blob     .= $str . "\x00";
	}

	// Header layout (7 uint32) + 2 tables of (length,offset) pairs.
	$header_size      = 28;
	$table_size       = 8 * $n;
	$ids_table_offset = $header_size;
	$strs_table_offset = $ids_table_offset + $table_size;
	$ids_blob_offset  = $strs_table_offset + $table_size;
	$strs_blob_offset = $ids_blob_offset + strlen( $ids_blob );

	$out  = pack( 'V', 0x950412de ); // magic
	$out .= pack( 'V', 0 );          // revision
	$out .= pack( 'V', $n );         // number of strings
	$out .= pack( 'V', $ids_table_offset );
	$out .= pack( 'V', $strs_table_offset );
	$out .= pack( 'V', 0 ); // hash table size (unused)
	$out .= pack( 'V', 0 ); // hash table offset (unused)

	foreach ( $ids_offsets as $o ) {
		$out .= pack( 'V', $o[0] ) . pack( 'V', $ids_blob_offset + $o[1] );
	}
	foreach ( $strs_offsets as $o ) {
		$out .= pack( 'V', $o[0] ) . pack( 'V', $strs_blob_offset + $o[1] );
	}

	$out .= $ids_blob;
	$out .= $strs_blob;

	return $out;
}

tripdesh_write_translation( $root . '/wp-content/themes/tripdesh/languages', $theme_strings, $theme_plurals );
tripdesh_write_translation( $root . '/wp-content/plugins/tripdesh-core/languages', $plugin_strings, $plugin_plurals );

echo "Done.\n";
