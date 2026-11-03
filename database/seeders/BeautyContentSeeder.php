<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\Concerns\SeedsImages;
use Illuminate\Database\Seeder;

/**
 * One starter article for each Beauty & Style subcategory. Existing posts are never touched,
 * so edits made in the admin stay. Run on its own: php artisan db:seed --class=BeautyContentSeeder --force
 */
class BeautyContentSeeder extends Seeder
{
    use SeedsImages;

    private const NOTE = '<p><em>These tips are general information, not medical advice. Patch test any new product, and see a qualified dermatologist or doctor for rashes, hair loss, infections or any problem that does not settle.</em></p>';

    public function run(): void
    {
        $author = User::where('email', 'contactstarsinfos@gmail.com')->value('id') ?? User::value('id');

        foreach (array_values($this->articles()) as $index => $article) {
            $category = Category::where('slug', $article['category'])->value('id');

            if (! $category) {
                $this->command?->warn("Skipped {$article['slug']}: no {$article['category']} category");

                continue;
            }

            Post::firstOrCreate(['slug' => $article['slug']], [
                'category_id' => $category,
                'author_id' => $author,
                'title' => $article['title'],
                'excerpt' => $article['excerpt'],
                'body' => $article['body'].$this->related($article['related']).self::NOTE,
                'featured_image' => $this->seedImage($article['image']),
                'meta_title' => $article['meta_title'],
                'meta_description' => $article['meta_description'],
                'tags' => $article['tags'],
                'faqs' => $article['faqs'],
                'status' => 'published',
                'is_trending' => $index < 2,
                'published_at' => now()->subHours($index * 5 + 1),
            ]);
        }
    }

    /** @param array<string, string> $links label => site path */
    private function related(array $links): string
    {
        $items = collect($links)->map(fn (string $url, string $label) => "<li><a href=\"{$url}\">{$label}</a></li>")->implode('');

        return "<h2>Keep Reading</h2><ul>{$items}</ul>";
    }

    /** @param array<string, string> $rows label => value */
    private function table(string $first, string $second, array $rows): string
    {
        $cells = collect($rows)->map(fn (string $value, string $label) => "<tr><td>{$label}</td><td>{$value}</td></tr>")->implode('');

        return "<table><thead><tr><th>{$first}</th><th>{$second}</th></tr></thead><tbody>{$cells}</tbody></table>";
    }

    private function faqs(array $pairs): array
    {
        return collect($pairs)->map(fn (string $answer, string $question) => ['question' => $question, 'answer' => $answer])->values()->all();
    }

    private function articles(): array
    {
        return [
            [
                'category' => 'skincare',
                'slug' => 'simple-daily-skincare-routine-for-pakistani-weather',
                'image' => 'beauty-skincare-routine',
                'title' => 'Simple Daily Skincare Routine for Pakistani Weather',
                'meta_title' => 'Simple Daily Skincare Routine for Pakistan’s Heat, Dust and Winter',
                'meta_description' => 'An easy morning and night skincare routine for hot, humid and dusty weather in Pakistan, with tips for oily, dry and sensitive skin.',
                'excerpt' => 'Cleanse, moisturise and protect: a three-step routine that works in Karachi humidity, Lahore heat and dry northern winters.',
                'tags' => ['Skincare', 'Daily Routine', 'Sunscreen', 'Oily Skin', 'Pakistan Weather'],
                'body' => '<p>A good skincare routine does not need ten products. In Pakistan, heat, dust, humidity and strong sun are the main things your skin deals with every day, so a short routine you can follow daily beats a long one you skip.</p>'
                    .'<h2>The Three Basic Steps</h2><ol><li><strong>Cleanse</strong> with a gentle face wash to remove sweat, dust and oil.</li><li><strong>Moisturise</strong> with a light lotion or gel, even if your skin is oily.</li><li><strong>Protect</strong> with a broad-spectrum sunscreen of SPF 30 or higher every morning.</li></ol>'
                    .'<h2>Morning and Night</h2>'
                    .$this->table('Time', 'What to do', ['Morning' => 'Gentle cleanser, light moisturiser, sunscreen', 'Afternoon' => 'Splash with water or blot oil; reapply sunscreen if you are outdoors', 'Night' => 'Remove makeup and sunscreen, cleanse, then moisturise'])
                    .'<h2>Choose Products by Skin Type</h2><ul><li><strong>Oily skin:</strong> gel moisturiser and a sunscreen labelled non-comedogenic or oil-free.</li><li><strong>Dry skin:</strong> cream moisturiser, especially in winter, and a mild non-foaming cleanser.</li><li><strong>Sensitive skin:</strong> fragrance-free products, and only one new product at a time.</li></ul>'
                    .'<h2>Seasonal Changes</h2><p>In summer, lighter textures and regular sunscreen matter most. In winter, especially in the north, switch to a richer cream and avoid very hot water on your face.</p>'
                    .'<h2>Common Mistakes</h2><ul><li>Scrubbing hard or using harsh soap on the face.</li><li>Skipping sunscreen on cloudy days or indoors near windows.</li><li>Trying many new products in the same week.</li><li>Applying home remedies such as lemon juice or toothpaste directly on the skin.</li></ul>',
                'faqs' => $this->faqs([
                    'Do I need sunscreen every day in Pakistan?' => 'Yes. The sun is strong for most of the year, so a broad-spectrum SPF 30 or higher sunscreen every morning helps prevent tanning, dark spots and early ageing.',
                    'Should oily skin use a moisturiser?' => 'Yes. A light, oil-free gel moisturiser keeps the skin balanced. Skipping moisturiser can make some people’s skin produce even more oil.',
                    'How often should I wash my face?' => 'Twice a day is enough for most people: once in the morning and once at night. Washing too often can dry out the skin.',
                ]),
                'related' => ['Everyday Makeup Look' => '/beauty-style/everyday-makeup-look-for-beginners', 'Men’s Grooming Basics' => '/beauty-style/mens-grooming-basics-skin-beard-and-hair'],
            ],
            [
                'category' => 'makeup',
                'slug' => 'everyday-makeup-look-for-beginners',
                'image' => 'beauty-makeup-everyday',
                'title' => 'Everyday Makeup Look for Beginners: Step by Step',
                'meta_title' => 'Easy Everyday Makeup Look for Beginners (Step by Step)',
                'meta_description' => 'A natural everyday makeup look for office, university or daily wear, with the order to apply products and tips for hot weather.',
                'excerpt' => 'A light, natural look you can finish in ten minutes, with tips to make makeup last in the heat.',
                'tags' => ['Makeup', 'Beginners', 'Everyday Look', 'Natural Makeup'],
                'body' => '<p>An everyday makeup look should make you feel fresh, not heavy. The steps below work for office, university and family visits, and need only a few basic products.</p>'
                    .'<h2>What You Need</h2><ul><li>Moisturiser and sunscreen (your skincare base)</li><li>A lightweight foundation, BB cream or tinted moisturiser</li><li>Concealer for under-eyes and spots</li><li>Loose or pressed powder</li><li>Mascara, a brow pencil and a lip tint or lipstick</li></ul>'
                    .'<h2>Step-by-Step Order</h2><ol><li>Start with clean, moisturised skin and sunscreen. Wait a few minutes.</li><li>Apply a thin layer of base with fingers, a sponge or a brush.</li><li>Dab concealer only where needed and blend the edges.</li><li>Set the T-zone with a little powder to control shine.</li><li>Fill brows lightly with short strokes.</li><li>Curl lashes if you like, then add one coat of mascara.</li><li>Finish with a lip colour close to your natural shade, and a soft blush on the cheeks.</li></ol>'
                    .'<h2>Make It Last in Hot Weather</h2><ul><li>Use less product. Thin layers stay longer than heavy ones.</li><li>Choose water-resistant mascara for humid days.</li><li>Carry blotting paper instead of adding more powder through the day.</li></ul>'
                    .'<h2>Hygiene Tips</h2><p>Wash brushes and sponges every week, never share mascara or eyeliner, and replace mascara about every three months. Always remove makeup before sleeping.</p>',
                'faqs' => $this->faqs([
                    'How do I choose the right foundation shade?' => 'Test the shade on your jawline in daylight. The right shade disappears into your skin without looking grey, orange or lighter than your neck.',
                    'Can I wear makeup every day?' => 'Yes, as long as you remove it fully at night, keep your tools clean and stop using any product that irritates your skin.',
                    'How do I stop makeup melting in summer?' => 'Use a light moisturiser, thin layers of base, a little powder on oily areas and blotting paper during the day.',
                ]),
                'related' => ['Daily Skincare Routine' => '/beauty-style/simple-daily-skincare-routine-for-pakistani-weather', 'Bridal Beauty Checklist' => '/beauty-style/bridal-beauty-preparation-checklist'],
            ],
            [
                'category' => 'hair-care',
                'slug' => 'hair-care-tips-for-healthy-hair-in-hot-weather',
                'image' => 'beauty-hair-care',
                'title' => 'Hair Care Tips for Healthy Hair in Hot and Humid Weather',
                'meta_title' => 'Hair Care Tips for Healthy Hair in Pakistan’s Heat and Humidity',
                'meta_description' => 'Practical hair care for hot and humid weather: washing, oiling, conditioning, heat styling and protecting hair from sun and dust.',
                'excerpt' => 'Washing, conditioning, oiling and styling tips to keep hair soft and frizz-free through summer.',
                'tags' => ['Hair Care', 'Frizzy Hair', 'Hair Oil', 'Summer Hair'],
                'body' => '<p>Sweat, dust, hard water and strong sun can leave hair dry, frizzy or greasy. A few simple habits make a big difference.</p>'
                    .'<h2>Washing and Conditioning</h2><ul><li>Wash two to three times a week, or more often if your scalp gets oily or you sweat a lot.</li><li>Focus shampoo on the scalp and conditioner on the lengths and ends.</li><li>Rinse with cool or lukewarm water, not very hot water.</li></ul>'
                    .'<h2>Oiling the Right Way</h2><p>Many families in Pakistan oil their hair regularly. A light massage with coconut, almond or olive oil before washing can soften dry ends. Keep it to a few hours or overnight, then wash it out, and use less if your scalp is oily or has dandruff.</p>'
                    .'<h2>Heat Styling</h2><ul><li>Let hair air-dry partly before using a dryer.</li><li>Use a heat protectant before straighteners or curling tongs.</li><li>Keep tools on a medium setting and limit heat styling to a few times a week.</li></ul>'
                    .'<h2>Protect Hair Outdoors</h2><p>Cover your hair with a scarf, dupatta or cap in strong sun and dust. After swimming, rinse hair with clean water.</p>'
                    .'<h2>Food and Lifestyle</h2><p>Drink enough water, eat a balanced diet with protein, and sleep well. Sudden or heavy hair fall should be checked by a doctor, as it can be linked to health conditions.</p>',
                'faqs' => $this->faqs([
                    'How often should I oil my hair?' => 'Once or twice a week before washing is enough for most people. Do not leave heavy oil on an oily or flaky scalp for long.',
                    'How can I control frizz in humid weather?' => 'Use conditioner after every wash, avoid rubbing hair with a rough towel, and apply a small amount of serum or leave-in cream on damp ends.',
                    'Is daily hair washing bad?' => 'Not always. If you sweat a lot, gentle daily washing with a mild shampoo is fine. Always use conditioner on the lengths.',
                ]),
                'related' => ['Bridal Beauty Checklist' => '/beauty-style/bridal-beauty-preparation-checklist', 'Men’s Grooming Basics' => '/beauty-style/mens-grooming-basics-skin-beard-and-hair'],
            ],
            [
                'category' => 'nail-care',
                'slug' => 'nail-care-at-home-for-strong-healthy-nails',
                'image' => 'beauty-nail-care',
                'title' => 'Nail Care at Home: How to Keep Nails Strong and Healthy',
                'meta_title' => 'Nail Care at Home: Simple Tips for Strong, Healthy Nails',
                'meta_description' => 'Easy at-home nail care: shaping, cuticle care, moisturising and safe use of nail polish and henna for strong, healthy nails.',
                'excerpt' => 'A simple weekly manicure routine, plus how to avoid weak, peeling and broken nails.',
                'tags' => ['Nail Care', 'Manicure', 'Nail Polish', 'Home Care'],
                'body' => '<p>Healthy nails do not need a salon visit every week. A short routine at home keeps them clean, smooth and less likely to break.</p>'
                    .'<h2>Weekly Home Manicure</h2><ol><li>Remove old polish with a gentle remover.</li><li>Trim nails straight across and shape the tips with a file, moving in one direction.</li><li>Soak fingers in warm soapy water for a few minutes.</li><li>Gently push back cuticles with a soft pusher. Do not cut them.</li><li>Massage hand cream or oil into nails and cuticles.</li></ol>'
                    .'<h2>Daily Habits</h2><ul><li>Wear gloves when washing dishes or using cleaning products.</li><li>Moisturise hands after washing them, especially in winter.</li><li>Do not bite nails or use them to open cans and packets.</li></ul>'
                    .'<h2>Nail Polish and Henna</h2><p>Give nails a break between polish layers, use a base coat to prevent staining, and buy products from trusted brands. If you use mehndi on nails, choose natural henna and avoid “black henna”, which can cause skin reactions.</p>'
                    .'<h2>When to See a Doctor</h2><p>Changes in nail colour or shape, pain, swelling or signs of infection around the nail should be checked by a doctor.</p>',
                'faqs' => $this->faqs([
                    'Why do my nails break easily?' => 'Frequent contact with water and cleaning products, dryness and using nails as tools are common reasons. Gloves and regular moisturising usually help.',
                    'Should I cut my cuticles?' => 'It is better not to. Cuticles protect the nail from infection. Soften them and gently push them back instead.',
                    'Is it safe to wear nail polish all the time?' => 'It is better to give nails a few days off between manicures and to use a base coat to reduce staining.',
                ]),
                'related' => ['Bridal Beauty Checklist' => '/beauty-style/bridal-beauty-preparation-checklist', 'Everyday Makeup Look' => '/beauty-style/everyday-makeup-look-for-beginners'],
            ],
            [
                'category' => 'fashion-clothing',
                'slug' => 'build-a-smart-wardrobe-on-a-budget',
                'image' => 'beauty-fashion',
                'title' => 'How to Build a Smart Wardrobe on a Budget',
                'meta_title' => 'How to Build a Smart Wardrobe on a Budget in Pakistan',
                'meta_description' => 'Build a wardrobe that works for office, events and daily wear: key pieces, fabrics for each season and tips to shop the sales wisely.',
                'excerpt' => 'Key pieces, season-wise fabrics and smart shopping tips to dress well without overspending.',
                'tags' => ['Fashion', 'Wardrobe', 'Budget Shopping', 'Lawn', 'Office Wear'],
                'body' => '<p>A smart wardrobe is not about owning more clothes. It is about having pieces that mix and match, suit the weather and fit well.</p>'
                    .'<h2>Start With the Basics</h2><ul><li>Two or three plain shirts or kurtas in neutral colours</li><li>Well-fitted trousers or shalwars in black, white and beige</li><li>A few dupattas or scarves that add colour to plain outfits</li><li>One formal outfit for weddings and events</li><li>Comfortable everyday shoes and one smart pair</li></ul>'
                    .'<h2>Fabrics by Season</h2>'
                    .$this->table('Season', 'Good choices', ['Summer' => 'Lawn, cotton, linen and other breathable fabrics in lighter colours', 'Spring and autumn' => 'Cambric, light khaddar and cotton blends', 'Winter' => 'Khaddar, karandi, wool blends, shawls and layering pieces'])
                    .'<h2>Shop Smarter</h2><ol><li>Check what you already own before buying anything new.</li><li>Make a short list and stick to it during sales.</li><li>Buy fabric and get it stitched if ready-made sizes do not fit you.</li><li>Choose quality over quantity for items you wear often.</li></ol>'
                    .'<h2>Care for Your Clothes</h2><p>Wash coloured clothes separately in cold water, dry them in shade to stop fading, and store winter clothes clean and dry.</p>',
                'faqs' => $this->faqs([
                    'Which colours are easiest to mix and match?' => 'Neutral colours such as white, black, beige, grey and navy go with almost everything. Add colour with dupattas, scarves and accessories.',
                    'Is ready-made or stitched clothing better value?' => 'Stitched clothing often fits better and can cost less. Ready-made clothes save time. Choose based on your budget, fit and how soon you need it.',
                    'How do I stop lawn clothes fading?' => 'Wash them inside out in cold water with a mild detergent, and dry them in the shade instead of direct sun.',
                ]),
                'related' => ['Bridal Beauty Checklist' => '/beauty-style/bridal-beauty-preparation-checklist', 'Everyday Makeup Look' => '/beauty-style/everyday-makeup-look-for-beginners'],
            ],
            [
                'category' => 'bridal',
                'slug' => 'bridal-beauty-preparation-checklist',
                'image' => 'beauty-bridal',
                'title' => 'Bridal Beauty Preparation Checklist: What to Do and When',
                'meta_title' => 'Bridal Beauty Preparation Checklist for Pakistani Weddings',
                'meta_description' => 'A month-by-month bridal beauty checklist for skin, hair, nails, makeup trials and the mehndi, barat and walima days.',
                'excerpt' => 'A calm, month-by-month plan for skin, hair, makeup trials and the wedding days.',
                'tags' => ['Bridal', 'Wedding', 'Bridal Makeup', 'Mehndi', 'Checklist'],
                'body' => '<p>Wedding preparations are busy, and beauty plans often get left to the last week. Starting early keeps things calm and avoids reactions to new products just before the big day.</p>'
                    .'<h2>Bridal Beauty Timeline</h2>'
                    .$this->table('When', 'What to do', ['Three months before' => 'Start a simple, regular skincare routine and book your makeup artist', 'Two months before' => 'Get a hair trim and decide on hairstyles for each event', 'One month before' => 'Have a full makeup and hair trial in similar light to the event', 'Two weeks before' => 'Final facial or clean-up, if you want one, and eyebrow shaping', 'One week before' => 'No new products. Sleep well, drink water and rest', 'Two days before' => 'Mehndi, manicure and pedicure'])
                    .'<h2>Makeup Trial Tips</h2><ul><li>Take photos of the trial in daylight and with flash.</li><li>Tell the artist about any skin sensitivity or allergies.</li><li>Check that the base matches your neck and hands.</li><li>Ask what products will be used, and test them on a small area first.</li></ul>'
                    .'<h2>Your Wedding-Day Kit</h2><ul><li>Blotting paper, a small powder and your lipstick</li><li>Safety pins, hair pins and a small mirror</li><li>Tissues, wet wipes and a water bottle</li><li>Pain relief or any medicines you normally need</li></ul>'
                    .'<h2>Look After Yourself</h2><p>Eat regular meals, sleep enough and keep some quiet time for yourself during the functions. Rest shows on your face more than any product.</p>',
                'faqs' => $this->faqs([
                    'When should I book a bridal makeup artist?' => 'Popular artists are often booked months ahead during wedding season, so book as early as you can, ideally two to three months before.',
                    'Should I try a new facial just before the wedding?' => 'No. Try any new treatment at least a few weeks before, so your skin has time to settle if it reacts.',
                    'Do I need a makeup trial?' => 'A trial is strongly recommended. It lets you check the look, the shade match and how long it lasts before the wedding day.',
                ]),
                'related' => ['Daily Skincare Routine' => '/beauty-style/simple-daily-skincare-routine-for-pakistani-weather', 'Nail Care at Home' => '/beauty-style/nail-care-at-home-for-strong-healthy-nails'],
            ],
            [
                'category' => 'mens-grooming',
                'slug' => 'mens-grooming-basics-skin-beard-and-hair',
                'image' => 'beauty-mens-grooming',
                'title' => 'Men’s Grooming Basics: Skin, Beard and Hair',
                'meta_title' => 'Men’s Grooming Basics: Simple Skin, Beard and Hair Routine',
                'meta_description' => 'A simple grooming routine for men: face wash, moisturiser, sunscreen, beard care, shaving tips and hair care for everyday life in Pakistan.',
                'excerpt' => 'A five-minute daily routine for clear skin, a neat beard and healthy hair.',
                'tags' => ['Men’s Grooming', 'Beard Care', 'Shaving', 'Men’s Skincare'],
                'body' => '<p>Good grooming helps you look confident at interviews, at work and at family events. It does not need many products, just a steady routine.</p>'
                    .'<h2>Daily Skin Routine</h2><ol><li>Wash your face morning and night with a gentle face wash, not body soap.</li><li>Apply a light moisturiser.</li><li>Use sunscreen in the morning if you spend time outdoors.</li></ol>'
                    .'<h2>Beard Care</h2><ul><li>Wash your beard a few times a week with a mild cleanser.</li><li>Use a few drops of beard oil to soften hair and the skin underneath.</li><li>Trim regularly and keep the neckline and cheek line neat.</li></ul>'
                    .'<h2>Shaving Without Irritation</h2><ol><li>Shave after a warm shower or wash, when hair is soft.</li><li>Use shaving cream or gel and a sharp, clean blade.</li><li>Shave in the direction of hair growth.</li><li>Rinse with cool water and apply a fragrance-free moisturiser.</li></ol>'
                    .'<h2>Hair and Hygiene</h2><p>Get a haircut every four to six weeks, wash hair regularly in summer, and keep nails trimmed. Use a deodorant and wear clean, ironed clothes. These small things are noticed first, especially at a job interview.</p>',
                'faqs' => $this->faqs([
                    'Do men need a moisturiser?' => 'Yes. Heat, sun, shaving and winter air dry out the skin. A light moisturiser keeps it comfortable and reduces irritation.',
                    'How do I avoid razor bumps?' => 'Soften the hair first, use a sharp blade and shaving gel, shave with the direction of growth and avoid pressing hard.',
                    'How often should I trim my beard?' => 'Every one to two weeks keeps most beards neat. Shape the neckline more often if you prefer a sharp look.',
                ]),
                'related' => ['Daily Skincare Routine' => '/beauty-style/simple-daily-skincare-routine-for-pakistani-weather', 'Hair Care in Hot Weather' => '/beauty-style/hair-care-tips-for-healthy-hair-in-hot-weather'],
            ],
        ];
    }
}
